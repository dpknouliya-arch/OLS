<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
	echo '<center>Please re-login again.</center>';
	exit();
}

include('../../db.php');

$of_id = $_POST["of_id"];

$sql_of = "SELECT * FROM tbl_order_form WHERE of_id='" . $of_id . "' AND enable=1;";
$rs_of = $conn->query($sql_of);
$row_of = $rs_of->fetch_assoc();
$prod_id = $row_of["prod_id"];

$sql_oi = "SELECT tbl_order_item.*,tbl_top_size.size_name AS top_size,tbl_bottom_size.size_name AS bottom_sizes FROM tbl_order_item ";
$sql_oi .= " LEFT JOIN tbl_size AS tbl_top_size ON tbl_order_item.product_size_id=tbl_top_size.size_id ";
$sql_oi .= " LEFT JOIN tbl_size AS tbl_bottom_size ON tbl_order_item.bottom_size=tbl_bottom_size.size_id WHERE of_id='" . $of_id . "' ;";

$rs_oi = $conn->query($sql_oi);


$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/';





$a_item = array();
while ($row_oi = $rs_oi->fetch_assoc()) {
	$a_item[] = $row_oi;
}

$sql_prod = "SELECT * FROM tbl_product WHERE prod_id='" . $prod_id . "';";
$rs_prod = $conn->query($sql_prod);
$row_product = $rs_prod->fetch_assoc();

?>
<style type="text/css">
	.tbl_order_form td {
		text-align: center;
		padding: 3px;
	}

	.tbl_doc_no td {
		font-size: 14px;
	}
