<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = $obj_user->user_id;

if (!isset($_GET['order_id'])) { echo "<p>Invalid order ID.</p>"; exit; }
$order_id = customDecode($_GET['order_id']);

$api_result = callAPI("get_order.php?order_id=$order_id");
$data       = $api_result['data'] ?? null;
if (!$data) { echo "<p>No data found.</p>"; exit; }

$order             = $data;
$order_team_data   = $data['team']   ?? [];
$order_design_data = isset($data['design']) ? [$data['design']] : [];
$designId          = $order['design_id']   ?? 0;
$sock_design       = $order['sock_design'] ?? '';
$added_date        = $order['added_date']  ?? '';

$a_data = [0 => null, 1 => null];
$sql_select = "SELECT * FROM tbl_address WHERE user_id='" . (int)$user_id . "' AND enable=1 AND (is_billing_addr=1 OR is_deliver_addr=1) ORDER BY is_billing_addr DESC, is_deliver_addr DESC";
$rs_select = $conn->query($sql_select);
if ($rs_select) {
    while ($row_select = $rs_select->fetch_assoc()) {
        if ($row_select['is_billing_addr'] == '1' && $a_data[0] === null) { $a_data[0] = $row_select; }
        if ($row_select['is_deliver_addr'] == '1' && $a_data[1] === null) { $a_data[1] = $row_select; }
    }
}

$design_name  = !empty($order_design_data[0]['name'])       ? $order_design_data[0]['name']       : '—';
$jersey_type  = !empty($order_design_data[0]['modal_type']) ? $order_design_data[0]['modal_type'] : '—';
$design_price = !empty($order_design_data[0]['price'])      ? $order_design_data[0]['price']      : '0.00';
$design_image = !empty($order_design_data[0]['image'])      ? $order_design_data[0]['image']      : '';
$order_id_enc = customEncode($order_id);
$total_qty    = count($order_team_data);
$order_date_fmt = !empty($added_date) ? date('d/m/Y', strtotime($added_date)) : date('d/m/Y');

$unique_sizes = [];
foreach ($order_team_data as $td) {
    if (!empty($td['jersey_size']) && !in_array($td['jersey_size'], $unique_sizes)) {
        $unique_sizes[] = $td['jersey_size'];
    }
}
$sizes_str = !empty($unique_sizes) ? implode(', ', $unique_sizes) : '—';

// Prefetch saved order form data for this 3D order
$of_row = null;
$stmt_of = $conn->prepare(
    "SELECT * FROM tbl_order_form WHERE design_order_id=? ORDER BY is_submitted DESC LIMIT 1"
);
$stmt_of->bind_param("i", $order_id);
$stmt_of->execute();
$res_of = $stmt_of->get_result();
if ($res_of->num_rows > 0) {
    $of_row = $res_of->fetch_assoc();
}
$stmt_of->close();

$TOKEN_KEY = 'jogsports_secure_key_' . session_id();
function encAddrId($id, $key) {
    $iv  = openssl_random_pseudo_bytes(16);
    $enc = openssl_encrypt((string)$id, 'AES-128-CBC', $key, 0, $iv);
    return base64_encode($enc . '::' . base64_encode($iv));
}
$billing_addr_enc  = !empty($a_data[0]['addr_id']) ? encAddrId($a_data[0]['addr_id'], $TOKEN_KEY) : '';
$delivery_addr_enc = !empty($a_data[1]['addr_id']) ? encAddrId($a_data[1]['addr_id'], $TOKEN_KEY) : '';

