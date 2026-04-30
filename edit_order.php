<?php
include('check-session.php');
include('db.php');
include('encryption_helper.php');



$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;



$draft_id = $_POST["draft_id"] ?? $_SESSION['draft_id'];
$_SESSION['draft_id'] = $draft_id;

$ttl_jersey = 0;
$ttl_jersey_qty_2 = 0;
$ttl_sock_qty = 0;
$ttl_sock_qty_2 = 0;
$TTL_SHELL = 0;
$ttl_top = 0;
$ttl_bottom = 0;

 



$sql_draft = "SELECT * FROM tbl_draft_of WHERE draft_id = ?  AND user_id = ?  AND enable = 1 ORDER BY of_id ASC";
$stmt = $conn->prepare($sql_draft);
$stmt->bind_param("si", $draft_id ,$user_id); // use "s" if draft_id is string
$stmt->execute();
$rs_draft = $stmt->get_result();
$num_row = $rs_draft->num_rows;


$a_data = array();
$a_of_id = array();

$a_sub_user = array();

if ($num_row > 0) {
	while ($row_draft = $rs_draft->fetch_assoc()) {
		$a_data[] = $row_draft;
		$a_of_id[] = $row_draft["of_id"];
	}
} else {
	echo "Data not found!!";
	exit();
}

$sql_sub_user = "SELECT sub_user_id,nick_name FROM tbl_sub_user WHERE parent_user_id= ?  AND enable=1 ORDER BY nick_name ASC";
$stmt = $conn->prepare($sql_sub_user);
$stmt->bind_param("i", $user_id); // use "s" if draft_id is string
$stmt->execute();
$rs_sub_user = $stmt->get_result();


if ($rs_sub_user->num_rows > 0) {
	while ($row_sub_user = $rs_sub_user->fetch_assoc()) {
		$a_sub_user[($row_sub_user["sub_user_id"])] = $row_sub_user["nick_name"];
	}
}

 

$num_of = $num_row;

$s_of_id_list = implode(",", $a_of_id);



?>
<style>
	fieldset {
		gap: 20px;
	}



	.boxes {
		background: #FFFFFF;
		border: 1px solid #DDDDDD;
		padding: 30px;
		border-radius: 5px;
	}

	#team-tab .boxes {
		padding: 30px 30px 30px 0;

	}

	.orderMultiStep {
		max-width: 900px;
		display: flex;
		align-items: center;
		margin: auto;
		background: #ECF0F9;
		padding: 15px 40px;
		justify-content: space-between;
	}

	.orderMultiStep .activeStep {
		color: var(--blueColor);
	}

	.form-horizontal {
		margin: 0 0 20px 0;
	}

	.form-horizontal .formTitle {
		margin: 0 0 20px 0;
		gap: 30px;
		justify-content: space-between;
	}

	.orederItems a {
		display: flex;
		align-content: center;
		gap: 11px;
		font-size: var(--small-Size);
		font-weight: 600;
		color: var(--grey-color);
	}

	.orederItems .iconImg {
		position: relative;
		top: 2px;
	}

	.submitBUtton {
		text-align: right;
	}

	.tab-pane {
		padding: 20px;
		background: #FFF;
		margin-top: 30px;
	}

	#team .tab-pane {
		margin-top: 0 !important;
		padding: 0 !important;
	}

	.main-content-header .nav-item .nav-link.active {
		color: var(--blueColor);

	}

	#myTab {
		display: flex;
		width: 100%;
		margin: auto;
		margin-bottom: auto;
		margin-bottom: auto;
		text-align: center;
		justify-content: space-between;
		border-bottom: 0;
		background: #ECF0F9;
		max-width: 50vw;
		border-radius: 5px;
	}


	.leftSide {
		background: #FFFFFF;
	}

	.rightSide {
		background: #F9F9F9;
		padding: 30px;
	}

	.rightSide {
		border: 1px solid #DDDDDD;
	}

	.rightSide .card,
	.leftSide .card {
		background: #F9F9F9;
	}

	.rightSide li {
		color: var(--grey-color);
	}

	.themeBtn {
		cursor: pointer;
	}

	.rightSide .themeBtn2 {
		color: #21A366;
		margin: 0 auto 0 0;
		border: 1px solid #DDDDDD !important;
		box-shadow: 0px 0px 4px 0px #0000002E;

	}

	.formBottom {
		grid-template-columns: 200px 200px;
	}

	#addNewTeam .formBottom {
		grid-template-columns: 1fr 1fr;
	}

	.RosterDetailsGuide .innerBox {
		background: #FFFFFF;
		text-align: left;
		padding: 30px;
		border: 1px solid #DDDDDD;
		border-radius: 5px;
	}

	.RosterDetailsGuide .innerBox li {
		color: var(--grey-color);
		font-size: var(--XSmall-Size);
	}

	.teamTabsSection .tableLower {
		padding: 20px 0;
	}

	#teamTab {
		box-shadow: 0px 1px 4px 0px #0000001A;
		border: 1px solid #DDDDDD;
		position: sticky;
		top: -32px;
		z-index: 100;
		padding: 10px;
	}

	#teamTab .nav-link.active {
		box-shadow: 0px 1px 4px 0px #0000001A;
		border: 1px solid #DDDDDD
	}

	#teamTab .nav-link {
		border: 1px solid #e8e8e8;
		margin: 0 2px;
	}

	.teamTabsSection {
		display: none;
	}

	.teamTabsSection .tab-pane {
		padding: 0;
	}

	.table-responsive th,
	.table-responsive td {
		font-size: var(--XSmall-Size);
		text-align: center;
	}

	.table-responsive th {
		background: var(--blueColor);
		color: #fff;
		font-weight: 400;
		font-size: 12px;
		padding: 10px 4px !important;
	}

	.upload-btn-wrapper button {
		box-shadow: 0px 0px 4px 0px #0000002E;
		border: 1px solid #DDDDDD;
		width: 100%;

	}

	.table-responsive td {
		padding: 10px 4px !important;

	}

	#toggleGuide {
		cursor: pointer;
	}

	table,
	th,
	td {
		border: 1px solid #C8C8C8
	}

	.theader th {
		background: #ECF0F9;
		color: var(--blueColor);
		padding: 15px;
		font-size: var(--small-Size);
	}

	.table-responsive {
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
		border: 1px solid #e4dfdf;
		border-radius: 5px;
		white-space: nowrap;
		margin-bottom: 30px;
	}

	.table-responsive input,
	.table-responsive select {
		text-align: center;
		font-size: 13px;
		background: none;
		border: none;
		color: var(--grey-color);
	}

	::placeholder {
		color: var(--grey-color);

	}

	table select {
		cursor: pointer;
	}

	#PreviewPdfMdoal\  {
		position: absolute;
		right: 0;
		bottom: 0;
		background: var(--blueColor);
		padding: 12px;
		color: #FFF;
		border-radius: 0 4px 5px 0;
		font-size: 23px;
	}

	#PreviewPdfMdoal embed {
		width: 100%;
		height: 80vh;
	}

	#PreviewPdfMdoal .modal-dialog {
		max-width: 800px;
	}

	/* .sp_outter_panel {
		border: 1px solid #2F50A366;
		padding: 5px;
		background-color: #2F50A317;
		color: #FFF;
		font-size: 13px;
		margin-right: 12px;
		border-radius: 4px;
	} */

	@media screen and (max-width:1200px) {
		#myTab {
			max-width: 65vw;
		}

		.boxes {
			padding: 20px;
		}
	}

	@media screen and (max-width:1000px) {
		#myTab {
			max-width: 65vw;
		}

		.main-content-header .nav-item .nav-link {
			gap: 10px;
			padding: 10px;
		}

		.boxes {
			padding: 20px;
		}
	}

	@media screen and (max-width:900px) {
		#myTab {
			max-width: 100%;
		}

		.billingAndDelivery .grid2 {
			grid-template-columns: 1fr;
		}
	}

	@media screen and (max-width:600px) {
		header {
			padding: 0;

		}

		.tab-pane {
			padding: 10px;
		}

		.boxes {
			padding: 15px;
		}
	}

	.formTitle {
		margin: 0 0 20px 0;
		gap: 30px;
		justify-content: space-between;
	}

	.c-label {
		padding-bottom: 5px;
		font-size: 13px;
		font-weight: 500;
		color: #535555;
	}

	#team .leftSide .boxes {
		padding: 1vw;
	}

	.required{
		 border: none !important;
		 color: red !important;
		 height: 0px;
	}

	  .w-45{
         width: 45px !important ;
    }
</style>

