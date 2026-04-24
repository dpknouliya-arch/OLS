<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id  = $obj_user->user_id;

if (isset($_GET['order_id'])) {

  $order_id = customDecode($_GET['order_id']);    

    $result = callAPI("get_order.php?order_id=$order_id");
    $data = $result['data'] ?? null;
    if (!$data || empty($data)) {
        echo "<p>No data found.</p>";
        exit;
    }

    // ✅ Map API response    
    $order = $data;
    
    $order_team_data = $data['team'] ?? [];
    $order_design_data = isset($data['design']) ? [$data['design']] : [];

    $designId = $order['design_id'] ?? 0;
    $added_date = $order['added_date'] ?? '';

    // ✅ Already arrays (no need json_decode if API is correct)
    //$zones = json_decode($order['colorDecals'] ?? '[]', true);
    $zones = json_decode($order['colorDecals'], true);
    $logos = json_decode($order['imagedecals'] ?? '[]', true);
    $texts = json_decode($order['textdecals'] ?? '[]', true);     
    
    function getFabricName($fabId) {
      if (empty($fabId)) return '';    
      $result = callAPI("get_fabric_byid.php?fab=$fabId");
      return $result['data']['title'] ?? '';
    }
    function getCollarName($colId) {
      if (empty($colId)) return '';
      $result = callAPI("get_collar_byid.php?coller=$colId");
      return $result['data']['title'] ?? '';
    }

    // Fabric
    $fab_ary = [
        'Base'     => getFabricName($order['fabric_base']),
        'Neck'     => getFabricName($order['fabric_neck']),
        'Mesh'     => getFabricName($order['fabric_mesh']),
        'Shoulder' => getFabricName($order['fabric_shoulder']),
    ];

    $jersey_coller = getCollarName($order['coller_id']);
    $jsname        = '';
    $stripes_name  = '';

    $frontImage = $order['front_image'] ?? '';
    $backImage  = $order['back_image'] ?? '';
    $leftImage  = $order['left_image'] ?? '';
    $rightImage = $order['right_image'] ?? '';

    // Design
    $design_name  = $order['name'] ?? '—';
    $jersey_type  = $order['modal_type'] ?? '—';
    $design_image = $order['image'] ?? '';

    $order_date_fmt = !empty($added_date) ? date('d-m-Y H:i:s', strtotime($added_date)) : '—';
    $order_id_enc   = customEncode($order_id);    

} else {
    echo "<p>Invalid order ID.</p>";
    exit;
}


// function getPantonName($conn4, $zone, $designId, $type = 'pantonName') {
//     $sql  = "SELECT panton_name, name FROM colors WHERE name = ?";
//     $stmt = $conn4->prepare($sql);
//     $stmt->bind_param("s", $zone);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($row = $result->fetch_assoc()) {
//         return $type === 'pantonName' ? $row['panton_name'] : $row['name'];
//     }

//     $sql  = "SELECT color_group FROM design_zones WHERE design_id = ? AND zone_name = ? OR sub_zone_name = ? LIMIT 1";
//     $stmt = $conn4->prepare($sql);
//     $stmt->bind_param("iss", $designId, $zone, $zone);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if (!($row = $result->fetch_assoc())) { return $zone; }

//     $colorGroup = $row['color_group'];
//     $map = ['primary' => 'primary_color', 'secondary' => 'secondary_color', 'tertiary' => 'tertiary_color'];
//     if (!isset($map[$colorGroup])) { return $colorGroup; }

//     $sql  = "SELECT {$map[$colorGroup]} AS color FROM designs WHERE id = ? LIMIT 1";
//     $stmt = $conn4->prepare($sql);
//     $stmt->bind_param("i", $designId);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if (!($row = $result->fetch_assoc())) { return $colorGroup; }
//     $colorName = $row['color'];

//     $sql  = "SELECT panton_name, name FROM colors WHERE name = ?";
//     $stmt = $conn4->prepare($sql);
//     $stmt->bind_param("s", $colorName);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     if ($row = $result->fetch_assoc()) {
//         return $type === 'pantonName' ? $row['panton_name'] : $row['name'];
//     }
//     return $colorName;
// }

