<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
include 'includes/order_helpers.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = $obj_user->user_id;

if (isset($_GET['order_id'])) {
    $order_id = customDecode($_GET['order_id']);

    $sql  = "SELECT * FROM design_order WHERE order_id = ?";
    $stmt = $conn4->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order       = $result->fetch_assoc();
        $designId    = $order['design_id'];
        $sock_design = $order['sock_design'];
        $added_date  = $order['added_date'];
    } else {
        echo "<p>No design found for this Order ID.</p>";
        exit;
    }
} else {
    echo "<p>Invalid order ID.</p>";
    exit;
}

$order_team_data   = [];
$order_design_data = [];

$sql_designs  = "SELECT * FROM designs WHERE id = ?";
$stmt_designs = $conn4->prepare($sql_designs);
$stmt_designs->bind_param("i", $designId);
$stmt_designs->execute();
$result_designs = $stmt_designs->get_result();
while ($row = $result_designs->fetch_assoc()) {
    $order_design_data[] = $row;
}

$design_name    = !empty($order_design_data[0]['name'])       ? $order_design_data[0]['name']       : '—';
$jersey_type    = !empty($order_design_data[0]['modal_type']) ? $order_design_data[0]['modal_type'] : '—';
$order_id_enc   = customEncode($order_id);
$order_date_fmt = !empty($added_date) ? date('d-m-Y H:i:s', strtotime($added_date)) : '—';
// Read team name/year from tbl_order_form (writable) if saved; fall back to design_order
$of_team_row = null;
$stmt_tn = $conn->prepare(
    "SELECT on_team_name, on_year FROM tbl_order_form WHERE design_order_id=? ORDER BY is_submitted DESC LIMIT 1"
);
$stmt_tn->bind_param("i", $order_id);
$stmt_tn->execute();
$res_tn = $stmt_tn->get_result();
if ($res_tn->num_rows > 0) { $of_team_row = $res_tn->fetch_assoc(); }
$stmt_tn->close();

$team_name_val = htmlspecialchars(
    ($of_team_row['on_team_name'] ?? '') !== '' ? $of_team_row['on_team_name'] : ($order['on_team_name'] ?? '')
);
$team_year_val = htmlspecialchars(
    ($of_team_row['on_year'] ?? '') !== '' ? $of_team_row['on_year'] : ($order['on_year'] ?? '')
);

// Build reverse size map: size_id (int) → size_name (text) for prefilling the dropdown
$size_id_to_name = [];
$sz_res = $conn->query("SELECT size_id, size_name FROM tbl_size");
if ($sz_res) {
    while ($sz = $sz_res->fetch_assoc()) {
        $size_id_to_name[(int)$sz['size_id']] = $sz['size_name'];
    }
}

// Get existing order form (submitted or draft); only create a new draft if none exists
$of_id          = 0;
$is_of_submitted = 0;
$stmt_find = $conn->prepare(
    "SELECT of_id, is_submitted FROM tbl_order_form WHERE design_order_id=? ORDER BY is_submitted DESC LIMIT 1"
);
$stmt_find->bind_param("i", $order_id);
$stmt_find->execute();
$res_find = $stmt_find->get_result();
if ($res_find->num_rows > 0) {
    $row_find       = $res_find->fetch_assoc();
    $of_id          = (int)$row_find['of_id'];
    $is_of_submitted = (int)$row_find['is_submitted'];
}
$stmt_find->close();
if ($of_id === 0) {
    $of_id = getOrCreateDraftOrder($conn, $order_id, $user_id);
}

// Load roster rows from the appropriate table
$source_table       = ($is_of_submitted === 1) ? 'tbl_order_item' : 'tbl_draft_oi';
$existing_rows_json = '[]';

