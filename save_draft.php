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
    .updated_dateTxt {
      color: #FFF;
      background: green;
      padding: 2px;
      border-radius: 26px;
      padding: 2px 5px;
      font-size: 13px !important;
      text-align: center;
    }
</style>
<div class=" manageOrder">
	<div class="innerMainContent">
		<div class="PageHeader">
			<h2>3D Save Draft</h2>
			<p>Review, Edit, and Submit Your 3D Modal </p>
		</div>

		<div class="boxes">
			<div class="formTitle d-flex align-items-center flex-row">
				<h6 class="subHeading mb-4">Drafts</h6>
			</div>
		</div>
		<div class="allOrders">
			<div class="row">   
                <?php                
                $sql = "
                  SELECT 
                      d_d.id AS draft_id,
                      d_d.design_id,
                      d_d.text_decals,
                      d_d.image_decals,
                      d_d.color_data,
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
                  FROM 
                      design_drafts AS d_d
                  INNER JOIN 
                      designs AS d 
                  ON 
                      d.id = d_d.design_id
                  WHERE
                      d_d.is_used = 0 AND 
                      d_d.user_id = $user_id";

                $result = $conn4->query($sql);
                if ($result !== false && $result->num_rows > 0) {                
                    while($row = $result->fetch_assoc()) {
                ?>                                        
                <div class="col-md-3 mb-xl-0 mb-4 mt-4">
                  <div class="card card-blog card-plain shadow-xl border-radius-xl">
                    <div class="card-header p-0 m-2 tetx-center bg-transparent">
                      <a class="d-block">
                        <img src="https://jogsports.com/jogdigital/admin/uploads/designs/images/<?=$row['image'];?>" alt="img-blur-shadow" class="img-fluid border-radius-lg" style="height:250px;width: 100%;object-fit: contain;">
                      </a>
                    </div>
                    <div class="card-body p-3 text-center">                                                                 
                    <h5 class="mb-2">
                        <?=$row['name'];?>
                    </h5>   
                    <div class="align-items-center mb-2">                                    
                        <span class="updated_dateTxt"><?php
                        $ts = strtotime($row['updated_date']);
                        echo date("Y-m-d H:i", $ts);
                        ?>
                        </span>
                    </div>

                      <div class="align-items-center Assign_user">
                        <?php 
                        $encodedDraftId = customEncode($row['draft_id']); 
                        $esubcategory_id = customEncode($row['subcategory_id']); 
                        $style_id = $row['style_id']; 
                        $ecategory_id = customEncode(1); 
                        ?>                        
                        <a href="https://jogsports.com/jogdigital/customize.php?cat=<?=urlencode($ecategory_id);?>&subcat=<?=urlencode($esubcategory_id);?>&style=<?=urlencode($style_id)?>&draft=<?=urlencode($encodedDraftId);?>" target="_blank" class="btn iconBTn cursor" style="border: 1px solid #DDDDDD;box-shadow: 0px 0px 6px 0px #0000001A;padding: 0 15px;border-radius: 8px;">
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