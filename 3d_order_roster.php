<?php
include('check-session.php');

include 'encryption_helper.php';
include 'includes/order_helpers.php';
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

$order             = $data['order'];
$api_team_data     = $data['team'];
$order_design_data = [$data['design']];
$color_list        = $data['colors'];

$designId    = $order['design_id'];
$added_date  = $order['added_date'];
$sock_design = $order['sock_design'] ?? '';

$design_name = $order_design_data[0]['name']       ?? '—';
$jersey_type = $order_design_data[0]['modal_type'] ?? '—';

$order_id_enc   = customEncode($order_id);
$order_date_fmt = !empty($added_date) ? date('d-m-Y H:i:s', strtotime($added_date)) : '—';

// Build reverse size map: size_id → size_name and deduplicated dropdown list
$size_id_to_name    = [];
$size_dropdown_list = [];
$size_names_seen    = [];
$sz_res = $conn->query("SELECT size_id, size_name FROM tbl_size ORDER BY size_id ASC");
if ($sz_res) {
    while ($sz = $sz_res->fetch_assoc()) {
        $size_id_to_name[(int)$sz['size_id']] = $sz['size_name'];
        $name = trim($sz['size_name']);
        if ($name !== '' && !in_array($name, $size_names_seen)) {
            $size_dropdown_list[] = $name;
            $size_names_seen[] = $name;
        }
    }
}
$size_list_json = json_encode($size_dropdown_list);

// Helper: load roster rows for one order form
function loadRosterRows3D($conn, $of_id, $is_submitted, $size_id_to_name) {
    $source = ($is_submitted === 1) ? 'tbl_order_item' : 'tbl_draft_oi';
    $rows   = [];
    $res    = $conn->query("SELECT * FROM $source WHERE of_id=" . (int)$of_id);
    if ($res) {
        while ($dr = $res->fetch_assoc()) {
            $sz = $size_id_to_name[(int)($dr['product_size_id'] ?? 0)] ?? '';
            $rows[] = [
                'item_id'          => (int)$dr['oi_id'],
                'player_name'      => $dr['player_name']      ?? '',
                'pattern_cut'      => $dr['sex']              ?? '',
                'player_or_goalie' => $dr['p_or_g']           ?? '',
                'jersey_size'      => $sz,
                'jersey_no'        => $dr['jersey_number']    ?? '',
                'jersey_color'     => $dr['color_top1']       ?? '',
                'jersey_qty'       => $dr['qty_top1']         ?? '',
                'jersey_color2'    => $dr['color_top2']       ?? '',
                'jersey_qty2'      => $dr['qty_top2']         ?? '',
                'sock_size'        => $dr['bottom_size']      ?? '',
                'sock_color'       => $dr['color_bottom1']    ?? '',
                'sock_qty'         => $dr['qty_bottom1']      ?? '',
                'sock_color2'      => $dr['color_bottom2']    ?? '',
                'sock_qty2'        => $dr['qty_bottom2']      ?? '',
                'cor_a'            => $dr['c_or_a']           ?? '',
                'name_for_packing' => $dr['name_for_packing'] ?? '',
                'notes'            => $dr['note']             ?? '',
            ];
        }
    }
    return $rows;
}

// Load ALL order forms for this design_order_id (one per team)
$all_forms = [];
$stmt_all  = $conn->prepare(
    "SELECT of_id, is_submitted, on_team_name, on_year FROM tbl_order_form WHERE design_order_id=? ORDER BY of_id ASC"
);
$stmt_all->bind_param("i", $order_id);
$stmt_all->execute();
$res_all = $stmt_all->get_result();
while ($rf = $res_all->fetch_assoc()) { $all_forms[] = $rf; }
$stmt_all->close();

// If no forms exist yet, create a draft
if (empty($all_forms)) {
    $new_of_id = getOrCreateDraftOrder($conn, $order_id, $user_id);
    $all_forms[] = [
        'of_id'        => $new_of_id,
        'is_submitted' => 0,
        'on_team_name' => $order['on_team_name'] ?? '',
        'on_year'      => $order['on_year']      ?? '',
    ];
}

