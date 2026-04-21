<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;
?>

<?php
$apiUrl = "http://localhost:9090/jog_3d/api/CategorySub/get_order_details.php";

$token = "413d893dbf3f4ffd4619712fd15ba501ed5acaf687253ab8961baf0482aaee78"; // replace with actual token

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $token",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Curl Error: ' . curl_error($ch);
    exit;
}

curl_close($ch);

// JSON → Array
$data = json_decode($response, true);


?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style/ols_3d_order.css">

<div class="ols3d-module">

  <!-- Page Header -->
  <div class="ols3d-page-header">
    <div>
      <h1>3D Orders</h1>
      <?php
      $total_orders = !empty($data['data']) ? count($data['data']) : 0;
      ?>
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
  <!-- api div -->
  <div class="ols3d-grid" id="ols3d-grid">
    <?php
    if (!empty($data['data'])) {
      foreach ($data['data'] as $row) {

        $order_id_enc = customEncode($row['order_id']);
        $order_date = !empty($row['added_date']) 
            ? date('d-m-Y', strtotime($row['added_date'])) 
            : '—';
          ?>
        <div class="ols3d-card"
            data-name="<?= htmlspecialchars(strtolower($row['name'])) ?>"
            data-id="<?= $row['order_id'] ?>"
            data-status="New">

          <div class="ols3d-card-img">
            <img src="<?= htmlspecialchars($row['image']) ?>"
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