function getPantonNameAPI($zone, $designId, $type = 'pantonName') {

    if (empty($zone)) return '';
    /* ───── STEP 1: CHECK DIRECT COLOR ───── */
    $res = callAPI("get_color_by_name.php?name=" . urlencode($zone));

    if (!empty($res['data'])) {
        return ($type === 'pantonName')
            ? $res['data']['panton_name']
            : $res['data']['name'];
    }

    /* ───── STEP 2: GET COLOR GROUP ───── */
    $res = callAPI("get_design_zone.php?design_id=$designId&zone=" . urlencode($zone));

    if (empty($res['data'])) {
        return $zone;
    }

    $colorGroup = $res['data']['color_group'];

    $map = [
        'primary'   => 'primary_color',
        'secondary' => 'secondary_color',
        'tertiary'  => 'tertiary_color'
    ];

    if (!isset($map[$colorGroup])) {
        return $colorGroup;
    }

    /* ───── STEP 3: GET DESIGN COLOR ───── */
    $res = callAPI("get_design_by_id.php?id=$designId");

    if (empty($res['data'])) {
        return $colorGroup;
    }

    $colorName = $res['data'][$map[$colorGroup]] ?? '';

    /* ───── STEP 4: GET FINAL COLOR NAME ───── */
    $res = callAPI("get_color_by_name.php?name=" . urlencode($colorName));

    if (!empty($res['data'])) {
        return ($type === 'pantonName')
            ? $res['data']['panton_name']
            : $res['data']['name'];
    }

    return $colorName;
}
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="Style/ols_3d_order.css">

<!-- Hidden fields for downstream use -->
<input type="hidden" name="coller_id"  value="<?= htmlspecialchars($order['coller_id'])  ?>">
<input type="hidden" name="style_id"   value="<?= htmlspecialchars($order['style_id'])   ?>">
<input type="hidden" name="stripes_id" value="<?= htmlspecialchars($order['stripes_id']) ?>">
<input type="hidden" name="order_id"  value="<?= $order['order_id'];?>">
<input type="hidden" name="design_id" value="<?= $order['design_id']; ?>">

<style>
@keyframes spin { to { transform: rotate(360deg); } }
.ols3d-preloader {
  position: absolute; inset: 0; background: rgba(248,249,252,.92);
  display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 0;
}
.ols3d-preloader-spinner {
  width: 44px; height: 44px; border: 4px solid #E8EDF5;
  border-top-color: #1B3FB0; border-radius: 50%;
  animation: spin .9s linear infinite;
}
</style>