// Build teams JSON for JS
$teams_data = [];
$is_first   = true;
foreach ($all_forms as $form) {
    $f_of_id = (int)$form['of_id'];
    $f_sub   = (int)$form['is_submitted'];
    $f_name  = $form['on_team_name'] ?? '';
    $f_year  = $form['on_year']      ?? '';

    // First team: fall back to API order data if saved value is empty
    if ($is_first && $f_name === '') { $f_name = $order['on_team_name'] ?? ''; }
    if ($is_first && $f_year === '') { $f_year = $order['on_year']      ?? ''; }

    $rows = loadRosterRows3D($conn, $f_of_id, $f_sub, $size_id_to_name);

    // First team with no saved rows: seed from API team data
    if ($is_first && empty($rows) && $f_sub === 0) {
        foreach (($api_team_data ?? []) as $row) {
            $rows[] = [
                'item_id'          => 0,
                'player_name'      => $row['player_name']      ?? '',
                'pattern_cut'      => $row['pattern_cut']      ?? '',
                'player_or_goalie' => $row['player_or_goalie'] ?? '',
                'jersey_size'      => $row['jersey_size']      ?? '',
                'jersey_no'        => $row['jersey_no']        ?? '',
                'jersey_color'     => $row['jersey_color']     ?? '',
                'jersey_qty'       => $row['jersey_qty']       ?? '',
                'jersey_color2'    => $row['jersey_color2']    ?? '',
                'jersey_qty2'      => $row['jersey_qty2']      ?? '',
                'sock_size'        => $row['sock_size']        ?? '',
                'sock_color'       => $row['sock_color']       ?? '',
                'sock_qty'         => $row['sock_qty']         ?? '',
                'sock_color2'      => $row['sock_color2']      ?? '',
                'sock_qty2'        => $row['sock_qty2']        ?? '',
                'cor_a'            => $row['cor_a']            ?? '',
                'name_for_packing' => $row['name_for_packing'] ?? '',
                'notes'            => $row['notes']            ?? '',
            ];
        }
    }

    $teams_data[] = [
        'of_id'     => $f_of_id,
        'name'      => $f_name,
        'year'      => $f_year,
        'rows'      => $rows,
        'submitted' => $f_sub,
    ];
    $is_first = false;
}

$primary_of_id   = (int)$all_forms[0]['of_id'];
$teams_json      = json_encode($teams_data);
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

  <p class="ols3d-hint">Add teams and player details below. Use <strong>+ Add New Team</strong> to add multiple teams, each with its own roster. Click <strong>+ Add Player</strong> to insert a row, or delete a row using the × button.</p>

  <!-- Socks preview (if any) -->
  <?php if (!empty($sock_design)): ?>
  <div class="ols3d-socks-panel">
    <h5>Socks</h5>
    <img src="<?= htmlspecialchars($sock_design) ?>" alt="Sock Design">
  </div>
  <?php endif; ?>

  <!-- ── Multi-Team Roster Section ──────────────────────── -->
  <div class="ols3d-teams-section">
    <!-- Tabs bar -->
    <div class="ols3d-teams-tabs-bar">
      <div class="ols3d-teams-tabs-nav" id="teamsTabsNav">
        <!-- team tab buttons injected by JS -->
      </div>
      <button type="button" class="ols3d-btn-add-team" onclick="openAddTeamModal()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Add New Team
      </button>
    </div>
    <!-- Team panels container — JS populates one panel per team -->
    <div id="teamPanelsContainer"></div>
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

<!-- Add New Team Modal -->
<div class="ols3d-modal-overlay" id="addTeamOverlay">
  <div class="ols3d-modal">
    <div class="ols3d-modal-header">
      <span>Add New Team</span>
      <button class="ols3d-modal-close" type="button" onclick="closeAddTeamModal()">×</button>
    </div>
    <div class="ols3d-modal-body">
      <div class="ols3d-modal-grid">
        <div class="ols3d-modal-field">
          <label>Team Name <span class="ols3d-required-star">*</span></label>
          <input type="text" id="newTeamNameIn" placeholder="e.g. Thunder Hawks" maxlength="100">
        </div>
        <div class="ols3d-modal-field">
          <label>Year <span class="ols3d-required-star">*</span></label>
          <input type="text" id="newTeamYearIn" placeholder="e.g. 2025" maxlength="4"
                 oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
      </div>
    </div>
    <div class="ols3d-modal-footer">
      <button class="ols3d-modal-cancel" type="button" onclick="closeAddTeamModal()">Cancel</button>
      <button class="ols3d-modal-save"   type="button" onclick="confirmAddTeam()">Add Team</button>
    </div>
  </div>
