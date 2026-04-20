<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

if (isset($_GET['order_id'])) {
    $order_id = customDecode($_GET['order_id']);

    // Fetch order data
    $sql = "SELECT * FROM design_order WHERE order_id = ?";
    $stmt = $conn4->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Extract values
        $designId = $order['design_id'];
        $sock_design = $order['sock_design'];
        $added_date = $order['added_date'];

        // 🧩 Decode JSON fields
        $zones = json_decode($order['colorDecals'], true);
        $logos = json_decode($order['imagedecals'], true);
        $texts = json_decode($order['textdecals'], true);

        // 🧵 Fabric fields
        $fab_ary = [
            'Base' => $order['fabric_base'],
            'Neck' => $order['fabric_neck'],
            'Mesh' => $order['fabric_mesh'],
            'Shoulder' => $order['fabric_shoulder']
        ];

        // You may also fetch style/stripe names if needed:
        $jersey_coller = $order['fabric_neck'];
        $jsname = '';
        $stripes_name = '';

        // 🧩 Optional: Fetch preview images if saved separately
        $frontImage = $order['front_image'] ?? '';
        $backImage = $order['back_image'] ?? '';
        $leftImage = $order['left_image'] ?? '';
        $rightImage = $order['right_image'] ?? '';

    } else {
        echo "<p>No design found for this Order ID.</p>";
        exit;
    }
} else {
    echo "<p>Invalid order ID.</p>";
    exit;
}

$order_team_data = [];
$order_id = customDecode($_GET['order_id']);
if (isset($order_id)) {
    $sql_team = "SELECT * FROM order_team WHERE order_id = ?";
    $stmt_team = $conn4->prepare($sql_team);
    $stmt_team->bind_param("i", $order_id);
    $stmt_team->execute();
    $result_team = $stmt_team->get_result();

    while ($row = $result_team->fetch_assoc()) {
        $order_team_data[] = $row;
    }    
}

if (isset($designId)) {
    $sql_designs = "SELECT * FROM designs WHERE id = ?";
    $stmt_designs = $conn4->prepare($sql_designs);
    $stmt_designs->bind_param("i", $designId);
    $stmt_designs->execute();
    $result_designs = $stmt_designs->get_result();

    while ($row = $result_designs->fetch_assoc()) {
        $order_design_data[] = $row;
    }    
}
?>

<?php
foreach ($order_design_data as $key => $value) {  
?>

    <input type="hidden" name="coller_id" value="<?= $value['coller_id']; ?>">

    <input type="hidden" name="style_id" value="<?= $value['style_id']; ?>">

    <input type="hidden" name="stripes_id" value="<?= $value['stripes_id']; ?>">

    
<?php
}   
?>
<input type="hidden" name="order_id" value="<?= $order_id; ?>">
<input type="hidden" name="design_id" value="<?= $designId; ?>">

<style>
    #threejs-container {

        width: 100%;

        height: 100%;

    }

    #threejs-container-user {

        width: 100%;

        height: 500px;

    }
</style>