// Pre-encode existing address data for JS
$billing_js  = json_encode([
    'company'  => $a_data[0]['addr_name']    ?? '',
    'contact'  => $a_data[0]['contact_name'] ?? '',
    'address'  => $a_data[0]['address']      ?? '',
    'city'     => $a_data[0]['city']         ?? '',
    'state'    => $a_data[0]['state']        ?? '',
    'postal'   => $a_data[0]['zip_code']     ?? ($a_data[0]['zipcode'] ?? ''),
    'country'  => $a_data[0]['country']      ?? '',
    'phone'    => $a_data[0]['tel']          ?? '',
    'email'    => $a_data[0]['email']        ?? '',
    'tax'      => $a_data[0]['tax_id']       ?? ($a_data[0]['tax_no'] ?? ''),
    'enc_id'   => $billing_addr_enc,
]);
$delivery_js = json_encode([
    'company'  => $a_data[1]['addr_name']    ?? '',
    'contact'  => $a_data[1]['contact_name'] ?? '',
    'address'  => $a_data[1]['address']      ?? '',
    'city'     => $a_data[1]['city']         ?? '',
    'state'    => $a_data[1]['state']        ?? '',
    'postal'   => $a_data[1]['zip_code']     ?? ($a_data[1]['zipcode'] ?? ''),
    'country'  => $a_data[1]['country']      ?? '',
    'phone'    => $a_data[1]['tel']          ?? '',
    'email'    => $a_data[1]['email']        ?? '',
    'tax'      => $a_data[1]['tax_id']       ?? ($a_data[1]['tax_no'] ?? ''),
    'enc_id'   => $delivery_addr_enc,
]);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style/ols_3d_order.css">

<?php foreach ($order_design_data as $value): ?>
<input type="hidden" name="coller_id"  value="<?= htmlspecialchars($value['coller_id'])  ?>">
<input type="hidden" name="style_id"   value="<?= htmlspecialchars($value['style_id'])   ?>">
<input type="hidden" name="stripes_id" value="<?= htmlspecialchars($value['stripes_id']) ?>">
<?php endforeach; ?>
<input type="hidden" name="order_id"   id="order_id"  value="<?= $order_id ?>">
<input type="hidden" name="design_id"  value="<?= $designId ?>">
<input type="hidden" name="order_date" id="order_date" value="<?= date('Y-m-d') ?>">