</style>
<table class="tbl_order_form orderStatusModal border-none" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td style="padding: 0px;" class="border-none">
			<!-- upper -->
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr class="grid3 customBorder">
					<td class="border-none ">
						<table class="border-none" style="width:100%; margin: 0px;  " cellspacing="0" cellpadding="0">
							<tr>
								<td style="width:65%; text-align: left; border-width: 0px; font-size: 15px;" colspan="2"><b><i>PURCHASE ORDER FORM / ORDER FORM</i></b></td>
							</tr>

							<td style="border-width: 0px;">
								<!-- <img src="/assets/images/logo-order-form.png"> -->
								<figure>
									<img src="<?php echo $brand['logo']; ?>" alt="">
								</figure>
							</td>
					</td>
				<?php if ($brand_id == 2): ?>
				<tr>
					<td style="text-align: left; border-width: 0px; " class="link"><u>www.bauer.com</u></td>
				</tr>
				<?php else: ?>
				<tr>
					<td style="text-align: left; border-width: 0px;  " class="link"><u>www.jogsportswear.com</u></td>
				</tr>
				<tr>
					<td style="text-align: left; border-width: 0px; " class="link"><u>www.jogsports.com</u></td>
				</tr>
				<?php endif; ?>
				<tr style="background-color: #CCF;">
					<td colspan="2" style="text-align: center; border-width: 0px;"><b>SALES REP + SALES MAN + PROC</b></td>
				</tr>
				<!-- <tr style="background-color: #FCA;">
								<td colspan="2" style="border-width: 0px;">&nbsp;</td>
							</tr> -->
				<tr>
					<td class="border-none area3">
						<?php
						$ols_code = "00000" . $row_of["of_id"];
						$ols_code = "OLS" . substr($ols_code, (strlen($ols_code) - 6), 6);
						?>
						<table class="tbl_doc_no border-none" style="width:100%; margin: 0px;  height: 100%; top:0;" cellspacing="0" cellpadding="0">
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px;"><b>ONLINE SERVICE CODE : </b><?php echo $ols_code; ?></td>
							</tr>
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px;"><b>ORDER DATE : </b><?php echo showDateShortFormat($row_of["order_date"]); ?></td>
							</tr>
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px;"><b>CUSTOMER PO : </b><?php echo $row_of["customer_po"]; ?></td>
							</tr>
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px;"><b>PROJECT NAME : </b><?php echo $row_of["project_name"]; ?></td>
							</tr>
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px; background-color: #AEA;"><b>Customer's Requested Due Date : </b><?php echo showDateShortFormat($row_of["req_due_date"]); ?></td>
							</tr>
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px; background-color: #AEA;"><b>Game/Event Date : </b><?php echo showDateShortFormat($row_of["game_event_date"]); ?></td>
							</tr>
							<!-- <tr>
								<td style="width:100%; text-align: left; border-width: 0px; line-height: 0.5;">&nbsp;</td>
							</tr> -->
							<tr>
								<td style="width:100%; text-align: left; border-width: 0px; height: 35px; background-color: #EE0;"><b>PRODUCT : </b><?php echo $row_product["prod_name"]; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>

		<td class="  border-none">
			<table cellspacing="0" cellpadding="0" class="w-100 border-none">
				<tr>
					<td style="width:100%; text-align: center; border-width: 0px; height: 40px; border:1px solid #000; background-color: #F9B;"><b>Mer.</b></td>
				</tr>
				<tr>
					<td style="border-width: 0px; height: 40px; border:1px solid #000; background-color: #CCF;">&nbsp;</td>
				</tr>
				<?php if ($brand_id == 1): ?>
				<tr>
					<td style="text-align: center; border-width: 0px; border:1px solid #000; font-size: 20px; font-weight: 700;">
						<b>J<br>O<br>I<br>N<br> <br>O<br>U<br>R<br> <br>G<br>A<br>M<br>E</b>
					</td>
				</tr>
				<?php elseif($brand_id == 2): ?>
				<tr>
					<td style="text-align: center; border-width: 0px; border:1px solid #000; font-size: 20px; font-weight: 700;">
						<b>B<br><br>A<br><br>U<br><br>E<br><br>R</b>
					</td>
				</tr>
				<?php endif; ?>
			</table>
		</td>
	</tr>
	<tr class="grid2">
		<td>
			<table class="border-none" style="width:100%; margin: 0px;" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2" style="width:100%; text-align: left; border-width: 0px; height: 40px;"><b>CUSTOMER BILLING INFORMATION</b></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="width:33%; text-align: left; border-width: 0px;"><b>Company Name:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_comp_name"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Contact Name:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_contact_name"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Address:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_address"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>City:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_city"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Country:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_country"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Zip code:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_zip_code"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Telephone:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_tel"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Email:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_email"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>TAX ID:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["bill_tax_id"]; ?></td>
				</tr>
			</table>
		</td>
		<td>
			<table class="border-none" style="width:100%; margin: 0px;" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2" style="width:100%; text-align: left; border-width: 0px; height: 40px;"><b>DELIVER INFORMATION</b></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="width:33%; text-align: left; border-width: 0px;"><b>Company Name:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_comp_name"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Contact Name:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_contact_name"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Address:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_address"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>City:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_city"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Country:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_country"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Zip code:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_zip_code"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Telephone:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_tel"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>Email:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_email"]; ?></td>
				</tr>
				<tr style="font-size: 14px;">
					<td style="text-align: left; border-width: 0px;"><b>TAX ID:</b></td>
					<td style="border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of["deli_tax_id"]; ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>

			<div style="border-width:1px 1px 0px 1px; border-style: solid; border-color: #000; height: 30px; line-height: 1.8; font-size: 14px; text-align: center;">
				<b>PAYMENT OPTIONS: &nbsp;<?php echo strtoupper($row_of["payment_opt"]);
											if ($row_of["payment_opt"] == "Credit card") {
												echo " (PROCESSING FEE 3%)";
											} ?>
			</div>
		</td>
	</tr>
	<?php
	if ($row_of["reorder_num"] != "" && $row_of["reorder_num"] != null) {
	?>
		<tr>
			<td style="background-color: #FCA; border: 1px solid #000; padding: 0px; height: 30px;" colspan="3">

				<div style="border-width:1px 1px 0px 1px; border-style: solid; border-color: #000; height: 30px; line-height: 1.8; font-size: 14px; text-align: center;">
					<b>Reorder #: &nbsp;<?php echo  $row_of["reorder_num"]; ?>
				</div>
			</td>
		</tr>
	<?php } ?>
</table>
</td>
</tr>
<tr>
	<td style="">
		<!-- Show form name -->
		<div style="background-color: #CCF;  height: 40px; padding: 0px;display: flex
; align-items: center;justify-content: center;    text-transform: capitalize;     box-shadow: 0px 0px 6px 0px #0000001A;"><?php echo $row_of["form_name"]; ?></div>
	</td>