<div class="editORderPage">
	<div class=" pageHeader">
		<h2>Editing Order</h2>
		<p>Edit your order.</p>
	</div>
	<div class="innerMainContent ">
		<ul class="nav nav-tabs" id="myTab" role="tablist">
			<li class="nav-item" role="presentation">
				<a class="nav-link active" id="billing-tab" data-bs-toggle="tab" href="#billing" role="tab"
					aria-controls="billing" aria-selected="true">
					<ion-icon name="checkmark-circle-outline" class="iconImg"></ion-icon>
					Billing & Delivery
				</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="order-tab" data-bs-toggle="tab" href="#order" role="tab"
					aria-controls="order" aria-selected="false">
					<ion-icon name="checkmark-circle-outline" class="iconImg"></ion-icon>
					Order Information
				</a>
			</li>
			<li class="nav-item" role="presentation">
				<a class="nav-link" id="team-tab" data-bs-toggle="tab" href="#team" role="tab"
					aria-controls="team" aria-selected="false">
					<ion-icon name="checkmark-circle-outline" class="iconImg"></ion-icon>
					Team & Roster Details
				</a>
			</li>
		</ul>

		<form name="form_manage_save" id="form_manage_save" method="post" enctype="multipart/form-data">
			<input type="hidden" name="of_id_list" value="<?php echo $s_of_id_list; ?>">
			<input type="hidden" name="of_id_delete" id="of_id_delete" value="">
			<input type="hidden" name="oi_id_delete" id="oi_id_delete" value="">
			<!-- <input type="hidden" name="edit_draft_id" id="edit_draft_id" value="<?php echo $_POST["draft_id"]; ?>"> -->
			<input type="hidden" name="edit_draft_id" id="edit_draft_id" value="<?php echo $draft_id ; ?>">

			<div class="tab-content" id="myTabContent">
				<div class="tab-pane tab-pane-forboc fade show active" id="billing" role="tabpanel" aria-labelledby="billing-tab">
					<div class="billingAndDelivery">
						<div class="grid2">
							<div class="boxes">
								<div class="formTitle d-flex align-items-center flex-row">
									<h6 class="subHeading m-0">Billing Information </h6>
									<a href="#" class="sm-Btn">Edit</a>
								</div>
								<fieldset class="grid2 singleFrom">
									<div class="form-group column2">
										<label for="">Company</label>
										<input type="text" name="company_name" id="bi_company_name" maxlength="150" value="<?php echo $a_data[0]["bill_comp_name"]; ?>">
										<!-- <input type="text" name="Project Name" value="Larsen Yang LLC"> -->
									</div>
									<div class="form-group column2">
										<label for="">Contact <span class="required">*</span></label>
										<input type="text"  name="contact" id="bi_contact"   maxlength="50" value="<?php echo $a_data[0]["bill_contact_name"]; ?>">
										
									</div>

									<div class="form-group">
										<label for="">City <span class="required">*</span> </label>
										<input type="text" name="city" id="bi_city" maxlength="80" value="<?php echo $a_data[0]["bill_city"]; ?>">
									</div>

									<div class="form-group">
										<label for="">ZipCode <span class="required">*</span> </label>
										<input type="text" name="zip_code" id="bi_zip_code" maxlength="20" value="<?php echo $a_data[0]["bill_zip_code"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Country <span class="required">*</span> </label>
										<input type="text"   name="country" id="bi_country"  maxlength="100" value="<?php echo $a_data[0]["bill_country"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">TAX-ID </label>
										<input type="text" name="tax_id" id="bi_tax_id" maxlength="30" value="<?php echo $a_data[0]["bill_tax_id"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Tel <span class="required">*</span> </label>
										<input type="number" name="tel" id="bi_tel" maxlength="30" value="<?php echo $a_data[0]["bill_tel"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Email <span class="required">*</span> </label>
										<input type="text" name="email" id="bi_email" maxlength="200" value="<?php echo $a_data[0]["bill_email"]; ?>">
									</div>

									<div class="form-group column2">
										<label for="">Address <span class="required">*</span> </label>
										<textarea name="address_info" id="bi_address" class="form-control" rows="3"><?php echo $a_data[0]["bill_address"]; ?></textarea>
									</div>
								</fieldset>
							</div>

							<div class="boxes">
								<div class="formTitle d-flex align-items-center flex-row">
									<div class="d-flex align-items-center gap-3">
										<h6 class="subHeading m-0">Delivery Information </h6>
										<a href="#">Edit</a>
									</div>
									<div class="checkbox">

										<div>
											<input type="checkbox" id="check" name="check" value=""  checked value=""/>
											<label for="check" class="XSmall">
												Same
												as Billing Info
												<span></span>
											</label>
										</div>

									</div>


								</div>
								<fieldset class="grid2 singleFrom">
									<div class="form-group column2">
										<label for="">Company</label>
										<input type="text" name="d_company_name" id="de_company_name" maxlength="150" value="<?php echo $a_data[0]["deli_comp_name"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Contact <span class="required">*</span> </label>
										<input type="text" name="d_contact" id="de_contact" maxlength="200" value="<?php echo $a_data[0]["deli_contact_name"]; ?>">
									
									</div>
									<div class="form-group">
										<label for="">City <span class="required">*</span> </label>
										<input type="text" name="d_city" id="de_city" maxlength="80" value="<?php echo $a_data[0]["deli_city"]; ?>">
									</div>

									<div class="form-group">
										<label for="">ZipCode <span class="required">*</span>  </label>
										<input type="text" name="d_zip_code" id="de_zip_code" maxlength="20" value="<?php echo $a_data[0]["deli_zip_code"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Country <span class="required">*</span> </label>
										<input type="text" name="d_country" id="de_country" maxlength="100" value="<?php echo $a_data[0]["deli_country"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">TAX-ID</label>
										<input type="text" name="d_tax_id" id="de_tax_id" maxlength="30" value="<?php echo $a_data[0]["deli_tax_id"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Tel <span class="required">*</span> </label>
										<input type="number" name="d_tel" id="de_tel" maxlength="30" value="<?php echo $a_data[0]["deli_tel"]; ?>">
									</div>
									<div class="form-group column2">
										<label for="">Email <span class="required">*</span> </label>
										<input type="text" name="d_email" id="de_email" maxlength="200" value="<?php echo $a_data[0]["deli_email"]; ?>">
									</div>

									<div class="form-group column2">
										<label for="">Address <span class="required">*</span> </label>
										<textarea name="d_address_info" class="form-control" id="de_address" rows="3"> <?php echo $a_data[0]["deli_address"]; ?></textarea>
										
									</div>
								</fieldset>
							</div>
						</div>


						<div class="submitBUtton">

						</div>



						<div class="submitBUtton column2">
				
							<a class="themeBtn iconBTn movetoOrderItem">
								Save and Continue
								<figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>
							</a>

							<a class="nav-link movetoOrderItem" >
								<ion-icon name="checkmark-circle-outline" class="iconImg md hydrated" role="img"></ion-icon>
								Order Information
							</a>
						</div>
					</div>
				</div>
				<div class="tab-pane  tab-pane-forboc fade" id="order" role="tabpanel" aria-labelledby="order-tab">
					<div class="boxes">
						<div class="formTitle d-flex align-items-center flex-row">
							<div class="orderBadge">
								Order Date :<?php echo date("m/d/Y"); ?>
								<input type="hidden" name="order_date" id="order_date" value="<?php echo date("Y-m-d"); ?>">
							</div>
						</div>
						<fieldset class="grid2 singleFrom">
							<div class="form-group  ">
								<label for="">Project Name</label>
								<input type="text" name="project_name" id="project_name" style="width: 100%;" value="<?php echo $a_data[0]["project_name"]; ?>">
							</div>
							<div class="form-group  ">
								<label for="">Customer PO</label>
								<input type="text" name="customer_po" id="customer_po" style="width: 100%;" value="<?php echo $a_data[0]["customer_po"]; ?>">
							</div>

							<div class="form-group  ">
								<label for="">Game / Event date <span class="required">*</span> </label>
								<input type="date" name="game_event_date" id="game_event_date" style="width: 100%;" value="<?php echo ($a_data[0]["game_event_date"] == "0000-00-00") ? "" : $a_data[0]["game_event_date"]; ?>">
								<span class="game_errormsg" style="color: red;font-size: 14px;padding: 12px 20px 6px 20px;"></span>
							</div>

							<div class="form-group">
								<label for="">Request due date <span class="required">*</span></label>

								<input type="date" name="req_due_date" id="req_due_date" style="width: 100%;" value="<?php echo ($a_data[0]["req_due_date"] == "0000-00-00") ? "" : $a_data[0]["req_due_date"]; ?>">
								<span class="req_errormsg" style="color: red;font-size: 14px;padding: 12px 20px 6px 20px;"></span>
							</div>
							<div class="form-group">
								<label for="">Payment Option</label>
								<div class="styled-select">
									<select style="width: 100%; font-size: 14px;" name="payment_opt" id="payment_opt">
										<option value="Wire transfer" <?php if ($a_data[0]["payment_opt"] == "Wire transfer") {
																			echo "selected";
																		} ?>>Wire transfer</option>
										<option value="ACH transfer" <?php if ($a_data[0]["payment_opt"] == "ACH transfer") {
																			echo "selected";
																		} ?>>ACH transfer</option>
										<option value="Credit card" <?php if ($a_data[0]["payment_opt"] == "Credit card") {
																		echo "selected";
																	} ?>>Credit card (Processing fee 3%)</option>
										<option value="Cheque" <?php if ($a_data[0]["payment_opt"] == "Cheque") {
																	echo "selected";
																} ?>>Cheque</option>
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

							<!-- <div class="form-group position-relative ">
								<label for="">Reorder? Type the EX
									here</label>


									
								<input type="text" name="reorder_num" id="reorder_num" style="width: 100%;" value="<?php echo $a_data[0]["reorder_num"]; ?>">
								<!- - <input type="text" name="Project Name" value="Project Name"
										placeholder="Project Name" class="position-relative"> -- 
								<a class="nav-link d-flex" id="PreviewPdfMdoal " style="cursor: pointer;"
									data-bs-toggle="modal" data-bs-target="#PreviewPdfMdoal">
									<ion-icon name="eye-outline"></ion-icon>

								</a>
							</div> -->



							   <div class="styled-select">
                                <label for="" class="w-100 text-start  c-label">Reorder? They the previous JOG order EX# here</label>
                                <select id="reorder_num" name="reorder_num" onchange="getReorder()">
                                    <option value="">Reorder? Type the EX# here</option>
                                    <?php
									$re_order_num = $a_data[0]["reorder_num"] ; 

                                    //$sql_new = "SELECT * FROM tbl_order_form WHERE user_id='$user_id'";
                                    $sql_new = "SELECT tbl_order_form.*,COUNT(DISTINCT tbl_order_form.prod_id) AS prod_num,COUNT(tbl_order_item.oi_id) AS item_num,SUM(tbl_order_item.qty_top1+tbl_order_item.qty_top2+tbl_order_item.qty_bottom1+tbl_order_item.qty_bottom2) AS qty_sum,tbl_user.full_name,tbl_user.customer_id FROM tbl_order_form LEFT JOIN tbl_product ON tbl_order_form.prod_id=tbl_product.prod_id LEFT JOIN tbl_order_item ON tbl_order_form.of_id=tbl_order_item.of_id LEFT JOIN tbl_user ON tbl_order_form.user_id=tbl_user.user_id WHERE tbl_order_form.user_id='$user_id' AND tbl_order_form.enable=1 AND tbl_order_form.order_status<>'finished' AND tbl_order_form.lkr_order_main_id IS NOT NULL AND tbl_order_form.order_status NOT IN ('shipped','received','archived') GROUP BY tbl_order_form.draft_id ORDER BY tbl_order_form.order_date DESC;";
                                    $emps = $conn->query($sql_new);
                                    $num_rows = $emps->num_rows;
                                    if ($num_rows > 0) {
                                        while ($row_selection = $emps->fetch_assoc()) {
                                            if (!empty($row_selection['code_match'])) {
											  $selection =	$re_order_num == $row_selection['code_match'] ? 'selected' : '';
                                    ?>
                                                <option value="<?= $row_selection['code_match'] ?>" <?= $selection ?> ><?= $row_selection['code_match'] ?></option>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                                <a class="nav-link d-flex" id="PreviewPdfMdoal " target="_blank" style="cursor: pointer;">
                                    <ion-icon name="eye-outline"></ion-icon>
                                </a>
                            </div>


						</fieldset>
					</div>
					<div class="d-flex justify-content-between my-4">

						<div class="goBackBtn  ">
							<a href="#" class=" themeBtn2grey switch-tab iconBTn" data-target="#billing">
								<figure class="m-0"><img src="images/vector/previousBtn.png" alt=""></figure> Go Back
							</a>
						</div>
						<div class="submitBUtton">
							<span class="themeBtn switch-tab iconBTn" id="movetoteam" onclick="movetoteam()">Save and
								Continue  <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure></span>
						</div>
					</div>
				</div>

				<div class="tab-pane tab-pane-forboc fade" id="team" role="tabpanel" aria-labelledby="team-tab">
					<div class="row">
						<div class="col-md-8 leftSide">
							<div class="boxes bg-none border-none">
								<div class="formTitle d-flex align-items-center flex-row">
									<h6>Create Order Form</h6>
								</div>
								<fieldset class="grid2 singleFrom">
									<div class="form-group">
										<label for="" class=" ">Team Name</label>
										<input type="text" name="input_on_team_name" id="input_on_team_name" value="" maxlength="150">
									</div>
									<div class="form-group">
										<label for="" class=" ">Year</label>
										<select class="form-select" name="input_on_year" id="input_on_year">
										<option value="">-- Select Year --</option>
                                             <?php
                                            $currentYear = date("Y");
                                            for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                                                echo '<option value="' . $year . '">' . $year . '</option>';
                                            }
                                            ?>
										</select>
										
									</div>

									<div class="form-group column2">
										<label for="" class=" ">Order Form</label>
										<div class="styled-select column2">
											<select id="prod_id">
												<option value="">--Select Order Form --</option>
												<?php
												$stmt = $conn->prepare("SELECT * FROM tbl_product ORDER BY prod_id ASC");

												if (!$stmt) {
												die("Prepare failed: " . $conn->error);
												}

												$stmt->execute();

												$rs_product = $stmt->get_result();
								

												while ($row_product = $rs_product->fetch_assoc()) {
													echo "<option value=\"" . $row_product["prod_id"] . '">' . $row_product["prod_name"] . "</option>'; ";
												}
												?>
											</select>
										</div>
									</div>
									<div class="d-flex justify-content-between column2 formBottom">
										<div class="goBackBtn themeBtn2grey">
											<a href="#" class="goback switch-tab iconBTn" data-target="#order">
												<figure class="m-0"><img src="images/vector/previousBtn.png" alt=""></figure> Go Back
											</a>
										</div>
										<div class="d-flex gap-2">
											<input type="hidden" id="form_id_inc" name="form_id_inc" value="<?php echo $num_of + 1; ?>"><br>
											<span class="themeBtn iconBTn" id="showTeamTabsSection">
												Save and
												Continue <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>
											</span>

											<!-- <div class="upload-btn-wrapper"> -->
												<!-- <button class="btn themeBtn2 d-flex gap-3">
													<figure class="m-0"><img src="images/vector/upload.png"
															alt=""></figure> Upload Order Form
												</button>
												<input type="file" name="myFile" /> 
												</figure> -->


												
												<span class="" style="height: 55px; width: 151px; overflow: hidden; position: relative ;top: 10px;">
													<input type="file" class="form-control " accept=".xlsx" name="order_form_file" id="order_form_file">
												</span>

												<button class="orderFormUpload btn btn-sm btn themeBtn2 iconBTn" type="button">
													<figure class="m-0">
														<img src="images/vector/upload.png" alt="">
													</figure>
													<span>Upload Order Form </span>
												</button>


											<!-- </div> -->
										</div>
									</div>
								</fieldset>
							</div>
						</div>
						<div class="col-md-4 rightSide d-flex">
							<div class="card bg-none border-none d-flex justify-content-evenly text-start">
								<div class="slideIcon" id="toggleButton">
									<figure class="m-0"><img src="images/vector/rightArrow.png" alt=""></figure>
								</div>
								<h5 class="subHeading">Another way to create order form</h5>
								<ol>
									<li class="XSmall"> You can download a Blank Order Form below.</li>
									<li class="XSmall"> Fill it accordingly and upload it using Upload Order
										Form button.</li>
								</ol>
								<p class="XSmall grey">You can download a Blank Order Form here</p>
								<a href="#" class="themeBtn2 d-flex gap-3">Download <figure class="m-0"><img
											src="images/vector/excel.png" alt=""></figure></a>
							</div>
						</div>
					</div>
					<div class="teamTabsSection1  ">
						<div class="mt-4">
							<ul class="nav nav-tabs whiteBg" id="teamTab" role="tablist">
								<?php
								$sql_draft = "SELECT * FROM tbl_draft_of 
								WHERE draft_id = ? 
								AND user_id = ? 
								AND enable = 1 
								ORDER BY of_id ASC";

								$stmt = $conn->prepare($sql_draft);

								$stmt->bind_param("si", $draft_id ,$user_id); 

								$stmt->execute();

								$rs_draft = $stmt->get_result();
								$a_item = array();
								$i = 0;
								$teamCount = 1; 
								while ($row_item = $rs_draft->fetch_assoc()) {
								?>
									<li class="nav-item" role="presentation">
										<a class="nav-link   teamDetailsNavitems  <?php if ($i == 0) {
																echo "active";
																$fisrtid = $row_item["of_id"];
															} ?> " id="fill-tab-<?php echo $row_item["of_id"]; ?>" data-bs-toggle="tab" href="#fill-tabpanel-<?php echo $row_item["of_id"]; ?>" role="tab"  data-i = "<?= $i ?>"   aria-controls="fill-tabpanel-<?php echo $row_item["of_id"]; ?>" aria-selected="true"> Team <?php echo $teamCount ?> </a>
									</li>
								<?php
									$i++;
									$teamCount++; 
								}
								?>
							</ul>

							<div class="bg-white tableLower">
								<div id="table_showing" class="table-responsive">
									<?php
									

									$sql_item = "SELECT * FROM tbl_draft_oi WHERE of_id IN (" . $s_of_id_list . ") ORDER BY of_id ASC,oi_id ASC;";
									$rs_item = $conn->query($sql_item);


									$a_item = array();
									while ($row_item = $rs_item->fetch_assoc()) {
										$a_item[($row_item["of_id"])][] = $row_item;
									}


									for ($k = 0; $k < sizeof($a_of_id); $k++) {

										$is_assigned = $a_data[$k]["is_assigned"];

										$prod_id = $a_data[$k]["prod_id"];
										$of_id = $a_of_id[$k];
										$form_name = $a_data[$k]["form_name"];
										$special_comm = $a_data[$k]["special_comment"];

										$form_id = $k + 1;

										$stmt = $conn->prepare("SELECT * FROM tbl_product WHERE prod_id = ? LIMIT 1");
										$stmt->bind_param("i", $prod_id); // use "s" if prod_id is string

										$stmt->execute();

										$rs_product = $stmt->get_result();

										$row_product = $rs_product->fetch_assoc(); // ✅ same final variable

										if ($a_data[$k]["xls_name"] != "") {
									?>
											<center id="sameteam<?php echo $of_id; ?>">
												   <div class="tab-content  xls-name-blank" id="tab-content">
													<div class="tab-pane <?php if ($fisrtid == $of_id) {
																				echo "active";
																			} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>"  data-first_id = "<?= $fisrtid ?>">
														<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>">
															<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
															<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="1">
															<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
															<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">
															<center>
																<h6>
																	<span class="sp_outter_panel" id="sp_outter_panel<?php echo $form_id; ?>">
																		<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
																		<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
																			<figure onclick="return editFormName(<?php echo $form_id; ?>);"><img src="images/vector/ediBlue.png" alt=""></figure>
																		</span>
																		<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
																			<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
																			<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
																		</span>
																	</span>
																	<?php echo " (" . $row_product["prod_name"] . ")"; ?>
																	<?php if ($a_data[$k]["re_order_id"] == "") { ?>


																		<i class="fa fa-minus-circle" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i>
																	<?php } else { ?>
																		(RE-ORDER) <i class="fa fa-minus-circle" style="font-size: 16px; color: #999; " title="Re-Order form can not be deleted."></i>
																	<?php } ?>
																</h6>


																<iframe
																	class="frame_content"
																	id="live_view<?php echo $of_id; ?>"
																	src="https://view.officeapps.live.com/op/embed.aspx?src=https://ols-test.jog-joinourgame.com/src/upload/<?php echo urlencode($a_data[$k]["xls_name"]); ?>"
																	width="100%"
																	height="500"
																	frameborder="0">
																</iframe>

																<!-- <iframe class="frame_content" id="live_view<?php echo $of_id; ?>" src="https://view.officeapps.live.com/op/embed.aspx?src=http://localhost/olsDash/src/upload/<?php echo $a_data[$k]["xls_name"]; ?>" width="100%" height="500" frameborder="0"></iframe> -->
															</center>
														</div>
													</div>
												</div>
											</center>
											<?php

										} else if ($is_assigned == "0") {

											$sql_size = "SELECT * FROM tbl_size 
											WHERE prod_id = ? 
											AND enable = 1 
											ORDER BY split_order ASC, sort_no ASC";

											$stmt = $conn->prepare($sql_size);

											if (!$stmt) {
											die("Prepare failed: " . $conn->error);
											}

											$stmt->bind_param("i", $prod_id); // use "s" if prod_id is string

											$stmt->execute();

											$rs_size = $stmt->get_result();
										
											$a_size = array();
											while ($row_size = $rs_size->fetch_assoc()) {
												$a_size[($row_size["split_order"])][] = $row_size;
												$spl_order = $row_size["split_order"];
											}

											$ttl_jersey = 0;
											$ttl_jersey_qty_2 = 0;
											$ttl_sock_qty = 0;
											$ttl_sock_qty_2 = 0;
											$TTL_SHELL = 0;
											$ttl_top = 0;
											$ttl_bottom = 0;

											$num_item = isset($a_item[$of_id]) ?  sizeof($a_item[$of_id]) : 0 ;

											if ($prod_id == "1") {
											?>
												<center id="sameteam<?php echo $of_id; ?>">
													<div class="tab-content   production_id-1" id="tab-content">
														<div class="tab-pane <?php if ($fisrtid == $of_id) {
																					echo "active";
																				} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>"   data-first_id = "<?= $fisrtid ?>">
															<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>">
																<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
																<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="1">
																<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
																<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">
																<table class="tbl_item_form hockey&sock" style="width: 100%;">
																	<tr class="theader">
																		<th class="tablecount text-center"></th>
																		<th colspan="15" class="text-center">
																			<h6 class="my-auto">


																				<span class="sp_outter_panel" id="sp_outter_panel<?php echo $form_id; ?>">
																					<span style="text-transform: capitalize;" id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
																					<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
																						<figure class="m-0 d-inline" onclick="return editFormName(<?php echo $form_id; ?>);"><img
																								src="images/vector/edit.png" alt="" style="width: 25px; margin:0 10px;">
																						</figure>
																					</span>
																					<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
																						<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
																						<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
																					</span>
																				  </span>


																				<?php echo " (" . $row_product["prod_name"] . ")"; ?>
																				<?php if ($a_data[$k]["re_order_id"] == "") { ?>
																					<figure class="m-0 d-inline iconBTn  delete_form_btn">
																						<img src="images/vector/delter.png" alt="" style="width: 30px; background: #FFF; padding: 6px;margin-left: 20px;"></figure>
																				<?php } else { ?>
																					(RE-ORDER) <i class="fa fa-minus-circle" style="font-size: 16px; color: #999; " title="Re-Order form can not be deleted."></i>
																				<?php } ?>
																			</h6>
																			
																		</th>
																		<th colspan="2">
																			<?php
																			if (sizeof($a_sub_user) > 0) {
																			?>
																				<div id="d_assign_select<?php echo $of_id; ?>" class="assign_tag text-center">
																					<span class="Small">Assign to </span> <br>
																					<select name="select_assign[<?php echo $form_id; ?>] " class="customStyle">
																						<option value="">==Select one==</option>
																						<?php
																						foreach ($a_sub_user as $sub_user_id => $nick_name) {
																							echo '<option value="' . $sub_user_id . '">' . $nick_name . '</option>';
																						}
																						?>
																					</select>
																				</div>
																			<?php
																			}
																			?>
																		</th>
																	</tr>
																	<tr>
																		<th onclick="return addItemRow(<?php echo $form_id; ?>,1);" class="text-center">

																			<i class="fa fa-plus-circle"></i>
																			<i id="loading_<?php echo $form_id; ?>" style="display: none; font-size: 12px;" class="fa fa-spinner fa-pulse fa-1x fa-fw"><br></i>
																			<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $num_item; ?>">
																		</th>
																		<th style="width:150px;">Name on Jersey</th>
																		<th style="width:125px;">Pattern Cut</th>
																		<th style="width:90px;">P or G</th>
																		<th style="width:72px;">Jersey Size</th>
																		<th>Jersey # (number)</th>
																		<th>Jersey Color</th>
																		<th style="width:50px;">QTY</th>
																		<th>Jersey Color</th>
																		<th style="width:50px;">QTY</th>
																		<th style="width:40px;">Sock Size</th>
																		<th>Sock Color</th>
																		<th style="width:50px;">QTY</th>
																		<th>Sock Color</th>
																		<th style="width:50px;">QTY</th>
																		<th style="width:75px;">C or A</th>
																		<th style="width:125px;">Name For Packing</th>
																		<th>Notes</th>
																	</tr>
																	<tbody id="prod_item_<?php echo $form_id; ?>">
																		<?php
																		for ($m = 0; $m < $num_item; $m++) {

																			$row_count = $m + 1;
																			$edit_item = $a_item[$of_id][$m];
																		?>
																			<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
																				<td class="text-center" onclick="return deleteItemRow(<?php echo $form_id; ?>,<?php echo $edit_item["oi_id"]; ?>,1,<?php echo $row_count; ?>);">
																					<i class="fa fa-minus-circle" style="color: #7a7a7a;"></i>
																					<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="<?php echo $edit_item["oi_id"]; ?>">
																				</td>
																				<td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo $edit_item["player_name"]; ?>"></td>
																				<td>
																					<select class="white_in w-fit" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','<?php echo $prod_id; ?>');" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_mf[<?php echo $form_id; ?>][]">
																						<option value="youth" <?php if ($edit_item["sex"] == "youth") {
																													echo "selected";
																												} ?>>MEN-YOUTH</option>
																						<option value="male" <?php if ($edit_item["sex"] == "male") {
																													echo "selected";
																												} ?>>MEN-ADULT</option>
																						<option value="female" <?php if ($edit_item["sex"] == "female") {
																													echo "selected";
																												} ?>>WOMEN-ADULT</option>
																					</select>
																				</td>
																				<td>
																					<select class="white_in w-fit" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','<?php echo $prod_id; ?>');" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,<?php echo $row_count; ?>);">
																						<option value="player" title="Player" <?php if ($edit_item["p_or_g"] == "player") {
																																	echo "selected";
																																} ?>>Player</option>
																						<option value="goalie" title="Goalie" <?php if ($edit_item["p_or_g"] == "goalie") {
																																	echo "selected";
																																} ?>>Goalie</option>
																					</select>
																				</td>
																				<td>
																					<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
																						<option value="0"></option>
																						<?php
																						$tmp_size = $a_size["1"];
																						if ($edit_item["p_or_g"] == "goalie") {
																							$tmp_size = $a_size["2"];
																						}

																						for ($i = 0; $i < sizeof($tmp_size); $i++) {
																						?>
																							<?php
																							if ($edit_item["sex"] == "youth" && $tmp_size[$i]["size_of_person"] == "youth") {
																							?>
																								<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																																							echo "selected";
																																						} ?>>
																									<?php echo $tmp_size[$i]["size_name"]; ?>
																								</option>
																							<?php
																							} elseif (($edit_item["sex"] == "male" || $edit_item["sex"] == "female") && $tmp_size[$i]["size_of_person"] == "adult") { ?>
																								<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																																							echo "selected";
																																						} ?>>
																									<?php echo $tmp_size[$i]["size_name"]; ?>
																								</option>
																						<?php
																							}
																						}
																						?>
																					</select>
																				</td>
																				<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo $edit_item["jersey_number"]; ?>"></td>
																				<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top1"]; ?>"></td>
																				<td>
																					<input class="white_in w-45  jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top1"]; ?>">
																				</td>
																				<td><input class="white_in" name="jersey_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top2"]; ?>"></td>
																				<td>
																					<input class="white_in  w-45 jersey_qty2_<?php echo $form_id; ?>" name="jersey_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top2"]; ?>">
																				</td>
																				<td>
																					<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_ssize[<?php echo $form_id; ?>][]">
																						<option value="0"></option>
																						<?php for ($i = 0; $i < sizeof($a_size["3"]); $i++) { ?>
																							<?php
																							if ($edit_item["sex"] == "youth" && ($a_size["3"][$i]["size_of_person"] == "youth") || $a_size["3"][$i]["size_of_person"] == "adult_youth") {
																							?>
																								<option value="<?php echo $a_size["3"][$i]["size_id"]; ?>" <?php if ($edit_item["bottom_size"] == $a_size["3"][$i]["size_id"]) {
																																								echo "selected";
																																							} ?>><?php echo $a_size["3"][$i]["size_name"]; ?></option>
																							<?php
																							} elseif (($edit_item["sex"] == "male" || $edit_item["sex"] == "female") && ($a_size["3"][$i]["size_of_person"] == "adult" || $a_size["3"][$i]["size_of_person"] == "adult_youth")) { ?>
																								<option value="<?php echo $a_size["3"][$i]["size_id"]; ?>" <?php if ($edit_item["bottom_size"] == $a_size["3"][$i]["size_id"]) {
																																								echo "selected";
																																							} ?>><?php echo $a_size["3"][$i]["size_name"]; ?></option>
																						<?php
																							}
																						}
																						?>
																					</select>
																				</td>
																				<td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_bottom1"]; ?>"></td>
																				<td>
																					<input class="white_in sock_qty_1" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'sock_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_bottom1"]; ?>">
																				</td>
																				<td><input class="white_in" name="sock_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_bottom2"]; ?>"></td>
																				<td>
																					<input class="white_in sock_qty2_<?php echo $form_id; ?>" name="sock_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'sock_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'sock_qty2_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_bottom2"]; ?>">
																				</td>
																				<td>
																					<select class="white_in w-fit"  name="select_ca[<?php echo $form_id; ?>][]">
																						<option value=""></option>
																						<option value="captain" <?php if ($edit_item["c_or_a"] == "captain") {
																													echo "selected";
																												} ?>>Captain</option>
																						<option value="assistant" <?php if ($edit_item["c_or_a"] == "assistant") {
																														echo "selected";
																													} ?>>Assistant</option>
																					</select>
																				</td>
																				<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["name_for_packing"]; ?>"></td>
																				<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["note"]; ?>"></td>
																			</tr>
																		<?php
																			$ttl_jersey = $ttl_jersey + $edit_item["qty_top1"];
																			$ttl_jersey_qty_2 = $ttl_jersey_qty_2 + $edit_item["qty_top2"];
																			$ttl_sock_qty = $ttl_sock_qty + $edit_item["qty_bottom1"];
																			$ttl_sock_qty_2 = $ttl_sock_qty_2 + $edit_item["qty_bottom2"];
																		}
																		?>
																	</tbody>
																	<tr>
																		<th colspan="2" onclick="return addItemRow(<?php echo $form_id; ?>,1);" class="text-center">

																			<i class="fa fa-plus-circle"></i>
																			<i id="loading_<?php echo $form_id; ?>" style="display: none; font-size: 12px;" class="fa fa-spinner fa-pulse fa-1x fa-fw"><br></i>
																			<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $num_item; ?>">
																			Add Row
																		</th>
																		<td style="width:150px;"></td>
																		<td style="width:125px;"></td>
																		<td style="width:90px;"></td>
																		<td style="width:72px;"></td>
																		<td></td>
																		<td></td>
																		<td style="width:50px;"></td>
																		<td> </td>
																		<td style="width:50px;"></td>
																		<td style="width:40px;"></td>
																		<td></td>
																		<td style="width:50px;"></td>
																		<td></td>
																		<td style="width:50px;"></td>
																		<td style="width:75px;"></td>
																		<td style="width:125px;"></td>

																	</tr>
																	<tr>

																		<th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>
																		<th></th>
																		<th></th>
																		<th></th>
																		<th></th>
																		<th></th>
																		<th id="total_jersey_qty_<?php echo $form_id; ?>"><?= $ttl_jersey ?></th>
																		<th></th>
																		<th id="total_jersey_qty2_<?php echo $form_id; ?>"><?= $ttl_jersey_qty_2 ?></th>
																		<th></th>
																		<th></th>
																		<th id="total_sock_qty_<?php echo $form_id; ?>"><?= $ttl_sock_qty ?></th>
																		<th></th>
																		<th id="total_sock_qty2_<?php echo $form_id; ?>"><?= $ttl_sock_qty_2 ?></th>
																		<th></th>
																		<th></th>
																		<th></th>

																	</tr>
																	<tr>
																		<th colspan="2" style="background: #F9F9F9 !important;   padding: 10px 0 10px 10px !important;">
																			<p class="mb-0" style="background: #222222;  text-align: center;  font-size: 13px;  font-weight: 500;  padding: 10px !important;  border-radius: 15px 0 0 15px;">
																				Special Comments (if any)
																			</p>
																		</th>
																		<th colspan="16" style="background-color: #F9F9F9 !important; padding: 10px  10px 10px 0 !important;"><input type="text" value="<?php echo $special_comm; ?>" name="special_comment[<?= $form_id ?>]" placeholder="Enter Special Comment here..." style="width: 100%; background:#FFF !important; border: 1px solid #eee; padding: 8px; border-radius: 0 20px 20px 0;text-align:left;"></th>

																	</tr>
																</table>

															</div>
														</div>
													</div>
												</center>
											<?php
											} else if (!empty($row_product['split_type']) && $row_product["split_type"] == "2") {

												$tmp_split = explode(",", $row_product["split_name"]);
												$split_name1 = $tmp_split[0];
												$split_name2 = $tmp_split[1];
											?>
												<center id="sameteam<?php echo $of_id; ?>">
													<div class="tab-content   splittype 2" id="tab-content">
														<div class="tab-pane <?php if ($fisrtid == $of_id) {
																					echo "active";
																				} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>">
															<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>">
																<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
																<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">
																<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
																<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">
																<table class="tbl_item_form bb" style="width: 100%;">
																	<tr class="theader">
																		<th class="tablecount"></th>
																		<th colspan="15">
																			<div class="d-inline">
																				<h6 class="my-auto">
																					
																				<span class="sp_outter_panel" id="sp_outter_panel<?php echo $form_id; ?>">
																					<span style="text-transform: capitalize;" id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
																					<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
																						<figure class="m-0 d-inline" onclick="return editFormName(<?php echo $form_id; ?>);"><img
																								src="images/vector/edit.png" alt="" style="width: 25px; margin:0 10px;">
																						</figure>
																					</span>
																					<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
																						<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
																						<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
																					</span>
																				  </span>

																				
																				
																				(<?php echo $row_product["prod_name"]; ?>) 
																			
																				<button type="button" class="bg-white d-inline deleteTable border-none  delete_form_btn">

																					<figure class="m-0 d-inline"><img
																							src="images/vector/delter.png" alt="">
																					</figure>

																				</button>
																			
																			
																			   </h6>
																			</div>
																		</th>
																		<th colspan="2">
																			<?php
																			if (sizeof($a_sub_user) > 0) {
																			?>
																				<div id="d_assign_select<?php echo $of_id; ?>" class="assign_tag">
																					<span class="Small">Assign to</span> <br>
																					<select name="select_assign[<?php echo $form_id; ?>]">
																						<option value="">==Select one==</option>
																						<?php
																						foreach ($a_sub_user as $sub_user_id => $nick_name) {
																							echo '<option value="' . $sub_user_id . '">' . $nick_name . '</option>';
																						}
																						?>
																					</select>
																				</div>
																			<?php
																			}
																			?>
																		</th>
																	</tr>
																	<tr>
																		<th onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">

																			<i class="fa fa-plus-circle"></i>
																			<i id="loading_<?php echo $form_id; ?>" style="display: none; font-size: 12px;" class="fa fa-spinner fa-pulse fa-1x fa-fw"><br></i>
																			<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $num_item; ?>">
																		</th>
																		<?php
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th style="width:158px;">Name on <?php echo $split_name1; ?></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th style="width:65px;">P or G</th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th style="width:100px;">Pattern Cut</th>
																		<?php
																		}
																		?>
																		<th style="width:70px;"><?php echo $split_name1; ?> Size</th>
																		<th><?php echo $split_name1; ?> #</th>
																		<th><?php echo $split_name1; ?> Color</th>
																		<th style="width:60px;">QTY</th>
																		<th style="width:70px;"><?php echo $split_name2; ?> Size</th>
																		<th><?php echo $split_name2; ?> Color</th>
																		<th style="width:60px;">QTY</th>
																		<th style="width:125px">Name For Packing</th>
																		<th>Notes</th>
																	</tr>
																	<tbody id="prod_item_<?php echo $form_id; ?>">
																		<?php
																		for ($m = 0; $m < $num_item; $m++) {

																			$row_count = $m + 1;
																			$edit_item = $a_item[$of_id][$m];
																		?>
																			<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
																				<td onclick="return deleteItemRow(<?php echo $form_id; ?>,<?php echo $edit_item["oi_id"]; ?>,<?php echo $prod_id; ?>,<?php echo $row_count; ?>,2);">
																					<i class="fa fa-minus-circle"></i>
																					<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="<?php echo $edit_item["oi_id"]; ?>">
																				</td>
																				<?php
																				if ($row_product["have_name"] == "1") {
																				?>
																					<td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo $edit_item["player_name"]; ?>"></td>
																				<?php
																				}

																				$tmp_size = array();
																				if (isset($a_size["1"])) {
																					//$tmp_size = $a_size["1"];
																					$tmp_size = $a_size[$spl_order];
																				}

																				if ($row_product["choose_pg"] == "1") {
																				?>
																					<td>
																						<select class="white_in w-fit" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,<?php echo $row_count; ?>);">
																							<option value="player" title="Player" <?php if ($edit_item["p_or_g"] == "player") {
																																		echo "selected";
																																	} ?>>Player</option>
																							<option value="goalie" title="Goalie" <?php if ($edit_item["p_or_g"] == "goalie") {
																																		echo "selected";
																																	} ?>>Goalie</option>
																						</select>
																					</td>
																					<?php
																					if ($edit_item["p_or_g"] == "goalie") {
																						$tmp_size = $a_size["2"];
																					}
																				}

																				if ($row_product["choose_mf"] == "1") {
																					?>
																					<td>
																						<select class="white_in w-fit" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','<?php echo $prod_id; ?>');">
																							<option value="youth" <?php if ($edit_item["sex"] == "youth") {
																														echo "selected";
																													} ?>>MEN-YOUTH</option>
																							<option value="male" <?php if ($edit_item["sex"] == "male") {
																														echo "selected";
																													} ?>>MEN-ADULT</option>
																							<option value="female_youth" <?php if ($edit_item["sex"] == "female_youth") {
																																echo "selected";
																															} ?>>WOMEN-YOUTH</option>
																							<option value="female" <?php if ($edit_item["sex"] == "female") {
																														echo "selected";
																													} ?>>WOMEN-ADULT</option>

																						</select>
																					</td>
																					<input type="hidden" value="uni" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
																				<?php
																					if ($edit_item["sex"] == "female") {
																						$tmp_size = $a_size["1"];
																						//$tmp_size = $a_size[$spl_order];
																					} elseif ($edit_item["sex"] == "youth" || $edit_item["sex"] == "adult_female") {
																						$tmp_size = $a_size["3"];
																					} elseif ($edit_item["sex"] == "male") {
																						$tmp_size = $a_size["1"];
																						//$tmp_size = $a_size[$spl_order];
																					}
																				}
																				?>

																				<td>
																					<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
																						<option value="0"></option>
																						<?php for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																							<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																																						echo "selected";
																																					} ?>><?php echo $tmp_size[$i]["size_name"]; ?></option>
																						<?php } ?>
																					</select>
																				</td>
																				<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo $edit_item["jersey_number"]; ?>"></td>
																				<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top1"]; ?>"></td>
																				<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top1"]; ?>"></td>
																				<td>
																					<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_ssize[<?php echo $form_id; ?>][]">
																						<option value="0"></option>
																						<?php for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																							<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["bottom_size"] == $tmp_size[$i]["size_id"]) {
																																						echo "selected";
																																					} ?>><?php echo $tmp_size[$i]["size_name"]; ?></option>
																						<?php } ?>
																					</select>
																				</td>
																				<td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_bottom1"]; ?>"></td>
																				<td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_bottom1"]; ?>"></td>
																				<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["name_for_packing"]; ?>"></td>
																				<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["note"]; ?>"></td>

																			</tr>
																		<?php
																			$ttl_top = $ttl_top + $edit_item["qty_top1"];;
																			$ttl_bottom = $ttl_bottom + $edit_item["qty_bottom1"];
																		}
																		?>
																	</tbody>
																	<tr>
																		<th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>

																		<?php
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_size"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_number"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}
																		?>
																		<th id="total_jersey_qty_<?php echo $form_id; ?>"><?= $ttl_top ?></th>
																		<th></th>
																		<th></th>
																		<th id="total_sock_qty_<?php echo $form_id; ?>"><?= $ttl_bottom ?></th>
																		<th></th>
																		<th></th>
																	</tr>
																	<!-- <tr>
																		<th></th>
																		<th>Special Comments <br> (if any)</th>
																		<th colspan="16" style="background-color: white;">
																			<input type="text" class="w-100 text-left" name="special_comment[<?= $form_id ?>]" value="<?php echo $special_comm; ?>" placeholder="Enter Special Comment here..."  style="text-align:left !important ; ">
																		</th>
																	</tr> -->


																	<tr>

																		<th colspan="2" style="background: #F9F9F9 !important;   padding: 10px 0 10px 10px !important;">
																			<p class="mb-0" style="background: #222222;  text-align: center;  font-size: 13px;  font-weight: 500;  padding: 10px !important;  border-radius: 15px 0 0 15px;">
																				Special Comments (if any)
																			</p>
																		</th>
																		<th colspan="10" style="background-color: #F9F9F9 !important; padding: 10px  10px 10px 0 !important;">
																			<input type="text" name="special_comment[<?= $form_id ?>]" value="<?php echo $special_comm; ?>" placeholder="Enter Special Comment here..." style=" width: 100%; background:#FFF !important; border: 1px solid #eee; padding: 8px; border-radius: 0 20px 20px 0;text-align:left;">
																		</th>
																	</tr>


																</table>

															</div>
														</div>
													</div>
												</center>
											<?php
											} else {

												$split_name =  !empty($row_product["split_name"]) ? $row_product["split_name"] : NULL ;
												
											?>
												<center id="sameteam<?php echo $of_id; ?>">
													<div class="tab-content  split-name " id="tab-content">
														<div class="tab-pane <?php if ($fisrtid == $of_id) {
																					echo "active";
																				} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>">
															<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>">
																<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
																<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">
																<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
																<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">
																<table class="tbl_item_form Bag-Hat-Accessories"  data-delete_id = "<?= customEncode($of_id) ?>"   style="width: 100%;">
																	<tr class="theader">
																		<th class="tablecount text-center"></th>
																		<th colspan="7">
																			<div class="d-inline">
																				
																
																				<h6 class="my-auto text-center">

																					<span class="sp_outter_panel" id="sp_outter_panel<?php echo $form_id; ?>">
																					<span style="text-transform: capitalize;" id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
																					<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
																						<figure class="m-0 d-inline" onclick="return editFormName(<?php echo $form_id; ?>);"><img
																								src="images/vector/edit.png" alt="" style="width: 25px; margin:0 10px;">
																						</figure>
																					</span>
																					<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
																						<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
																						<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
																					</span>
																				  </span>
																				
																				(<?php echo $row_product["prod_name"] ?? "" ?>)
																		
																				<button class="delete_form_btn bg-transparent border-none p-0 m-0" type="button">
																					<figure class="m-0 d-inline iconBTn" >
																						<img src="images/vector/delter.png" alt="" style="width: 30px; background: #FFF; padding: 6px;margin-left: 20px;">
																					</figure>
																				</button>
																			</h6>

																			</div>
																		</th>
																		<th colspan="2">
																			<?php
																			if (sizeof($a_sub_user) > 0) {
																			?>
																				<div id="d_assign_select<?php echo $of_id; ?>" class="assign_tag d-flex  align-items-center text-center flex-column justify-content-center">Assign to
																					<select name="select_assign[<?php echo $form_id; ?>]" class="customStyle">
																						<option value="">==Select one==</option>
																						<?php
																						foreach ($a_sub_user as $sub_user_id => $nick_name) {
																							echo '<option value="' . $sub_user_id . '">' . $nick_name . '</option>';
																						}
																						?>
																					</select>
																				</div>
																			<?php
																			}
																			?>
																		</th>
																	</tr>
																	<tr>
																		<th class="text-center" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">
																			<i class="fa fa-plus-circle"></i>
																			<i id="loading_<?php echo $form_id; ?>" style="display: none; font-size: 12px;" class="fa fa-spinner fa-pulse fa-1x fa-fw"><br></i>
																			<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $num_item; ?>">
																		</th>
																		<?php
																		if (!empty($row_product["have_name"])  &&  $row_product["have_name"] == "1") {
																		?>
																			<th style="width:158px;"><?php echo $split_name; ?></th>
																		<?php
																		}

																		if (!empty($row_product["choose_pg"])  && $row_product["choose_pg"] == "1") {
																		?>
																			<th style="width:65px;">P or G</th>
																		<?php
																		}

																		if (!empty($row_product["prod_id"]) && $row_product["prod_id"] == "2") {
																		?>
																			<th style="width:100px;">Pattern Cut</th>
																		<?php
																		}

																		if (!empty($row_product["choose_mf"]) &&  $row_product["choose_mf"] == "1") {
																		?>
																			<th style="width:100px;">Pattern Cut</th>
																		<?php
																		}

																		if (!empty($row_product["have_size"]) &&   $row_product["have_size"] == "1" && $prod_id == "4") {
																		?>
																			<th style="width:80px;" class="glued_body">Glue</th>
																		<?php
																		}

																		if (!empty($row_product["have_size"]) &&   $row_product["have_size"] == "1") {
																		?>
																			<th style="width:80px;"><?php echo $split_name; ?> Size</th>
																		<?php
																		}

																		if (!empty($row_product["have_number"]) &&  $row_product["have_number"] == "1") {
																		?>
																			<th><?php echo $split_name; ?> #</th>
																		<?php
																		}
																		?>
																		<th><?php echo $split_name; ?> Color</th>
																		<th style="width:50px;">QTY</th>
																		<?php
																		if (!empty($row_product["have_size"]) &&  $row_product["have_size"] == "1" && $prod_id == "4") {
																		?>
																			<th style="width:100px;">Name On Namebar</th>
																		<?php
																		}
																		?>
																		<th style="width:125px;">Name For Packing</th>
																		<th>Notes</th>
																	</tr>
																	<tbody id="prod_item_<?php echo $form_id; ?>">
																		<?php
																		for ($m = 0; $m < $num_item; $m++) {

																			$row_count = $m + 1;
																			$edit_item = $a_item[$of_id][$m];
																		?>
																			<tr class="text-center" id="prod_item_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
																				<td onclick="return deleteItemRow(<?php echo $form_id; ?>,<?php echo $edit_item["oi_id"]; ?>,<?php echo $prod_id; ?>,<?php echo $row_count; ?>,1);">
																					<i class="fa fa-minus-circle"></i>
																					<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="<?php echo $edit_item["oi_id"]; ?>">
																				</td>
																				<?php
																				if ($row_product["have_name"] == "1") {
																				?>
																					<td>
																						<?php
																						$sql_choices = "SELECT * FROM tbl_product_choices WHERE prod_id='" . $prod_id . "' AND enable=1 ORDER BY sort_no ASC;";
																						$rs_choices = $conn->query($sql_choices);

																						if ($rs_choices->num_rows > 0) {
																						?>
																							<select class="white_in  w-fit" id="product_id_<?php echo $form_id; ?>_<?php echo $row_count; ?>" onchange="return changePatternExtra('select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>','product_id_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_glue_num_<?php echo $form_id; ?>_<?php echo $row_count; ?>','select_jersey_num_<?php echo $form_id; ?>_<?php echo $row_count; ?>','<?php echo $prod_id; ?>');" name="player_name[<?php echo $form_id; ?>][]">
																								<?php
																								$matched_selected = 0;
																								while ($row_choices = $rs_choices->fetch_assoc()) {
																								?>
																									<option value="<?php echo $row_choices["choice_name"]; ?>" <?php if ($edit_item["player_name"] == $row_choices["choice_name"]) {
																																									echo "selected";
																																									$matched_selected = 1;
																																								} ?>>
																										<?php echo $row_choices["choice_name"]; ?>
																									</option>
																								<?php
																								}

																								if ($matched_selected == 0) {
																									echo '<option value="' . $edit_item["player_name"] . '" selected>' . $edit_item["player_name"] . '</option>';
																								}

																								?>
																							</select>
																						<?php
																						} else {
																						?>
																							<input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo $edit_item["player_name"]; ?>">
																						<?php
																						}
																						?>
																					</td>
																				<?php
																				}

																				$tmp_size = array();
																				if (isset($a_size["1"])) {
																					$tmp_size = $a_size["1"];
																				}


																				if ($row_product["choose_pg"] == "1") {
																				?>
																					<td>
																						<select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,<?php echo $row_count; ?>);">
																							<option value="player" title="Player" <?php if ($edit_item["p_or_g"] == "player") {
																																		echo "selected";
																																	} ?>>Player</option>
																							<option value="goalie" title="Goalie" <?php if ($edit_item["p_or_g"] == "goalie") {
																																		echo "selected";
																																	} ?>>Goalie</option>
																						</select>
																					</td>
																					<?php

																					if ($edit_item["p_or_g"] == "goalie") {
																						$tmp_size = $a_size["2"];
																					}
																				}

																				if ($row_product["prod_id"] == "2") {
																					?>
																					<td>
																						<select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_1','select_pg_<?php echo $form_id; ?>_1','select_jsize_<?php echo $form_id; ?>_1','select_ssize_<?php echo $form_id; ?>_1','<?php echo $prod_id; ?>');" id="select_mf_<?php echo $form_id; ?>_1" name="select_mf[<?php echo $form_id; ?>][]">
																							<option value="youth" <?php if ($edit_item["sex"] == "youth") {
																														echo "selected";
																													} ?>>YOUTH</option>
																							<option value="male" <?php if ($edit_item["sex"] == "male") {
																														echo "selected";
																													} ?>>ADULT</option>
																						</select>
																					</td>
																				<?php
																				}

																				if ($row_product["choose_mf"] == "1") {
																				?>
																					<td>
																						<select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,<?php echo $row_count; ?>,1);">
																							<option value="male" <?php if ($edit_item["sex"] == "male") {
																														echo "selected";
																													} ?>>Adult</option>
																							<option value="female" <?php if ($edit_item["sex"] == "female") {
																														echo "selected";
																													} ?>>Women</option>
																							<option value="youth" <?php if ($edit_item["sex"] == "youth") {
																														echo "selected";
																													} ?>>Youth</option>
																						</select>
																					</td>
																					<?php

																					if ($edit_item["sex"] == "female") {
																						$tmp_size = $a_size["2"];
																					}
																				}

																				if ($row_product["have_size"] == "1" && $prod_id == "4") {
																					?>

																					<td class="glued_body">
																						<select class="white_in" name="select_mf[<?php echo $form_id; ?>][]" id="select_glue_num_<?php echo $form_id; ?>_<?php echo $row_count; ?>" <?php if ($edit_item["sex"] == "na") {
																																																										echo "disabled";
																																																									} ?>>
																							<?php
																							if ($edit_item["sex"] == "na" || str_word_count($edit_item["sex"]) == 0) {
																							?>
																								<option value="na" <?php if ($edit_item["sex"] == "na") {
																														echo "selected";
																													} ?>>N/A</option>
																							<?php
																							}
																							if ($edit_item["sex"] == "na" || str_word_count($edit_item["sex"]) == 0) {
																							} else {
																							?>
																								<option value="Yes" <?php if ($edit_item["sex"] == "Yes") {
																														echo "selected";
																													} ?>>Yes</option>
																								<option value="No" <?php if ($edit_item["sex"] == "No") {
																														echo "selected";
																													} ?>>No</option>
																							<?php
																							}
																							?>
																						</select>
																					</td>
																				<?php
																				}

																				if ($row_product["have_size"] == "1" && $prod_id == "4") {
																				?>
																					<td>
																						<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
																							<?php
																							if ($edit_item["player_name"] == "Hats" || $edit_item["player_name"] == "Beanies" || $edit_item["player_name"] == "Pom Poms") {
																							?>
																								<option value="67" selected>OSFA</option>
																							<?php
																							} elseif ($edit_item["player_name"] == "Fight Strap") {
																							?>
																								<option value="0" selected>N/A</option>
																							<?php
																							} elseif ($edit_item["player_name"] == "Namebars" || $edit_item["player_name"] == "Captain Letters" || $edit_item["player_name"] == "Assistant Letters") {
																							?>
																								<option value="68" <?php
																													for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																									<?php if (68 == $edit_item["product_size_id"]) {
																															echo "selected";
																														} ?>
																									<?php } ?>>Youth</option>
																								<option value="97" <?php
																													for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																									<?php if (97 == $edit_item["product_size_id"]) {
																															echo "selected";
																														} ?>
																									<?php } ?>>Adult</option>
																							<?php
																							} elseif ($edit_item["player_name"] == "Sports Bags" || $edit_item["player_name"] == "Garment Bags") {
																							?>
																								<option value="0" selected>N/A</option>
																							<?php
																							} elseif ($edit_item["player_name"] == "Hockey Bags") {
																							?>
																								<option value="98" <?php
																													for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																									<?php if (98 == $edit_item["product_size_id"]) {
																															echo "selected";
																														} ?>
																									<?php } ?>>Junior</option>
																								<option value="99" <?php
																													for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																									<?php if (99 == $edit_item["product_size_id"]) {
																															echo "selected";
																														} ?>
																									<?php } ?>>Senior</option>
																								<option value="100" <?php
																													for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																									<?php if (100 == $edit_item["product_size_id"]) {
																															echo "selected";
																														} ?>
																									<?php } ?>>Goalie</option>
																								<option value="101" <?php
																													for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																									<?php if (101 == $edit_item["product_size_id"]) {
																															echo "selected";
																														} ?>
																									<?php } ?>>Coach</option>
																							<?php } ?>

																						</select>
																					</td>
																				<?php
																				}

																				if ($row_product["have_size"] == "1" && $prod_id != "4" && $prod_id != "2") {
																				?>
																					<td>
																						<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
																							<option value="0"></option>
																							<?php for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																								<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																																							echo "selected";
																																						} ?>><?php echo $tmp_size[$i]["size_name"]; ?></option>
																							<?php } ?>
																						</select>
																					</td>

																				<?php
																				}

																				if ($row_product["have_size"] == "1" && $prod_id == "2") {
																				?>
																					<td>
																						<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
																							<option value="0"></option>
																							<?php
																							for ($i = 0; $i < sizeof($tmp_size); $i++) { ?>
																								<?php
																								if ($edit_item["sex"] == "male" &&  $tmp_size[$i]["size_of_person"] == "adult") {
																								?>
																									<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																																								echo "selected";
																																							} ?>><?php echo $tmp_size[$i]["size_name"]; ?></option>
																								<?php } elseif ($edit_item["sex"] == "youth" &&  $tmp_size[$i]["size_of_person"] == "youth") { ?>
																									<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																																								echo "selected";
																																							} ?>><?php echo $tmp_size[$i]["size_name"]; ?></option>
																							<?php }
																							} ?>
																						</select>
																					</td>
																				<?php
																				}

																				if ($row_product["have_number"] == "1" && $prod_id != "4") {
																				?>
																					<td><input class="white_in" id="select_jersey_num_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo $edit_item["jersey_number"]; ?>"></td>
																				<?php
																				}
																				if ($row_product["have_number"] == "1" && $prod_id == "4") {
																				?>
																					<td><input class="white_in" <?php
																												if ($edit_item["player_name"] != "Sports Bags" || $edit_item["player_name"] != "Garment Bags" || $edit_item["player_name"] != "Hockey Bags") {
																													echo "readonly ";
																													echo "placeholder='N/A'";
																												}
																												?> name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" id="select_jersey_num_<?php echo $form_id; ?>_<?php echo $row_count; ?>" value="<?php echo $edit_item["jersey_number"]; ?>"></td>
																				<?php
																				}
																				?>
																				<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top1"]; ?>"></td>
																				<td><input class="white_in  w-45  jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top1"]; ?>"></td>
																				<?php
																				if ($row_product["have_size"] == "1" && $prod_id == "4") {
																				?>
																					<td class="namebar_td">
																						<input type="text" <?php
																											if ($edit_item["player_name"] != "Namebars") {
																												echo "readonly";
																											}
																											?> value='<?php if ($edit_item["p_or_g"] == "") {
																															echo "";
																														} else {
																															echo $edit_item["p_or_g"];
																														} ?>' class="white_in" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_pg[<?php echo $form_id; ?>][]">
																					</td>
																				<?php
																				} ?>
																				<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["name_for_packing"]; ?>"></td>
																				<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["note"]; ?>"></td>

																			</tr>
																		<?php
																			$TTL_SHELL = $TTL_SHELL + $edit_item["qty_top1"];
																		}
																		?>
																	</tbody>
																	<tr>
																		<th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>
																		<?php
																	if(!empty($row_product)){
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_size"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_number"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["prod_id"] == "2") {
																		?>
																			<th>
																			<?php
																		}
																		if ($row_product["prod_id"] == "4") {
																			?>
																			<th>
																			<?php
																		}
																	}
																			?>
																			<th id="total_jersey_qty_<?php echo $form_id; ?>"><?= $TTL_SHELL ?></th>
																			<th></th>
																			<th></th>
																			<th></th>
																	</tr>
																	<tr>

																		<th colspan="2" style="background: #F9F9F9 !important;   padding: 10px 0 10px 10px !important;">
																			<p class="mb-0" style="background: #222222;  text-align: center;  font-size: 13px;  font-weight: 500;  padding: 10px !important;  border-radius: 15px 0 0 15px;">
																				Special Comments (if any)
																			</p>
																		</th>
																		<th colspan="16" style="background-color: #F9F9F9 !important; padding: 10px  10px 10px 0 !important;">
																			<input type="text" name="special_comment[<?= $form_id ?>]" value="<?php echo $special_comm; ?>" placeholder="Enter Special Comment here..." style=" width: 100%; background:#FFF !important; border: 1px solid #eee; padding: 8px; border-radius: 0 20px 20px 0;text-align:left;">
																		</th>
																	</tr>
																</table>

															</div>
														</div>
													</div>
												</center>
											<?php
											}
										} else {


											/*$sql_of = "SELECT * FROM tbl_order_form WHERE of_id='".$of_id."' AND enable=1;";
													$rs_of = $conn->query($sql_of);
													$row_of = $rs_of->fetch_assoc();
													$prod_id = $row_of["prod_id"];*/

											$sql_oi = "SELECT tbl_draft_oi.*,tbl_top_size.size_name AS top_size,tbl_bottom_size.size_name AS bottom_size FROM tbl_draft_oi ";
											$sql_oi .= " LEFT JOIN tbl_size AS tbl_top_size ON tbl_draft_oi.product_size_id=tbl_top_size.size_id ";
											$sql_oi .= " LEFT JOIN tbl_size AS tbl_bottom_size ON tbl_draft_oi.bottom_size=tbl_bottom_size.size_id WHERE of_id='" . $of_id . "' ORDER BY tbl_draft_oi.of_id ASC,tbl_draft_oi.oi_id ASC;";
											$rs_oi = $conn->query($sql_oi);

											$a_item_show = array();
											while ($row_oi = $rs_oi->fetch_assoc()) {
												$a_item_show[] = $row_oi;

												
											}

											$num_item = sizeof($a_item_show);

											$assign_person = "";
											if ($a_data[$k]["assign_user_id"] != "") {

												$sql_sub_user = "SELECT nick_name FROM tbl_sub_user WHERE sub_user_id='" . $a_data[$k]["assign_user_id"] . "'; ";
												$rs_sub_user = $conn->query($sql_sub_user);
												if ($rs_sub_user->num_rows > 0) {
													$row_sub_user = $rs_sub_user->fetch_assoc();
													$assign_person = $row_sub_user["nick_name"];
												}
											}

											if ($prod_id == "1") {
											?>
											<input type="hidden" name="assigned_edit_of_id"  value="<?=$of_id?>">

												<center id="sameteam<?php echo $of_id; ?>">
													<div class="tab-content  prod-id-1" id="tab-content">
                                                       
														<div class="tab-pane <?php if ($fisrtid == $of_id) {
																					echo "active";
																				} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>">
															<div class="prod_card">

																<div class="   tableHeadingTr">

																	<h6 class="m-0">
																		<?php echo $form_name . " (" . $row_product["prod_name"] . ")";
																		if ($a_data[$k]["re_order_id"] != "") {
																			echo "(RE-ORDER)";
																		} ?>
																	</h6>

																	<div id="d_assign_tag<?php echo $of_id; ?>" class="assign_tag" style="margin-right:40px;">Assigned to <?php echo $assign_person; ?>
																	</div>
																	<div id="d_unassign_btn<?php echo $of_id; ?>" class="assign_tag" style="height: 28px; font-size: 16px; cursor: pointer;" title="Unassign by click here." onclick="return cancelAssign(<?php echo $of_id; ?>);">
																		<i class="fa fa-ban" style="color: #F55;"></i>
																	</div>
																</div>
																<div id="d_just_unassign<?php echo $of_id; ?>" class="assign_tag" style="display: none;">
																	You can edit after you "Save Data" or "Cancel" .
																</div>
																<table class="tbl_item_form_show">
																	<tr style="background-color: #EE0;">
																		<th style="width:150px; text-align: center;">Name on Jersey</th>
																		<th style="width:65px; text-align: center;">P or G</th>
																		<th style="width:72px; text-align: center;">Jersey Size</th>
																		<th style="text-align: center;">Jersey # (Number)</th>
																		<th style="text-align: center;">Jersey Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="text-align: center;">Jersey Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="width:40px; text-align: center;">Sock Size</th>
																		<th style="text-align: center;">Sock Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="text-align: center;">Sock Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="width:75px; text-align: center;">C or A</th>
																		<th>Notes</th>
																	</tr>
																	<tbody>
																		<?php
																		$sum_qty_top1 = 0;
																		$sum_qty_top2 = 0;
																		$sum_qty_bottom1 = 0;
																		$sum_qty_bottom2 = 0;

																		for ($m = 0; $m < $num_item; $m++) {

																			$row_count = $m + 1;
																			$edit_item = $a_item_show[$m];

																			$sum_qty_top1 += $edit_item["qty_top1"];
																			$sum_qty_top2 += $edit_item["qty_top2"];
																			$sum_qty_bottom1 += $edit_item["qty_bottom1"];
																			$sum_qty_bottom2 += $edit_item["qty_bottom2"];
																		?>
																			<tr>
																				<td><?php echo $edit_item["player_name"]; ?></td>
																				<td><?php echo ($edit_item["p_or_g"] != "") ? capitalize($edit_item["p_or_g"]) : ""; ?></td>
																				<td><?php echo $edit_item["top_size"]; ?></td>
																				<td><?php echo $edit_item["jersey_number"]; ?></td>
																				<td><?php echo $edit_item["color_top1"]; ?></td>
																				<td><?php echo $edit_item["qty_top1"]; ?></td>
																				<td><?php echo $edit_item["color_top2"]; ?></td>
																				<td><?php echo $edit_item["qty_top2"]; ?></td>
																				<td><?php echo $edit_item["bottom_size"]; ?></td>
																				<td><?php echo $edit_item["color_bottom1"]; ?></td>
																				<td><?php echo $edit_item["qty_bottom1"]; ?></td>
																				<td><?php echo $edit_item["color_bottom2"]; ?></td>
																				<td><?php echo $edit_item["qty_bottom2"]; ?></td>
																				<td><?php echo ($edit_item["c_or_a"] != "") ? capitalize($edit_item["c_or_a"]) : ""; ?></td>
																				<td><?php echo $edit_item["note"]; ?></td>
																			</tr>
																		<?php
																		}
																		?>
																	</tbody>
																	<tr>
																		<th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>

																		<th></th>
																		<th></th>
																		<th></th>
																		<th></th>
																		<th><?php echo $sum_qty_top1; ?></th>
																		<th></th>
																		<th><?php echo $sum_qty_top2; ?></th>
																		<th></th>
																		<th></th>
																		<th><?php echo $sum_qty_bottom1; ?></th>
																		<th></th>
																		<th><?php echo $sum_qty_bottom2; ?></th>
																		<th></th>
																		<th></th>
																	</tr>
																</table>
															</div>
														</div>
													</div>
												</center>
											<?php
											} else if ($row_product["split_type"] == "2") {

												$tmp_split = explode(",", $row_product["split_name"]);
												$split_name1 = $tmp_split[0];
												$split_name2 = $tmp_split[1];
											?>

											<input type="hidden" name="assigned_edit_of_id"  value="<?=$of_id?>">

												<center id="sameteam<?php echo $of_id; ?>">
													<div class="tab-content  split-type2" id="tab-content">
														<div class="tab-pane <?php if ($fisrtid == $of_id) {
																					echo "active";
																				} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>">
															<div class="prod_card" style="border:10px solid #AAA; border-radius: 5px; background-color: #AAA; width: 100%;">
																<center>
																	<h6>
																		<?php echo $form_name . " (" . $row_product["prod_name"] . ")";
																		if ($a_data[$k]["re_order_id"] != "") {
																			echo "(RE-ORDER)";
																		} ?>
																	</h6>
																</center>
																<div id="d_assign_tag<?php echo $of_id; ?>" class="assign_tag" style="margin-right:40px;">Assigned to <?php echo $assign_person; ?>
																</div>
																<div id="d_unassign_btn<?php echo $of_id; ?>" class="assign_tag" style="height: 28px; font-size: 16px; cursor: pointer;" title="Unassign by click here." onclick="return cancelAssign(<?php echo $of_id; ?>);">
																	<i class="fa fa-ban" style="color: #F55;"></i>
																</div>
																<div id="d_just_unassign<?php echo $of_id; ?>" class="assign_tag" style="display: none;">
																	You can edit after you "Save Data" or "Cancel" .
																</div>
																<table class="tbl_item_form_show">

																	<tr style="background-color: #EE0;">
																		<?php
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th style="width:150px; text-align: center;">Name on <?php echo $split_name1; ?></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th style="width:65px; text-align: center;">P or G</th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th style="width:100px; text-align: center;">Pattern Cut</th>
																		<?php
																		}
																		?>
																		<th style="width:80px; text-align: center;"><?php echo $split_name1; ?> Size</th>
																		<th style="text-align: center;"><?php echo $split_name1; ?> # </th>
																		<th style="text-align: center;"><?php echo $split_name1; ?> Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="width:80px; text-align: center;"><?php echo $split_name2; ?> Size</th>
																		<th style="text-align: center;"><?php echo $split_name2; ?> Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="text-align: center;">Notes</th>
																	</tr>
																	<tbody>
																		<?php
																		$sum_qty_top1 = 0;
																		$sum_qty_bottom1 = 0;

																		for ($m = 0; $m < $num_item; $m++) {

																			$row_count = $m + 1;
																			$edit_item = $a_item_show[$m];

																			$sum_qty_top1 += $edit_item["qty_top1"];
																			$sum_qty_bottom1 += $edit_item["qty_bottom1"];
																		?>
																			<tr>
																				<?php
																				if ($row_product["have_name"] == "1") {
																				?>
																					<td><?php echo $edit_item["player_name"]; ?></td>
																				<?php
																				}
																				if ($row_product["choose_pg"] == "1") {
																				?>
																					<td><?php echo ($edit_item["p_or_g"] != "") ? capitalize($edit_item["p_or_g"]) : ""; ?></td>
																				<?php

																				}

																				if ($row_product["choose_mf"] == "1") {
																				?>
																					<td><?php echo ($edit_item["sex"] != "") ? capitalize($edit_item["sex"]) : ""; ?></td>
																				<?php

																				}
																				?>
																				<td><?php echo $edit_item["top_size"]; ?></td>
																				<td><?php echo $edit_item["jersey_number"]; ?></td>
																				<td><?php echo $edit_item["color_top1"]; ?></td>
																				<td><?php echo $edit_item["qty_top1"]; ?></td>
																				<td><?php echo $edit_item["bottom_size"]; ?></td>
																				<td><?php echo $edit_item["color_bottom1"]; ?></td>
																				<td><?php echo $edit_item["qty_bottom1"]; ?></td>
																				<td><?php echo $edit_item["note"]; ?></td>

																			</tr>
																		<?php
																		}
																		?>
																	</tbody>
																	<tr>

																		<th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>

																		<?php
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_size"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_number"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}
																		?>
																		<th style="text-align: center;"><?php echo $sum_qty_top1; ?></th>
																		<th></th>
																		<th></th>
																		<th style="text-align: center;"><?php echo $sum_qty_bottom1; ?></th>
																		<th></th>
																	</tr>
																</table>
															</div>
														</div>
													</div>
												</center>
											<?php
											} else {

												$split_name = $row_product["split_name"];
											?>
											<input type="hidden" name="assigned_edit_of_id"  value="<?=$of_id?>">

												<center id="sameteam<?php echo $of_id; ?>">
													<div class="tab-content split-name" id="tab-content">
														<div class="tab-pane <?php if ($fisrtid == $of_id) {
																					echo "active";
																				} ?>" id="fill-tabpanel-<?php echo $of_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $of_id; ?>">
															<div class="prod_card">
																<center>
																	<h6>
																		<?php echo $form_name . " (" . $row_product["prod_name"] . ")";
																		if ($a_data[$k]["re_order_id"] != "") {
																			echo "(RE-ORDER)";
																		} ?>
																	</h6>
																</center>
																<div id="d_assign_tag<?php echo $of_id; ?>" class="assign_tag" style="margin-right:40px;">Assigned to <?php echo $assign_person; ?>
																</div>
																<div id="d_unassign_btn<?php echo $of_id; ?>" class="assign_tag" style="height: 28px; font-size: 16px; cursor: pointer;" title="Unassign by click here." onclick="return cancelAssign(<?php echo $of_id; ?>);">
																	<i class="fa fa-ban" style="color: #F55;"></i>
																</div>
																<div id="d_just_unassign<?php echo $of_id; ?>" class="assign_tag" style="display: none;">
																	You can edit after you "Save Data" or "Cancel" .
																</div>
																<table class="tbl_item_form_show">
																	<tr style="background-color: #EE0;">
																		<?php
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th style="width:150px; text-align: center;">Name on <?php echo $split_name; ?></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th style="width:65px; text-align: center;">P or G</th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th style="width:100px; text-align: center;">Pattern Cut</th>
																		<?php
																		}

																		if ($row_product["have_size"] == "1") {
																		?>
																			<th style="width:80px; text-align: center;"><?php echo $split_name; ?> Size</th>
																		<?php
																		}

																		if ($row_product["have_number"] == "1") {
																		?>
																			<th style="text-align: center;"><?php echo $split_name; ?> # </th>
																		<?php
																		}
																		?>
																		<th style="text-align: center;"><?php echo $split_name; ?> Color</th>
																		<th style="width:50px; text-align: center;">QTY</th>
																		<th style="text-align: center;">Notes</th>
																	</tr>
																	<tbody>
																		<?php
																		$sum_qty_top1 = 0;

																		for ($m = 0; $m < $num_item; $m++) {

																			$row_count = $m + 1;
																			$edit_item = $a_item_show[$m];

																			$sum_qty_top1 += $edit_item["qty_top1"];
																		?>
																			<tr>

																				<?php
																				if ($row_product["have_name"] == "1") {
																				?>
																					<td><?php echo $edit_item["player_name"]; ?></td>
																				<?php
																				}

																				if ($row_product["choose_pg"] == "1") {
																				?>
																					<td><?php echo ($edit_item["p_or_g"] != "") ? capitalize($edit_item["p_or_g"]) : ""; ?></td>
																				<?php

																				}

																				if ($row_product["choose_mf"] == "1") {
																				?>
																					<td><?php echo ($edit_item["sex"] != "") ? capitalize($edit_item["sex"]) : ""; ?></td>
																				<?php

																				}

																				if ($row_product["have_size"] == "1") {
																				?>
																					<td><?php echo $edit_item["top_size"]; ?></td>
																				<?php
																				}

																				if ($row_product["have_number"] == "1") {
																				?>
																					<td><?php echo $edit_item["jersey_number"]; ?></td>
																				<?php
																				}
																				?>
																				<td><?php echo $edit_item["color_top1"]; ?></td>
																				<td><?php echo $edit_item["qty_top1"]; ?></td>
																				<td><?php echo $edit_item["note"]; ?></td>

																			</tr>
																		<?php
																		}
																		?>
																	</tbody>
																	<tr>
																		<th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>

																		<?php
																		if ($row_product["have_name"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_pg"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["choose_mf"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		if ($row_product["have_size"] == "1") {
																		?>
																			<th></th>
																		<?php
																		}

																		?>
																		<th style="text-align: center;"><?php echo $sum_qty_top1; ?></th>
																		<th></th>
																	</tr>
																</table>
															</div>
														</div>
													</div>
												</center>
									<?php
											}
										}
									}
									?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2 col-sm-12" style="padding-top: 10px;">
								<a href="?vp=bWFuYWdlX29yZGVy" class="btn iconBTn themeBtn2grey" onclick="return confirm('Your updated info will be cancel. OK?');">
									<figure class="m-0"><img src="images/vector/cancel.png" alt=""></figure> Cancel
								</a>
							</div>
							<div class="col-md-10 col-sm-12" style="padding-top: 10px; text-align:right;">
								<button type="button" class="btn  iconBTn themeBtn2" onclick="return saveDraft(event);" id="btn_save_data">Review <figure class="m-0"><img src="images/vector/saveGreen.png" alt=""></figure></button>
								<button type="button" class="btn themeBtn iconBTn" onclick="return submitOrder();" id="btn_submit_order">Continue <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure></button>
							</div>
							<input type="hidden" name="is_submit_order" id="is_submit_order" value="no">
						</div>
					</div>
				</div>


			</div>
	</div>
	</form>
