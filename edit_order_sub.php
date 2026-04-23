<?php
include('check-session_sub.php');
include('db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLSSUB"]));
$sub_user_id = $obj_user->sub_user_id;

$draft_id = $_POST["draft_id"];

$sql_draft = "SELECT * FROM tbl_draft_of WHERE draft_id='" . $draft_id . "' AND assign_user_id='" . $sub_user_id . "' AND enable=1 ORDER BY of_id ASC;";
$rs_draft = $conn->query($sql_draft);
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

$s_of_id_list = implode(",", $a_of_id);

//echo "<hr>".$s_of_id_list."<hr>";
?>


<div class="teamMemberManageORder">
	<div class="card">
		<div class="boxes">
			<div class="text-center">
				<h2>Manage Order</h2>
				<p>Review details and submit</p>
			</div>
		</div>
		<form name="form1" id="form1" method="post">
			<input type="hidden" name="of_id_list" value="<?php echo $s_of_id_list; ?>">
			<input type="hidden" name="of_id_delete" id="of_id_delete" value="">
			<input type="hidden" name="oi_id_delete" id="oi_id_delete" value="">
			<input type="hidden" name="edit_draft_id" id="edit_draft_id" value="<?php echo $_POST["draft_id"]; ?>">
			<h6 class="refNum">Reference number: <?php echo $_POST["draft_id"]; ?></h6>


			<div id="table_showing" class="table-responsive">
				<center>
					<?php

					$sql_item = "SELECT * FROM tbl_draft_oi WHERE of_id IN (" . $s_of_id_list . ") ORDER BY of_id ASC,oi_id ASC;";
					$rs_item = $conn->query($sql_item);

					//echo $sql_item;

					$a_item = array();
					while ($row_item = $rs_item->fetch_assoc()) {
						$a_item[($row_item["of_id"])][] = $row_item;
					}



					for ($k = 0; $k < sizeof($a_of_id); $k++) {

						$is_assigned = $a_data[$k]["is_assigned"];

						$prod_id = $a_data[$k]["prod_id"];
						$of_id = $a_of_id[$k];
						$form_name = $a_data[$k]["form_name"];

						$form_id = $k + 1;

						$sql_product = "SELECT * FROM tbl_product WHERE prod_id='" . $prod_id . "';";
						$rs_product = $conn->query($sql_product);
						$row_product = $rs_product->fetch_assoc();

						$sql_size = "SELECT * FROM tbl_size WHERE prod_id='" . $prod_id . "' AND enable=1 ORDER BY split_order ASC,sort_no ASC;";
						$rs_size = $conn->query($sql_size);

						$a_size = array();
						while ($row_size = $rs_size->fetch_assoc()) {
							$a_size[($row_size["split_order"])][] = $row_size;
						}

						$num_item = sizeof($a_item[$of_id]);

						if ($prod_id == "1") {
					?>
							<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>">
								<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
								<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="1">
								<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
								<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">

								<table class="tbl_item_form">
									<tr style="background: #ECF0F9; text-align: center;font-weight: 600;
								  text-transform: capitalize; color:#2F50A3;font-weight: 600;">
										<td colspan="16">
											<span id="sp_outter_panel<?php echo $form_id; ?>">
												<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
											</span>
											<?php echo " (" . $row_product["prod_name"] . ")"; ?>
										</td>
									</tr>
									<tr>
										<th class="iconBTn" onclick="return addItemRow(<?php echo $form_id; ?>,1);">

											<figure class="my-0"><img src="images/vector/addWhite.png" alt=""></figure>
											<i id="loading_<?php echo $form_id; ?>" style="display: none; font-size: 12px;" class="fa fa-spinner fa-pulse fa-1x fa-fw"><br></i>
											<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $num_item; ?>">
										</th>
										<th style="width:150px;">Name on Jersey</th>
										<th style="width:65px;">P or G</th>
										<th style="width:72px;">Jersey Size</th>
										<th>Jersey #</th>
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
										<th>Note.</th>
									</tr>
									<tbody id="prod_item_<?php echo $form_id; ?>">
										<?php

										$sum_qty_top1 = 0;
										$sum_qty_top2 = 0;
										$sum_qty_bottom1 = 0;
										$sum_qty_bottom2 = 0;
										for ($m = 0; $m < $num_item; $m++) {

											$row_count = $m + 1;
											$edit_item = $a_item[$of_id][$m];


											$sum_qty_top1 += $edit_item["qty_top1"];
											$sum_qty_top2 += $edit_item["qty_top2"];
											$sum_qty_bottom1 += $edit_item["qty_bottom1"];
											$sum_qty_bottom2 += $edit_item["qty_bottom2"];
										?>
											<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
												<td class="iconBTn" onclick="return deleteItemRow(<?php echo $form_id; ?>,<?php echo $edit_item["oi_id"]; ?>,1,<?php echo $row_count; ?>);">
													<figure class="m-auto"><img src="images/vector/delete.png" alt=""></figure>
													<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="<?php echo $edit_item["oi_id"]; ?>">
												</td>
												<td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo $edit_item["player_name"]; ?>"></td>
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
															<option value="<?php echo $tmp_size[$i]["size_id"]; ?>" <?php if ($edit_item["product_size_id"] == $tmp_size[$i]["size_id"]) {
																														echo "selected";
																													} ?>>
																<?php echo $tmp_size[$i]["size_name"]; ?>
															</option>
														<?php
														}
														?>
													</select>
												</td>
												<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo $edit_item["jersey_number"]; ?>"></td>
												<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top1"]; ?>"></td>
												<td>
													<input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top1"]; ?>">
												</td>
												<td><input class="white_in" name="jersey_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top2"]; ?>"></td>
												<td>
													<input class="white_in jersey_qty2_<?php echo $form_id; ?>" name="jersey_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top2"]; ?>">
												</td>
												<td>
													<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_ssize[<?php echo $form_id; ?>][]">
														<option value="0"></option>
														<?php for ($i = 0; $i < sizeof($a_size["3"]); $i++) { ?>
															<option value="<?php echo $a_size["3"][$i]["size_id"]; ?>" <?php if ($edit_item["bottom_size"] == $a_size["3"][$i]["size_id"]) {
																															echo "selected";
																														} ?>><?php echo $a_size["3"][$i]["size_name"]; ?></option>
														<?php } ?>
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
													<select class="white_in" name="select_ca[<?php echo $form_id; ?>][]">
														<option value=""></option>
														<option value="captain" <?php if ($edit_item["c_or_a"] == "captain") {
																					echo "selected";
																				} ?>>Captain</option>
														<option value="assistant" <?php if ($edit_item["c_or_a"] == "assistant") {
																						echo "selected";
																					} ?>>Assistant</option>
													</select>
												</td>
												<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["note"]; ?>"></td>
											</tr>
										<?php
										}
										?>
									</tbody>
									<tr>
										<th style="border-width: 0px; background-color: #FFFF;"></th>
										<th>TOTAL ORDER</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th id="total_jersey_qty_<?php echo $form_id; ?>"><?= $sum_qty_top1 ?></th>
										<th></th>
										<th id="total_jersey_qty2_<?php echo $form_id; ?>"><?= $sum_qty_top2 ?></th>
										<th></th>
										<th></th>
										<th id="total_sock_qty_<?php echo $form_id; ?>"><?= $sum_qty_bottom1 ?></th>
										<th></th>
										<th id="total_sock_qty2_<?php echo $form_id; ?>"><?= $sum_qty_bottom2 ?></th>
										<th></th>
										<th></th>
									</tr>
								</table>

							</div>
						<?php
						} else if ($row_product["split_type"] == "2") {

							$tmp_split = explode(",", $row_product["split_name"]);
							$split_name1 = $tmp_split[0];
							$split_name2 = $tmp_split[1];
						?>
							<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>" style="border:10px solid #FFA; border-radius: 5px; background-color: #FFA; width: 900px;">
								<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
								<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">
								<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
								<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">
								<center>
									<h6>
										<span id="sp_outter_panel<?php echo $form_id; ?>">
											<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
										</span>
										<?php echo " (" . $row_product["prod_name"] . ")"; ?>
									</h6>
								</center>

								<table class="tbl_item_form" align="center">
									<tr>
										<th style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #0A0; cursor: pointer; text-align: right;" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">

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
											<th style="width:90px;">Sex</th>
										<?php
										}
										?>
										<th style="width:70px;"><?php echo $split_name1; ?> Size</th>
										<th><?php echo $split_name1; ?> #  </th>
										<th><?php echo $split_name1; ?> Color</th>
										<th style="width:60px;">QTY</th>
										<th style="width:70px;"><?php echo $split_name2; ?> Size</th>
										<th><?php echo $split_name2; ?> Color</th>
										<th style="width:60px;">QTY</th>
										<th>Note.</th>
									</tr>
									<tbody id="prod_item_<?php echo $form_id; ?>">
										<?php
										$sum_qty_top1 = 0;
										$sum_qty_bottom1 = 0;
										for ($m = 0; $m < $num_item; $m++) {

											$row_count = $m + 1;
											$edit_item = $a_item[$of_id][$m];

											$sum_qty_top1 += $edit_item["qty_top1"];
											$sum_qty_bottom1 += $edit_item["qty_bottom1"];
										?>
											<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
												<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow(<?php echo $form_id; ?>,<?php echo $edit_item["oi_id"]; ?>,<?php echo $prod_id; ?>,<?php echo $row_count; ?>,2);">
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

												if ($row_product["choose_mf"] == "1") {
													?>
													<td>
														<select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,<?php echo $row_count; ?>,2);">
															<option value="male" <?php if ($edit_item["sex"] == "male") {
																						echo "selected";
																					} ?>>Male</option>
															<option value="female" <?php if ($edit_item["sex"] == "female") {
																						echo "selected";
																					} ?>>Female</option>
														</select>
													</td>
												<?php
													if ($edit_item["sex"] == "female") {
														$tmp_size = $a_size["2"];
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
												<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["note"]; ?>"></td>

											</tr>
										<?php
										}
										?>
									</tbody>
									<tr>
										<th style="border-width: 0px; background-color: #FFA;"></th>
										<th>TOTAL ORDER</th>
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
										<th id="total_jersey_qty_<?php echo $form_id; ?>"><?= $sum_qty_top1 ?></th>
										<th></th>
										<th></th>
										<th id="total_sock_qty_<?php echo $form_id; ?>"><?= $sum_qty_bottom1 ?></th>
										<th></th>
									</tr>
								</table>

							</div>
						<?php
						} else {

							$split_name = $row_product["split_name"];
						?>
							<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>" style="border:10px solid #FFA; border-radius: 5px; background-color: #FFA; width: 800px;">
								<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
								<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">
								<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
								<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="<?php echo $of_id; ?>">
								<center>
									<h6>
										<span id="sp_outter_panel<?php echo $form_id; ?>">
											<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
										</span>
										<?php echo " (" . $row_product["prod_name"] . ")"; ?>
									</h6>
								</center>

								<table class="tbl_item_form" align="center">
									<tr>
										<th style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #0A0; cursor: pointer; text-align: right;" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">
											<i class="fa fa-plus-circle"></i>
											<i id="loading_<?php echo $form_id; ?>" style="display: none; font-size: 12px;" class="fa fa-spinner fa-pulse fa-1x fa-fw"><br></i>
											<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $num_item; ?>">
										</th>
										<?php
										if ($row_product["have_name"] == "1") {
										?>
											<th style="width:158px;">Name on <?php echo $split_name; ?></th>
										<?php
										}

										if ($row_product["choose_pg"] == "1") {
										?>
											<th style="width:65px;">P or G</th>
										<?php
										}

										if ($row_product["choose_mf"] == "1") {
										?>
											<th style="width:90px;">Sex</th>
										<?php
										}

										if ($row_product["have_size"] == "1") {
										?>
											<th style="width:80px;"><?php echo $split_name; ?> Size</th>
										<?php
										}

										if ($row_product["have_number"] == "1") {
										?>
											<th><?php echo $split_name; ?> #</th>
										<?php
										}
										?>
										<th><?php echo $split_name; ?> Color</th>
										<th style="width:50px;">QTY</th>
										<th>Note.</th>
									</tr>
									<tbody id="prod_item_<?php echo $form_id; ?>">
										<?php
										$sum_qty_top1 = 0;
										for ($m = 0; $m < $num_item; $m++) {

											$row_count = $m + 1;
											$edit_item = $a_item[$of_id][$m];
											$sum_qty_top1 += $edit_item["qty_top1"];
										?>
											<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_count; ?>">
												<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow(<?php echo $form_id; ?>,<?php echo $edit_item["oi_id"]; ?>,<?php echo $prod_id; ?>,<?php echo $row_count; ?>,1);">
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

												if ($row_product["choose_mf"] == "1") {
													?>
													<td>
														<select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_count; ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,<?php echo $row_count; ?>,1);">
															<option value="male" <?php if ($edit_item["sex"] == "male") {
																						echo "selected";
																					} ?>>Male</option>
															<option value="female" <?php if ($edit_item["sex"] == "female") {
																						echo "selected";
																					} ?>>Female</option>
														</select>
													</td>
													<?php

													if ($edit_item["sex"] == "female") {
														$tmp_size = $a_size["2"];
													}
												}

												if ($row_product["have_size"] == "1") {
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

												if ($row_product["have_number"] == "1") {
												?>
													<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo $edit_item["jersey_number"]; ?>"></td>
												<?php
												}
												?>
												<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo $edit_item["color_top1"]; ?>"></td>
												<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo $edit_item["qty_top1"]; ?>"></td>
												<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo $edit_item["note"]; ?>"></td>

											</tr>
										<?php
										}
										?>
									</tbody>
									<tr>
										<th style="border-width: 0px; background-color: #FFA;"></th>
										<th>TOTAL ORDER</th>
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
										<th id="total_jersey_qty_<?php echo $form_id; ?>"><?= $sum_qty_top1 ?></th>
										<th></th>
									</tr>
								</table>

							</div>
					<?php
						}
					}
					?>
				</center>
			</div>
			<center>
				<div class="row tfoot">

					<div class="col-md-12 col-sm-12" style="padding-top: 10px;">
						<div class="d-flex gap-2 justify-content-end align-items-center">
							<a href="?vp=<?php echo base64_encode('order_form_sub'); ?>" class="btn themeBtn2grey  " onclick="return confirm('Your updated info will be cancel. OK?');">Cancel</a>
							<input type="button" class="btn  themeBtn" value="Save Data" onclick="return saveDraft();">

						</div>
					</div>

				</div>
			</center>

		</form>
	</div>
</div>

<pre id="test_pre"></pre>

<script type="text/javascript">
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

	function addItemRow(form_id, prod_id) {

		var num_item = $('#num_item_' + form_id).val();

		$('#loading_' + form_id).show();

		var of_id = $('#edit_of_id' + form_id).val();

		$.ajax({
			type: "POST",
			dataType: "html",
			url: "ajax/order_form_sub/add_item_row.php",
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

	function saveDraft() {

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "ajax/order_form_sub/submit_draft.php",
			data: $('#form1').serialize(),
			success: function(resp) {

				if (resp.result == "success") {
					window.location.href = "?vp=<?php echo base64_encode('order_form_sub'); ?>";
				} else {
					alert(resp.msg);
				}

			}
		});

		/*$.ajax({  
		    type: "POST",  
		    dataType: "html", 
		    url:"ajax/order_form_sub/submit_draft_test.php" ,
		    data: $('#form1').serialize() ,
		    success: function(resp){

		    	$('#test_pre').html(resp);
		    	
		    }
		});*/
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