$oi_res = $conn->query("SELECT * FROM $source_table WHERE of_id=" . (int)$of_id);
if ($oi_res && $oi_res->num_rows > 0) {
    $loaded_rows = [];
    while ($dr = $oi_res->fetch_assoc()) {
        $size_text     = $size_id_to_name[(int)($dr['product_size_id'] ?? 0)] ?? '';
        $loaded_rows[] = [
            'item_id'         => (int)$dr['oi_id'],
            'player_name'     => $dr['player_name']      ?? '',
            'pattern_cut'     => $dr['sex']              ?? '',
            'player_or_goalie'=> $dr['p_or_g']           ?? '',
            'jersey_size'     => $size_text,
            'jersey_no'       => $dr['jersey_number']    ?? '',
            'jersey_color'    => $dr['color_top1']       ?? '',
            'jersey_qty'      => $dr['qty_top1']         ?? '',
            'jersey_color2'   => $dr['color_top2']       ?? '',
            'jersey_qty2'     => $dr['qty_top2']         ?? '',
            'sock_size'       => $dr['bottom_size']      ?? '',
            'sock_color'      => $dr['color_bottom1']    ?? '',
            'sock_qty'        => $dr['qty_bottom1']      ?? '',
            'sock_color2'     => $dr['color_bottom2']    ?? '',
            'sock_qty2'       => $dr['qty_bottom2']      ?? '',
            'cor_a'           => $dr['c_or_a']           ?? '',
            'name_for_packing'=> $dr['name_for_packing'] ?? '',
            'notes'           => $dr['note']             ?? '',
        ];
    }
    $existing_rows_json = json_encode($loaded_rows);
} else if ($is_of_submitted === 0) {
    // No saved rows yet — fall back to order_team (read-only 3D DB) with item_id=0
    $sql_team  = "SELECT * FROM order_team WHERE order_id = ?";
    $stmt_team = $conn4->prepare($sql_team);
    $stmt_team->bind_param("i", $order_id);
    $stmt_team->execute();
    $result_team = $stmt_team->get_result();
    $team_rows = [];
    while ($row = $result_team->fetch_assoc()) {
        $team_rows[] = [
            'item_id'         => 0,
            'player_name'     => $row['player_name']      ?? '',
            'pattern_cut'     => $row['pattern_cut']      ?? '',
            'player_or_goalie'=> $row['player_or_goalie'] ?? '',
            'jersey_size'     => $row['jersey_size']      ?? '',
            'jersey_no'       => $row['jersey_no']        ?? '',
            'jersey_color'    => $row['jersey_color']     ?? '',
            'jersey_qty'      => $row['jersey_qty']       ?? '',
            'jersey_color2'   => $row['jersey_color2']    ?? '',
            'jersey_qty2'     => $row['jersey_qty2']      ?? '',
            'sock_size'       => $row['sock_size']        ?? '',
            'sock_color'      => $row['sock_color']       ?? '',
            'sock_qty'        => $row['sock_qty']         ?? '',
            'sock_color2'     => $row['sock_color2']      ?? '',
            'sock_qty2'       => $row['sock_qty2']        ?? '',
            'cor_a'           => $row['cor_a']            ?? '',
            'name_for_packing'=> $row['name_for_packing'] ?? '',
            'notes'           => $row['notes']            ?? '',
        ];
    }
    $existing_rows_json = json_encode($team_rows);
}

// Fetch color list dynamically from DB
$color_list = [];
$color_res  = $conn4->query("SELECT name FROM colors ORDER BY name");
if ($color_res) {
    while ($cr = $color_res->fetch_assoc()) {
        $color_list[] = $cr['name'];
    }
}
if (empty($color_list)) {
    $color_list = ['Red','Navy','Black','White','Royal Blue','Forest Green','Gold','Maroon'];
}

$color_list_json = json_encode($color_list);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style/ols_3d_order.css">

