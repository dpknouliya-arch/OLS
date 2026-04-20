<?php

include('check-session.php');

include('db.php');



$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;

$customer_id = $obj_user->customer_id;



$conn3 = new mysqli($serverName, $userName, $userPassword, $dbName3);

mysqli_set_charset($conn3, "utf8");



//---Select Design from LR

$sql = "SELECT 
            order_head.order_id,
            order_head.order_name,
            employee.employee_name,
            order_head.order_status,
            order_head.order_create_date
        FROM order_head
        LEFT JOIN employee 
            ON order_head.order_sale_id = employee.employee_id
        WHERE order_head.customer_id = ?
        ORDER BY order_head.order_create_date DESC";

$stmt = $conn3->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn3->error);
}

$stmt->bind_param("i", $customer_id); 
$stmt->execute();
$rs_select = $stmt->get_result();  


$a_row = array();
$a_draft_num = array();
$a_order_id_list = array();

while ($row_select = $rs_select->fetch_assoc()) {


    $a_row[] = $row_select;

    if (!isset($a_draft[($row_select["order_id"])])) {

        $a_draft_num[($row_select["order_id"])] = 0;

    }



    if (!in_array($row_select["order_id"], $a_order_id_list)) {

        $a_order_id_list[] = $row_select["order_id"];

    }

}



if (sizeof($a_order_id_list) > 0) {



    $s_order_id_list = implode(",", $a_order_id_list);



    //---Select draft from OLS

    $sql_select2 = "SELECT order_id,COUNT(*) AS draft_num FROM tbl_design_draft WHERE customer_id=" . $customer_id . " AND enable=1 AND order_id IN (" . $s_order_id_list . ") GROUP BY order_id";

    // echo $sql_select2 ; 
    
    $rs_select2 = $conn->query($sql_select2);

    while ($row_select2 = $rs_select2->fetch_assoc()) {



        $a_draft_num[($row_select2["order_id"])] = $row_select2["draft_num"];

    }

}

?>