</tr>
<tr>
	<td>
		<?php
		if ($row_of["xls_name"] != "") {
		?>
			<!-- <iframe class="frame_content" id="live_view" src="https://view.officeapps.live.com/op/embed.aspx?src=http://localhost/olsDash/src/upload/<?php echo $row_of["xls_name"]; ?>" width="100%" height="500" frameborder="0"></iframe> -->

			<iframe class="frame_content" 
				id="live_view" 
				src="https://view.officeapps.live.com/op/embed.aspx?src=<?=$base_url?>/src/upload/<?php echo $row_of["xls_name"]; ?>" width="100%"  
				height="500" 
				frameborder="0">
			</iframe>
		<?php
		
		} else {
		?>
			<div>
				<!-- Items -->
				<?php
				$num_item = sizeof($a_item);

				if ($prod_id == "1") {
				?>
					<table width="100%" cellspacing="0" cellpadding="2" style="font-size: 14px;" border="1">
						<tr style="background-color: #EE0;">
							<th style="width:150px; text-align: center;">Name on Jersey</th>
							<th style="width:65px; text-align: center;">Pattern Cut</th>
							<th style="width:65px; text-align: center;">P or G</th>
							<th style="width:72px; text-align: center;">Jersey Size</th>
							<th style="text-align: center;">Jersey #</th>
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
							<th style="width:75px; text-align: center;">Name For Packing</th>
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
								$edit_item = $a_item[$m];

								

								$sum_qty_top1 += $edit_item["qty_top1"];
								$sum_qty_top2 += $edit_item["qty_top2"];
								$sum_qty_bottom1 += $edit_item["qty_bottom1"];
								$sum_qty_bottom2 += $edit_item["qty_bottom2"];

								$show_c_or_a = "";
								if ($edit_item["c_or_a"] != "") {
									if ($edit_item["c_or_a"] == "captain") {
										$show_c_or_a = "C";
									} else if ($edit_item["c_or_a"] == "assistant") {
										$show_c_or_a = "A";
									}
									else if ($edit_item["c_or_a"] == "C") {
										$show_c_or_a = "C";
									}
									else if ($edit_item["c_or_a"] == "A") {
										$show_c_or_a = "A";
									}
								}
							?>
								<tr>
									<td><?php echo $edit_item["player_name"]; ?></td>
									<td><?php //echo ($edit_item["sex"]!="")?capitalize($edit_item["sex"]):""; 
										?>
										<?php
										if ($edit_item["sex"] == "youth") {
											echo "MEN-YOUTH";
										} elseif ($edit_item["sex"] == "male") {
											echo "MEN-ADULT";
										} elseif ($edit_item["sex"] == "female") {
											echo "WOMEN-ADULT";
										} elseif ($edit_item["sex"] == "female_youth") {
											echo "WOMEN-YOUTH";
										}
										elseif ($edit_item["sex"] == "Adult") {
											echo "Adult";
										}
										elseif ($edit_item["sex"] == "Youth") {
											echo "Youth";
										}
										?>
									</td>
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
									<td><?php echo ($show_c_or_a != "") ? $show_c_or_a : ""; ?></td>
									<td><?php echo $edit_item["name_for_packing"]; ?></td>
									<td><?php echo $edit_item["note"]; ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
						<tr>
							<th style="text-align: center; font-size: 13px;">TOTAL ORDER</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th style="text-align: center;"><?php echo $sum_qty_top1; ?></th>
							<th></th>
							<th style="text-align: center;"><?php echo $sum_qty_top2; ?></th>
							<th></th>
							<th></th>
							<th style="text-align: center;"><?php echo $sum_qty_bottom1; ?></th>
							<th></th>
							<th style="text-align: center;"><?php echo $sum_qty_bottom2; ?></th>
							<th></th>
							<th colspan="2"></th>
						</tr>
						<tr>
							<!--<th style="border-width: 0px; background-color: #FFA;"></th>-->
							<th>Special Comments <br> (if any)</th>
							<th colspan="16" style="background-color: white;border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of['special_comment']; ?></th>
						</tr>
					</table>
				<?php
				} else if ($row_product["split_type"] == "2") {

					$tmp_split = explode(",", $row_product["split_name"]);
					$split_name1 = $tmp_split[0];
					$split_name2 = $tmp_split[1];
				?>
					<table cellspacing="0" cellpadding="2" border="1" style="width:100%; font-size: 14px;" align="center">
						<tr style="background-color: #EE0;">
							<?php
							if ($row_product["have_name"] == "1") {
							?>
								<th style="width:150px; text-align: center;"> Name on <?php echo $split_name1; ?></th>
							<?php
							}

							if ($row_product["choose_pg"] == "1") {
							?>
								<th style="width:65px; text-align: center;">P or G</th>
							<?php
							}

							if ($row_product["choose_mf"] == "1") {
							?>
								<th style="width:90px; text-align: center;">Pattern Cut</th>
							<?php
							}
							?>
							<th style="width:80px; text-align: center;"><?php echo $split_name1; ?> Size</th>
							<th style="text-align: center;"><?php echo $split_name1; ?> #</th>
							<th style="text-align: center;"><?php echo $split_name1; ?> Color</th>
							<th style="width:50px; text-align: center;">QTY</th>
							<th style="width:80px; text-align: center;"><?php echo $split_name2; ?> Size</th>
							<th style="text-align: center;"><?php echo $split_name2; ?> Color</th>
							<th style="width:50px; text-align: center;">QTY</th>
							<th style="width:75px; text-align: center;">Name For Packing</th>
							<th style="text-align: center;">Notes  </th>
						</tr>
						<tbody>
							<?php
							$sum_qty_top1 = 0;
							$sum_qty_bottom1 = 0;

							for ($m = 0; $m < $num_item; $m++) {

								$row_count = $m + 1;
								$edit_item = $a_item[$m];

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
										<td><?php //echo ($edit_item["sex"]!="")?capitalize($edit_item["sex"]):""; 
											?>
											<?php
											if ($edit_item["sex"] == "youth") {
												echo "MEN-YOUTH";
											} elseif ($edit_item["sex"] == "male") {
												echo "MEN-ADULT";
											} elseif ($edit_item["sex"] == "female") {
												echo "WOMEN-ADULT";
											} elseif ($edit_item["sex"] == "female_youth") {
												echo "WOMEN-YOUTH";
											}
											?>
										</td>
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
									<td><?php echo $edit_item["name_for_packing"]; ?></td>
									<td><?php echo $edit_item["note"]; ?></td>

								</tr>
							<?php
							}
							?>
						</tbody>
						<tr>

							<th style="text-align: center; font-size: 13px;">TOTAL ORDER</th>
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
						<tr>
							<!--<th style="border-width: 0px; background-color: #FFA;"></th>-->
							<th>Special Comments <br> (if any)</th>
							<th colspan="16" style="background-color: white;border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of['special_comment']; ?></th>
						</tr>
					</table>
				<?php
				} else {

					$split_name = $row_product["split_name"];
				?>
					<table cellspacing="0" cellpadding="2" border="1" style="width:100%; font-size: 14px;" align="center" class="borderTable">
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
								<th style="width:90px; text-align: center;">Pattern Cut</th>
							<?php
							}

							if ($row_product["have_size"] == "1") {
							?>
								<th style="width:80px; text-align: center;"><?php echo $split_name; ?> Size</th>
							<?php
							}

							if ($row_product["have_number"] == "1") {
							?>
								<th style="text-align: center;"><?php echo $split_name; ?> #</th>
							<?php
							}
							?>
							<th style="text-align: center;"><?php echo $split_name; ?> Color</th>
							<th style="width:50px; text-align: center;">QTY</th>
							<th style="width:75px; text-align: center;">Name For Packing</th>
							<th style="text-align: center;">Notes   </th>
						</tr>
						<tbody>
							<?php
							$sum_qty_top1 = 0;

							for ($m = 0; $m < $num_item; $m++) {

								$row_count = $m + 1;
								$edit_item = $a_item[$m];

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
										<td><?php //echo ($edit_item["sex"]!="")?capitalize($edit_item["sex"]):""; 
											?>
											<?php
											if ($edit_item["sex"] == "youth") {
												echo "MEN-YOUTH";
											} elseif ($edit_item["sex"] == "male") {
												echo "MEN-ADULT";
											} elseif ($edit_item["sex"] == "female") {
												echo "WOMEN-ADULT";
											} elseif ($edit_item["sex"] == "female_youth") {
												echo "WOMEN-YOUTH";
											}
											?>
										</td>
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
									<td><?php echo $edit_item["name_for_packing"]; ?></td>
									<td><?php echo $edit_item["note"]; ?></td>

								</tr>
							<?php
							}
							?>
						</tbody>
						<tr>

							<th style="text-align: center; font-size: 13px;">TOTAL ORDER</th>
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
							<th colspan="2"></th>
						</tr>
						<tr>
							<!--<th style="border-width: 0px; background-color: #FFA;"></th>-->
							<th>Special Comments <br> (if any)</th>
							<th colspan="16" style="background-color: white;border-bottom: 1px solid #000; background-color: #CCF;"><?php echo $row_of['special_comment']; ?></th>
						</tr>
					</table>
				<?php
				}
				?>
			</div>
		<?php
		}
		?>
	</td>
</tr>
</table>
<?php
function capitalize($str)
{
	$new_str1 = strtoupper(substr($str, 0, 1));
	$new_str2 = substr($str, 1);

	return $new_str1 . $new_str2;
}

function showDateLongFormat($date_in)
{

	if (($date_in == "") || ($date_in == "0000-00-00")) {
		return "";
	} else {
		return date("l, F j, Y", strtotime($date_in));
	}
}

function showDateShortFormat($date_in)
{

	if (($date_in == "") || ($date_in == "0000-00-00")) {
		return "";
	} else {
		return date("D. j M. Y", strtotime($date_in));
	}
}
?>