<div class="ols3d-module">

  <!-- Title Row -->
  <div class="ols3d-title-row">
    <a href="?vp=<?= base64_encode('3d_orders') ?>" class="ols3d-back-btn" title="Back to Orders">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
    </a>
    <div>
      <h1>Order Details</h1>
      <p class="ols3d-breadcrumb">
        <a href="?vp=<?= base64_encode('3d_orders') ?>">Orders</a>
        <span class="sep">›</span>
        <span class="current">#<?= $order['order_id'] ?></span>
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
        <div class="ols3d-step-num active">1</div>
        <span class="ols3d-step-label active">Order Details</span>
      </div>
      <div class="ols3d-step-connector"></div>
      <div class="ols3d-step">
        <div class="ols3d-step-num">2</div>
        <span class="ols3d-step-label">Add Roster</span>
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
    <div class="ols3d-info-divider"></div>
    <span class="ols3d-badge ols3d-badge-new">New</span>
  </div>

  <!-- ── Two-column preview panels ─────────────────────────── -->
  <div class="ols3d-panels">

    <!-- 3D Preview -->
    <div class="ols3d-panel">
      <div class="ols3d-panel-header">
        <span class="ols3d-panel-title">3D Preview</span>
        <span class="ols3d-panel-tag">Interactive</span>
      </div>
      <div class="ols3d-preview-area">
        <div class="ols3d-preloader" id="preloader">
          <div class="ols3d-preloader-spinner"></div>
        </div>
        <div id="threejs-container-user"></div>
      </div>
      <div class="ols3d-preview-footer">
        <button class="ols3d-view-tab active" onclick="setView('front',this)">Front</button>
        <button class="ols3d-view-tab" onclick="setView('back',this)">Back</button>
        <button class="ols3d-view-tab" onclick="setView('left',this)">Left</button>
        <button class="ols3d-view-tab" onclick="setView('right',this)">Right</button>
      </div>
    </div>

    <!-- Customer Art Approval -->
    <div class="ols3d-panel">
      <div class="ols3d-panel-header">
        <span class="ols3d-panel-title">Customer Art Approval</span>
        <span class="ols3d-badge ols3d-badge-preapproval">Pre-approval</span>
      </div>
      <div class="ols3d-panel-body">        
        <div id="svgLoader" style="position: relative;inset: 0;background: rgba(255,255,255,0.85);display: none;align-items: center;justify-content: center;z-index: 20;">
            <div style="width: 60px;height: 60px;border: 6px solid #ddd;border-top: 6px solid #333;border-radius: 50%;animation: spin 1s linear infinite;">                            
            </div>
        </div>
        <div class="my-auto d-flex align-items-center" id="frontPreview"></div>
        <div class="my-auto d-flex align-items-center">
            <div id="backPreview">
            </div>
            <div>
                <figure class="h-100 my-auto d-flex align-items-center"><img src="../<?= $design['sock_design']; ?>" alt="" class="w-30"></figure>
            </div>
        </div>                          
        <div class="ols3d-art-actions">        
        </div>
        <button class="ols3d-btn-approve">✓ Approve</button>
        <button class="ols3d-btn-changes">Request Changes</button>
      </div>
      </div>
    </div>

  </div><!-- /.ols3d-panels -->

  <!-- ── Specifications ────────────────────────────────────── -->
  <div class="ols3d-specs-card">
    <div class="ols3d-specs-card-title">Specifications</div>
    <div class="ols3d-specs-card-body">

      <!-- Fabric & Collar Details -->
      <div class="ols3d-spec-section">
        <div class="ols3d-spec-header" onclick="toggleSpec(this)">
          <span class="ols3d-spec-header-title">Fabric &amp; Collar Details</span>
          <svg class="ols3d-spec-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
          </svg>
        </div>
        <div class="ols3d-spec-body">
          <div class="ols3d-fabric-grid">
            <div class="ols3d-fabric-col">
              <div class="ols3d-fabric-col-title">Fabric Details</div>
              <?php foreach ($fab_ary as $fkey => $fval): ?>
              <div class="ols3d-kv">
                <span class="ols3d-kv-key"><?= htmlspecialchars($fkey) ?></span>
                <span class="ols3d-kv-val"><?= htmlspecialchars($fval ?: '—') ?></span>
              </div>
              <?php endforeach; ?>
            </div>
            <div class="ols3d-fabric-col">
              <div class="ols3d-fabric-col-title">Collar</div>
              <div class="ols3d-kv">
                <span class="ols3d-kv-key">Style</span>
                <span class="ols3d-kv-val"><?= htmlspecialchars($jersey_coller ?: '—') ?></span>
              </div>
            </div>
            <div class="ols3d-fabric-col">
              <div class="ols3d-fabric-col-title">Stripes</div>
              <div class="ols3d-kv">
                <span class="ols3d-kv-key">Style</span>
                <span class="ols3d-kv-val"><?= htmlspecialchars($jsname ?: '—') ?></span>
              </div>
              <div class="ols3d-kv">
                <span class="ols3d-kv-key">Pattern</span>
                <span class="ols3d-kv-val"><?= htmlspecialchars($stripes_name ?: '—') ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Color Details -->
      <div class="ols3d-spec-section">
        <div class="ols3d-spec-header" onclick="toggleSpec(this)">
          <span class="ols3d-spec-header-title">Color Details</span>
          <svg class="ols3d-spec-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
          </svg>
        </div>
        <div class="ols3d-spec-body">
          <?php if (!empty($zones)): ?>
          <div class="ols3d-colors-grid">
            <?php
            foreach ($zones as $zkey => $zval) {
                if (is_array($zval)) {
                    foreach ($zval as $subKey => $subValue) {
                        $colorName  = getPantonNameAPI($subValue, $designId, 'colorName');
                        $pantonName = getPantonNameAPI($subValue, $designId, 'pantonName');
            ?>
            <div class="ols3d-color-row">
              <span class="ols3d-color-row-key"><?= htmlspecialchars($subKey) ?></span>
              <div class="ols3d-color-swatch-wrap">
                <span class="ols3d-color-swatch" style="background-color:<?= htmlspecialchars($colorName) ?>"></span>
                <span class="ols3d-color-name"><?= htmlspecialchars($pantonName ?: $colorName) ?></span>
              </div>
            </div>
            <?php
                    }
                } else {
                    $colorName  = getPantonNameAPI($zval, $designId, 'colorName');
                    $pantonName = getPantonNameAPI($zval, $designId, 'pantonName');
            ?>
            <div class="ols3d-color-row">
              <span class="ols3d-color-row-key"><?= htmlspecialchars($zkey) ?></span>
              <div class="ols3d-color-swatch-wrap">
                <span class="ols3d-color-swatch" style="background-color:<?= htmlspecialchars($colorName) ?>"></span>
                <span class="ols3d-color-name"><?= htmlspecialchars($pantonName ?: $colorName) ?></span>
              </div>
            </div>
            <?php
                }
            }
            ?>
          </div>
          <?php else: ?>
          <div style="padding:16px 20px;font-size:13px;color:#9DAABF;">No color data available.</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Number Details -->
      <div class="ols3d-spec-section">
        <div class="ols3d-spec-header" onclick="toggleSpec(this)">
          <span class="ols3d-spec-header-title">Number Details</span>
          <svg class="ols3d-spec-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
          </svg>
        </div>
        <div class="ols3d-spec-body">
          <table class="ols3d-spec-table">
            <thead>
              <tr>
                <th>Placement</th><th>Size</th><th>Font</th><th>Fill</th><th>Outline</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $has_numbers = false;
              if (!empty($texts)) {
                  foreach ($texts as $text) {
                    // echo "<pre>";
                    // print_r($text);
                    // echo "</pre>";
                      if (!isset($text['displayType']) || trim($text['displayType']) === '') continue;
                      $has_numbers  = true;
                      $displayName  = $text['displayName']  ?? 'Unknown';
                      $Text         = $text['text']         ?? '';
                      $FontFamily   = $text['fontFamily']   ?? 'Default Font';
                      $FontColor    = $text['color']    ?? '#000000';
                      $OutlineColor = $text['outlineColor'] ?? '#FFFFFF';
                      $height       = isset($text['bounds']['height']) ? number_format(floatval($text['bounds']['height']) * 39.3701, 2) : '0.00';
                      $width        = isset($text['bounds']['width'])  ? number_format(floatval($text['bounds']['width'])  * 39.3701, 2) : '0.00';
                      $fillPanton   = getPantonNameAPI($FontColor,    $designId, 'pantonName');
                      $outPanton    = getPantonNameAPI($OutlineColor, $designId, 'pantonName');
              ?>
              <tr>
                <td class="fw"><?= htmlspecialchars($displayName) ?>: <?= htmlspecialchars($Text) ?></td>
                <td class="mono"><?= $height ?>"H × <?= $width ?>"W</td>
                <td><?= htmlspecialchars($FontFamily) ?></td>
                <td>
                  <div class="ols3d-color-inline">
                    <span class="ols3d-color-dot" style="background:<?= htmlspecialchars($FontColor) ?>"></span>
                    <span class="ols3d-tag-blue"><?= htmlspecialchars($fillPanton ?: $FontColor) ?></span>
                  </div>
                </td>
                <td>
                  <div class="ols3d-color-inline">
                    <span class="ols3d-color-dot" style="background:<?= htmlspecialchars($OutlineColor) ?>"></span>
                    <span class="ols3d-tag-grey"><?= htmlspecialchars($outPanton ?: $OutlineColor) ?></span>
                  </div>
                </td>
              </tr>
              <?php
                  }
              }
              if (!$has_numbers): ?>
              <tr><td colspan="5" style="color:#9DAABF;text-align:center;padding:16px;">No number details available.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Name Details -->
      <div class="ols3d-spec-section">
        <div class="ols3d-spec-header" onclick="toggleSpec(this)">
          <span class="ols3d-spec-header-title">Name Details</span>
          <svg class="ols3d-spec-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
          </svg>
        </div>
        <div class="ols3d-spec-body">
          <table class="ols3d-spec-table">
            <thead>
              <tr>
                <th>Placement</th><th>Size</th><th>Font</th><th>Fill</th><th>Outline</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $has_names = false;
              if (!empty($texts)) {
                  foreach ($texts as $text) {
                      if (!isset($text['displayType']) || $text['displayType'] === 'text') continue;
                      $has_names    = true;
                      $displayName  = $text['displayName']  ?? 'Unknown';
                      $Text         = $text['text']         ?? '';
                      $FontFamily   = $text['fontFamily']   ?? 'Default Font';
                      $FontColor    = $text['color']    ?? '#000000';
                      $OutlineColor = $text['outlineColor'] ?? '#FFFFFF';
                      $height       = isset($text['bounds']['height']) ? number_format(floatval($text['bounds']['height']) * 39.3701, 2) : '0.00';
                      $width        = isset($text['bounds']['width'])  ? number_format(floatval($text['bounds']['width'])  * 39.3701, 2) : '0.00';
                      $fillPanton   = getPantonNameAPI($FontColor,    $designId, 'pantonName');
                      $outPanton    = getPantonNameAPI($OutlineColor, $designId, 'pantonName');
              ?>
              <tr>
                <td class="fw"><?= htmlspecialchars($displayName) ?>: <?= htmlspecialchars($Text) ?></td>
                <td class="mono"><?= $height ?>"H × <?= $width ?>"W</td>
                <td><?= htmlspecialchars($FontFamily) ?></td>
                <td>
                  <div class="ols3d-color-inline">
                    <span class="ols3d-color-dot" style="background:<?= htmlspecialchars($FontColor) ?>"></span>
                    <span class="ols3d-tag-blue"><?= htmlspecialchars($fillPanton ?: $FontColor) ?></span>
                  </div>
                </td>
                <td>
                  <div class="ols3d-color-inline">
                    <span class="ols3d-color-dot" style="background:<?= htmlspecialchars($OutlineColor) ?>"></span>
                    <span class="ols3d-tag-grey"><?= htmlspecialchars($outPanton ?: $OutlineColor) ?></span>
                  </div>
                </td>
              </tr>
              <?php
                  }
              }
              if (!$has_names): ?>
              <tr><td colspan="5" style="color:#9DAABF;text-align:center;padding:16px;">No name details available.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Logo / Crest Details -->
      <?php if (!empty($logos)): ?>
      <div class="ols3d-spec-section">
        <div class="ols3d-spec-header" onclick="toggleSpec(this)">
          <span class="ols3d-spec-header-title">Logo / Crest Details</span>
          <svg class="ols3d-spec-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
          </svg>
        </div>
        <div class="ols3d-spec-body">
          <div class="ols3d-logo-grid">
            <?php foreach ($logos as $logo): ?>
            <?php
                $lh = isset($logo['height']) ? number_format(floatval($logo['height']) * 39.3701, 2) : '0.00';
                $lw = isset($logo['width'])  ? number_format(floatval($logo['width'])  * 39.3701, 2) : '0.00';
            ?>
            <div class="ols3d-logo-item">
              <?php if (!empty($logo['imageSrc'])): ?>
              <img src="<?= htmlspecialchars($logo['imageSrc']) ?>" alt="Logo">
              <?php endif; ?>
              <div class="logo-label"><?= htmlspecialchars($logo['displayName'] ?? 'Logo') ?></div>
              <p>Placement: <?= htmlspecialchars($logo['displayName'] ?? '—') ?></p>
              <p>Size: <?= $lh ?>"H × <?= $lw ?>"W</p>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div><!-- /.ols3d-specs-card-body -->
  </div><!-- /.ols3d-specs-card -->

  <!-- Action Buttons -->
  <div class="ols3d-actions">
    <a href="?vp=<?= base64_encode('3d_order_roster') ?>&order_id=<?= $order_id_enc ?>"
       class="ols3d-btn-primary">
      Continue to Add Roster →
    </a>
    <a href="?vp=<?= base64_encode('3d_orders') ?>" class="ols3d-btn-secondary">
      Save Draft
    </a>
  </div>

</div><!-- /.ols3d-module -->

<!-- Three.js and related scripts -->
<script src="https://cdn.jsdelivr.net/npm/opentype.js@1.3.4/dist/opentype.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/DecalGeometry.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/renderers/SVGRenderer.js"></script>
<script src="https://unpkg.com/three@0.160.0/examples/js/utils/BufferGeometryUtils.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

	<script>
		window.BASE_3D_URL = "<?php echo D_BASE_URL; ?>";
    console.log("Base 3D URL:", window.BASE_3D_URL);
	</script>
<script type="module" src="js/3dmodel.js?ver=1.0"></script>

<script>
function toggleSpec(header) {
  var body = header.nextElementSibling;
  if (!body) return;
  var collapsed = header.classList.toggle('collapsed');
  body.classList.toggle('hidden', collapsed);
}

function setView(view, btn) {
  document.querySelectorAll('.ols3d-view-tab').forEach(function (b) { b.classList.remove('active'); });
  btn.classList.add('active');
  // viewpoint control hook – extend with three.js camera logic as needed
}
</script>
