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

$sql_select = "SELECT * FROM tbl_address WHERE user_id='" . $user_id . "' AND enable=1 AND (is_billing_addr=1 OR is_deliver_addr=1) AND contact_name<>'' AND address<>'' AND city<>'' AND contact_name<>'' AND country<>'' AND zip_code<>'' AND tel<>'' AND email<>'' ORDER BY is_billing_addr ASC;";
$rs_select = $conn->query($sql_select);
$num_row = $rs_select->num_rows;

$a_data = array();

if ($num_row == 1) {
    $a_data[0] = $rs_select->fetch_assoc();
    $a_data[1] = $a_data[0];
} else {
    while ($row_select = $rs_select->fetch_assoc()) {
        if ($row_select["is_billing_addr"] == "1") {
            $a_data[0] = $row_select;
        } else if ($row_select["is_deliver_addr"] == "1") {
            $a_data[1] = $row_select;
        }
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

<section class="generatePO">
    <div class="container">
        <div class="row" >
            <div class="  pageHeader">
                <h2>Checkout</h2>             
            </div>
            <div >            
                <ul class="nav justify-content-center" id="myTab" role="tablist">                
                    <li class="nav-item" role="presentation">
                        1. Order Details                        
                    </li>
                    <li class="nav-item" role="presentation">                        
                        2. Checkout                        
                    </li>
                </ul>
            </div>            
            <div class="col-md-6" id="pritn_details">
                <div class="boxes">
                    <div class="formTitle d-flex align-items-center flex-row">
                        <div class="orderBadge">
                            Order Date :<?php echo date("m/d/Y"); ?>
                            <input type="hidden" name="order_date" id="order_date" value="<?php echo date("Y-m-d"); ?>">
                        </div>
                    </div>
                    <fieldset class="singleFrom">
                        <div class="form-group  ">
                            <label for="">Project Name</label>
                            <input type="text" name="project_name" id="project_name" style="width: 100%;" value="">
                        </div>
                        <div class="form-group  ">
                            <label for="">Customer PO</label>
                            <input type="text" name="customer_po" id="customer_po" style="width: 100%;" value="">
                        </div>

                        <div class="form-group  ">
                            <label for="">Game / Event date</label>
                            <input type="date" name="game_event_date" id="game_event_date" style="width: 100%;" value="">
                        </div>

                        <div class="form-group">
                            <label for="">Request due date</label>

                            <input type="date" name="req_due_date" id="req_due_date" style="width: 100%;" value="">
                        </div>
                        <div class="form-group">
                            <label for="">Payment Option</label>
                            <div class="styled-select">
                                <select style="width: 100%; font-size: 14px;" name="payment_opt" id="payment_opt">
                                    <option value="Net 30">Net 30</option>
                                    <option value="Net 15">Net 15</option>
                                    <option value="Net 7">Net 7</option>
                                    <option value="Due on receipt">Due on receipt</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Sales rep</label>
                            <div class="styled-select">
                                <select style="width: 100%; font-size: 14px;" name="sales_rep" id="sales_rep">
                                    <?php
                                    $sql_new = "SELECT * FROM employee WHERE employee_position_id='5'";
                                    $emps = $conn3->query($sql_new);
                                    $num_rows = $emps->num_rows;
                                    if ($num_rows > 0) {
                                        while ($row_selection = $emps->fetch_assoc()) {
                                    ?>
                                            <option value="<?= $row_selection['employee_id'] ?>" <?php if ($a_data[0]["sales_rep_id"] == $row_selection['employee_id']) {
                                                                                                        echo "selected";
                                                                                                    } ?>>
                                                <?= $row_selection['employee_name'] ?>
                                            </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group position-relative ">
                            <label for="">Reorder? Type the EX
                                here</label>
                            <input type="text" name="reorder_num" id="reorder_num" style="width: 100%;" value="">                            
                        </div>


                    </fieldset>
                </div>
            </div>
            <div class="col-md-6 m-auto"> 
                <div class="card">
                    <div class="row">
                        <div class="col-md-3 align-items-center m-3">   
                            <div style="background:#dfdfdf; width:150px">
                                <img src="http://localhost:9090/jog_3d/admin/uploads/designs/images/<?php echo $order_design_data[0]['image']; ?>" alt="" class="w-100" >                            
                            </div>                          
                        </div>
                        <div class="col-md-5 align-items-right" >
                            style: <?= $order_design_data[0]['name']; ?><br>
                            price: $<?= $order_design_data[0]['price']; ?>
                            Jersey Type: <?= $order_design_data[0]['modal_type']; ?>
                        </div>
                    </div>
                </div>

                <div class="boxes card  mt-2 p-3">
                    <h5>Billing Information <a href="?vp=<?= base64_encode("billinginfo") ?>" class="sm-Btn">Edit</a></h5>
                    <p> Company : <span><?php echo $a_data[0]["addr_name"]; ?></span> </p>    
                    <p> Address : <span><?php echo $a_data[0]["address"]; ?></span> </p>    
                    <p> Email : <span><?php echo $a_data[0]["email"]; ?></span> </p>    
                    <p> Tel : <span><?php echo $a_data[0]["tel"]; ?></span> </p>    
                    <p> TAX ID : <span><?php echo $a_data[0]["tax_id"]; ?></span> </p>    
                </div> 
                <div class="boxes card mt-2 p-3 ">
                    <h5>Delivery Information <a href="?vp=<?= base64_encode("billinginfo") ?>" class="sm-Btn">Edit</a></h5>
                    <p> Company : <span><?php echo $a_data[1]["addr_name"]; ?></span> </p>    
                    <p> Address : <span><?php echo $a_data[1]["address"]; ?></span> </p>    
                    <p> Email : <span><?php echo $a_data[1]["email"]; ?></span> </p>    
                    <p> Tel : <span><?php echo $a_data[1]["tel"]; ?></span> </p>    
                    <p> TAX ID : <span><?php echo $a_data[1]["tax_id"]; ?></span> </p>    
                </div>                                             
            </div>            
        </div>
        <div class="row">
            <div class="col-4">
                <button class="btn btn-primary m-auto mt-3 w-20" id="printBtn">create order</button>
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
<script>
    showBillingInfo();
     function showBillingInfo() {

        $('#billing_addr_content').html('<i class="fa fa-cog fa-spin fa-1x fa-fw"></i> Loding...');

        $.ajax({
            type: "POST",
            dataType: "html",
            url: "ajax/billing/show_billing_info.php",
            success: function(resp) {

                $('#billing_addr_content').html(resp);

            }
        });

    }
</script>
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