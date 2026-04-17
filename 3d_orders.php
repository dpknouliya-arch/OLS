<?php
include('check-session.php');
include('db.php');
include 'encryption_helper.php';
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

$sql_select = "SELECT draft_id,order_date,req_due_date,customer_po,project_name,COUNT(prod_id) AS num_prod,COUNT(re_order_id) AS num_re_order FROM tbl_draft_of WHERE user_id='" . $user_id . "' AND enable=1 GROUP BY draft_id ORDER BY draft_id ASC;";
$rs_select = $conn->query($sql_select);

$a_row_draft = array();

while ($row_select = $rs_select->fetch_assoc()) {
  $a_row_draft[($row_select["draft_id"])]["row_normal"] = $row_select;
  $a_row_draft[($row_select["draft_id"])]["row_file"] = array();
}

?>


<style>
  .allOrders .card-body .orderName {
    font-size: 14px;
    margin: 0;
    font-weight: 500;
    padding: 5px 0;
  }

  .allOrders .card-body {
    padding: 5px;
  }

  .defaultHeader {
    height: 100%;
  }
</style>

<div class=" manageOrder h-100">
  <div class="innerMainContent h-100">
    <div class="PageHeader">
      <h2>3D Orders</h2>
      <p> Your 3D Modal Orders</p>
    </div>

    <div class="boxes">
      <div class="formTitle d-flex align-items-center flex-row">
        <h6 class="subHeading  ">3D Modal</h6>
      </div>
    </div>
    <div class="allOrders">
      <div class="row">
        <?php
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
                    where o.user_id = $user_id
                    ORDER BY o.added_date DESC
                    ;
                    ";

        $result = $conn4->query($sql);
        if ($result !== false && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
        ?>
            <!-- <div class="offset-md-1 col-xl-3 col-md-4 mb-xl-0 mb-4 shadow offset-md-1" style="margin: 10px;">
                  <div class="">
                    <div class="bg-transparent">
                      <a class="d-block">
                        <img src="https://jogsports.com/jogdigital/admin/uploads/designs/images/<?= $row['image']; ?>" alt="img-blur-shadow" class="img-fluid">
                      </a>
                    </div>
                    <div class="card-body p-3 text-center">                                                                 
                        <h5 class="mb-4">
                          <?= $row['name']; ?>
                        </h5>                                            
                    </div>
                  </div>
                </div> -->
            <div class="col-md-2 mb-xl-0 mb-4 mt-4">
              <div class="card card-blog card-plain shadow-xl border-radius-xl">
                <div class="card-header p-0   tetx-center bg-transparent">
                  <a class="d-block">
                    <img src="https://jogsports.com/jogdigital/admin/uploads/designs/images/<?= $row['image']; ?>" alt="img-blur-shadow" class="img-fluid border-radius-lg" style="height:180px;width: 100%;object-fit: contain;">
                  </a>
                </div>
                <div class="card-body  text-center">
                  <h5 class="orderName">
                    <?= $row['name']; ?>
                  </h5>
                  <div class="align-items-center Assign_user">
                        <?php
                        $order_id = customEncode($row['order_id']);
                        ?>                        
                        <a href="?vp=<?php echo base64_encode('3d_order_details'); ?>&order_id=<?php echo $order_id; ?>" target="_blank" class="btn iconBTn cursor" style="border: 1px solid #DDDDDD;box-shadow: 0px 0px 6px 0px #0000001A;padding: 0 15px;border-radius: 8px;">
                            View Draft  <figure class="iconImg m-0"><img src="images/vector/loginBTn.png" alt=""></figure>
                        </a>                        
                      </div>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo "0 results";
        }
        $conn->close();
        ?>
      </div>
    </div>
  </div>
</div>