</div>

<script>
(function () {
  /* ── Server data ── */
  var DESIGN_ORDER_ID = <?= (int)$order_id ?>;
  var ORDER_ID_ENC    = '<?= $order_id_enc ?>';
  var CHECKOUT_URL    = '?vp=<?= base64_encode('order_info') ?>&order_id=' + ORDER_ID_ENC;
  var initialTeams    = <?= $teams_json ?>;

  /* ── Dropdown lists (colors from DB) ── */
  var JERSEY_SIZES  = <?= $size_list_json ?>;
  var SOCK_SIZES    = ['S','M','L','XL','XXL'];
  var PATTERN_CUTS  = ['Adult','Youth'];
  var PG_OPTIONS    = ['Player','Goalie'];
  var JERSEY_COLORS = <?= $color_list_json ?>;
  var COR_A_OPTS    = ['','C','A'];

  /* ── State ── */
  var teamIdCtr   = 0;
  var teams       = [];          // [{teamId, of_id, name, year}]
  var activeTeamId = null;
  var deletedOfIds = [];         // of_ids removed by user (to delete on save)

  /* ── Helpers ── */
  function sel(opts, val, placeholder) {
    var h = '<select><option value="">' + (placeholder || '—') + '</option>';
    opts.forEach(function (o) {
      h += '<option value="' + escHtml(o) + '"' + (o === val ? ' selected' : '') + '>' + escHtml(o) + '</option>';
    });
    return h + '</select>';
  }

  function inp(val, placeholder, type, attrs) {
    return '<input type="' + (type || 'text') + '" value="' + escHtml(val) + '" placeholder="' + escHtml(placeholder || '') + '"' + (attrs ? ' ' + attrs : '') + '>';
  }

  function escHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g,'&amp;').replace(/"/g,'&quot;')
      .replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  var TRASH_ICON = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg>';
  var PLUS_SVG   = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>';

  /* ── Row builder ── */
  /* tr.dataset.tid holds the DB oi_id (0 = new, not yet persisted) */
  function makeRow(d) {
    d = d || {};
    var tid = d.item_id || 0;
    var tr  = document.createElement('tr');
    tr.dataset.tid = tid;
    tr.innerHTML =
      '<td class="ols3d-td-del"><button class="ols3d-del-btn" type="button" onclick="rosterDelRow(this)" title="Remove row">' + TRASH_ICON + '</button></td>' +
      '<td>' + inp(d.player_name, 'Player name', 'text', 'maxlength="50" oninput="this.value=this.value.replace(/[^a-zA-Z\\s]/g,\'\')"') + '</td>' +
      '<td>' + sel(PATTERN_CUTS,  d.pattern_cut,        'Cut')    + '</td>' +
      '<td>' + sel(PG_OPTIONS,    d.player_or_goalie,   'P/G')    + '</td>' +
      '<td>' + sel(JERSEY_SIZES,  d.jersey_size,        'Size')   + '</td>' +
      '<td>' + inp(d.jersey_no,   '#', 'text', 'maxlength="10" required oninput="this.value=this.value.replace(/[^0-9]/g,\'\')"') + '</td>' +
      '<td>' + inp(d.jersey_color,  'Jersey color')  + '</td>' +
      '<td>' + inp(d.jersey_qty,   '1', 'number')    + '</td>' +
      '<td>' + inp(d.jersey_color2, 'Jersey color 2') + '</td>' +
      '<td>' + inp(d.jersey_qty2,  '0', 'number')    + '</td>' +
      '<td>' + sel(SOCK_SIZES,    d.sock_size,          'Size')   + '</td>' +
      '<td>' + inp(d.sock_color,   'Sock color')    + '</td>' +
      '<td>' + inp(d.sock_qty,    '0', 'number')    + '</td>' +
      '<td>' + inp(d.sock_color2,  'Sock color 2')  + '</td>' +
      '<td>' + inp(d.sock_qty2,   '0', 'number')    + '</td>' +
      '<td>' + sel(COR_A_OPTS,    d.cor_a,              '—')      + '</td>' +
      '<td>' + inp(d.name_for_packing, 'Packing name') + '</td>' +
      '<td>' + inp(d.notes,        'Notes')          + '</td>';
    return tr;
  }

  /* ── Count updater ── */
  function updateTeamCount(teamId) {
    var tbody = document.getElementById('rosterBody_' + teamId);
    if (!tbody) return;
    var n = tbody.rows.length;
    var txt = n + ' player' + (n !== 1 ? 's' : '');
    var a = document.getElementById('rc_'  + teamId);
    var b = document.getElementById('rcf_' + teamId);
    if (a) a.textContent = txt;
    if (b) b.textContent = n;
  }

  /* ── Team panel builder ── */
  function makeTeamPanel(team) {
    var id  = team.teamId;
    var nv  = escHtml(team.name || '');
    var yv  = escHtml(team.year || '');

    var panel = document.createElement('div');
    panel.className = 'ols3d-team-panel';
    panel.id = 'teamPanel_' + id;

    panel.innerHTML =
      '<div class="ols3d-team-fields">' +
        '<div class="ols3d-team-field">' +
          '<label>Team Name</label>' +
          '<input type="text" id="team_name_' + id + '" value="' + nv + '" placeholder="e.g. Thunder Hawks" maxlength="100">' +
        '</div>' +
        '<div class="ols3d-team-field">' +
          '<label>Year</label>' +
          '<input type="text" id="team_year_' + id + '" value="' + yv + '" placeholder="e.g. 2025" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,\'\')" style="max-width:140px;">' +
        '</div>' +
      '</div>' +
      '<div class="ols3d-roster-header-row">' +
        '<h5 id="rh5_' + id + '" style="margin:0;font-size:14px;font-weight:800;">Roster Details ' +
          '<span id="rc_' + id + '" style="font-size:12px;font-weight:500;color:#6B7A9F;margin-left:10px;">0 players</span>' +
        '</h5>' +
        '<button class="ols3d-btn-add-row" type="button" onclick="rosterAddRow(' + id + ')">' +
          PLUS_SVG + ' Add Player' +
        '</button>' +
      '</div>' +
      '<div class="ols3d-roster-scroll">' +
        '<table class="ols3d-roster-table">' +
          '<thead>' +
            '<tr class="ols3d-roster-thead-dark">' +
              '<th class="ols3d-th-del"></th>' +
              '<th>Name on Jersey</th><th>Pattern Cut</th><th>P or G</th>' +
              '<th>Jersey Size</th><th>Jersey No</th>' +
              '<th>Jersey Color</th><th>QTY</th>' +
              '<th>Jersey Color 2</th><th>QTY</th>' +
              '<th>Sock Size</th><th>Sock Color</th><th>QTY</th>' +
              '<th>Sock Color 2</th><th>QTY</th>' +
              '<th>C or A</th><th>Name for Packing</th><th>Notes</th>' +
            '</tr>' +
          '</thead>' +
          '<tbody id="rosterBody_' + id + '"></tbody>' +
        '</table>' +
      '</div>' +
      '<div class="ols3d-roster-table-footer">' +
        '<span class="ols3d-roster-total">Total players: <strong id="rcf_' + id + '">0</strong></span>' +
      '</div>';

    // Populate existing rows
    var tbody = panel.querySelector('#rosterBody_' + id);
    (team.rows || []).forEach(function (d) { tbody.appendChild(makeRow(d)); });
    updateTeamCount(id);

    return panel;
  }

  /* ── Tab renderer ── */
  function renderTeamTabs() {
    var nav = document.getElementById('teamsTabsNav');
    nav.innerHTML = '';
    teams.forEach(function (team, idx) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'ols3d-team-tab' + (team.teamId === activeTeamId ? ' active' : '');
      btn.textContent = 'Team ' + (idx + 1);
      btn.setAttribute('data-tid', team.teamId);
      btn.onclick = (function (tid) { return function () { switchTeam(tid); }; }(team.teamId));

      if (teams.length > 1) {
        var del = document.createElement('span');
        del.className = 'ols3d-team-tab-del';
        del.innerHTML = '×';
        del.title = 'Remove team';
        del.onclick = (function (tid) {
          return function (e) {
            e.stopPropagation();
            removeTeam(tid);
          };
        }(team.teamId));
        btn.appendChild(del);
      }
      nav.appendChild(btn);
    });
  }

  /* ── Switch active panel ── */
  function switchTeam(tid) {
    if (activeTeamId !== null) {
      var prev = document.getElementById('teamPanel_' + activeTeamId);
      if (prev) prev.style.display = 'none';
    }
    activeTeamId = tid;
    var next = document.getElementById('teamPanel_' + tid);
    if (next) next.style.display = 'block';
    renderTeamTabs();
  }

  /* ── Add a new team ── */
  function addTeam(name, year) {
    var tid  = ++teamIdCtr;
    var team = { teamId: tid, of_id: 0, name: name, year: year, rows: [] };
    teams.push(team);

    var container = document.getElementById('teamPanelsContainer');
    var panel = makeTeamPanel(team);
    panel.style.display = 'none';
    container.appendChild(panel);

    switchTeam(tid);
  }

  /* ── Remove a team ── */
  function removeTeam(tid) {
    if (teams.length <= 1) {
      showToast('At least one team is required.');
      return;
    }

    // Track deleted of_ids so backend can clean up
    var t = teams.filter(function (x) { return x.teamId === tid; })[0];
    if (t && t.of_id > 0) { deletedOfIds.push(t.of_id); }

    // Remove from state and DOM
    teams = teams.filter(function (x) { return x.teamId !== tid; });
    var panel = document.getElementById('teamPanel_' + tid);
    if (panel) panel.remove();

    // Switch to another team if we removed the active one
    if (activeTeamId === tid) {
      activeTeamId = null;
      switchTeam(teams[0].teamId);
    } else {
      renderTeamTabs();
    }
  }

  /* ── Public: add player row ── */
  window.rosterAddRow = function (teamId) {
    var tbody = document.getElementById('rosterBody_' + teamId);
    if (!tbody) return;
    tbody.appendChild(makeRow({ item_id: 0 }));
    updateTeamCount(teamId);
  };

  /* ── Public: delete player row ── */
  window.rosterDelRow = function (btn) {
    var tr    = btn.closest('tr');
    var tbody = tr.closest('tbody');
    tr.remove();
    var teamId = parseInt(tbody.id.replace('rosterBody_', ''), 10);
    updateTeamCount(teamId);
  };

  /* ── Modal controls ── */
  window.openAddTeamModal = function () {
    document.getElementById('newTeamNameIn').value = '';
    document.getElementById('newTeamYearIn').value = '';
    document.getElementById('addTeamOverlay').classList.add('open');
    document.getElementById('newTeamNameIn').focus();
  };

  window.closeAddTeamModal = function () {
    document.getElementById('addTeamOverlay').classList.remove('open');
  };

  window.confirmAddTeam = function () {
    var name = document.getElementById('newTeamNameIn').value.trim();
    var year = document.getElementById('newTeamYearIn').value.trim();
    if (!name) {
      document.getElementById('newTeamNameIn').focus();
      showToast('Please enter a Team Name.');
      return;
    }
    if (!year || !/^\d{4}$/.test(year)) {
      document.getElementById('newTeamYearIn').focus();
      showToast('Please enter a valid 4-digit Year.');
      return;
    }
    closeAddTeamModal();
    addTeam(name, year);
  };

  /* ── Collect rows from one team tbody ── */
  function collectRows(tbody) {
    var rows = [];
    Array.prototype.forEach.call(tbody.rows, function (tr) {
      var cells = tr.cells;
      function v(i) {
        var el = cells[i].querySelector('input,select');
        return el ? el.value : '';
      }
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
    return rows;
  }

  /* ── Save all teams ── */
  window.rosterSave = function () {
    var teamsPayload = [];

    for (var i = 0; i < teams.length; i++) {
      var team   = teams[i];
      var tid    = team.teamId;
      var teamNum = i + 1;

      var teamName = (document.getElementById('team_name_' + tid) || {}).value || '';
      var teamYear = (document.getElementById('team_year_' + tid) || {}).value || '';
      teamName = teamName.trim();
      teamYear = teamYear.trim();

      if (!teamName) {
        showToast('Team ' + teamNum + ': Please enter a Team Name.');
        switchTeam(tid);
        return;
      }
      if (!teamYear) {
        showToast('Team ' + teamNum + ': Please enter the Year.');
        switchTeam(tid);
        return;
      }
      if (!/^\d{4}$/.test(teamYear)) {
        showToast('Team ' + teamNum + ': Year must be a 4-digit number (e.g. 2025).');
        switchTeam(tid);
        return;
      }

      var tbody = document.getElementById('rosterBody_' + tid);
      var rows  = collectRows(tbody);

      // Validate rows
      for (var ri = 0; ri < rows.length; ri++) {
        var rowNum = ri + 1;
        var jNo    = rows[ri].jersey_no;
        if (jNo === '') {
          showToast('Team ' + teamNum + ', Row ' + rowNum + ': Jersey # is required.');
          switchTeam(tid);
          return;
        }
        if (!/^\d+$/.test(jNo)) {
          showToast('Team ' + teamNum + ', Row ' + rowNum + ': Jersey # must contain numbers only.');
          switchTeam(tid);
          return;
        }
        if (rows[ri].player_name.length > 50) {
          showToast('Team ' + teamNum + ', Row ' + rowNum + ': Name on Jersey must be 50 characters or fewer.');
          switchTeam(tid);
          return;
        }
      }

      teamsPayload.push({
        of_id:     team.of_id,
        team_name: teamName,
        team_year: teamYear,
        rows:      rows
      });
    }

    var btn = document.getElementById('rosterSaveBtn');
    btn.disabled    = true;
    btn.textContent = 'Saving…';

    fetch('ajax/roster/save_roster.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        design_order_id: DESIGN_ORDER_ID,
        teams:           teamsPayload,
        deleted_of_ids:  deletedOfIds
      })
    })
    .then(function (r) {
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    })
    .then(function (data) {
      if (data.success) {
        // Update of_id for newly created teams
        if (data.team_of_ids && data.team_of_ids.length === teams.length) {
          teams.forEach(function (t, idx) { t.of_id = data.team_of_ids[idx]; });
        }
        deletedOfIds = [];
        var total = (data.inserted || 0) + (data.updated || 0);
        showToast('Roster saved (' + (data.inserted||0) + ' added, ' + (data.updated||0) + ' updated, ' + (data.deleted||0) + ' removed)');
        setTimeout(function () { window.location.href = CHECKOUT_URL; }, 1000);
      } else {
        showToast('Error: ' + (data.message || 'Save failed'));
        btn.disabled    = false;
        btn.textContent = 'Save & Continue →';
      }
    })
    .catch(function (e) {
      showToast('Save failed: ' + e.message);
      btn.disabled    = false;
      btn.textContent = 'Save & Continue →';
    });
  };

  /* ── Toast ── */
  function showToast(msg) {
    var t = document.getElementById('ols3dToast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(function () { t.classList.remove('show'); }, 3500);
  }

  /* Teleport toast to <body> to escape CSS transform context */
  (function () {
    var t = document.getElementById('ols3dToast');
    if (t && t.parentNode !== document.body) document.body.appendChild(t);
  }());

  /* Close modal when clicking overlay backdrop */
  document.getElementById('addTeamOverlay').addEventListener('click', function (e) {
    if (e.target === this) closeAddTeamModal();
  });

  /* ── Init: build team objects and panels from server data ── */
  (function () {
    var container = document.getElementById('teamPanelsContainer');

    initialTeams.forEach(function (t) {
      var tid  = ++teamIdCtr;
      var team = { teamId: tid, of_id: t.of_id, name: t.name, year: t.year, rows: t.rows || [] };
      teams.push(team);

      var panel = makeTeamPanel(team);
      panel.style.display = 'none';
      container.appendChild(panel);
    });

    if (teams.length > 0) {
      activeTeamId = teams[0].teamId;
      var firstPanel = document.getElementById('teamPanel_' + activeTeamId);
      if (firstPanel) firstPanel.style.display = 'block';
      renderTeamTabs();
    }
  }());
}());
</script>