<div class="ols3d-module">

  <!-- Title Row -->
  <div class="ols3d-title-row">
    <a href="?vp=<?= base64_encode('3d_order_details') ?>&order_id=<?= $order_id_enc ?>"
       class="ols3d-back-btn" title="Back to Order Details">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
    </a>
    <div>
      <h1>Add Roster</h1>
      <p class="ols3d-breadcrumb">
        <a href="?vp=<?= base64_encode('3d_orders') ?>">Orders</a>
        <span class="sep">›</span>
        <a href="?vp=<?= base64_encode('3d_order_details') ?>&order_id=<?= $order_id_enc ?>">#<?= $order['order_id'] ?></a>
        <span class="sep">›</span>
        <span class="current">Add Roster</span>
      </p>
    </div>
  </div>

  <!-- Step Progress Bar -->
  <div class="ols3d-step-bar">
    <div class="ols3d-step-bar-id">
      <span>Order ID:</span>
      <strong>#<?= $order['order_id'] ?></strong>
    </div>
    <div class="ols3d-steps">
      <div class="ols3d-step">
        <div class="ols3d-step-num done">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        </div>
        <span class="ols3d-step-label">Order Details</span>
      </div>
      <div class="ols3d-step-connector done"></div>
      <div class="ols3d-step">
        <div class="ols3d-step-num active">2</div>
        <span class="ols3d-step-label active">Add Roster</span>
      </div>
      <div class="ols3d-step-connector"></div>
      <div class="ols3d-step">
        <div class="ols3d-step-num">3</div>
        <span class="ols3d-step-label">Checkout</span>
      </div>
    </div>
    <div class="ols3d-step-bar-note">
      Last Modified: <strong><?= $order_date_fmt ?></strong>
    </div>
  </div>

  <!-- Style / Jersey info bar -->
  <div class="ols3d-info-bar">
    <span class="ols3d-info-label">Style:</span>
    <span class="ols3d-info-value"><?= htmlspecialchars($design_name) ?></span>
    <div class="ols3d-info-divider"></div>
    <span class="ols3d-info-label">Jersey Type:</span>
    <span class="ols3d-info-value"><?= htmlspecialchars($jersey_type) ?></span>
  </div>

  <p class="ols3d-hint">Add player details below. Click <strong>+ Add Player</strong> to insert a row, or delete a row using the × button. Changes are saved when you click <strong>Save &amp; Continue</strong>.</p>

  <!-- Socks preview (if any) -->
  <?php if (!empty($sock_design)): ?>
  <div class="ols3d-socks-panel">
    <h5>Socks</h5>
    <img src="https://jogsports.com/jogdigital/<?= htmlspecialchars($sock_design) ?>" alt="Sock Design">
  </div>
  <?php endif; ?>

  <!-- Team Name + Year -->
  <div class="ols3d-team-fields">
    <div class="ols3d-team-field">
      <label for="roster_team_name">Team Name</label>
      <input type="text" id="roster_team_name" placeholder="e.g. Thunder Hawks" value="<?= $team_name_val ?>">
    </div>
    <div class="ols3d-team-field">
      <label for="roster_team_year">Year</label>
      <input type="text" id="roster_team_year" placeholder="e.g. 2025" value="<?= $team_year_val ?>" style="max-width:140px;">
    </div>
  </div>

  <!-- ── Roster Table ──────────────────────────────────── -->
  <div class="ols3d-roster-wrap">
    <div class="ols3d-roster-header-row">
      <h5 id="ols3d-roster-count-title">
        Roster Details
        <span id="ols3d-row-count" style="font-size:12px;font-weight:500;color:#6B7A9F;margin-left:10px;">0 players</span>
      </h5>
      <button class="ols3d-btn-add-row" type="button" onclick="rosterAddRow()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add Player
      </button>
    </div>

    <div class="ols3d-roster-scroll">
      <table class="ols3d-roster-table" id="rosterTable">
        <thead>
          <tr class="ols3d-roster-thead-dark">
            <th class="ols3d-th-del"></th>
            <th>Name on Jersey</th>
            <th>Pattern Cut</th>
            <th>P or G</th>
            <th>Jersey Size</th>
            <th>Jersey No</th>
            <th>Jersey Color</th>
            <th>QTY</th>
            <th>Jersey Color 2</th>
            <th>QTY</th>
            <th>Sock Size</th>
            <th>Sock Color</th>
            <th>QTY</th>
            <th>Sock Color 2</th>
            <th>QTY</th>
            <th>C or A</th>
            <th>Name for Packing</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody id="rosterBody">
          <!-- rows injected by JS -->
        </tbody>
      </table>
    </div>
    <!-- Footer: player count + add button -->
    <div class="ols3d-roster-table-footer">
      <span class="ols3d-roster-total">Total players: <strong id="ols3d-row-count-foot">0</strong></span>
    </div>
  </div>

  <!-- Action Buttons -->
  <div class="ols3d-actions">
    <button type="button" class="ols3d-btn-primary" id="rosterSaveBtn" onclick="rosterSave()">
      Save &amp; Continue →
    </button>
    <a href="?vp=<?= base64_encode('3d_order_details') ?>&order_id=<?= $order_id_enc ?>"
       class="ols3d-btn-secondary">
      ← Back
    </a>
  </div>

