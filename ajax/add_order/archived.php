<?php
include('check-session.php');
include('db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;
$full_name = $obj_user->full_name;

$sql_select = "SELECT tbl_order_form.*,tbl_product.prod_name,tbl_product.split_name,COUNT(tbl_order_item.oi_id) AS item_num,SUM(tbl_order_item.qty_top1+tbl_order_item.qty_top2+tbl_order_item.qty_bottom1+tbl_order_item.qty_bottom2) AS qty_sum ";
$sql_select .= " FROM tbl_order_form LEFT JOIN tbl_product ON tbl_order_form.prod_id=tbl_product.prod_id ";
$sql_select .= " LEFT JOIN tbl_order_item ON tbl_order_form.of_id=tbl_order_item.of_id";
$sql_select .= " WHERE tbl_order_form.user_id='".$user_id."' AND tbl_order_form.enable=1 AND tbl_order_form.order_status='archived' GROUP BY tbl_order_form.of_id ORDER BY tbl_order_form.order_date DESC; ";
//echo $sql_select;

$rs_tmp = $conn->query($sql_select);

$a_row_select = array();

$a_order_main_id = array();
while($row_tmp = $rs_tmp->fetch_assoc()){

	$a_row_select[] = $row_tmp;

	if($row_tmp["lkr_order_main_id"]!=""){
		if(!in_array($row_tmp["lkr_order_main_id"], $a_order_main_id)){
			$a_order_main_id[] = $row_tmp["lkr_order_main_id"];
		}
	}
	
}

$design_found = array();
if(sizeof($a_order_main_id)>0){

	$conn3 = new mysqli($serverName,$userName,$userPassword,$dbName3);
	mysqli_set_charset($conn3, "utf8");

	$s_order_main_id = implode(",", $a_order_main_id);
	$sql_chk_design = "SELECT order_main_id,order_main_code,order_design_file FROM order_main WHERE order_main_id IN (".$s_order_main_id."); ";
	$rs_chk_design = $conn3->query($sql_chk_design);

	while($row_chk_design = $rs_chk_design->fetch_assoc()){
		$design_found[($row_chk_design["order_main_id"])] = $row_chk_design;
	}

}

?>
<style type="text/css">
.main_area{
	background-color: #FFF;
	border-radius: 5px;
	min-height: 550px;
}
.tbl_order_content{
	width: 100%;
}
.tbl_order_content thead>tr{
	background-image: linear-gradient(to right, #DDD , #999);

}
.tbl_order_content th{
	/*border:1px #99F solid;*/
	text-align: center;
	padding: 10px;
	font-size: 14px;
}
.tbl_order_content td{
	/*border:1px #99F solid;*/
	text-align: center;
	padding: 10px;
	border-bottom: 1px solid #EEE;
	font-size: 14px;
}

#d_chat_panel_outter{
	background-color: #333;
	width: 100%;
	max-height: 350px;
	min-height: 350px;
	overflow-y: scroll;
	border:1px solid #555;
}
#d_chat_panel{
	
	margin: auto;
}


.msg_box_answer{
	width: 100%;
	
}
.msg_box_answer .meta_info{
	float: right;
	font-size: 11px;
	color: #FFF;
	margin: 10px 3px -5px 0px; 
}
.msg_box_answer .msg_info{
	float: right;
	font-size: 14px;
	color: #000;
	background-color: #AFA;
	border: 2px solid #9D9;
	border-radius: 20px;
	margin: 0px 3px 0px 0px; 
	
    max-width: 600px;
}

.msg_box_question{
	width: 100%;
}
.msg_box_question .meta_info{
	float: left;
	font-size: 11px;
	color: #FFF;
	margin: 10px 3px -5px 0px; 

}
.msg_box_question .msg_info{
	float: left;
	font-size: 14px;
	color: #000;
	background-color: #FFA;
	border: 2px solid #DD9;
	border-radius: 20px;
	margin: 0px 3px 0px 0px; 

    max-width: 600px;
}