<div class="ols3d-module">

  <!-- Title Row -->
  <div class="ols3d-title-row">
    <a href="?vp=<?= base64_encode('3d_order_roster') ?>&order_id=<?= $order_id_enc ?>" class="ols3d-back-btn">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
    </a>
    <div>
      <h1>Checkout</h1>
      <p class="ols3d-breadcrumb">
        <a href="?vp=<?= base64_encode('3d_orders') ?>">Orders</a>
        <span class="sep">›</span>
        <a href="?vp=<?= base64_encode('3d_order_details') ?>&order_id=<?= $order_id_enc ?>">#<?= $order['order_id'] ?></a>
        <span class="sep">›</span>
        <a href="?vp=<?= base64_encode('3d_order_roster') ?>&order_id=<?= $order_id_enc ?>">Add Roster</a>
        <span class="sep">›</span>
        <span class="current">Checkout</span>
      </p>
    </div>
  </div>

  <!-- Step Progress Bar -->
  <div class="ols3d-step-bar">
    <div class="ols3d-step-bar-date">Order Date: <?= $order_date_fmt ?></div>
    <div class="ols3d-steps">
      <div class="ols3d-step">
        <div class="ols3d-step-num done">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        </div>
        <span class="ols3d-step-label">Order Details</span>
      </div>
      <div class="ols3d-step-connector done"></div>
      <div class="ols3d-step">
        <div class="ols3d-step-num done">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        </div>
        <span class="ols3d-step-label">Add Roster</span>
      </div>
      <div class="ols3d-step-connector done"></div>
      <div class="ols3d-step">
        <div class="ols3d-step-num active">3</div>
        <span class="ols3d-step-label active">Checkout</span>
      </div>
    </div>
    <div></div>
  </div>

  <p class="ols3d-hint">Please fill the details accordingly.</p>

  <!-- Main checkout grid -->
  <div class="ols3d-checkout-grid">

    <!-- LEFT: Order Form -->
    <div class="ols3d-form-card">

      <?php
      $pf_customer_po     = htmlspecialchars($of_row['customer_po']     ?? '');
      $pf_req_due_date    = ($of_row['req_due_date']    ?? '' ) !== '0000-00-00' ? htmlspecialchars($of_row['req_due_date']    ?? '') : '';
      $pf_game_event_date = ($of_row['game_event_date'] ?? '' ) !== '0000-00-00' ? htmlspecialchars($of_row['game_event_date'] ?? '') : '';
      $pf_project_name    = htmlspecialchars($of_row['project_name']    ?? '');
      $pf_payment_opt     = $of_row['payment_opt']  ?? '';
      $pf_reorder_num     = htmlspecialchars($of_row['reorder_num']     ?? '');
      $pf_sales_rep_id    = (int)($of_row['sales_rep_id'] ?? 0);
      ?>

      <div class="ols3d-field">
        <label for="customer_po">Customer PO</label>
        <input type="text" id="customer_po" name="customer_po" placeholder="Customer PO number" value="<?= $pf_customer_po ?>">
      </div>

      <div class="ols3d-field">
        <label for="req_due_date">Request Due Date</label>
        <input type="date" id="req_due_date" name="req_due_date" value="<?= $pf_req_due_date ?>">
      </div>

      <div class="ols3d-field">
        <label for="sales_rep">Sales Representative</label>
        <select id="sales_rep" name="sales_rep">
          <option value="">Select</option>
          <?php
          $sql_emp = "SELECT * FROM employee WHERE employee_position_id='5'";
          $emps    = $conn3->query($sql_emp);
          if ($emps && $emps->num_rows > 0) {
              while ($emp = $emps->fetch_assoc()) {
                  $sel = ($pf_sales_rep_id === (int)$emp['employee_id']) ? ' selected' : '';
                  echo '<option value="' . htmlspecialchars($emp['employee_id']) . '"' . $sel . '>'
                      . htmlspecialchars($emp['employee_name']) . '</option>';
              }
          }
          ?>
        </select>
      </div>

      <div class="ols3d-field">
        <label for="project_name">Project Name</label>
        <input type="text" id="project_name" name="project_name" placeholder="Enter project name" value="<?= $pf_project_name ?>">
      </div>

      <div class="ols3d-field">
        <label for="game_event_date">Game / Event Date</label>
        <input type="date" id="game_event_date" name="game_event_date" value="<?= $pf_game_event_date ?>">
      </div>

      <div class="ols3d-field">
        <label for="payment_opt">Payment Options</label>
        <select id="payment_opt" name="payment_opt">
          <option value="">Select</option>
          <?php
          $pay_opts = ['Credit Card'=>'Credit Card','Net 30'=>'Invoice / Net 30','Net 15'=>'Net 15','Net 7'=>'Net 7','Due on receipt'=>'Due on receipt','Bank Transfer'=>'Bank Transfer'];
          foreach ($pay_opts as $val => $label) {
              $sel = ($pf_payment_opt === $val) ? ' selected' : '';
              echo '<option value="' . htmlspecialchars($val) . '"' . $sel . '>' . htmlspecialchars($label) . '</option>';
          }
          ?>
        </select>
      </div>

      <div class="ols3d-field">
        <label for="reorder_num">Reorder? Type the EX here</label>
        <input type="text" id="reorder_num" name="reorder_num" placeholder="Re Order" value="<?= $pf_reorder_num ?>">
      </div>

      <div style="margin-top:8px;">
        <button id="printBtn" class="ols3d-btn-primary" type="button" onclick="submitOrder()">
          Submit Order
        </button>
      </div>

    </div><!-- /.ols3d-form-card -->

    <!-- RIGHT: Summary Stack -->
    <div class="ols3d-summary-stack">

      <!-- Order details card -->
      <div class="ols3d-summary-card">
        <div class="ols3d-summary-card-head">Order ID : #<?= $order['order_id'] ?></div>
        <div class="ols3d-design-thumb">
          <div class="ols3d-design-thumb-img">
            <?php if (!empty($design_image)): ?>
            <img src="http://65.1.164.81/jogdigital/admin/uploads/designs/images/<?= htmlspecialchars($design_image) ?>" alt="<?= htmlspecialchars($design_name) ?>">
            <?php else: ?>
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#CBD3E8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <?php endif; ?>
          </div>
          <div class="ols3d-design-meta">
            <div>Style: <strong><?= htmlspecialchars($design_name) ?></strong></div>
            <div>Type: <strong><?= htmlspecialchars($jersey_type) ?></strong></div>
            <?php if (!empty($sock_design)): ?>
            <div>Socks: <strong style="background:#F0F2F7;padding:1px 8px;border-radius:5px;font-size:11px;">Added</strong></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Roster summary card -->
      <div class="ols3d-summary-card">
        <div class="ols3d-summary-card-head">Roster Summary</div>
        <div class="ols3d-summary-card-body">
          <div class="ols3d-summary-row">
            <span class="ols3d-summary-key">Total QTY</span>
            <span class="ols3d-summary-val"><?= $total_qty ?></span>
          </div>
          <div class="ols3d-summary-row">
            <span class="ols3d-summary-key">Sizes</span>
            <span class="ols3d-summary-val"><?= htmlspecialchars($sizes_str) ?></span>
          </div>
          <div class="ols3d-summary-row">
            <span class="ols3d-summary-key">Status</span>
            <span class="ols3d-summary-val"><span class="ols3d-badge ols3d-badge-new">New</span></span>
          </div>
        </div>
      </div>

      <!-- Billing Information card -->
      <div class="ols3d-addr-card" id="billingCard">
        <div class="ols3d-addr-card-head">
          <span class="ols3d-addr-card-title">Billing Information</span>
          <div class="ols3d-addr-card-actions">
            <button type="button" class="ols3d-addr-btn <?= empty($a_data[0]) ? 'ols3d-addr-btn-add' : 'ols3d-addr-btn-edit' ?>"
                    id="billingToggleBtn"
                    onclick="toggleAddrCard('billing')">
              <?= empty($a_data[0]) ? '+ Add' : 'Edit' ?>
            </button>
          </div>
        </div>
        <?php if (!empty($a_data[0])): ?>
        <div class="ols3d-addr-card-body" id="billingDisplay">
          <?php $rows = [['Company',$a_data[0]['addr_name']??''],['Contact',$a_data[0]['contact_name']??''],['Address',$a_data[0]['address']??''],['City',$a_data[0]['city']??''],['Email',$a_data[0]['email']??''],['Phone',$a_data[0]['tel']??''],['TAX ID',$a_data[0]['tax_id']??($a_data[0]['tax_no']??'')]]; foreach($rows as [$k,$v]): if(trim($v)==='') continue; ?>
          <div class="ols3d-billing-row"><span><?= $k ?></span><span><?= htmlspecialchars($v) ?></span></div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="ols3d-addr-card-body" id="billingDisplay" style="display:none;"></div>
        <?php endif; ?>
        <!-- Inline billing form -->
        <div id="billingForm" style="display:none;">
          <div class="ols3d-inline-form">
            <div class="ols3d-inline-form-grid">
              <div class="ols3d-inline-form-field"><label>Company Name <span style="color:#EF4444">*</span></label><input type="text" id="b_company" placeholder="Company name"></div>
              <div class="ols3d-inline-form-field"><label>Contact Name <span style="color:#EF4444">*</span></label><input type="text" id="b_contact" placeholder="Contact person"></div>
              <div class="ols3d-inline-form-field full"><label>Address <span style="color:#EF4444">*</span></label><input type="text" id="b_address" placeholder="Street address"></div>
              <div class="ols3d-inline-form-field"><label>City <span style="color:#EF4444">*</span></label><input type="text" id="b_city" placeholder="City"></div>
              <div class="ols3d-inline-form-field"><label>State / Province</label><input type="text" id="b_state" placeholder="State or Province"></div>
              <div class="ols3d-inline-form-field"><label>Postal Code <span style="color:#EF4444">*</span></label><input type="text" id="b_postal" placeholder="ZIP code"></div>
              <div class="ols3d-inline-form-field"><label>Country <span style="color:#EF4444">*</span></label><input type="text" id="b_country" placeholder="Country"></div>
              <div class="ols3d-inline-form-field"><label>Phone <span style="color:#EF4444">*</span></label><input type="text" id="b_phone" placeholder="Phone number"></div>
              <div class="ols3d-inline-form-field"><label>Email <span style="color:#EF4444">*</span></label><input type="email" id="b_email" placeholder="Email address"></div>
              <div class="ols3d-inline-form-field"><label>TAX / GST Number</label><input type="text" id="b_tax" placeholder="Tax ID (optional)"></div>
            </div>
            <div class="ols3d-inline-form-actions">
              <button type="button" class="ols3d-inline-btn-cancel" onclick="toggleAddrCard('billing')">Cancel</button>
              <button type="button" class="ols3d-inline-btn-save" id="billingSaveBtn" onclick="saveAddrInline('billing')">Save Address</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Delivery Information card -->
      <div class="ols3d-addr-card" id="deliveryCard">
        <div class="ols3d-addr-card-head">
          <span class="ols3d-addr-card-title">Delivering to</span>
          <div class="ols3d-addr-card-actions">
            <button type="button" class="ols3d-addr-btn <?= empty($a_data[1]) ? 'ols3d-addr-btn-add' : 'ols3d-addr-btn-edit' ?>"
                    id="deliveryToggleBtn"
                    onclick="toggleAddrCard('delivery')">
              <?= empty($a_data[1]) ? '+ Add' : 'Edit' ?>
            </button>
          </div>
        </div>
        <?php if (!empty($a_data[1])): ?>
        <div class="ols3d-addr-card-body" id="deliveryDisplay">
          <?php $rows = [['Company',$a_data[1]['addr_name']??''],['Contact',$a_data[1]['contact_name']??''],['Address',$a_data[1]['address']??''],['City',$a_data[1]['city']??''],['Email',$a_data[1]['email']??''],['Phone',$a_data[1]['tel']??''],['TAX ID',$a_data[1]['tax_id']??($a_data[1]['tax_no']??'')]]; foreach($rows as [$k,$v]): if(trim($v)==='') continue; ?>
          <div class="ols3d-billing-row"><span><?= $k ?></span><span><?= htmlspecialchars($v) ?></span></div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="ols3d-addr-card-body" id="deliveryDisplay" style="display:none;"></div>
        <?php endif; ?>
        <!-- Inline delivery form -->
        <div id="deliveryForm" style="display:none;">
          <div class="ols3d-inline-form">
            <div class="ols3d-inline-form-grid">
              <div class="ols3d-inline-form-field"><label>Company Name <span style="color:#EF4444">*</span></label><input type="text" id="d_company" placeholder="Company name"></div>
              <div class="ols3d-inline-form-field"><label>Contact Name <span style="color:#EF4444">*</span></label><input type="text" id="d_contact" placeholder="Contact person"></div>
              <div class="ols3d-inline-form-field full"><label>Address <span style="color:#EF4444">*</span></label><input type="text" id="d_address" placeholder="Street address"></div>
              <div class="ols3d-inline-form-field"><label>City <span style="color:#EF4444">*</span></label><input type="text" id="d_city" placeholder="City"></div>
              <div class="ols3d-inline-form-field"><label>State / Province</label><input type="text" id="d_state" placeholder="State or Province"></div>
              <div class="ols3d-inline-form-field"><label>Postal Code <span style="color:#EF4444">*</span></label><input type="text" id="d_postal" placeholder="ZIP code"></div>
              <div class="ols3d-inline-form-field"><label>Country <span style="color:#EF4444">*</span></label><input type="text" id="d_country" placeholder="Country"></div>
              <div class="ols3d-inline-form-field"><label>Phone <span style="color:#EF4444">*</span></label><input type="text" id="d_phone" placeholder="Phone number"></div>
              <div class="ols3d-inline-form-field"><label>Email <span style="color:#EF4444">*</span></label><input type="email" id="d_email" placeholder="Email address"></div>
              <div class="ols3d-inline-form-field"><label>TAX / GST Number</label><input type="text" id="d_tax" placeholder="Tax ID (optional)"></div>
            </div>
            <div class="ols3d-inline-form-actions">
              <button type="button" class="ols3d-inline-btn-cancel" onclick="toggleAddrCard('delivery')">Cancel</button>
              <button type="button" class="ols3d-inline-btn-save" id="deliverySaveBtn" onclick="saveAddrInline('delivery')">Save Address</button>
            </div>
          </div>
        </div>
      </div>

      <a href="?vp=<?= base64_encode('3d_order_roster') ?>&order_id=<?= $order_id_enc ?>"
         class="ols3d-btn-secondary" style="justify-content:center;">
        ← Back to Roster
      </a>

    </div><!-- /.ols3d-summary-stack -->

  </div><!-- /.ols3d-checkout-grid -->

