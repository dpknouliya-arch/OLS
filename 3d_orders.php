<?php
include('check-session.php');
require_once('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = (int) $obj_user->user_id;
// $brand_id is already resolved by db.php via get_ols_brand_id()

// ── 1. Fetch 3D orders for this user, scoped to the active brand
$api_response = callAPI("get_order_details.php?brand_id=" . $brand_id . "&user_id=" . $user_id);
$api_orders   = !empty($api_response['data']) ? $api_response['data'] : [];

// Client-side safety net: strip any cross-brand orders the API might return
$api_orders = array_values(array_filter($api_orders, function ($o) use ($brand_id) {
    return !isset($o['brand_id']) || (int)$o['brand_id'] === $brand_id;
}));
// ── 2. Load ALL tbl_order_form rows for this user in ONE query.
//       Map: design_order_id → { is_submitted, order_status }
//       This avoids N+1 queries inside the loop below.
$local_map = [];
$stmt = $conn->prepare(
    "SELECT design_order_id, is_submitted, order_status
     FROM   tbl_order_form
     WHERE  user_id = ?
       AND  design_order_id IS NOT NULL"
);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $key = (int) $r['design_order_id'];
        // When multiple rows exist for the same order, prefer the submitted one
        // so the exclusion logic is never bypassed.
        if (!isset($local_map[$key]) || (int)$r['is_submitted'] > $local_map[$key]['is_submitted']) {
            $local_map[$key] = [
                'is_submitted' => (int) $r['is_submitted'],
                'order_status' => trim((string) $r['order_status']),
            ];
        }
    }
    $stmt->close();
}

// ── 3. Merge API orders with local data and assign display status.
//
//   This page shows SUBMITTED orders (is_submitted = 1) tracked through production.
//   Unsubmitted drafts (is_submitted = 0) belong on save_draft.php.
//   Orders not yet in tbl_order_form at all are shown as "New".
//
$orders = [];

foreach ($api_orders as $order) {
    $oid   = (int) ($order['order_id'] ?? 0);
    $local = $local_map[$oid] ?? null;

    // Determine submission state:
    //   0 = not started or form in progress (needs submission)
    //   1 = order form fully completed and submitted
    $is_submitted = $local ? $local['is_submitted'] : 0;

    // Determine production status label:
    //   • Not in local DB            → "New"
    //   • is_submitted=1, status=new → "New"  (awaiting production update from JOG)
    //   • Any other order_status     → normalised display value
    $status = 'New';
    if ($local && $local['order_status'] !== '') {
        $raw = strtolower($local['order_status']);
        $status_map_norm = [
            'new'               => 'New',
            'producing'         => 'Producing',
            'processing'        => 'Processing',
            'received'          => 'Received',
            'partially shipped' => 'Partially Shipped',
            'shipped'           => 'Shipped',
            'archived'          => 'Archived',
            'delivered'         => 'Delivered',
        ];
        $status = $status_map_norm[$raw] ?? ucfirst($raw);
    }

    $order['_status']       = $status;
    $order['_is_submitted'] = $is_submitted;
    $orders[] = $order;
}

$total_orders = count($orders);

// ── 4. Badge CSS class map (add matching rules to ols_3d_order.css)
$badge_cls = [
    'New'               => 'ols3d-badge-new',
    'Producing'         => 'ols3d-badge-producing',
    'Processing'        => 'ols3d-badge-processing',
    'Received'          => 'ols3d-badge-received',
    'Partially Shipped' => 'ols3d-badge-partial',
    'Shipped'           => 'ols3d-badge-shipped',
    'Archived'          => 'ols3d-badge-archived',
    'Delivered'         => 'ols3d-badge-delivered',
];

// All supported filter statuses in display order
$all_statuses = ['New', 'Producing', 'Processing', 'Received',
                 'Partially Shipped', 'Shipped', 'Archived', 'Delivered'];
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style/ols_3d_order.css">