</div><!-- /.ols3d-module -->

<!-- Toast -->
<div class="ols3d-toast" id="ols3dToast"></div>

<script>
(function () {
  /* ── Server data ── */
  var DESIGN_ORDER_ID = <?= (int)$order_id ?>;
  var OF_ID           = <?= (int)$of_id ?>;
  var ORDER_ID_ENC    = '<?= $order_id_enc ?>';
  var CHECKOUT_URL    = '?vp=<?= base64_encode('order_info') ?>&order_id=' + ORDER_ID_ENC;
  var existingRows    = <?= $existing_rows_json ?>;

  /* ── Dropdown option lists (colors dynamic from DB) ── */
  var JERSEY_SIZES  = ['AS-46','AS-48','A4XL-50','A4XL-52','A5XL-54','A5XL-56','Youth-S','Youth-M','Youth-L'];
  var SOCK_SIZES    = ['S','M','L','XL','XXL'];
  var PATTERN_CUTS  = ['Adult','Youth'];
  var PG_OPTIONS    = ['Player','Goalie'];
  var JERSEY_COLORS = <?= $color_list_json ?>;   // fetched from colors table
  var COR_A_OPTS    = ['','C','A'];

  /* ── Helpers ── */
  function sel(opts, val, placeholder) {
    var h = '<select><option value="">' + (placeholder || '—') + '</option>';
    opts.forEach(function (o) {
      h += '<option value="' + escHtml(o) + '"' + (o === val ? ' selected' : '') + '>' + escHtml(o) + '</option>';
    });
    return h + '</select>';
  }

  function inp(val, placeholder, type) {
    return '<input type="' + (type || 'text') + '" value="' + escHtml(val) + '" placeholder="' + escHtml(placeholder || '') + '">';
  }

  function escHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g,'&amp;').replace(/"/g,'&quot;')
      .replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  var TRASH_ICON = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg>';

  /* Each row carries data-tid = oi_id from tbl_draft_oi (0 = not yet saved) */
  function makeRow(d, idx) {
    d = d || {};
    var tid = d.item_id || 0;
    var tr  = document.createElement('tr');
    tr.dataset.tid = tid;
    tr.innerHTML =
      /* col 0: trash delete */
      '<td class="ols3d-td-del"><button class="ols3d-del-btn" type="button" onclick="rosterDelRow(this)" title="Remove row">' + TRASH_ICON + '</button></td>' +
      /* col 1..17: data fields */
      '<td>' + inp(d.player_name,       'Player name')  + '</td>' +
      '<td>' + sel(PATTERN_CUTS,  d.pattern_cut,        'Cut')    + '</td>' +
      '<td>' + sel(PG_OPTIONS,    d.player_or_goalie,   'P/G')    + '</td>' +
      '<td>' + sel(JERSEY_SIZES,  d.jersey_size,        'Size')   + '</td>' +
      '<td>' + inp(d.jersey_no,         '#')             + '</td>' +
      '<td>' + sel(JERSEY_COLORS, d.jersey_color,       'Color')  + '</td>' +
      '<td>' + inp(d.jersey_qty,        '1', 'number')  + '</td>' +
      '<td>' + sel(JERSEY_COLORS, d.jersey_color2,      'Color')  + '</td>' +
      '<td>' + inp(d.jersey_qty2,       '0', 'number')  + '</td>' +
      '<td>' + sel(SOCK_SIZES,    d.sock_size,          'Size')   + '</td>' +
      '<td>' + sel(JERSEY_COLORS, d.sock_color,         'Color')  + '</td>' +
      '<td>' + inp(d.sock_qty,          '0', 'number')  + '</td>' +
      '<td>' + sel(JERSEY_COLORS, d.sock_color2,        'Color')  + '</td>' +
      '<td>' + inp(d.sock_qty2,         '0', 'number')  + '</td>' +
      '<td>' + sel(COR_A_OPTS,    d.cor_a,              '—')      + '</td>' +
      '<td>' + inp(d.name_for_packing,  'Packing name') + '</td>' +
      '<td>' + inp(d.notes,             'Notes')         + '</td>';
    return tr;
  }

  function updateCount() {
    var n = document.getElementById('rosterBody').rows.length;
    var txt = n + ' player' + (n !== 1 ? 's' : '');
    var a = document.getElementById('ols3d-row-count');
    var b = document.getElementById('ols3d-row-count-foot');
    if (a) a.textContent = txt;
    if (b) b.textContent = n;
  }

  function renumber() { updateCount(); }

  /* ── Public API ── */
  window.rosterAddRow = function () {
    var tbody = document.getElementById('rosterBody');
    tbody.appendChild(makeRow({item_id: 0}, tbody.rows.length));
    updateCount();
  };

  window.rosterDelRow = function (btn) {
    btn.closest('tr').remove();
    renumber();
  };

  window.rosterSave = function () {
    var tbody = document.getElementById('rosterBody');
    var rows  = [];

    Array.prototype.forEach.call(tbody.rows, function (tr) {
      var cells = tr.cells;
      /* col 0 = trash btn; data cols start at 1 */
      function v(i) { var el = cells[i].querySelector('input,select'); return el ? el.value : ''; }
      rows.push({
        item_id:          parseInt(tr.dataset.tid || '0', 10),
        player_name:      v(1),
        pattern_cut:      v(2),
        player_or_goalie: v(3),
        jersey_size:      v(4),
        jersey_no:        v(5),
        jersey_color:     v(6),
        jersey_qty:       v(7),
        jersey_color2:    v(8),
        jersey_qty2:      v(9),
        sock_size:        v(10),
        sock_color:       v(11),
        sock_qty:         v(12),
        sock_color2:      v(13),
        sock_qty2:        v(14),
        cor_a:            v(15),
        name_for_packing: v(16),
        notes:            v(17)
      });
    });

    var teamName = document.getElementById('roster_team_name').value.trim();
    var teamYear = document.getElementById('roster_team_year').value.trim();

    var btn = document.getElementById('rosterSaveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving…';

    fetch('ajax/roster/save_roster.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ design_order_id: DESIGN_ORDER_ID, of_id: OF_ID, rows: rows, team_name: teamName, team_year: teamYear })
    })
    .then(function (r) {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(function (data) {
      if (data.success) {
        if (data.of_id) { OF_ID = data.of_id; }
        showToast('Roster saved (' + (data.inserted||0) + ' added, ' + (data.updated||0) + ' updated, ' + (data.deleted||0) + ' removed)');
        setTimeout(function () { window.location.href = CHECKOUT_URL; }, 1000);
      } else {
        showToast('Error: ' + (data.message || 'Save failed'));
        btn.disabled = false;
        btn.textContent = 'Save & Continue →';
      }
    })
    .catch(function (e) {
      showToast('Save failed: ' + e.message);
      btn.disabled = false;
      btn.textContent = 'Save & Continue →';
    });
  };

  function showToast(msg) {
    var t = document.getElementById('ols3dToast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(function () { t.classList.remove('show'); }, 3000);
  }

  /* ── Teleport toast to <body> to escape any CSS transform context ── */
  (function () {
    var t = document.getElementById('ols3dToast');
    if (t && t.parentNode !== document.body) document.body.appendChild(t);
  })();

  /* ── Init: render existing rows from DB ── */
  (function () {
    var tbody = document.getElementById('rosterBody');
    if (existingRows && existingRows.length > 0) {
      existingRows.forEach(function (d, i) { tbody.appendChild(makeRow(d, i)); });
    }
    updateCount();
  }());
}());
</script>