</div><!-- /.ols3d-module -->

<!-- Toast -->
<div class="ols3d-toast" id="ols3dToast"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

<script>
var BILLING_DATA  = <?= $billing_js ?>;
var DELIVERY_DATA = <?= $delivery_js ?>;

/* ── Toggle inline address form ── */
function toggleAddrCard(type) {
  var form    = document.getElementById(type + 'Form');
  var display = document.getElementById(type + 'Display');
  var btn     = document.getElementById(type + 'ToggleBtn');
  var isOpen  = form.style.display !== 'none' && form.style.display !== '';

  if (isOpen) {
    form.style.display = 'none';
    var data = type === 'billing' ? BILLING_DATA : DELIVERY_DATA;
    if (display) display.style.display = data.company ? '' : 'none';
    btn.textContent = data.company ? 'Edit' : '+ Add';
    btn.className   = 'ols3d-addr-btn ' + (data.company ? 'ols3d-addr-btn-edit' : 'ols3d-addr-btn-add');
  } else {
    if (display) display.style.display = 'none';
    form.style.display = 'block';
    btn.textContent = 'Close';
    btn.className   = 'ols3d-addr-btn ols3d-addr-btn-edit';
    prefillForm(type);
  }
}

function prefillForm(type) {
  var data = type === 'billing' ? BILLING_DATA : DELIVERY_DATA;
  var p    = type === 'billing' ? 'b_' : 'd_';
  document.getElementById(p+'company').value = data.company || '';
  document.getElementById(p+'contact').value = data.contact || '';
  document.getElementById(p+'address').value = data.address || '';
  document.getElementById(p+'city').value    = data.city    || '';
  document.getElementById(p+'state').value   = data.state   || '';
  document.getElementById(p+'postal').value  = data.postal  || '';
  document.getElementById(p+'country').value = data.country || '';
  document.getElementById(p+'phone').value   = data.phone   || '';
  document.getElementById(p+'email').value   = data.email   || '';
  document.getElementById(p+'tax').value     = data.tax     || '';
}