</div>
</div>



<div class="modal fade bd-example-modal-sm" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Note</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" style="text-align: center;">
				<h4>Women’s cuts available for full team orders/reorders. </h4>
			</div>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>


 function getReorder() {
        const selectedValue = document.getElementById('reorder_num').value;
        const previewLink = document.getElementById('PreviewPdfMdoal ');
        if (selectedValue) {

            $.ajax({
                type: "POST",
                dataType: "html",
                url: "getOrder_code.php",
                data: {
                    "order_main_code": selectedValue
                },
                success: function(resp) {
                    previewLink.href = `https://locker.jog-joinourgame.com/view/?id=${encodeURIComponent(resp)}`;
                }
            });

            // Replace with your actual PDF preview URL pattern

        } else {
            previewLink.href = "";
        }
    }
getReorder(); 


	function editFormName(form_id) {

		var form_name = $('#form_name_list' + form_id).val();
		var input_tag = '<input type="text" id="input_fn_' + form_id + '" style="width:180px;" value="' + form_name + '">';

		$('#show_edit_form_name' + form_id).html(input_tag);

		$('#sp_save_edit_fn_panel' + form_id).show();
		$('#sp_edit_fn_panel' + form_id).hide();

		$('#sp_outter_panel' + form_id).css("border", "solid 1px #A50").css("padding", "3px").css("background-color", "#F90");
	}

	function editFormNameCancel(form_id) {

		var form_name = $('#form_name_list' + form_id).val();
		$('#show_edit_form_name' + form_id).html(form_name);

		$('#sp_save_edit_fn_panel' + form_id).hide();
		$('#sp_edit_fn_panel' + form_id).show();

		$('#sp_outter_panel' + form_id).css("border", "0px").css("padding", "0px").css("background-color", "transparent");
	}

	function deleteProductCard(form_id) {

		if (confirm("Are you sure?")) {

			var of_id = $('#edit_of_id' + form_id).val();
			if (of_id != "new") {
				var of_id_delete = $('#of_id_delete').val();
				if (of_id_delete != "") {
					of_id_delete = of_id_delete + "," + of_id;
				} else {
					of_id_delete = of_id;
				}
				$('#of_id_delete').val(of_id_delete);
			}

			$('#prod_card' + form_id).remove();
		}

	}
	let hasModalBeenShown = false;

	function changePattern(pattern_id, p_g_id, jersey_size_id, sock_size_id, prod_id) {
		var select_id = pattern_id;
		var select = '#' + select_id;
		var pattern_cut = $(select).val();
		var p_or_g = $('#' + p_g_id).val();
		var prod_id = prod_id;
		if (prod_id == "1") {
			var htmls = '';
			if (p_or_g == "player") {
				htmls += '<option value="player" selected title="Player">Player</option>';
				htmls += '<option value="goalie" title="Goalie">Goalie</option>';
				$('#' + p_g_id).empty().append(htmls);
			} else {
				htmls += '<option value="player" title="Player">Player</option>';
				htmls += '<option value="goalie" selected title="Goalie">Goalie</option>';
				$('#' + p_g_id).empty().append(htmls);
			}
		}

		if (pattern_cut == 'female' && prod_id == 1 && !hasModalBeenShown) {
			$('#exampleModal').modal('show');
			hasModalBeenShown = true;
		}
		// else if(prod_id=="1" && pattern_cut=="youth"){
		//     var htmls='';
		//     htmls+='<option value="player" title="Player">Player</option>';
		//     $('#'+p_g_id).empty();
		//     $('#'+p_g_id).append(htmls);
		// }
		pattern_cut = $(select).val();
		p_or_g = $('#' + p_g_id).val();
		$.ajax({
			type: 'POST',
			data: {
				pattern_cut: pattern_cut,
				p_or_g: p_or_g,
				prod_id: prod_id
			},
			url: 'ajax/add_order/change_pattern.php',
			success: function(response) {
				var response = JSON.parse(response);
				if (response.status == 1) {
					var html = '';
					html += '<option value="0"></option>';
					for (var i = 0; i < response.jersey_id.length; i++) {
						html += '<option value="' + response.jersey_id[i] + '">' + response.jersey_size[i] + '</option>';
					}

					var sock = '';
					sock += '<option value="0"></option>';
					for (var z = 0; z < response.sock_id.length; z++) {
						sock += '<option value="' + response.sock_id[z] + '">' + response.sock_size[z] + '</option>';
					}
					$('#' + jersey_size_id).empty();
					$('#' + jersey_size_id).append(html);

					$('#' + sock_size_id).empty();
					$('#' + sock_size_id).append(sock);

				} else {
					alert('Something Went Wrong');
				}

			}
		})
	}

	function changePatternExtra(select_p_g, product_id, product_size_id, glue_id, jersey_num_id, prod_id) {
		var select_p_g = select_p_g;
		var product_id = product_id;
		var product_size_id = product_size_id;
		var glue_id = glue_id;
		var jersey_num_id = jersey_num_id;
		var prod_id = prod_id;
		var product_name = $('#' + product_id).val();
		var html = '';
		if (product_name == "Hats" || product_name == "Beanies" || product_name == "Pom Poms") {
			$('#' + glue_id).prop("disabled", true);
			html = '';
			html += '<option value="0" selected>N/A</option>';
			$('#' + glue_id).empty();
			$('#' + glue_id).append(html);

			$('#' + jersey_num_id).prop("readonly", true);
			$('#' + jersey_num_id).val("N/A");

			html = '';
			html += '<option value="67">OSFA</option>';
			$('#' + product_size_id).empty();
			$('#' + product_size_id).append(html);

			$('#' + select_p_g).prop("readonly", true);
			$('#' + select_p_g).val("N/A");

			// var glue_name = $('#'+glue_id).removeAttr("disabled");
			// var html = '';
			// html+='<option value="Yes">Yes</option>';
			// html+='<option value="No>No</option>';
			// $('#'+glue_id).empty();
			// $('#'+glue_id).append(html);
		} else if (product_name == "Fight Strap") {
			$('#' + glue_id).prop("disabled", true);
			html = '';
			html += '<option value="0" selected>N/A</option>';
			$('#' + glue_id).empty();
			$('#' + glue_id).append(html);

			$('#' + jersey_num_id).prop("readonly", true);
			$('#' + jersey_num_id).val("N/A");

			$('#' + product_size_id).prop("disabled", true);
			html = '';
			html += '<option value="0" selected>N/A</option>';
			$('#' + product_size_id).empty();
			$('#' + product_size_id).append(html);

			$('#' + select_p_g).prop("readonly", true);
			$('#' + select_p_g).val("N/A");
		} else if (product_name == "Namebars") {
			$('#' + jersey_num_id).prop("readonly", true);
			$('#' + jersey_num_id).val("N/A");

			$('#' + product_size_id).prop("disabled", false);
			html = '';
			html += '<option value="68" selected>Youth</option>';
			html += '<option value="97">Adult</option>';
			$('#' + product_size_id).empty();
			$('#' + product_size_id).append(html);

			var glue_name = $('#' + glue_id).removeAttr("disabled");
			html = '';
			html += '<option value="Yes">Yes</option>';
			html += '<option value="No">No</option>';
			$('#' + glue_id).empty();
			$('#' + glue_id).append(html);

			$('#' + select_p_g).prop("readonly", false);
			$('#' + select_p_g).val("");
			$('#' + select_p_g).attr("placeholder", "Input Nameber Name...");
		} else if (product_name == "Captain Letters" || product_name == "Assistant Letters") {
			$('#' + product_size_id).prop("disabled", false);
			html = '';
			html += '<option value="68" selected>Youth</option>';
			html += '<option value="97">Adult</option>';
			$('#' + product_size_id).empty();
			$('#' + product_size_id).append(html);

			var glue_name = $('#' + glue_id).removeAttr("disabled");
			html = '';
			html += '<option value="Yes">Yes</option>';
			html += '<option value="No">No</option>';
			$('#' + glue_id).empty();
			$('#' + glue_id).append(html);

			$('#' + jersey_num_id).prop("readonly", true);
			$('#' + jersey_num_id).val("N/A");

			$('#' + select_p_g).prop("readonly", true);
			$('#' + select_p_g).val("N/A");
		} else if (product_name == "Sports Bags" || product_name == "Garment Bags") {
			$('#' + glue_id).prop("disabled", true);
			html = '';
			html += '<option value="0" selected>N/A</option>';
			$('#' + glue_id).empty();
			$('#' + glue_id).append(html);

			$('#' + product_size_id).prop("disabled", true);
			html = '';
			html += '<option value="0" selected>N/A</option>';
			$('#' + product_size_id).empty();
			$('#' + product_size_id).append(html);

			$('#' + jersey_num_id).removeAttr("readonly");
			$('#' + jersey_num_id).val("");
			$('#' + jersey_num_id).attr("placeholder", "Input Number Here...");

			$('#' + select_p_g).prop("readonly", true);
			$('#' + select_p_g).val("N/A");
		} else if (product_name == "Hockey Bags") {
			$('#' + product_size_id).prop("disabled", false);
			html = '';
			html += '<option value="98" selected>Junior</option>';
			html += '<option value="99">Senior</option>';
			html += '<option value="100">Goalie</option>';
			html += '<option value="101">Coach</option>';
			$('#' + product_size_id).empty();
			$('#' + product_size_id).append(html);

			$('#' + glue_id).prop("disabled", true);
			html = '';
			html += '<option value="0" selected>N/A</option>';
			$('#' + glue_id).empty();
			$('#' + glue_id).append(html);

			$('#' + jersey_num_id).removeAttr("readonly");
			$('#' + jersey_num_id).val("");
			$('#' + jersey_num_id).attr("placeholder", "Input Number Here...");

			$('#' + select_p_g).prop("readonly", true);
			$('#' + select_p_g).val("N/A");
		}
	}

	function chooseUploadProcess() {

		if ($('#input_on_team_name').val() == "") {
			alert("Please fill the Team Name.");
			return false;
		}
		$('.teamTabsSection').show(300);
		var prod_id = $('#prod_id').val();
		var form_id = $('#form_id_inc').val();
		var on_team_name = window.btoa($('#input_on_team_name').val());
		var on_year = window.btoa($('#input_on_year').val());
		const teamId = $('#teamTab li').length;
		$('#teamTab .nav-link').removeClass('active');
		$('#table_showing .tab-pane').removeClass('active');

		const teamTab = `
			<li class="nav-item" role="presentation">
				<a class="nav-link  active" id="fill-tab-${form_id}" data-bs-toggle="tab" href="#fill-tabpanel-${form_id}" role="tab" aria-controls="fill-tabpanel-${form_id}" aria-selected="true"> Team ${form_id} </a>
			</li>`;

		$('#teamTab').append(teamTab);


		$.ajax({
			type: "POST",
			dataType: "html",
			url: "ajax/manage_order/new_card_upload.php",
			data: {
				"prod_id": prod_id,
				"form_id": form_id,
				"on_team_name": on_team_name,
				"on_year": on_year
			},
			success: function(resp) {

				$('#table_showing').append(resp);
				$('#input_on_team_name').val("");
				$('#input_on_year').val("");
				form_id = parseInt(form_id);
				form_id++;
				$('#form_id_inc').val(form_id);

				$('#form_id_inc').val(form_id);

				tmp_num_form = parseInt($('#tmp_num_form').val());
				tmp_num_form++;
				$('#tmp_num_form').val(tmp_num_form);

			}
		});
	}

	function editFormNameDone(form_id) {

		var new_form_name = $('#input_fn_' + form_id).val();

		$('#form_name_list' + form_id).val(new_form_name);
		$('#show_edit_form_name' + form_id).html(new_form_name);

		$('#sp_save_edit_fn_panel' + form_id).hide();
		$('#sp_edit_fn_panel' + form_id).show();

		$('#sp_outter_panel' + form_id).css("border", "0px").css("padding", "0px").css("background-color", "transparent");
	}



    function base64EncodeUnicode(str) {
        return btoa(
            new TextEncoder().encode(str)
            .reduce((data, byte) => data + String.fromCharCode(byte), '')
        );
    }



	$(document).ready(function() {
		$('#showTeamTabsSection').click(function() {
			let  isValid = CheckOrderFormValidation();

            if (!isValid) {
                console.log("Validation issue");
                return false;
            }


			$('.teamTabsSection').show(300); // Adjust duration as needed
			var prod_id = $('#prod_id').val();
			var form_id = $('#form_id_inc').val();
			var on_team_name = base64EncodeUnicode($('#input_on_team_name').val());
			var on_year = window.btoa($('#input_on_year').val());

			$('.teamTabsSection').show(300); // Show the section

			const teamId = $('#teamTab li').length;
			//const teamId = `team${nextTeamNumber}`;
			// const teamTab = `
			//     <li class="nav-item" role="presentation">
			//         <a class="nav-link" id="${teamId}-tab" data-bs-toggle="tab" href="#${teamId}" role="tab" aria-controls="${teamId}" aria-selected="false">
			//             Team ${nextTeamNumber}
			//         </a>
			//     </li>`;
			$('#teamTab .nav-link').removeClass('active');
			$('#table_showing .tab-pane').removeClass('active');

			const teamTab = `
				<li class="nav-item" role="presentation">
					<a class="nav-link  active" id="fill-tab-${teamId}" data-bs-toggle="tab" href="#fill-tabpanel-${teamId}" role="tab" aria-controls="fill-tabpanel-${teamId}" aria-selected="true"> Team ${teamId} </a>
				</li>`;

			$('#teamTab').append(teamTab);

			$.ajax({
				type: "POST",
				dataType: "html",
				url: "ajax/add_order/new_card.php",
				data: {
					"prod_id": prod_id,
					"form_id": form_id,
					"on_team_name": on_team_name,
					"on_year": on_year,
					"teamno": teamId
				},
				success: function(resp) {

					//if(resp=="success"){

					$('#table_showing').append(resp);
					// $('#input_on_team_name').val("");
					// $('#input_on_year').val("");
					form_id = parseInt(form_id);
					form_id++;
					$('#form_id_inc').val(form_id);
					$('#form_id_inc_modal').val(form_id);
					tmp_num_form = parseInt($('#tmp_num_form').val());
					tmp_num_form++;
					$('#tmp_num_form').val(tmp_num_form);
					// }else{
					//     alert(resp.msg);
					// }
				}
			});
		});
	});

	function changePG(form_id, row_id) {

		var obj_size = $.parseJSON(window.atob($('#obj_size' + form_id).val()));

		var inner_select = '';

		if ($('#select_pg_' + form_id + '_' + row_id).val() == "player") {

			inner_select += '<option value="0"></option>';
			for (var i = 0; i < obj_size[1].length; i++) {
				inner_select += '<option value="' + obj_size[1][i].size_id + '">' + obj_size[1][i].size_name + '</option>';
			}

		} else if ($('#select_pg_' + form_id + '_' + row_id).val() == "goalie") {

			inner_select += '<option value="0"></option>';
			for (var i = 0; i < obj_size[2].length; i++) {
				inner_select += '<option value="' + obj_size[2][i].size_id + '">' + obj_size[2][i].size_name + '</option>';
			}

		}

		if (inner_select != '') {
			$('#select_jsize_' + form_id + '_' + row_id).html(inner_select);
		}

	}
 
	function movetoteam() {
        let isValid = CheckBillingFormValidation(); 
		if(!isValid){
			 return false ; 
		}


		const orderTab = document.querySelector('#team-tab');
		const tab = new bootstrap.Tab(orderTab);
		tab.show();
		return false;
	}




	$(document).on('click', '.movetoOrderItem', function() {

	  let isValid = CheckBillingFormValidation(); 
	
	 
	  if(!isValid){
			 return false ; 
		}



		const orderTab = document.querySelector('#order-tab');

		const tab = new bootstrap.Tab(orderTab);
		tab.show();
		return false;
	});


	function saveDraft(eve) {
		eve.preventDefault();

	


		if ($('#req_due_date').val() == "") {
			$('.req_errormsg').text('Please input Request Due date.');
			const orderTab = document.querySelector('#order-tab');
			const tab = new bootstrap.Tab(orderTab);
			tab.show();
			return false;
		}

		if ($('#game_event_date').val() == "") {
			$('.game_errormsg').text('Please input Game/Event date.');
			const orderTab = document.querySelector('#order-tab');
			const tab = new bootstrap.Tab(orderTab);
			tab.show();
			return false;
		}

		if ($('#tmp_num_form').val() == "0") {
			alert("There is no form to submit.");
			return false;
		}

		//var bi_company_name = $('#bi_company_name').val();
		var bi_contact = $('#bi_contact').val();
		var bi_address = $('#bi_address').val();
		var bi_city = $('#bi_city').val();
		var bi_country = $('#bi_country').val();
		var bi_zip_code = $('#bi_zip_code').val();
		var bi_tel = $('#bi_tel').val();
		var bi_email = $('#bi_email').val();
		//var bi_tax_id = $('#bi_tax_id').val();

		//var de_company_name = $('#de_company_name').val();
		var de_contact = $('#de_contact').val();
		var de_address = $('#de_address').val();
		var de_city = $('#de_city').val();
		var de_country = $('#de_country').val();
		var de_zip_code = $('#de_zip_code').val();
		var de_tel = $('#de_tel').val();
		var de_email = $('#de_email').val();
		//var de_tax_id = $('#de_tax_id').val();

		if (bi_contact == "" || bi_address == "" || bi_city == "" || bi_country == "" || bi_zip_code == "" || bi_tel == "" || bi_email == "" || de_contact == "" || de_address == "" || de_city == "" || de_country == "" || de_zip_code == "" || de_tel == "" || de_email == "") {
			alert("All data is required except Company and TAX ID.");
			return false;
		}

		var check_file_blank = 0;
		var check_not_excel = 0;

		var file_ext_allow = ['xls', 'xlsx'];

		$('.file_field').each(function() {

			if ($(this).val() == "") {

				check_file_blank = 1;
				return false;
			}
			if ($.inArray($(this).val().split('.').pop().toLowerCase(), file_ext_allow) == -1) {

				check_not_excel = 1;
				return false;
			}

		});

		if (check_file_blank == 1) {
			alert("Please choose file");
			return false;
		}

		if (check_not_excel == 1) {
			alert("Allow only Excel file [xls or xlsx]");
			return false;
		}

		$('#btn_save_data').attr("disabled", true).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Saving...');

		$('#form_manage_save').attr("action", "ajax/manage_order/submit_draft.php");
		$('#form_manage_save').submit();


				// $('#form_manage_save').on('submit', function(e) {
				//     e.preventDefault();

				//    console.log("form sunmit "); 
				// $.ajax({
				// 		url: "./ajax/manage_order/submit_draft.php",
				// 		type: "POST",
				// 		data: $(this).serialize(),
				// 		success: function(res) {
				// 		console.log(res);
				// }
				// });
				// });

				// $('#form_manage_save').submit();

	}

	function submitOrder() {

		if (confirm("Are you sure you want to submit? Changes will not be allowed after clicking Submit.")) {

			if ($('#req_due_date').val() == "") {
				$('.req_errormsg').text('Please input Request Due date.');
				const orderTab = document.querySelector('#order-tab');
				const tab = new bootstrap.Tab(orderTab);
				tab.show();
				return false;
			}

			if ($('#game_event_date').val() == "") {
				$('.game_errormsg').text('Please input Game/Event date.');
				const orderTab = document.querySelector('#order-tab');
				const tab = new bootstrap.Tab(orderTab);
				tab.show();
				return false;
			}

			if ($('#tmp_num_form').val() == "0") {
				alert("There is no form to submit.");
				return false;
			}

			//var bi_company_name = $('#bi_company_name').val();
			var bi_contact = $('#bi_contact').val();
			var bi_address = $('#bi_address').val();
			var bi_city = $('#bi_city').val();
			var bi_country = $('#bi_country').val();
			var bi_zip_code = $('#bi_zip_code').val();
			var bi_tel = $('#bi_tel').val();
			var bi_email = $('#bi_email').val();
			//var bi_tax_id = $('#bi_tax_id').val();

			//var de_company_name = $('#de_company_name').val();
			var de_contact = $('#de_contact').val();
			var de_address = $('#de_address').val();
			var de_city = $('#de_city').val();
			var de_country = $('#de_country').val();
			var de_zip_code = $('#de_zip_code').val();
			var de_tel = $('#de_tel').val();
			var de_email = $('#de_email').val();
			//var de_tax_id = $('#de_tax_id').val();

			if (bi_contact == "" || bi_address == "" || bi_city == "" || bi_country == "" || bi_zip_code == "" || bi_tel == "" || bi_email == "" || de_contact == "" || de_address == "" || de_city == "" || de_country == "" || de_zip_code == "" || de_tel == "" || de_email == "") {
				alert("All data is required except Company and TAX ID.");
				return false;
			}

			$('#btn_submit_order').attr("disabled", true).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Submiting...');

			$('#is_submit_order').val("yes");
			
			$('#form_manage_save').attr("action", "ajax/manage_order/submit_draft.php");
			$('#form_manage_save').submit();
		}

	}

	function saveDraftSuccessBeforeSubmitOrder() {

		var draft_id = $('#edit_draft_id').val();


		$.ajax({
			type: "POST",
			dataType: "json",
			url: "ajax/manage_order/submit_order.php",
			data: {
				"draft_id": draft_id
			},
			success: function(resp2) {

				if (resp2.result == "success") {
					window.location.href = "?vp=<?php echo base64_encode('manage_order'); ?>";
				} else {
					alert(resp2.msg);
					$('#btn_submit_order').attr("disabled", false).html('Submit Order');
				}

			}
		});
	}

	function changeMF(form_id, row_id, split_type) {

		var obj_size = $.parseJSON(window.atob($('#obj_size' + form_id).val()));

		var inner_select = '';

		if ($('#select_mf_' + form_id + '_' + row_id).val() == "male") {

			inner_select += '<option value="0"></option>';
			for (var i = 0; i < obj_size[1].length; i++) {
				inner_select += '<option value="' + obj_size[1][i].size_id + '">' + obj_size[1][i].size_name + '</option>';
			}

		} else if ($('#select_mf_' + form_id + '_' + row_id).val() == "female") {

			inner_select += '<option value="0"></option>';
			for (var i = 0; i < obj_size[2].length; i++) {
				inner_select += '<option value="' + obj_size[2][i].size_id + '">' + obj_size[2][i].size_name + '</option>';
			}

		}

		if (inner_select != '') {
			$('#select_jsize_' + form_id + '_' + row_id).html(inner_select);
			$('#select_ssize_' + form_id + '_' + row_id).html(inner_select);
		}

	}

	function cancelAssign(of_id) {

		if (confirm("Be careful! If this form is editing, you will lost the data.")) {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/manage_order/unassign_form.php",
				data: {
					"of_id": of_id
				},
				success: function(resp) {

					if (resp.result == "success") {
						$('#d_assign_tag' + of_id).hide();
						$('#d_unassign_btn' + of_id).hide();
						$('#d_just_unassign' + of_id).show();
					} else {
						alert(resp.msg);
					}

				}
			});
		}

	}

	function addItemRow(form_id, prod_id) {

		var num_item = $('#num_item_' + form_id).val();

		$('#loading_' + form_id).show();

		var of_id = $('#edit_of_id' + form_id).val();

		$.ajax({
			type: "POST",
			dataType: "html",
			url: "ajax/manage_order/add_item_row.php",
			data: {
				"form_id": form_id,
				"prod_id": prod_id,
				"num_item": num_item,
				"of_id": of_id
			},
			success: function(resp) {
				num_item = parseInt(num_item) + 1;
				$('#num_item_' + form_id).val(num_item);

				$('#prod_item_' + form_id).append(resp);

				$('#loading_' + form_id).hide();
			}
		});
	}

	function deleteItemRow(form_id, oi_id, prod_id, row_id, split_no = 1) {

		if (confirm("Deleting row. Confirm?")) {

			if (oi_id != "new") {
				var oi_id_delete = $('#oi_id_delete').val();
				if (oi_id_delete != "") {
					oi_id_delete = oi_id_delete + "," + oi_id;
				} else {
					oi_id_delete = oi_id;
				}
				$('#oi_id_delete').val(oi_id_delete);
			}

			$('#prod_item_' + form_id + '_' + row_id).remove();

			if (prod_id == "1") {
				calculateQTY(1, 'jersey_qty_' + form_id);
				calculateQTY(1, 'jersey_qty2_' + form_id);
				calculateQTY(1, 'sock_qty_' + form_id);
				calculateQTY(1, 'sock_qty2_' + form_id);
			} else {
				if (split_no == 1) {
					calculateQTY(prod_id, 'jersey_qty_' + form_id);
				} else {
					calculateQTY(prod_id, 'jersey_qty_' + form_id);
					calculateQTY(prod_id, 'sock_qty_' + form_id);
				}
			}

		}

	}

	function calculateQTY(prod_id, class_name) {

		var qty_total = 0;
		$('.' + class_name).each(function() {
			if ($(this).val() != "") {
				qty_total += parseInt($(this).val());
			}

		});

		$('#total_' + class_name).html(qty_total);
	}