<section class="generatePO">
    <div class="container">
        <div class="row" >            
            <div class="col-lg-6 col-md-6 OriginalModal">
                <div id="preloader" class="preloader">
                    <div class="preloader-content">
                        <div class="preloader-spinner"></div>
                        <div class="MainContent">
                            <img src="../images/logo1.png" class="mob-logo">
                            <div id="preloaderProgress" class="preloader-progress">
                                0%
                            </div>
                            <div id="preloaderTimeRemaining" class="preloader-time">Loading...</div>
                        </div>
                    </div>
                </div>
                <div class="card h-100">
                    <div id="threejs-container-user"></div>                   
                </div>
            </div>
             <div class="col-lg-6 col-md-6 OriginalModal">
                <div class="card h-100">
                    <div id="svgLoader" style="
                        position: absolute;
                        inset: 0;
                        background: rgba(255,255,255,0.85);
                        display: none;
                        align-items: center;
                        justify-content: center;
                        z-index: 20;
                    ">
                        <div style="
                            width: 60px;
                            height: 60px;
                            border: 6px solid #ddd;
                            border-top: 6px solid #333;
                            border-radius: 50%;
                            animation: spin 1s linear infinite;
                        "></div>
                    </div>

                    <div class="my-auto d-flex align-items-center" id="frontPreview"></div>

                    <div class="my-auto d-flex align-items-center">
                        <div id="backPreview">
                        </div>
                        <div>
                            <figure class="h-100 my-auto d-flex align-items-center"><img src="../<?= $design['sock_design']; ?>" alt="" class="w-30"></figure>
                        </div>
                    </div>                                                    
                    <!-- <figure class="h-100 my-auto d-flex align-items-center"><img src="../images/main/sectionArtApprovalMain.png" alt="" class="w-100"></figure> -->

                </div>
             </div>
            <div class="col-md-12 m-auto" id="pritn_details">

                <!-- PDF 1 -->
                <div class="innerDiv">
                    <div class="step1 stepsItems">
                        <div class="upperHead">
                            <figure class="logo my-auto">
                                <img src="images/logo1.png" alt="">
                            </figure>
                        </div>
                        <div>
                            
                        </div>
                        <div class="stepNavigate">
                            <h6>Style <span class="styleName">The Premier Hockey Jersey</span> : Jog2026<?= $order['order_id'] ?> </h6>
                        </div>
                        <div class="modelAngleView grid4">
                            <figure>
                                <img src="<?= htmlspecialchars($frontImage) ?>" >
                            </figure>
                            <figure>
                                <img src="<?= htmlspecialchars($backImage) ?>">
                            </figure>
                            <figure>
                                <img src="<?= htmlspecialchars($leftImage) ?>"  alt="">
                            </figure>
                            <figure>
                                <img src="<?= htmlspecialchars($rightImage) ?>" alt="">
                            </figure>
                        </div>
                        <div class="tableArea fabricDetails">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Fabric Details</th>
                                        <th scope="col">Collar</th>
                                        <th scope="col">Style</th>
                                        <th scope="col">Stripes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <?php
                                            foreach ($fab_ary as $key => $value) {
                                                ?>
                                                <h6> <span><?php echo $key; ?></span> <?php echo $value; ?></h6>
                                                <?php
                                            }
                                            ?>                                               
                                        </td>
                                        <td>
                                            <h6><?php echo $jersey_coller;?></h6>
                                        </td>
                                        <td>
                                            <h6> <?php echo $jsname;?></h6>
                                        </td>
                                        <td>
                                            <h6><?= $stripes_name; ?></h6>                                                
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tableArea ColorDetails">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="2">Color Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="grid2">
                                        <?php
                                        if (!empty($zones)) {
                                            foreach ($zones as $key => $zone) {
                                                if (is_array($zone)) {
                                                    foreach ($zone as $subKey => $subValue) {
                                                        ?>
                                                        <td>
                                                            <h6 class="tableDataStyle2">
                                                                <span><?= htmlspecialchars($subKey) ?></span>
                                                                <div class="colorArea" style="padding: 5px 0px;">
                                                                    <?php
                                                                        $colorName = getPantonName($conn4, $subValue, $designId , 'colorName');
                                                                    ?>
                                                                    <span class="ColorApply activeColor" 
                                                                        style="background-color: <?= $colorName ?>; box-shadow: rgba(0,0,0,0.35) 0px 5px 15px; border: 1px solid #55555536;padding: 1px 10px;border-radius: 50%;margin-right: 10px;"></span>
                                                                    <?= getPantonName($conn4, $subValue, $designId , 'pantonName') ?: "No matching panton name found." ?>
                                                                </div>
                                                            </h6>
                                                        </td>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <td>
                                                        <h6 class="tableDataStyle2">
                                                            <span><?= htmlspecialchars($key) ?></span>
                                                            <div class="colorArea" style="padding: 5px 0px;">
                                                                <?php
                                                                    $colorName = getPantonName($conn4, $zone, $designId , 'colorName');
                                                                ?>
                                                                <span class="ColorApply activeColor" 
                                                                    style="background-color: <?= $colorName ?>; box-shadow: rgba(0,0,0,0.35) 0px 5px 15px; border: 1px solid #55555536;padding: 1px 10px;border-radius: 50%;margin-right: 10px;"></span>
                                                                <?= getPantonName($conn4, $zone, $designId , 'pantonName') ?: "No matching panton name found." ?>
                                                            </div>
                                                        </h6>
                                                    </td>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>                                            
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- PDF 2 -->
                <div class="innerDiv ">
                    <div class="step2 stepsItems">
                        <div class="upperHead">
                            <figure class="logo my-auto">
                                <img src="images/logo1.png" alt="">
                            </figure>
                        </div>
                        <div class="stepNavigate">
                            <h6>Style <span class="styleName">The Premier Hockey Jersey</span></h6>
                        </div>
                        <div class="tableArea numberDetails">
                            <h5 class="tableFor">Number Details</h5>
                            <table class="table">
                                <thead>
                                    <tr class="thead2">
                                        <th scope="col">Placement</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Font</th>
                                        <th scope="col">Fill</th>
                                        <th scope="col">Outline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($texts)) {
                                        foreach ($texts as $key => $text) {
                                            // Skip if no displayType
                                            if (!isset($text['displayType']) || trim($text['displayType']) === '') continue;

                                            // Safe extraction with fallbacks
                                            $displayName  = $text['displayName'] ?? 'Unknown';
                                            $Text         = $text['Text'] ?? '';
                                            $FontFamily   = $text['FontFamily'] ?? 'Default Font';
                                            $FontColor    = $text['FontColor'] ?? '#000000';
                                            $OutlineColor = $text['OutlineColor'] ?? '#FFFFFF';
                                            $height       = isset($text['height']) ? floatval($text['height']) * 39.3701 : 0;
                                            $width        = isset($text['width']) ? floatval($text['width']) * 39.3701 : 0;

                                            ?>
                                            <tr>
                                                <td><h6 class="fw6"><?= htmlspecialchars($displayName) ?>: <?= htmlspecialchars($Text) ?></h6></td>
                                                <td><?= number_format($height, 2) ?>"H X <?= number_format($width, 2) ?>"W</td>
                                                <td><?= htmlspecialchars($FontFamily) ?></td>
                                                <td>
                                                    <div class="colorArea flexRow">
                                                        <span class="ColorApply activeColor" style="background-color: <?= htmlspecialchars($FontColor) ?>;"></span>
                                                        <?= htmlspecialchars(getPantonName($conn4, $FontColor, $designId , 'pantonName')) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="colorArea flexRow">
                                                        <span class="ColorApply activeColor" style="background-color: <?= htmlspecialchars($OutlineColor) ?>;"></span>
                                                        <?= htmlspecialchars(getPantonName($conn4, $OutlineColor, $designId , 'pantonName')) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }

                                    ?>                                       
                                </tbody>
                            </table>
                        </div>
                        <div class="tableArea nameDetails">
                            <h5 class="tableFor">Name Details</h5>
                            <table class="table">
                                <thead>
                                    <tr class="thead2">
                                        <th scope="col">Placement</th>
                                        <th scope="col">Size</th>
                                        <th scope="col">Font</th>
                                        <th scope="col">Fill</th>
                                        <th scope="col">Outline</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($texts)) {
                                        foreach ($texts as $key => $text) {
                                            if (!isset($text['displayType']) || $text['displayType'] === 'text') continue;

                                            $displayName  = $text['displayName'] ?? 'Unknown';
                                            $Text         = $text['Text'] ?? '';
                                            $FontFamily   = $text['FontFamily'] ?? 'Default Font';
                                            $FontColor    = $text['FontColor'] ?? '#000000';
                                            $OutlineColor = $text['OutlineColor'] ?? '#FFFFFF';
                                            $height       = isset($text['height']) ? floatval($text['height']) * 39.3701 : 0;
                                            $width        = isset($text['width']) ? floatval($text['width']) * 39.3701 : 0;
                                            ?>
                                            <tr>
                                                <td><h6 class="fw6"><?= $displayName ?>: <?= $Text?></h6></td>
                                                <td><?= number_format($height, 2) ?>"H X <?= number_format($width, 2) ?>"W</td>
                                                <td><?= $FontFamily ?></td>
                                                <td>
                                                    <div class="colorArea flexRow">
                                                        <span class="ColorApply activeColor" style="background-color: <?= $FontColor ?>;"></span>
                                                        <?= getPantonName($conn4, $FontColor, $designId , 'pantonName') ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="colorArea flexRow">
                                                        <span class="ColorApply activeColor" style="background-color: <?= $OutlineColor ?>;"></span>
                                                        <?= getPantonName($conn4, $OutlineColor, $designId , 'pantonName') ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }

                                    ?>
                                                                        
                                </tbody>
                            </table>
                        </div>
                        <div class="grid2">
                            <?php if (!empty($logos)) {
                                foreach ($logos as $key => $logo) {
                                    // $height = $logo['height'] * 39.3701;
                                    // $width = $logo['width'] * 39.3701;
                                    $height       = isset($logo['height']) ? floatval($logo['height']) * 39.3701 : 0;
                                    $width        = isset($logo['width']) ? floatval($logo['width']) * 39.3701 : 0;
                                    ?>
                                    <div class="tableArea shoulderYokeLogo LogoPlacement">
                                        <h5 class="tableFor"><?= htmlspecialchars($logo['displayName']) ?></h5>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <figure><img src="<?= htmlspecialchars($logo['imageSrc']) ?>" alt="" width="150px"></figure>
                                                        <h6><span>Placement</span> <?= htmlspecialchars($logo['displayName']) ?></h6>
                                                        <h6><span>Size</span> <?= number_format($height, 2) ?>"H X <?= number_format($width, 2) ?>"W</h6>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                }
                            }

                            ?>                                
                        </div>
                    </div>
                </div>                    
                <div class="tableArea shoulderYokeLogo LogoPlacement">
                    <h5 class="tableFor">socks</h5>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>                                                            
                                    <figure><img src="https://jogsports.com/jogdigital/<?php echo $sock_design;?>" width="200"></figure>  
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- PDF 3 -->
                <div class="innerDiv ">
                    <div class="step3 stepsItems">
                        <div class="upperHead">
                            <figure class="logo my-auto">
                                <img src="images/logo1.png" alt="">
                            </figure>
                        </div>
                        <div class="stepNavigate">
                            <h6>Style <span class="styleName">The Premier Hockey Jersey</span></h6>
                        </div>
                        <div class="tableArea numberDetails">
                            <h5 class="tableFor">Roster Details</h5>
                            <table class="table">
                                <thead>
                                    <tr class="thead2">
                                        <th>#</th>
                                        <th>Name on Jersey</th>
                                        <th>Pattern Cut</th>
                                        <th>P or G</th>
                                        <th>Jersey Size</th>
                                        <th>Jersey No</th>
                                        <th>Jersey Color</th>
                                        <th>QTY</th>
                                        <th>Jersey Color</th>
                                        <th>QTY</th>
                                        <th>Sock Size</th>
                                        <th>Sock Color</th>
                                        <th>QTY</th>
                                        <th>Sock Color</th>
                                        <th>QTY</th>
                                        <th>C or A</th>
                                        <th>Name For Packing</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($order_team_data)): ?>
                                        <?php $i = 1; foreach ($order_team_data as $row): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>

                                                <!-- Name -->
                                                <td><?= htmlspecialchars($row['player_name']) ?></td>

                                                <!-- Pattern Cut -->
                                                <td><?= htmlspecialchars($row['pattern_cut']) ?></td>

                                                <!-- Player or Goalie -->
                                                <td><?= htmlspecialchars($row['player_or_goalie']) ?></td>

                                                <!-- Jersey Size -->
                                                <td><?= htmlspecialchars($row['jersey_size']) ?></td>

                                                <!-- Jersey No -->
                                                <td><?= htmlspecialchars($row['jersey_no']) ?></td>

                                                <!-- Jersey Color -->
                                                <td><?= htmlspecialchars($row['jersey_color']) ?></td>

                                                <!-- Qty -->
                                                <td><?= htmlspecialchars($row['jersey_qty']) ?></td>

                                                <!-- Jersey Color 2 -->
                                                <td><?= htmlspecialchars($row['jersey_color2']) ?></td>

                                                <!-- Qty 2 -->
                                                <td><?= htmlspecialchars($row['jersey_qty2']) ?></td>

                                                <!-- Sock Size -->
                                                <td><?= htmlspecialchars($row['sock_size']) ?></td>

                                                <!-- Sock Color -->
                                                <td><?= htmlspecialchars($row['sock_color']) ?></td>

                                                <!-- Sock Qty -->
                                                <td><?= htmlspecialchars($row['sock_qty']) ?></td>

                                                <!-- Sock Color 2 -->
                                                <td><?= htmlspecialchars($row['sock_color2']) ?></td>

                                                <!-- Sock Qty 2 -->
                                                <td><?= htmlspecialchars($row['sock_qty2']) ?></td>

                                                <!-- C or A -->
                                                <td><?= htmlspecialchars($row['cor_a']) ?></td>

                                                <!-- Name For Packing -->
                                                <td><?= htmlspecialchars($row['name_for_packing']) ?></td>

                                                <!-- Notes -->
                                                <td><?= htmlspecialchars($row['notes']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="18" class="text-center">No team members found</td>
                                        </tr>
                                    <?php endif; ?>                               
                                </tbody>
                                <tfoot>                                    
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div>
                    <?php
                    $order_id = customEncode($order_id);
                    ?> 
                    <a href="?vp=<?php echo base64_encode('order_info'); ?>&order_id=<?php echo $order_id; ?>" class="btn btn-primary">Continue Checkout</a>                    
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ReviewDetailsMain d-none">
    <div class="container">
        <div class="row">
            <div class="col-md-7 leftSide">
                <div class="card border-none bg-none">
                    <h6 ITEMS class="divTitle">
                        YOUR ITEMS
                    </h6>
                    <div class="items d-flex ">
                        <figure class="m-0"><img src="images/AllProducts/product1.png" alt="" class="designImg">
                        </figure>
                        <div class="itemsDetails">
                            <h5 class="itemName ">Premier Jerseys</h5>
                            <p class="itemDesc sp">Classic Cut and Sew Jersey with twill appliques</p>
                            <a href="#" data-toggle="modal" data-target="#roasterDetailsPopup">Roster details</a>
                            <a href="#">Preview 3d Jersey</a>
                            <div class="threeDots">
                                <figure>
                                    <img src="images/icons/threeDot.png" alt="" class="toggleButton">
                                </figure>
                                <button class="deleteButton" style="display: none;">Delete</button>
                            </div>

                        </div>
                    </div>
                    <a href="#" class="blueBtn">Submit
                    </a>
                </div>
            </div>
            
            <div class="col-md-5 rightSide">
                <div class="card border-none bg-none">
                    <h6 ITEMS class="divTitle">
                        SUMMARY <img src="images/icons/shirt.png" alt="" class="iconImg">
                    </h6>
                    <div class="card border-none">
                        <table class="table  table-striped table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th colspan="2" style="padding-left: 20px;">GARMENT INFO</th>
                                </tr>
                                <tr>
                                    <td scope="row" style="width: 50%;">Sport</td>
                                    <td>Hockey</td>
                                </tr>
                                <tr>
                                    <td scope="row" style="width: 50%;">Product Type</td>
                                    <td>Premier Jersey</td>
                                </tr>
                                <tr>
                                    <td scope="row" style="width: 50%;">Fabric</td>
                                    <td>Polyester</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table  table-striped table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th colspan="2" style="padding-left: 20px;">COLOR</th>
                                </tr>
                                <tr>
                                    <td scope="row" style="width: 50%;">Primary</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #1BBEC6;  width: 20px;    height: 20px;">
                                            </div>
                                            <div> ( BRIGHT CYAN )</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row" style="width: 50%;">Secondary</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #A21D27;  width: 20px;    height: 20px;">
                                            </div>
                                            <div>
                                                ( CRIMSON RED )
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row" style="width: 50%;">Tertiary</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #060606;  width: 20px;    height: 20px;">
                                            </div>
                                            <div>
                                                ( RICH BLACK )
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                        <table class="table  table-striped table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th colspan="3" style="padding-left: 20px;">LOGO PLACEMENT</th>
                                </tr>
                                <tr>
                                    <td scope="row">Front Chest</td>
                                    <td><input type="text" id="name" name="name" required></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #1A1617;  width: 20px;    height: 20px;">
                                            </div>
                                            <div> ( CHARCOAL BLACK )</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row">Back Center</td>
                                    <td><input type="text" id="name" name="name" required></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #EA4423;  width: 20px;    height: 20px;">
                                            </div>
                                            <div> ( BRIGHT CYAN )</div>
                                        </div>
                                    </td>
                                </tr>


                            </tbody>
                        </table>
                        <table class="table  table-striped table-bordered table-hover">
                            <tbody>
                                <tr>
                                    <th colspan="3" style="padding-left: 20px;">ROSTER DECORATION</th>
                                </tr>
                                <tr>
                                    <td scope="row">Shoulders</td>
                                    <td><input type="text" id="name" name="name" required></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #1A1617;  width: 20px;    height: 20px;">
                                            </div>
                                            <div> ( CHARCOAL BLACK )</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td scope="row">Back Center</td>
                                    <td><input type="text" id="name" name="name" required></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="color"
                                                style="background: #EA4423;  width: 20px;    height: 20px;">
                                            </div>
                                            <div> ( BRIGHT CYAN )</div>
                                        </div>
                                    </td>
                                </tr>


                            </tbody>
                        </table>

                    </div>
                    <div class="quantitySize">
                        <h6 ITEMS class="divTitle">
                            QUANTITY & SIZE REVIEW
                        </h6>
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="text-center">Size</th>
                                    <th scope="col" class="text-center">M</th>
                                    <th scope="col" class="text-center">L</th>
                                    <th scope="col" class="text-center">XL</th>
                                    <th scope="col" class="text-center">XXL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-center">QTY</th>
                                    <td scope="row" class="text-center">10</td>
                                    <td scope="row" class="text-center">5</td>
                                    <td scope="row" class="text-center">2</td>
                                    <td scope="row" class="text-center">3</td>
                                </tr>
                                <tr>
                                    <th scope="row" colspan="4" style="padding-left: 45px;">Total</th>

                                    <td scope="row" class="text-center">20</td>
                                </tr>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/opentype.js@1.3.4/dist/opentype.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/build/three.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/loaders/GLTFLoader.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/controls/OrbitControls.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/DecalGeometry.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/three@0.132.2/examples/js/renderers/SVGRenderer.js"></script>

    <script src="https://unpkg.com/three@0.160.0/examples/js/utils/BufferGeometryUtils.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>



    <!-- Jquery -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"

        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"

        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"

        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"

        crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.activity-indicator/1.0.0/jquery.activity-indicator.min.js"

        integrity="sha512-vIgIa++fkxuAQ95xP3yHzA33Z+iwePLCFeeMcIOqmHhTEAvfBoFap1nswEwU/xE/o4oW0putZ6dbY7JS1emkdQ=="

        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="module" src="js/3dmodel.js?ver=1.0"></script>
<?php    
    function getPantonName($conn4, $zone, $designId, $type='pantonName') {
        // Step 1: Try direct match in colors table
        $sql = "SELECT panton_name,name FROM colors WHERE name = ?";
        $stmt = $conn4->prepare($sql);
        $stmt->bind_param("s", $zone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($type === 'pantonName'){                
                return $row['panton_name'];
            } else {
                    return $row['name']; // direct color name match
            }
        }

        // Step 2: If not found, check design_zones for color_group
        $sql = "SELECT color_group FROM design_zones WHERE design_id = ? AND zone_name = ? OR sub_zone_name = ? LIMIT 1";
        $stmt = $conn4->prepare($sql);
        $stmt->bind_param("iss", $designId, $zone, $zone);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!($row = $result->fetch_assoc())) {
            return $zone; // no zone match
        }

        $colorGroup = $row['color_group'];
        $map = [
            'primary'   => 'primary_color',
            'secondary' => 'secondary_color',
            'tertiary'  => 'tertiary_color'
        ];

        if (!isset($map[$colorGroup])) {
            return $colorGroup;
        }

        // Step 3: Get the actual color value from designs
        $sql = "SELECT {$map[$colorGroup]} AS color FROM designs WHERE id = ? LIMIT 1";
        $stmt = $conn4->prepare($sql);
        $stmt->bind_param("i", $designId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!($row = $result->fetch_assoc())) {
            return $colorGroup;
        }
        $colorName = $row['color'];

        // Step 4: Lookup again in colors to get panton_name
        $sql = "SELECT panton_name,name FROM colors WHERE name = ?";
        $stmt = $conn4->prepare($sql);
        $stmt->bind_param("s", $colorName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($type === 'pantonName'){       
            return $row['panton_name'];
            } else {
                    return $row['name'];
            }
        }

        return $colorName; // no match at all
    }                
?>