/* ── Save inline address ── */
function saveAddrInline(type) {
  var p    = type === 'billing' ? 'b_' : 'd_';
  var data = type === 'billing' ? BILLING_DATA : DELIVERY_DATA;

  var payload = {
    mode:         data.enc_id ? 'edit' : 'add',
    addr_type:    type,
    company_name: document.getElementById(p+'company').value.trim(),
    contact:      document.getElementById(p+'contact').value.trim(),
    address_info: document.getElementById(p+'address').value.trim(),
    city:         document.getElementById(p+'city').value.trim(),
    state:        document.getElementById(p+'state').value.trim(),
    zipcode:      document.getElementById(p+'postal').value.trim(),
    country:      document.getElementById(p+'country').value.trim(),
    tel:          document.getElementById(p+'phone').value.trim(),
    email_info:   document.getElementById(p+'email').value.trim(),
    tax_no:       document.getElementById(p+'tax').value.trim()
  };

  var required = ['company_name','contact','address_info','city','zipcode','country','tel','email_info'];
  for (var i = 0; i < required.length; i++) {
    if (!payload[required[i]]) { showToast('Please fill all required fields.'); return; }
  }

  if (data.enc_id) { payload.edit_addr_id = data.enc_id; }

  var btn = document.getElementById(type + 'SaveBtn');
  btn.disabled = true; btn.textContent = 'Saving…';

  $.ajax({
    type: 'POST', url: 'ajax/billing/save_address_modal.php', data: payload, dataType: 'json',
    success: function(resp) {
      if (resp.result === 'success') {
        showToast(payload.mode === 'add' ? 'Address added!' : 'Address updated!');
        setTimeout(function(){ location.reload(); }, 800);
      } else {
        showToast('Error: ' + (resp.msg || 'Save failed'));
        btn.disabled = false; btn.textContent = 'Save Address';
      }
    },
    error: function() {
      showToast('Network error. Please try again.');
      btn.disabled = false; btn.textContent = 'Save Address';
    }
  });
}