<style>
  /* ── Badge colours ────────────────────────────────────────── */
  .ols3d-badge-new        { background:#e0f2fe; color:#0369a1; }
  .ols3d-badge-producing  { background:#fef9c3; color:#854d0e; }
  .ols3d-badge-processing { background:#ede9fe; color:#6d28d9; }
  .ols3d-badge-received   { background:#dcfce7; color:#166534; }
  .ols3d-badge-partial    { background:#ffedd5; color:#9a3412; }
  .ols3d-badge-shipped    { background:#dbeafe; color:#1e40af; }
  .ols3d-badge-archived   { background:#f3f4f6; color:#374151; }
  .ols3d-badge-delivered  { background:#d1fae5; color:#065f46; }

  /* ── Submission segment tabs ──────────────────────────────── */
  .ols3d-segment          { display:flex; gap:0; margin-bottom:16px; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; width:fit-content; }
  .ols3d-segment-btn      { padding:8px 20px; font-size:13px; font-weight:500; border:none; background:#fff; color:#64748b; cursor:pointer; border-right:1px solid #e2e8f0; transition:background .15s,color .15s; white-space:nowrap; }
  .ols3d-segment-btn:last-child { border-right:none; }
  .ols3d-segment-btn.active     { background:#1d4ed8; color:#fff; }
  .ols3d-segment-btn .ols3d-seg-count { display:inline-block; margin-left:6px; padding:1px 7px; border-radius:12px; font-size:11px; font-weight:700; background:rgba(255,255,255,.25); }
  .ols3d-segment-btn:not(.active) .ols3d-seg-count { background:#f1f5f9; color:#475569; }

  /* ── Pending-submission indicator on card ─────────────────── */
  .ols3d-needs-submit-bar { background:#fef3c7; border-bottom:1px solid #fde68a; padding:6px 14px; font-size:11.5px; font-weight:600; color:#92400e; display:flex; align-items:center; gap:6px; }
</style>

<?php
// Pre-count for segment tab badges
$count_needs = 0;
$count_submitted = 0;
foreach ($orders as $o) {
    if ($o['_is_submitted']) $count_submitted++;
    else $count_needs++;
}
?>

<div class="ols3d-module">

  <!-- Page Header -->
  <div class="ols3d-page-header">
    <div>
      <h1>3D Orders</h1>
      <p><?= $total_orders ?> order<?= $total_orders !== 1 ? 's' : '' ?> found</p>
    </div>
  </div>

  <!-- Submission Segment Tabs -->
  <div class="ols3d-segment">
    <button class="ols3d-segment-btn active" data-segment="all">
      All <span class="ols3d-seg-count"><?= $total_orders ?></span>
    </button>
    <button class="ols3d-segment-btn" data-segment="0">
      Needs Submission <span class="ols3d-seg-count"><?= $count_needs ?></span>
    </button>
    <button class="ols3d-segment-btn" data-segment="1">
      Submitted <span class="ols3d-seg-count"><?= $count_submitted ?></span>
    </button>
  </div>

  <!-- Status Filters Row -->
  <div class="ols3d-filters">
    <div class="ols3d-search">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
      </svg>
      <input type="text" id="ols3d-search-input" placeholder="Search orders…">
    </div>
    <div class="ols3d-filter-btns">
      <button class="ols3d-filter-btn active" data-status="All">All Status</button>
      <?php foreach ($all_statuses as $s): ?>
      <button class="ols3d-filter-btn" data-status="<?= htmlspecialchars($s) ?>">
        <?= htmlspecialchars($s) ?>
      </button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Order Cards Grid -->
  <div class="ols3d-grid" id="ols3d-grid">
    <?php if ($orders): ?>
      <?php foreach ($orders as $order):
        $oid          = (int) $order['order_id'];
        $enc_id       = customEncode($oid);
        $status       = $order['_status'];
        $brand_id     = (int) ($order['brand_id'] ?? 1);
        $is_submitted = $order['_is_submitted'];
        $cls          = $badge_cls[$status] ?? 'ols3d-badge-new';
        $date_str     = !empty($order['added_date'])
                        ? date('d-m-Y', strtotime($order['added_date'])) : '—';
        $name         = htmlspecialchars($order['name'] ?? '');
        $img          = htmlspecialchars($order['jersey_style_image'] ?? '');
      ?>
      <div class="ols3d-card"
           data-name="<?= strtolower($name) ?>"
           data-id="<?= $oid ?>"
           data-status="<?= htmlspecialchars($status) ?>"
           data-submitted="<?= $is_submitted ?>">

        <div class="ols3d-card-img">
          <img src="<?= S3_Buckets ?><?= $brand_id ?>/<?= $img ?>" alt="<?= $name ?>">
        </div>

        <?php if (!$is_submitted): ?>
        <div class="ols3d-needs-submit-bar">
          ⚠ Order form not yet submitted
        </div>
        <?php endif; ?>

        <div class="ols3d-card-body">
          <div class="ols3d-card-meta">
            <div>
              <div class="ols3d-card-id">#<?= $oid ?></div>
              <div class="ols3d-card-date">Created: <?= $date_str ?></div>
            </div>
            <span class="ols3d-badge <?= $cls ?>"><?= htmlspecialchars($status) ?></span>
          </div>

          <div class="ols3d-card-name"><?= $name ?></div>

          <a href="?vp=<?= base64_encode('3d_order_details') ?>&order_id=<?= $enc_id ?>"
             class="ols3d-btn-view">
            <?= $is_submitted ? 'View Order' : 'Complete & Submit' ?>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="ols3d-empty">
        <p>No 3D orders found.</p>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
(function () {
  var searchInput  = document.getElementById('ols3d-search-input');
  var statusBtns   = document.querySelectorAll('.ols3d-filter-btn');
  var segmentBtns  = document.querySelectorAll('.ols3d-segment-btn');
  var grid         = document.getElementById('ols3d-grid');

  function activeValue(selector, attr) {
    var el = document.querySelector(selector + '.active');
    return el ? el.dataset[attr] : null;
  }

  function filterOrders() {
    var q        = searchInput.value.toLowerCase().trim();
    var status   = activeValue('.ols3d-filter-btn',  'status')  || 'All';
    var segment  = activeValue('.ols3d-segment-btn', 'segment') || 'all';

    grid.querySelectorAll('.ols3d-card').forEach(function (card) {
      var name      = card.dataset.name      || '';
      var id        = String(card.dataset.id || '');
      var cstatus   = card.dataset.status    || '';
      var submitted = card.dataset.submitted || '0';

      var matchSearch   = !q || name.includes(q) || id.includes(q);
      var matchStatus   = status  === 'All' || cstatus   === status;
      var matchSegment  = segment === 'all' || submitted === segment;

      card.style.display = (matchSearch && matchStatus && matchSegment) ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterOrders);

  statusBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      statusBtns.forEach(function (b) { b.classList.remove('active'); });
      this.classList.add('active');
      filterOrders();
    });
  });

  segmentBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      segmentBtns.forEach(function (b) { b.classList.remove('active'); });
      this.classList.add('active');
      if (this.dataset.segment === 'all') {
        statusBtns.forEach(function (b) { b.classList.remove('active'); });
        document.querySelector('.ols3d-filter-btn[data-status="All"]').classList.add('active');
      }
      filterOrders();
    });
  });
}());
</script>