</script>
<script>

	
		document.addEventListener('DOMContentLoaded', () => {
			const tabs = document.querySelectorAll('.nav-link');
			const tabContent = document.querySelectorAll('.tab-pane-forboc');

			// Function to activate a tab
			function activateTab(tabId) {

				tabs.forEach(tab => {
					if (tab.classList.contains('teamDetailsNavitems')) {
					return;
					}
					tab.classList.remove('active');
					if (tab.getAttribute('href') === `#${tabId}`) {
						tab.classList.add('active');
					}
				});

				tabContent.forEach(content => {
					content.classList.remove('show', 'active');
					if (content.id === tabId) {
						content.classList.add('show', 'active');
					}
				});
			}

			// // Handle tab click
	
			tabs.forEach(tab => {
			tab.addEventListener('show.bs.tab', function (event) {

			let currentTab = event.relatedTarget; // ✅ tab you're leaving
			let nextTab = event.target;           // ✅ tab you're going to

			let currentId = nextTab ? nextTab.id : null;
			let isValid = true;

			

			// 👉 VALIDATE CURRENT TAB (not next tab)
			if (currentId === 'order-tab') {
				isValid = CheckBillingFormValidation();
			} else if (currentId === 'team-tab') {
				isValid = CheckOrderInformationValidation();
			}

			 
			

			if (!isValid) {
				event.preventDefault(); // ✅ THIS WILL NOW WORK
			}
			});
			});

			// Handle back/forward
			window.addEventListener('popstate', (event) => {
				const tabId = event.state?.tabId || 'billing';
               

				activateTab(tabId);
			});

			// Initial load
			const initialTabId = window.location.hash
				? window.location.hash.substring(1)
				: 'billing';

			activateTab(initialTabId);

			if (!window.location.hash) {
				history.replaceState({ tabId: 'billing' }, '', '#billing');
			}
		});



    // same as billing info 


	$(document).on('change' , '#check' ,function(){
         const a_data_json = <?php echo json_encode($a_data); ?>;
	     const a_data = a_data_json[0];
        if ($(this).is(':checked')) {
             $('#bi_company_name').val(a_data['bill_comp_name']);
             $('#bi_contact').val(a_data['bill_contact_name']);
             $('#bi_country').val(a_data["bill_country"]); 
             $('#bi_city').val(a_data["bill_city"]); 
             $('#bi_zip_code').val(a_data["bill_zip_code"]); 
             $('#bi_email').val(a_data["bill_email"]); 
             $('#bi_tel').val(a_data["bill_tel"]);
            //  $('#bi_addr_id').val(a_data[0]["addr_id"]);
             $('#bi_address').val(a_data["bill_address"]);
             $('#bi_tax_id').val(a_data["bill_tax_id"]);

             $('#de_company_name').val(a_data["deli_comp_name"]); 
             $('#de_contact').val(a_data["deli_contact_name"]);
             $('#de_country').val(a_data["deli_country"]); 
             $('#de_city').val(a_data["deli_city"]); 
             $('#de_zip_code').val(a_data["deli_zip_code"]); 
             $('#de_email').val(a_data["deli_email"]); 
             $('#de_tel').val(a_data["deli_tel"]); 
             $('#de_tax_id').val(a_data["deli_tax_id"]); 
            //  $('#de_addr_id').val(a_data[1]["addr_id"]); 
             $('#de_address').val(a_data["deli_address"]); 

         }else{
               $('#bi_company_name ,#bi_contact ,#bi_country ,#bi_city ,#bi_zip_code ,#de_company_name ,#de_contact ,#de_country ,#de_city ,#de_zip_code ,#de_email ,#de_tel ,#de_tax_id  ,#de_address ,#bi_email ,#bi_tel  ,#bi_address ,#bi_tax_id' ).val('');
         }
    })





	
    // Validation patterns
    const VALIDATION_PATTERNS = {
        textNumber: /^(?=.*[a-zA-Z]).{3,50}$/,
		OrderName: /^(?=.*[a-zA-Z])[a-zA-Z0-9\s&'.,\-()]{3,100}$/ , 
        city: /^[a-zA-Z\s]{2,300}$/, // Letters and spaces only
        text: /^[a-zA-Z\s]{2,200}$/, // Letters and spaces only
        zipcode: /^[a-zA-Z0-9\-\s]{2,20}$/, // Letters, numbers, hyphens
        tel: /^[0-9+\-\s\(\)]{3,30}$/, // Numbers, +, -, spaces, parentheses
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ // Standard email 
    };

    // Check the first step 



	




    function showFieldError(fieldId, message) {
        let mainDiv = $('#' + fieldId).closest('.form-group');
        // Remove existing error first
        mainDiv.find('.errorMessage').remove();

        let errorMessage = '<p class="errorMessage" style="color:red;">' + message + '</p>';
        mainDiv.append(errorMessage);
    }

    function clearFieldError(fieldId) {
        let mainDiv = $('#' + fieldId).closest('.form-group');
        mainDiv.find('.errorMessage').remove();
    }


    function validateField(selector, pattern, message, canEmpty = false) {
        let val = $(selector).val().trim();
        let id = $(selector).attr('id');

        // 👉 Case 1: Field is empty
        if (!val) {
            if (canEmpty) {
                clearFieldError(id);
                return true; // empty is allowed
            } else {
                showFieldError(id, message);
                return false; // empty NOT allowed
            }
        }

        // 👉 Case 2: Field has value → must match pattern
        if (!pattern.test(val)) {
            showFieldError(id, message);
            return false;
        }

        // 👉 Valid case
        clearFieldError(id);
        return true;
    }


	function validateDateField(selector, message) {
		let val = $(selector).val();
		let id = $(selector).attr('id');

		if (!val) {
			showFieldError(id, message);
			return false;
		}

		// Check valid date
		let date = new Date(val);
		if (isNaN(date.getTime())) {
			showFieldError(id, "Invalid date");
			return false;
		}

		clearFieldError(id);
		return true;
	}

	function ValidateOnlyEmpty(selector, message) {
		let val = $(selector).val();
		let selectedVal = $(selector).find('option:selected').val();
		let id = $(selector).attr('id');


		if (!val || selectedVal == '') {
			showFieldError(id, message);
			return false;
		}

		clearFieldError(id);
		return true;
	}

	
    function CheckBillingFormValidation() {
        let isValid = true;

        $('.errorMessage').remove();

        // TEXT FIELDS
        isValid &= validateField('#bi_contact', VALIDATION_PATTERNS.text, 'Invalid contact name');
        isValid &= validateField('#de_contact', VALIDATION_PATTERNS.text, 'Invalid contact name');

        isValid &= validateField('#bi_country', VALIDATION_PATTERNS.text, 'Only letters and spaces allowed');
        isValid &= validateField('#de_country', VALIDATION_PATTERNS.text, 'Only letters and spaces allowed');

        isValid &= validateField('#bi_city', VALIDATION_PATTERNS.city, 'Only letters and spaces allowed');
        isValid &= validateField('#de_city', VALIDATION_PATTERNS.city, 'Only letters and spaces allowed');

        isValid &= ValidateOnlyEmpty('#bi_address',  'Please fill the address');
        isValid &= ValidateOnlyEmpty('#de_address','Please fill the address');

        isValid &= validateField('#bi_zip_code', VALIDATION_PATTERNS.zipcode, 'Invalid zipcode');
        isValid &= validateField('#de_zip_code', VALIDATION_PATTERNS.zipcode, 'Invalid zipcode');

        isValid &= validateField('#bi_tel', VALIDATION_PATTERNS.tel, 'Invalid telephone number');
        isValid &= validateField('#de_tel', VALIDATION_PATTERNS.tel, 'Invalid telephone number');

        isValid &= validateField('#bi_email', VALIDATION_PATTERNS.email, 'Invalid email address');
        isValid &= validateField('#de_email', VALIDATION_PATTERNS.email, 'Invalid email address');

        isValid &= validateField('#bi_company_name', VALIDATION_PATTERNS.textNumber, 'Invalid company name', true);
        isValid &= validateField('#de_company_name', VALIDATION_PATTERNS.textNumber, 'Invalid company name', true);


        return Boolean(isValid);
    }


    function CheckOrderInformationValidation() {
        let isValid = true;

        $('.errorMessage').remove();

        let eventDateValid = validateDateField('#game_event_date', 'Please select event date');
        let dueDateValid = validateDateField('#req_due_date', 'Please select due date');

        if (!eventDateValid || !dueDateValid) {
            isValid = false;
        }

        // 🔥 Optional: logical validation (important)
        let eventDate = $('#game_event_date').val();
        let dueDate = $('#req_due_date').val();

        if (eventDate && dueDate) {
            if (new Date(dueDate) < new Date(eventDate)) {
                showFieldError('req_due_date', 'Due date cannot be before event date');
                isValid = false;
            }
        }

        isValid &= validateField('#project_name', VALIDATION_PATTERNS.OrderName, 'Invalid order name', true);
        isValid &= validateField('#customer_po', VALIDATION_PATTERNS.textNumber, 'Invalid customer PO', true);



        return isValid;
    }

    function CheckOrderFormValidation(is_new = false) {
        let isValid = true;
        $('.errorMessage').remove();


        if (is_new) {
            isValid &= ValidateOnlyEmpty('#input_on_team_name_new', 'Invalid team name');
            isValid &= ValidateOnlyEmpty('#input_on_year_new', 'Select Year');
            isValid &= ValidateOnlyEmpty('#prod_id_new', 'Select Order Form');
        } else {
            isValid &= ValidateOnlyEmpty('#input_on_team_name', 'Invalid team name');
            isValid &= ValidateOnlyEmpty('#input_on_year', 'Select Year');
            isValid &= ValidateOnlyEmpty('#prod_id', 'Select Order Form');
        }

        return isValid;
    }



		//  delete draft function 

   $(document).on('click' , '.delete_form_btn'  , function(){
	   let closestTable = $(this).closest('.tbl_item_form') ; 
	   let draft_id = closestTable.data('delete_id'); 

	   let tab_pane = closestTable.closest('.tab-pane') ;
	 
	 
	   deleteDraft(draft_id ,tab_pane) ;  
   }) ; 



	function deleteDraft(draft_id ,tab_pane) {

		if (confirm("Confirm to delete draft")) {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/manage_order/delete.php",
				data: {
					"draft_id": draft_id
				},
				success: function(resp) {

					if (resp.result == "success") {
						    let centerElement = tab_pane.closest('center') ; 
	                       let active_nav_link = tab_pane.attr('aria-labelledby'); 

						   centerElement.remove(); 
						   $('#'+active_nav_link).remove(); 

											// Activate first available tab
						var firstTab = $('.teamDetailsNavitems:first');
						firstTab.addClass('active')
						var href = firstTab.attr('href'); 
						$(href).addClass('show active'); 
		 
					}

				}
			});
		}
	}




    $(document).on('click', '.orderFormUpload', function() {
        let isValid = CheckOrderFormValidation();
        let spanText = $(this).find('span');
        let button = $(this);

        if (!isValid) {
            console.log("validation failed");
            return false;
        }



        var prod_id = $('#prod_id').val();

        var form_id = $('#form_id_inc').val();

        var on_team_name = base64EncodeUnicode($('#input_on_team_name').val());

        var on_year = window.btoa($('#input_on_year').val());
        const teamId = $('#teamTab li').length;
        let fileInput = $('#order_form_file')[0];
        let file = fileInput.files[0];

        if (!file) return;

        let formData = new FormData();
        formData.append('file', file);
        formData.append('form_id', form_id);
        formData.append('prod_id', prod_id);
        formData.append('on_team_name', on_team_name);
        formData.append('teamno', teamId);
        formData.append('on_year', on_year);










        // 👉 Optional: add extra data
        // formData.append('order_id', $('#order_id').val());
        spanText.text('Uploading.....');
        button.attr('disabled', true);
        $.ajax({
            url: 'ajax/add_order/upload_order_form.php', // 🔁 change to your PHP file
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                button.removeAttr('disabled');
                if (response.upload == false) {
                    spanText.text('Upload Order Form');
                    alert("Blank or wrong order form . Please add correct order form");
                    return false;
                }


				$('#teamTab .nav-link').removeClass('active');

				$('#table_showing .tab-pane').removeClass('active');



				const teamTab = `
				<li class="nav-item" role="presentation">

				<a class="nav-link  active" id="fill-tab-${teamId}" data-bs-toggle="tab" href="#fill-tabpanel-${teamId}" role="tab" aria-controls="fill-tabpanel-${teamId}" aria-selected="true"> Team ${teamId} </a>

				</li>`;



				$('#teamTab').append(teamTab);

                $('.teamTabsSection').show(300); // Adjust duration as needed
                $('#table_showing').append(response.html);

                spanText.text('Upload Order Form');

            },
            error: function(xhr, status, error) {
                console.error("Upload failed:", error);
                alert('Upload failed');
            }
        });

    });


	function deleteRow(button) {

		const row = button.closest('tr'); // works if button is DOM element

		// Safely get data attributes (works without jQuery)
		let prod_id = row.getAttribute('data-prod_id');
		let form_id = row.getAttribute('data-form_id');

		if (row) {
			row.remove(); // modern cleaner way
		}

		// Convert to number if needed
		prod_id = parseInt(prod_id);

		// Make sure split_no exists
		let split = (typeof split_no !== 'undefined') ? split_no : 1;
	

		if (prod_id === 1) {
			calculateQTY(1, 'jersey_qty_' + form_id);
			calculateQTY(1, 'jersey_qty2_' + form_id);
			calculateQTY(1, 'sock_qty_' + form_id);
			calculateQTY(1, 'sock_qty2_' + form_id);
		} else {
			if (split === 1) {
				calculateQTY(prod_id, 'jersey_qty_' + form_id);
			} else {
				calculateQTY(prod_id, 'jersey_qty_' + form_id);
				calculateQTY(prod_id, 'sock_qty_' + form_id);
			}
		}

		// Remove row safely
		
	}

	   function removeTable(el) {

        // Get current tab-pane
        var $tabPane = $(el).closest('.tab-pane');

        var tabPaneId = $tabPane.attr('id'); // e.g. fill-tabpanel-1

        // Remove the tab-pane
        $tabPane.remove();

        // Remove corresponding tab button
        $('button[data-bs-target="#' + tabPaneId + '"], a[href="#' + tabPaneId + '"]').remove();

        // Activate first available tab
		 var firstTab = $('.teamDetailsNavitems:first');
		 firstTab.addClass('active')
		 var href = firstTab.attr('href'); 
         $(href).addClass('show active'); 
		 
    }
</script>


<?php
function capitalize($str)
{
	$new_str1 = strtoupper(substr($str, 0, 1));
	$new_str2 = substr($str, 1);

	return $new_str1 . $new_str2;
}



?>