function submitOrder() {
  var design_order_id = document.getElementById('order_id').value;
  var customer_po     = document.getElementById('customer_po').value.trim();
  var req_due_date    = document.getElementById('req_due_date').value.trim();
  var game_event_date = document.getElementById('game_event_date').value.trim();
  var project_name    = document.getElementById('project_name').value.trim();
  var payment_opt     = document.getElementById('payment_opt').value.trim();
  var sales_rep_id    = document.getElementById('sales_rep').value.trim();
  var reorder_num     = document.getElementById('reorder_num').value.trim();

  if (!design_order_id) { showToast('Missing order ID. Please refresh the page.'); return; }

  var btn = document.getElementById('printBtn');
  btn.disabled = true;
  btn.textContent = 'Submitting…';

  var params = new URLSearchParams();
  params.append('design_order_id', design_order_id);
  params.append('customer_po',     customer_po);
  params.append('req_due_date',    req_due_date);
  params.append('game_event_date', game_event_date);
  params.append('project_name',    project_name);
  params.append('payment_opt',     payment_opt);
  params.append('sales_rep_id',    sales_rep_id);
  params.append('reorder_num',     reorder_num);

  fetch('ajax/3d_order/submit_order.php', {
    method:  'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body:    params.toString()
  })
  .then(function(r) {
    if (!r.ok) throw new Error('HTTP ' + r.status);
    return r.json();
  })
  .then(function(data) {
    if (data.result === 'success') {
      showToast('Order submitted successfully!');
      setTimeout(function() {
        window.location.href = '?vp=<?= base64_encode('3d_orders') ?>';
      }, 1200);
    } else {
      showToast('Error: ' + (data.msg || 'Submission failed.'));
      btn.disabled = false;
      btn.textContent = 'Submit Order';
    }
  })
  .catch(function(e) {
    showToast('Network error: ' + e.message);
    btn.disabled = false;
    btn.textContent = 'Submit Order';
  });
}

function showToast(msg) {
  var t = document.getElementById('ols3dToast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(function(){ t.classList.remove('show'); }, 2800);
}

(function () {
  var toast = document.getElementById('ols3dToast');
  if (toast && toast.parentNode !== document.body) document.body.appendChild(toast);
})();
</script>
