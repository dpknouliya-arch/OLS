<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
	echo '<center>Error: Login session gone.</center>';
	exit();
}

if (!isset($_POST["order_id"])) {
	echo '<center>Error: Invalid request.</center>';
	exit();
}

include('../../db.php');

$order_id = $_POST["order_id"];

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;
$customer_id = $obj_user->customer_id;


$conn3 = new mysqli($serverName, $userName, $userPassword, $dbName3);
mysqli_set_charset($conn3, "utf8");

//---Select from LKR
$sql_status = "SELECT order_status ";
$sql_status .= "FROM order_head ";
$sql_status .= "WHERE order_id=" . $order_id . " AND customer_id ='$customer_id'";

$rs_status = $conn3->query($sql_status);
$row_status = $rs_status->fetch_assoc();
$order_status = $row_status["order_status"];

$sql_d_draft = "SELECT * FROM tbl_design_draft WHERE order_id='" . $order_id . "' AND customer_id ='$customer_id' AND enable=1 ORDER BY dd_id DESC;";
$rs_d_draft = $conn->query($sql_d_draft);

$num_draft = $rs_d_draft->num_rows;

$first_dd_id = "";

if ($num_draft == 0) {
	echo '<center>Data not found.</center>';
} else {

	$use_path = "https://ols-test.jog-joinourgame.com/";
	if ($_SERVER["SERVER_NAME"] == "online.jog-joinourgame.com") {
		$use_path = "https://locker.jog-joinourgame.com";
	}elseif($_SERVER['SERVER_NAME'] == "ols-test.jog-joinourgame.com"){
		$use_path = "https://locker-test.jog-joinourgame.com";
	}

	$a_draft = array();
	$draft_no = $num_draft;
	while ($row_d_draft = $rs_d_draft->fetch_assoc()) {

		$row_d_draft["draft_no"] = $draft_no;
		$a_draft[] = $row_d_draft;

		$draft_no--;
	}
?>

	<style>
		#d_show_tab {
			padding: 10px 0;
			display: flex;
			justify-content: space-between;
		}

		.table-responsive {
			overflow-y: auto;
			scrollbar-width: none;

		}

		.themeBtn,
		.themeBtn2grey {
			padding: 5px 15px;
			font-size: 13px;
		}

		.themeBtn {
			background: none;
			color: #323639;
			border: 1px solid #DDDDDD;
			font-weight: 500;
		}

		.themeBtn.active_tab {
			border: 1px solid #2f50a35c;
			color: #2F50A3;
			font-weight: 7000;
			background: #2f50a308;
		}

		.pageHeader {
			padding: 20px 20px 10px 0;
		}

		.statusAlertDate {
			background: #eee;
			padding: 5px 10px;
			font-size: 14px;
			font-weight: 500;
			border-radius: 4px;
		}

		.reject_status {
			background: #FFE1E2;
			padding: 5px 10px;
			font-size: 14px;
			border-radius: 4px;
			font-weight: 500;
			color: #FF4B50;
			align-content: center;
		}

		.approve_status {
			background: #19875426;
			padding: 5px 10px;
			font-size: 14px;
			border-radius: 4px;
			font-weight: 500;
			color: #198754;
			align-content: center;
		}

		.status_box {
			display: flex;
			align-items: center;
		}
	</style>
	<div id="d_show_tab">

		<div>
			<button title="Back" class="goBackBtn themeBtn2grey  iconBTn" type="button" onclick="return showContentZone();">
				<figure class="m-0"><img src="images/vector/previousBtn.png" alt=""></figure> Go Back
			</button>
			<?php

			foreach ($a_draft as $key => $draft_row) {
				if ($first_dd_id == "") {
					$first_dd_id = $draft_row["dd_id"];
				}
			?>
				<button type="button" id="tab_id<?php echo $draft_row["draft_no"]; ?>" class="themeBtn  iconBTn tab_btn <?php if ($draft_row["draft_no"] == $num_draft) {
																															echo 'active_tab';
																														} ?>" onclick="return showTab(<?php echo $draft_row["draft_no"]; ?>,<?php echo $draft_row["dd_id"]; ?>);">
					Draft <?php echo $draft_row["draft_no"]; ?>
				</button>
			<?php
			}
			?>
		</div>
		<div id="approval_zone">
			<?php
			if ($draft_row["draft_no"] == $num_draft && $order_status == "2") {
				if ($draft_row["approve_status"] == "new") {
			?>
					<div class="btn_panel">
						<a type="button" href="#add_comment_btn" class="btn btn-primary add_cmt_btn" style="float:left;">Add Comment</a>
						<button type="button" class="btn btn-success approval_modal" style="width:150px" draft_id="<?php echo $draft_row["dd_id"]; ?>">Approve</button>
						<button type="button" class="btn btn-danger reject_modal" style="width:150px;" draft_id="<?php echo $draft_row["dd_id"]; ?>">Reject</button>
						<!--<button type="button" class="btn btn-success" style="width:150px;" onclick="return approveDraft(<?php echo $draft_row["dd_id"]; ?>);">Approve</button>-->
						<!--<button type="button" class="btn btn-danger" style="width:150px;" onclick="return rejectDraft(<?php echo $draft_row["dd_id"]; ?>);">Reject</button>-->
						<div style="text-align: left; color: #F00;">* Note: Available to comment only in the latest Draft.</div>
					</div>
				<?php
				} else {
				?>
					<div class="btn_panel">
						<a type="button" href="#add_comment_btn" class="btn btn-primary add_cmt_btn" style="float:left;">Add Comment</a>

						<?php

						$tmp_app_time = '';
						if ($draft_row["approve_time"] != "") {
							$tmp_app_time = '&nbsp;&nbsp;<span class="statusAlertDate">' . $draft_row["approve_time"] . '</span>';
						}

						if ($draft_row["approve_status"] == "approved") {
							echo '<div class="status_box"><span class="approve_status">APPROVED</span>' . $tmp_app_time . '</div>';
						} else {
							echo '<div class="status_box"><span class="reject_status">REJECTED</span>' . $tmp_app_time . '</div>';
						}
						?>

						<div style="text-align: left; color: #F00;">* Note: Available to comment only in the latest Draft.</div>
					</div>
				<?php
				}
			} else {
				?>
				<div class="btn_panel">
					<center>
						<?php
						$tmp_app_time = '';
						if ($draft_row["approve_time"] != "") {
							$tmp_app_time = '&nbsp;&nbsp;<span class="statusAlertDate">' . $draft_row["approve_time"] . '</span>';
						}

						if ($draft_row["approve_status"] == "approved") {
							echo '<div class="status_box"><span class="approve_status">APPROVED</span>' . $tmp_app_time . '</div>';
						} else {
							echo '<div class="status_box"><span class="reject_status">REJECTED</span>' . $tmp_app_time . '</div>';
						}
						?>
					</center>
				</div>
			<?php
			}
			?>
		</div>
	</div>

	<?php
	foreach ($a_draft as $key => $draft_row) {

		/*$sql_order_file = 'SELECT * FROM order_main_file WHERE order_main_id="'.$rs_order_main['order_main_id'].'" AND order_main_file_type="Design" ORDER BY order_main_file_title ASC';
		$rs_order_file = $conn->query($sql_order_file);
		while($row_order_file = $rs_order_file->fetch_assoc()){
			if($first_show_tab==""){
			  $first_show_tab = $row_order_file["order_main_file_title"];//---first tab show design
			}

			$a_design_folder[($row_order_file["order_main_file_title"])]["folder"] = "../files/_for_customer/".$rs_order_main['order_main_id']."/".$row_order_file["order_main_file_title"]."/";
			  
			$a_design_folder[($row_order_file["order_main_file_title"])]["use_process"] = "old";

			if(file_exists($a_design_folder[($row_order_file["order_main_file_title"])]["folder"])){
			  
			  $a_design_folder[($row_order_file["order_main_file_title"])]["use_process"] = "new";

			  $files= scandir($a_design_folder[($row_order_file["order_main_file_title"])]["folder"]);

			  unset($files[0],$files[1]);
			  $count_file = 0;
			  foreach ($files as $file) {
			    $count_file++;
			  }
			  $a_design_folder[($row_order_file["order_main_file_title"])]["page_max"] = $count_file-1;

			}

		}*/
	?>
		<div id="d_show_design<?php echo $draft_row["draft_no"]; ?>" style="width: 100%; <?php if ($draft_row["draft_no"] != $num_draft) {
																								echo 'display:none; ';
																							} ?>" class="d_show_design">
			<?php
			$path_file = "../../../internal/files";

			if ($_SERVER["SERVER_NAME"] == "online.jog-joinourgame.com") {
				$path_file = "https://locker.jog-joinourgame.com/files";
			}elseif($_SERVER['SERVER_NAME'] == "ols-test.jog-joinourgame.com"){
				$path_file = "https://locker-test.jog-joinourgame.com/files" ;
			}


			//$path_file = "/home1/whitty99/public_html/lockerroom/files/";
			//$path_file = "../../../internal/files/";
			//echo "TTTTTT=".$draft_row["order_file_id"];

			
			

			if (file_exists($path_file . "/_design/" . $draft_row["order_file_id"])) {

				$files = scandir($path_file . "/_design/" . $draft_row["order_file_id"] . "/");

				unset($files[0], $files[1]);
				$count_file = 0;
				foreach ($files as $file) {
					$count_file++;
				}
				$count_file--; //--- minus DESIGN.zip

				$page_max = $count_file - 1;

				$a_draft["folder"] = $path_file . "/_design/" . $draft_row["order_file_id"] . "/";
				$a_draft["page_max"] = $page_max;

				$img_tag = "";
				$back_index = 0;
				$next_index = 0;

				if ($page_max == 0) {
					$img_tag = '<img src="' . $a_draft["folder"] . 'page.jpg" height="700">';
				} else {
					$img_tag = '<img src="' . $a_draft["folder"] . 'page-0.jpg" height="700">';
					$back_index = $page_max;
					$next_index = 1;
				}

			?>
				<div id="showIMG<?php echo $draft_row["order_file_id"]; ?>">

					<table style="width: 100%; border-spacing: 0px; margin-top: -2px;">
						<tr>
							<td style="border-right-width:2px; border-right-style: solid; border-right-color:#000; text-align: center;" id="td_click_back<?php echo $draft_row["order_file_id"]; ?>" class="change_page" onclick="return changeInnerDesignIMG(<?php echo $draft_row["order_file_id"]; ?>,<?php echo $back_index; ?>);">

								<input type="hidden" id="obj_design<?php echo $draft_row["order_file_id"]; ?>" value="<?php echo base64_encode(json_encode($a_draft)); ?>">
								<i class="fa fa-angle-left" aria-hidden="true"></i>
							</td>
							<td style="background-color: #525659; border-width:2px 0px 2px 0px; border-style: solid; border-color:#000;">
								<center style="padding: 10px;">
									<div style="color:#FFF; padding-bottom: 5px;" id="d_show_title<?php echo $draft_row["order_file_id"]; ?>">Page: 1</div>
									<div id="d_show_img<?php echo $draft_row["order_file_id"]; ?>">
										<?php echo $img_tag; ?>
									</div>
								</center>
							</td>
							<td style="border-left-width:2px; border-left-style: solid; border-left-color:#000; text-align: center;" id="td_click_next<?php echo $draft_row["order_file_id"]; ?>" class="change_page" onclick="return changeInnerDesignIMG(<?php echo $draft_row["order_file_id"]; ?>,<?php echo $next_index; ?>);">
								<i class="fa fa-angle-right" aria-hidden="true"></i>
							</td>
						</tr>
					</table>
				</div>
			<?php
			} else {
			//  echo '<pre> actpath' ; 
			// //  print_r("$use_path./files/".$draft_row['order_design_code']/$draft_row["order_file_name"]");
			//  print_r("$use_path/files/'".$draft_row['order_design_code']."'/'".$draft_row['order_file_name']."'");
			
			?>

				<iframe src="<?php echo $use_path; ?>/files/<?php echo $draft_row["order_design_code"]; ?>/<?php echo $draft_row["order_file_name"]; ?>" type="frame&vlink=xx&link=xx&css=xxx&bg=xx&bgcolor=xx" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scorlling="yes" width="100%" height="600" style="width: 100%;"></iframe>
			<?php
			}
			?>


			<div id="comment_list_zone">
				<?php
				$is_first_comment = "yes";
				$a_dc_id_list = array();

				$sql_comment = "SELECT * FROM tbl_design_comment WHERE dd_id=" . $draft_row["dd_id"] . " AND enable=1 ORDER BY dc_id ASC";
				$rs_comment = $conn->query($sql_comment);
				if ($rs_comment->num_rows > 0) {

					$is_first_comment = "no";

					$a_comment = array();
					$a_comm_user_id = array();
					$a_comm_emp_id = array();
					while ($row_comment = $rs_comment->fetch_assoc()) {

						if ($row_comment["is_read"] == "0" && $row_comment["comment_from"] == "LKR") {
							$a_dc_id_list[($row_comment["dd_id"])][] = $row_comment["dc_id"];
						}

						$a_comment[] = $row_comment;
						if ($row_comment["user_id"] != "" && !in_array($row_comment["user_id"], $a_comm_user_id)) {
							$a_comm_user_id[] = $row_comment["user_id"];
						}
						if ($row_comment["employee_id"] != "" && !in_array($row_comment["employee_id"], $a_comm_emp_id)) {
							$a_comm_emp_id[] = $row_comment["employee_id"];
						}
					}

					$a_user_info = array();
					if (sizeof($a_comm_user_id) > 0) {
						$sql_user = "SELECT user_id,full_name FROM tbl_user WHERE user_id IN (" . implode(",", $a_comm_user_id) . ");";
						$rs_user = $conn->query($sql_user);
						while ($row_user = $rs_user->fetch_assoc()) {
							$a_user_info[($row_user["user_id"])] = $row_user["full_name"];
						}
					}

					$a_emp_info = array();
					if (sizeof($a_comm_emp_id) > 0) {
						$sql_emp = "SELECT employee_id,employee_name FROM employee WHERE employee_id IN (" . implode(",", $a_comm_emp_id) . ");";
						$rs_emp = $conn3->query($sql_emp);
						while ($row_emp = $rs_emp->fetch_assoc()) {
							$a_emp_info[intval($row_emp["employee_id"])] = $row_emp["employee_name"];
						}
					}

				?>
					<div id="d_comment_list" class="row bg_weather">
						<h1>Comments Area</h1>
						<div class="col-12"></div>
						<?php
						foreach ($a_comment as $tmp_key => $row_tmp) {

							if ($row_tmp["comment_from"] == "LKR") {
						?>
								<!--<div class="col-3"></div>-->
								<div class="col-8 ">
									<div style="text-align:right;color:white;"><?php echo $a_emp_info[($row_tmp["employee_id"])] . '<span style="color:white;" class="comment_time"> @ ' . date("M d, Y H:i:s", strtotime($row_tmp["date_add"])) . '</span>'; ?></div>
									<div class="comment_box_LKR"><?php echo $row_tmp["comment"]; ?></div>
								</div>
								<div class="col-4"></div>
							<?php
							} else {
							?>
								<div class="col-4"></div>
								<div class="col-8">
									<div style="text-align:left;color:white;"><?php echo $a_user_info[($row_tmp["user_id"])] . '<span style="color:white;" class="comment_time"> @ ' . date("M d, Y H:i:s", strtotime($row_tmp["date_add"])) . '</span>'; ?></div>
									<div class="comment_box_OLS"><?php echo $row_tmp["comment"]; ?></div>
								</div>
								<!--<div class="col-3"></div>-->
						<?php
							}
						}
						?>
					</div>
				<?php
				}
				?>
			</div>
			<?php if ($draft_row["draft_no"] == $num_draft && intval($order_status) < 6 /*&& $order_status=="2"*/) { ?>
				<center style="display:none;" id="cmt_box">
					<h4 align="left">Comment</h4>
					<form action="ajax/design_approval/save_comment.php" method="post" enctype="multipart/form-data" name="form1" target="hidden_frame" style="width: 90%;">
						<textarea name="input_comment_detail"></textarea>
						<input type="hidden" name="dd_id" value="<?php echo $draft_row["dd_id"]; ?>">
						<input type="hidden" id="add_is_first_comment" name="is_first_comment" value="<?php echo $is_first_comment; ?>">
						<button id="add_comment_btn" style="margin:10px;" onclick="hider();" class="btn btn-primary" type="submit">Submit Comment</button>
					</form>
				</center>
			<?php } ?>
		</div>
		<?php
	}

	if (sizeof($a_dc_id_list) > 0) {
		foreach ($a_dc_id_list as $tmp_dd_id => $tmp_a_dc_list) {

			$tmp_s_dc_list = implode(",", $tmp_a_dc_list);
		?>
			<input type="hidden" id="dc_list_for<?php echo $tmp_dd_id; ?>" value="<?php echo $tmp_s_dc_list; ?>">
	<?php
		}
	}
	?>

	<iframe name="hidden_frame" style="display: none;"></iframe>

	<script>
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
	</script>

	<div id="approvalModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">

			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header" style="background-color:black;">
					<div class="container-fluid">
						<h4 class="float-left">Approve Design</h4>
						<button type="button" style="margin-top:-20px;" class="close float-right" data-dismiss="modal">&times;</button>
					</div>
				</div>
				<div class="modal-body" style="background-image: url('https://online.jog-joinourgame.com/assets/images/tutorial-background.jpg');">
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
				<div class="modal-header" style="background-color:black;">
					<div class="container-fluid">
						<h4 class="float-left">Reject Design</h4>
						<button type="button" style="margin-top:-20px;" class="close float-right" data-dismiss="modal">&times;</button>
					</div>
				</div>
				<div class="modal-body" style="background-image: url('https://online.jog-joinourgame.com/assets/images/tutorial-background.jpg');">
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
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		CKEDITOR.replace('input_comment_detail');
		CKEDITOR.replace('reject_textarea');
		CKEDITOR.replace('approval_textarea');
	</script>

	<script src="assets/vendors/tinymce/tinymce.js"></script>
	<script src="assets/vendors/tinymce/themes/modern/theme.js"></script>

	<script type="text/javascript">
		function hider() {
			$('#cmt_box').hide();
		}

		function changeInnerDesignIMG(order_file_id, page_index) {

			var obj_design = jQuery.parseJSON(window.atob($('#obj_design' + order_file_id).val()));
			//alert(obj_design[tab_id]);

			var folder_name = obj_design.folder;
			var index_max = parseInt(obj_design.page_max);

			var index_back = parseInt(page_index) - 1;
			if (index_back < 0) {
				index_back = index_max;
			}

			var index_next = parseInt(page_index) + 1;
			if (index_next > index_max) {
				index_next = 0;
			}

			$('#td_click_back' + order_file_id).attr("onclick", 'return changeInnerDesignIMG(' + order_file_id + ',' + index_back + ');');
			$('#td_click_next' + order_file_id).attr("onclick", 'return changeInnerDesignIMG(' + order_file_id + ',' + index_next + ');');

			var img_tag = '';
			if (index_max == 0) {
				img_tag = '<img src="' + folder_name + 'page.jpg" height="700">';
			} else {
				img_tag = '<img src="' + folder_name + 'page-' + page_index + '.jpg" height="700">';
			}

			$('#d_show_title' + order_file_id).html('Page: ' + (page_index + 1));
			$('#d_show_img' + order_file_id).html(img_tag);

		}

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

		$(document).on('click', '.add_cmt_btn', function() {
			$('#cmt_box').show();
			$('html, body').animate({
				scrollTop: $("#cmt_box").offset().top
			}, 2000);
		})

		function saveSuccess(inner_tag, is_first) {

			if (is_first == "yes") {
				$('#comment_list_zone').html(window.atob(inner_tag));
				$('#add_is_first_comment').val("no");

			} else {
				$('#d_comment_list').append(window.atob(inner_tag));
			}

			$('#tiny-comment_ifr').contents().find('#tinymce').html("");

		}

		function setReadComment(dd_id) {

			var dc_id_list = $('#dc_list_for' + dd_id).val();

			if (dc_id_list != "" && dc_id_list != null) {

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "ajax/design_approval/set_read_comment.php",
					data: {
						"dc_id_list": dc_id_list
					},
					success: function(resp) {

						if (resp.result == "fail") {
							alert(resp.msg);
						}

					}
				});
			}
		}

		$(document).ready(function() {
			setTimeout(function() {
				$("#tiny-comment_ifr").css("height", "300px");
			}, 500);
		});

		tinymce.init({
			selector: '#tiny-comment',
			height: 300,
			theme: 'modern',
			plugins: [
				'advlist autolink lists charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons template paste textcolor colorpicker textpattern imagetools toc help'
			],
			menubar: false,
			toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor'

		});

		setTimeout(function() {
			$('.mce-branding-powered-by').remove();

			setReadComment(<?php echo $first_dd_id; ?>);

		}, 3000);
	</script>
<?php
}
?>