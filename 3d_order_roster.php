<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = $obj_user->user_id;

if (!isset($_GET['order_id'])) {
    echo "<p>Invalid order ID.</p>";
    exit;
}

$order_id = customDecode($_GET['order_id']);

$data = callAPI("get_roster_details.php?order_id=$order_id");

if (!$data || !$data['status']) {
    echo "<p>API Error</p>";
    exit;
}

//echo "<pre>"; print_r($data); echo "</pre>"; // debug
/* ✅ MAP DATA */
$order = $data['order'];
$order_team_data = $data['team'];
$order_design_data = [$data['design']];
$color_list = $data['colors'];

$designId = $order['design_id'];
$added_date = $order['added_date'];

$design_name  = $order_design_data[0]['name'] ?? '—';
$jersey_type  = $order_design_data[0]['modal_type'] ?? '—';

$order_id_enc   = customEncode($order_id);
$order_date_fmt = !empty($added_date) ? date('d-m-Y H:i:s', strtotime($added_date)) : '—';

$team_name_val  = htmlspecialchars($order['on_team_name'] ?? '');
$team_year_val  = htmlspecialchars($order['on_year'] ?? '');

/* 👇 Important for JS */
$existing_rows_json = json_encode($order_team_data);
$color_list_json    = json_encode($color_list);
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
  var ORDER_ID     = <?= (int)$order_id ?>;
  var ORDER_ID_ENC = '<?= $order_id_enc ?>';
  var CHECKOUT_URL = '?vp=<?= base64_encode('order_info') ?>&order_id=' + ORDER_ID_ENC;
  var existingRows = <?= $existing_rows_json ?>;

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

  /* Each row carries data-tid = DB primary key (0 = new unsaved row) */
  function makeRow(d, idx) {
    d = d || {};
    var tid = d.team_id || 0;
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
    tbody.appendChild(makeRow({team_id: 0}, tbody.rows.length));
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

      function v(i) {
        var el = cells[i].querySelector('input,select');
        return el ? el.value : '';
      }

      rows.push({
        team_id:          parseInt(tr.dataset.tid || '0', 10),
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

    /* ✅ CHANGE ONLY THIS PART */
    fetch('http://localhost:9090/jog_3d/api/CategorySub/save_roster.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        // 'Authorization': 'Bearer YOUR_TOKEN'  // optional
      },
      body: JSON.stringify({
        order_id: ORDER_ID,
        rows: rows,
        team_name: teamName,
        team_year: teamYear
      })
    })
    .then(function (r) {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(function (data) {
      if (data.status) {   // 👈 API uses "status"
        showToast(
          'Roster saved (' +
          (data.inserted || 0) + ' added, ' +
          (data.updated || 0) + ' updated, ' +
          (data.deleted || 0) + ' removed)'
        );

        setTimeout(function () {
          window.location.href = CHECKOUT_URL;
        }, 1000);

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