.msg_info pre{
	padding: 5px;
	margin: 0px;
}

.msg_info ::-webkit-scrollbar {
  height: 10px;
}

/* Track */
.msg_info ::-webkit-scrollbar-track {
  box-shadow: inset 0 0 5px grey; 
  border-radius: 20px;
  margin:0px 10px;
}
 
/* Handle */
.msg_info ::-webkit-scrollbar-thumb {
  background: #777; 
  border-radius: 20px;
}

/* Handle on hover */
.msg_info ::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

.noti_msg{
	background-color: #F00;
	border: solid 1px #DDD;
	border-radius: 20px;
	color: #FFF;
	font-size: 11px;
	font-weight:bold;
	padding: 3px;
	height: 18px;
	min-width: 18px;
	float:right;
	margin-top: -2px;
	margin-left: 2px;
	line-height: 1.0;
	vertical-align: middle;
}

.q_read{
	float: left;
	margin-top:20px;
	color: #FFF;
	font-size: 10px;
}

.showing_status_new{
	font-size: 12px;
	font-weight: bold;
	background-color: #944;
	border: 2px solid #D77;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}

.showing_status_processing{
	font-size: 12px;
	font-weight: bold;
	background-color: #974;
	border: 2px solid #D87;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}

.showing_status_producing{
	font-size: 12px;
	font-weight: bold;
	background-color: #479;
	border: 2px solid #78D;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}

.showing_status_partially_shipped{
	font-size: 12px;
	font-weight: bold;
	background-color: #559;
	border: 2px solid #88E;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}

.showing_status_shipped{
	font-size: 12px;
	font-weight: bold;
	background-color: #339;
	border: 2px solid #55E;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}

.showing_status_delivered{
	font-size: 12px;
	font-weight: bold;
	background-color: #339;
	border: 2px solid #55E;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}

.showing_status_received{
	font-size: 12px;
	font-weight: bold;
	background-color: #393;
	border: 2px solid #5E5;
	border-radius: 10px;
	color: #FFF;
	padding: 2px;
}
.bg_name_info{
	background-color: #FFE;
}
.bg_number_info{
	background-color: #EFF;
}
.bg_status_info{
	background-color: #FEF;
}
.tracking_link{
	font-size: 20px;
	color: #33F;
	cursor: pointer;
}
.tracking_link:hover{
	color: #00A;
}

.tbl_show_tracking{
	width: 100%;
}
.tbl_show_tracking th{
	background-color: #FC0;
	color: #FFF;
	font-weight: bold;
	border:#FFF solid 2px;
	text-align: center;
}
.tbl_show_tracking td{

	background-color: #DDD;
	color: #000;
	border:#FFF solid 2px;
	text-align: center;
}
</style>
<div class="container-fluid" >
	<div class="row main_area">
		<div class="col-12 text-center table-responsive" style="padding:0px 0px;">
			
			<table class="tbl_order_content" style="min-width:1200px; width: 100%;">
				<thead>
					<tr>
						<th>#</th><th>Order name</th><th>Design</th><th>Customer PO</th><th>Order Date</th><th>Items</th><th>Total QTY</th><th>Order Form</th><th>Re-Order</th><th>Tracking</th>
					</tr>
				</thead>
				<?php
				$row_count = 1;
				$list_of_id = "";
				//while($row_order = $rs_select->fetch_assoc()){
				for($i=0;$i<sizeof($a_row_select);$i++){

					$row_order = $a_row_select[$i];

					if($list_of_id!=""){
						$list_of_id .= ",";
					}
					$list_of_id .= $row_order["of_id"];
				?>
				<tr id="tr_show_row<?php echo $row_order["of_id"]; ?>">
					<td class="bg_name_info"><?php echo $row_count; ?></td>
					<td class="bg_name_info" style="text-align: left;"><img src="assets/images/icons/<?php echo $row_order["split_name"]; ?>.png" style="width: 32px; margin-right: 5px;"><?php echo $row_order["form_name"]; ?></td>
					
					<td class="bg_name_info">
						<?php
						if( isset($design_found[($row_order["re_order_id"])]) && $design_found[($row_order["re_order_id"])]["order_design_file"]!="" ){
							$row_design = $design_found[($row_order["re_order_id"])];

							echo '<i class="fa fa-image" style="color:#933; font-size:18px; cursor:pointer;" onclick="return showDesign(\''.$row_design["order_main_code"].'\',\''.$row_design["order_design_file"].'\');" data-toggle="modal" data-target="#showDesignModal"></i>';
						}else if( isset($design_found[($row_order["lkr_order_main_id"])]) && $design_found[($row_order["lkr_order_main_id"])]["order_design_file"]!="" ){
							$row_design = $design_found[($row_order["lkr_order_main_id"])];

							echo '<i class="fa fa-image" style="color:#933; font-size:18px; cursor:pointer;" onclick="return showDesign(\''.$row_design["order_main_code"].'\',\''.$row_design["order_design_file"].'\');" data-toggle="modal" data-target="#showDesignModal"></i>';
						}else{
							echo '<i class="fa fa-image" style="color:#DDD; font-size:18px;"></i>';
						}
						?>
					</td>
					<td class="bg_name_info"><?php echo $row_order["customer_po"]; ?></td>
					<td class="bg_name_info"><?php echo date("F d, Y",strtotime($row_order["order_date"])); ?></td>
					<td class="bg_number_info"><?php echo $row_order["item_num"]; ?></td>
					<td class="bg_number_info"><?php echo $row_order["qty_sum"]; ?></td>
					<td class="bg_status_info">
						
						<button style="width:100px; font-size: 14px;" type="button" class="btn btn-primary" onclick="return viewOrderForm(<?php echo $row_order["of_id"]; ?>);" data-toggle="modal" data-target="#orderFormModal"><i class="fa fa-file-text-o" aria-hidden="true"></i>View
							<span id="sp_noti<?php echo $row_order["of_id"]; ?>" class="noti_msg" style="display:none;"></span>
						</button>
						
					</td>
					<td class="bg_status_info">
						
						<div class="tracking_link" onclick="return reOrder(<?php echo $row_order["of_id"]; ?>);">
							<i class="fa fa-retweet"></i>
						</div>
						
					</td>
					<td class="bg_status_info">
						<?php
						if($row_order["ship_status"]=="yes"){

						?>
						<div class="tracking_link" data-toggle="modal" data-target="#trackingLinkModal" onclick="return getTrackingList(<?php echo $row_order["lkr_order_main_id"]; ?>);">
							<i class="fa fa-truck"></i>
						</div>
						<?php
						}
						?>
					</td>
					
				</tr>
				<?php
					$row_count++;
				}
				?>
			</table>
			<input type="hidden" id="s_list_of_id" value="<?php echo $list_of_id; ?>">
		</div>
	</div>
</div>

<!-- Modal -->
<div id="orderFormModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style="margin-top:15px;">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header" style="padding:15px 26px 1px 26px;">
        <button type="button" class="close" style="float: right;" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body" style="padding: 0px 26px;">
        <div id="order_form_content" style="border: 1px solid #000;" class="table-responsive">
        </div>
        <div class="text-center" style="padding: 5px;">
        	<button id="btn_mini" class="btn btn-info" onclick="$('#order_form_content').css('max-height','220px').css('overflow-y','scroll'); $(this).hide(); $('#btn_maxi').show();">Minimize</button>
        	<button id="btn_maxi" style="display: none;" class="btn btn-info" onclick="$('#order_form_content').css('max-height','').css('overflow-y',''); $(this).hide(); $('#btn_mini').show();">Maximize</button>
        	<input type="hidden" id="download_of_id" >
        	<input type="hidden" id="sending_msg" value="no">
        </div>
	   	<div id="d_chat_panel_outter" > 
	        <div id="d_chat_panel" class="row" >
	        	
	        </div>
	        <span id="sp_bottom"></span>
	    </div>
        <input type="hidden" id="max_chat_id" value="0">
      </div>
      <div class="modal-footer">
       
      </div>
    </div>

  </div>
</div>
<input type="hidden" id="modal_is_close" value="yes">
<input type="hidden" id="chk_msg_read" value="">


<!-- Modal -->
<div id="trackingLinkModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header" style="padding:15px 26px 1px 26px;">
      	<h5>Tracking</h5>
        <button type="button" class="close" style="float: right;" data-dismiss="modal">&times;</button>
        
      </div>
      <div class="modal-body" style="padding: 0px 26px;" id="show_tracking_list">
        
      </div>
      <div class="modal-footer">
        
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="showDesignModal" class="modal draggable fade" role="dialog">
  <div class="modal-dialog modal-lg" >

    <!-- Modal content-->
    <div class="modal-content" >
      <div class="modal-header" style="padding:15px 26px 1px 26px; cursor: move;">
        <button type="button" class="close" style="float: right;" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" style="padding: 0px 26px;" id="show_tracking_list">
        <iframe id="show_design_frame" src="" type=frame&vlink=xx&link=xx&css=xxx&bg=xx&bgcolor=xx marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scorlling=yes width=100% height=600></iframe>
      </div>
      <div class="modal-footer">
        
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">

function viewOrderForm(of_id){

	$('#order_form_content').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Loading...');

	$.ajax({  
        type: "POST",  
        dataType: "html", 
        url:"ajax/new_order/show_order_form.php" ,
        data: {
            "of_id": of_id
        } ,
        success: function(resp){ 
            
            $('#download_of_id').val(of_id);
			$('#order_form_content').html(resp);

			$('#modal_is_close').val("no");

			getMessage(of_id);
                
        }
    });

}

function getMessage(of_id){

	$.ajax({  
        type: "POST",  
        dataType: "json", 
        url:"ajax/new_order/get_chat_msg.php" ,
        data: {
            "of_id": of_id
        } ,
        success: function(resp){ 
            
            if(resp.result=="success"){

            	$('#d_chat_panel').html(window.atob(resp.msg_box));
            	
            	setTimeout(function() {

            		$('#d_chat_panel_outter').animate({ scrollTop: $('#d_chat_panel').height() }, 1000);

            	}, 1500);
            }else{
                
                $('#d_chat_panel').html("");

        	}

        }
    });

}

function getTrackingList(lkr_order_main_id){

	$('#show_tracking_list').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Loading...');

	$.ajax({  
    	type: "POST",  
        dataType: "json", 
        url:"ajax/new_order/show_tracking_list.php" ,
        data: {
            "lkr_order_main_id": lkr_order_main_id
        } ,
        success: function(resp){ 
            
            if(resp.result=="success"){

            	$('#show_tracking_list').html(resp.inner_content);
            	
            }else{
                
                alert(resp.msg);

        	}

        }
    });
}

function showDesign(order_main_code,order_design_file){

	var inner_src = '';

	if(order_design_file!=""){
		inner_src = 'https://jogsports.com/lockerroom/files/'+order_main_code+'/'+order_design_file;
	}

	$('#show_design_frame').attr("src",inner_src);
}

function reOrder(of_id){

	$.ajax({  
    	type: "POST",  
        dataType: "json", 
        url:"ajax/manage_order/re_order.php" ,
        data: {
            "of_id": of_id
        } ,
        success: function(resp){ 
            
            if(resp.result=="success"){

            	window.location.href = '?vp=<?php echo base64_encode('manage_order'); ?>';
            	
            }else{
                
                alert(resp.msg);

        	}

        }
    });

}
</script>