<div class="design_approvalPage">
    <div class="innerMainContent">
        <div class="pageHeader">
            <h2>Design Approval</h2>
            <p>This is where your Design Info will go.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-striped" id="d_content_zone">
                <thead class="themebg">
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Design</th>
                        <th class="text-center">Review</th>
                        <th class="text-center">Sales rep</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Draft</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    $count_row = 0;
                    foreach ($a_row as $key => $a_data) {
                        $count_row++;
                        $show_status = "";
                        if (intval($a_data["order_status"]) >= 6) {
                            $show_status = "Design Completed";
                        } else {
                            switch ($a_data["order_status"]) {
                                case '2':
                                    $show_status = "Wait for Review";
                                    break;
                                case '3':
                                case '4':
                                    $show_status = "Updating Design";
                                    break;
                                default:
                                    $show_status = "In Process";
                            }
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?php echo $count_row; ?></td>
                            <td class="text-center">
                                <?php echo $a_data["order_name"]; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if ($a_draft_num[($a_data["order_id"])] != "0") {
                                    $use_bg_color = "#AAA";
                                    if ($a_data["order_status"] == '2') {
                                        $use_bg_color = "#55F";
                                    }
                                ?>
                                    <div style="cursor:pointer; font-size:20px; color: <?php echo $use_bg_color; ?>;" onclick="return showDesignZone(<?php echo $a_data["order_id"]; ?>);"><i class="fa fa-image"></i></div>
                                <?php
                                }
                                ?>
                            </td>
                            <td class="text-center"><?php echo $a_data["employee_name"]; ?></td>
                            <td class="text-center">
                                <?php echo $show_status; ?>
                            </td>
                            <td class="text-center">
                                <?php echo $a_draft_num[($a_data["order_id"])]; ?>
                            </td>
                            <td class="text-center">
                                <?php echo date("M d, Y", strtotime($a_data["order_create_date"])); ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
            <div id="d_design_zone" class="col-12" style="padding:2px;">
		
            </div>
        </div>
    </div>
</div>





	<div id="approvalModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" >
						<h4>Approve Design</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div>
						<div class="form-group">
							<label for="exampleInputEmail1" style="color:white;">DESIGN NAME</label>
							<input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Input Design Option">
						</div>
						<div class="form-group">
							<label for="exampleFormControlTextarea1" style="color:white;">Notes(If Any)</label>
							<textarea name="approval_textarea" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
						</div>
						<button type="button" class="btn btn-warning approval_btn" onclick="approveDraft()">Approve Design</button>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div id="rejectModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
						<h4>Reject Design</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					
				</div>
				<div class="modal-body">
					<form id="reject_form">
						<div class="form-group">
							<label for="exampleInputEmail2" style="color:white;">DESIGN NAME</label>
							<input type="text" name="design_name" class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp" placeholder="Input Design Option">
							<input type="hidden" name="dd_id" id="dd_id_reject">
						</div>
						<div class="form-group">
							<label for="exampleFormControlTextarea2" style="color:white;">Reject Reason(Important)</label>
							<textarea name="reject_textarea" class="form-control" id="exampleFormControlTextarea2" rows="3"></textarea>
						</div>
						<button type="submit" class="btn btn-danger">Reject Design</button>
					</form>
				</div>
			</div>

		</div>
	</div>


<script type="text/javascript">
function showDesignZone(order_id){
	//$('#d_content_zone').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Loading...');
	$('#d_design_zone').show();
	$('#d_design_zone').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw" style="color:white;"></i> <span style="color:white">Loading...</span>');
	$('#d_content_zone').hide(500);

	$.ajax({  
        type: "POST",  
        dataType: "html", 
        url:"ajax/design_approval/show_design.php" ,
        data: {
            "order_id": order_id
        } ,
        success: function(resp){ 
            
            //setTimeout(function() {
            	$('#d_design_zone').html(resp);
				
			//}, 500);
        }
    });

	
}

function showContentZone(){

	$('#d_design_zone').hide(500);
	setTimeout(function() {
    	$('#d_design_zone').html('');
		$('#d_content_zone').fadeIn(500);
	}, 500);

}

function showTab(draft_no,dd_id=''){

	$('.tab_btn').removeClass('active_tab');
	$('#tab_id'+draft_no).addClass('active_tab');

	$('.d_show_design').hide();
	$('#d_show_design'+draft_no).show();

	setTimeout(function(){

		setReadComment(dd_id);
	}, 3000);

}




	
$(document).on('click', '.approval_modal', function() {
var draft_id = $(this).attr('draft_id');
$('.approval_btn').attr('dd_id', draft_id);
$('#approvalModal').modal('show');
})

$(document).on('click', '.reject_modal', function() {
var draft_id = $(this).attr('draft_id');
$('#dd_id_reject').val(draft_id);
$('#rejectModal').modal('show');
})




		function approveDraft() {

			var dd_id = $('.approval_btn').attr("dd_id");
			var approval_note = CKEDITOR.instances.exampleFormControlTextarea1.getData();
			var design_note = $('#exampleInputEmail1').val();
			if (design_note.length == 0) {
				alert("Design Option can't be left blank");
				return;
			}

			if (confirm("Confirm APPROVE?")) {

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "ajax/design_approval/approve_draft.php",
					data: {
						"dd_id": dd_id,
						"approval_note": approval_note,
						"design_note": design_note
					},
					success: function(resp) {

						if (resp.result == "success") {
							var inner_approval = '<div class="btn_panel"><a type="button" href="#add_comment_btn" class="btn btn-primary add_cmt_btn" style="float:left;">Add Comment</a>';
							inner_approval += '<center><div class="approve_status">APPROVED</div></center>';
							inner_approval += '<div style="text-align: left; color: #F00;">* Note: Available to comment only in the latest Draft.</div>';
							inner_approval += '</div>';

							$('#approval_zone').html(inner_approval);
							$('#approvalModal').modal('hide');
						} else {
							alert(resp.msg);
						}

					}
				});

			}

		}

		$(document).on('submit', '#reject_form', function(e) {
			e.preventDefault();
			var form = $(this);
			var formdata = new FormData(form[0]);
			if (confirm("Confirm REJECT?")) {

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "ajax/design_approval/reject_draft.php",
					data: formdata,
					contentType: false,
					processData: false,
					success: function(resp) {

						if (resp.result == "success") {
							var inner_approval = '<div class="btn_panel"><a type="button" href="#add_comment_btn" class="btn btn-primary add_cmt_btn" style="float:left;">Add Comment</a>';
							inner_approval += '<center><div class="reject_status">REJECTED</div></center>';
							inner_approval += '<div style="text-align: left; color: #F00;">* Note: Available to comment only in the latest Draft.</div>';
							inner_approval += '</div>';

							$('#approval_zone').html(inner_approval);
							$('#rejectModal').modal('hide');
						} else {
							alert(resp.msg);
						}

					}
				});

			}

		})

		function rejectDraft(dd_id) {

			if (confirm("Confirm REJECT?")) {

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "ajax/design_approval/reject_draft.php",
					data: {
						"dd_id": dd_id
					},
					success: function(resp) {

						if (resp.result == "success") {
							var inner_approval = '<div class="btn_panel"><a type="button" href="#add_comment_btn" class="btn btn-primary add_cmt_btn" style="float:left;">Add Comment</a>';
							inner_approval += '<center><div class="reject_status">REJECTED</div></center>';
							inner_approval += '<div style="text-align: left; color: #F00;">* Note: Available to comment only in the latest Draft.</div>';
							inner_approval += '</div>';

							$('#approval_zone').html(inner_approval);
						} else {
							alert(resp.msg);
						}

					}
				});

			}

		}



</script>