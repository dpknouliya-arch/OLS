<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

$sql = "SELECT
            o.order_id,
            o.design_id,
            o.user_id,
            o.textdecals,
            o.imagedecals,
            o.added_date,
            d.id AS design_id,
            d.subcategory_id,
            d.coller_id,
            d.style_id,
            d.stripes_id,
            d.fabric_id,
            d.name,
            d.image,
            d.model,
            d.price,
            d.modal_type,
            d.primary_color,
            d.secondary_color,
            d.tertiary_color,
            d.insert_date,
            d.updated_date
        FROM design_order o
        INNER JOIN designs d ON o.design_id = d.id
        WHERE o.user_id = $user_id
        ORDER BY o.added_date DESC";

$result = $conn4->query($sql);
$total_orders = ($result !== false) ? $result->num_rows : 0;
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style/ols_3d_order.css">

<div class="ols3d-module">

  <!-- Page Header -->
  <div class="ols3d-page-header">
    <div>
      <h1>3D Orders</h1>
      <p><?= $total_orders ?> order<?= $total_orders !== 1 ? 's' : '' ?> found</p>
    </div>
  </div>

  <!-- Filters Row -->
  <div class="ols3d-filters">
    <div class="ols3d-search">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
      </svg>
      <input type="text" id="ols3d-search-input" placeholder="Search orders…">
    </div>
    <div class="ols3d-filter-btns">
      <button class="ols3d-filter-btn active" data-status="All">All</button>
      <button class="ols3d-filter-btn" data-status="New">New</button>
      <button class="ols3d-filter-btn" data-status="Pre-approval">Pre-approval</button>
      <button class="ols3d-filter-btn" data-status="Approved">Approved</button>
      <button class="ols3d-filter-btn" data-status="Rejected">Rejected</button>
    </div>
  </div>

  <!-- Orders Grid -->
  <div class="ols3d-grid" id="ols3d-grid">
    <?php
    if ($result !== false && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $order_id_enc = customEncode($row['order_id']);
        $order_date   = !empty($row['added_date']) ? date('d-m-Y', strtotime($row['added_date'])) : '—';
    ?>
    <div class="ols3d-card"
         data-name="<?= htmlspecialchars(strtolower($row['name'])) ?>"
         data-id="<?= $row['order_id'] ?>"
         data-status="New">
      <div class="ols3d-card-img">
        <img src="https://jogsports.com/jogdigital/admin/uploads/designs/images/<?= htmlspecialchars($row['image']) ?>"
             alt="<?= htmlspecialchars($row['name']) ?>">
      </div>
      <div class="ols3d-card-body">
        <div class="ols3d-card-meta">
          <div>
            <div class="ols3d-card-id">#<?= $row['order_id'] ?></div>
            <div class="ols3d-card-date">Created: <?= $order_date ?></div>
          </div>
          <span class="ols3d-badge ols3d-badge-new">New</span>
        </div>
        <div class="ols3d-card-name"><?= htmlspecialchars($row['name']) ?></div>
        <a href="?vp=<?= base64_encode('3d_order_details') ?>&order_id=<?= $order_id_enc ?>"
           class="ols3d-btn-view">
          View Order
        </a>
      </div>
    </div>
    <?php
      }
    } else {
    ?>
    <div class="ols3d-empty">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD3E8" stroke-width="1.4">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
      </svg>
      <p>No 3D orders found.</p>
    </div>
    <?php } ?>
  </div>

</div>

<script>
(function () {
  var searchInput = document.getElementById('ols3d-search-input');
  var filterBtns  = document.querySelectorAll('.ols3d-filter-btn');
  var cards       = document.querySelectorAll('#ols3d-grid .ols3d-card');

  function filterOrders() {
    var q      = searchInput.value.toLowerCase().trim();
    var active = document.querySelector('.ols3d-filter-btn.active');
    var status = active ? active.dataset.status : 'All';

    cards.forEach(function (card) {
      var name    = card.dataset.name   || '';
      var id      = String(card.dataset.id || '');
      var cstatus = card.dataset.status || '';

      var matchSearch = !q || name.includes(q) || id.includes(q);
      var matchStatus = status === 'All' || cstatus === status;

      card.style.display = (matchSearch && matchStatus) ? '' : 'none';
    });
  }

  searchInput.addEventListener('input', filterOrders);

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterBtns.forEach(function (b) { b.classList.remove('active'); });
      this.classList.add('active');
      filterOrders();
    });
  });
}());
</script>
