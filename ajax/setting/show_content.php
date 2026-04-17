<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
	echo '<center>Please re-login again.</center>';
	exit();
}

include('../../db.php');

$prod_id = $_POST["prod_id"];

if ($prod_id == "1") {

	$sql_size = "SELECT * FROM tbl_size WHERE prod_id=1 AND enable=1 ORDER BY split_order ASC,sort_no ASC";
	$rs_size = $conn->query($sql_size);

	$a_size = array();

	while ($row_size = $rs_size->fetch_assoc()) {

		$a_size[($row_size["split_order"])][] = $row_size;
	}

	foreach ($a_size as $a_split_type => $a_tmp_row_size) {

		$num_size_row = sizeof($a_tmp_row_size);
		$tmp_index = 0;

		foreach ($a_tmp_row_size as $tmp_size_key => $a_row_size) {
			if ($tmp_index == 0) {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = 0;
			} else {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = $a_size[($a_row_size["split_order"])][($tmp_index - 1)]["size_id"];
			}

			if (($tmp_index + 1) == $num_size_row) {

				if (($tmp_index - 1) < 0) {
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				} else {
					$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				}
			} else if ($tmp_index > 0) {
				$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
			}

			$tmp_index++;
		}
	}

	/*echo "<pre>";
	print_r($a_size);
	echo "</pre>";*/

	$pg_selected = isset($_POST["select_pg"]) ? $_POST["select_pg"] : "";

?>
	<div class="singleCat grid2">
		<div class="col-12">
			<div class="singleTableItems">
				<div class="setting_sub_head">
					<div class="grid2 upper">
						<h6 class="m-0 XSmall">Jersey size of</h6>
						<select id="select_pg_1" onchange="return changeSelectPG(1);">
							<option value="1" <?php if ($pg_selected == "1") {
													echo "selected";
												} ?>>Player</option>
							<option value="2" <?php if ($pg_selected == "2") {
													echo "selected";
												} ?>>Goalie</option>
						</select>
					</div>
					<div class="column2 text-end">
						<button class="btn btn-dark iconBTn" onclick="return addNewSize(1);">
							<figure class="m-0"><img src="images/vector/addWhite.png" alt=""></figure> Size
						</button>
					</div>
				</div>
				<div id="p_hockey_jersey_size">
					<table class="tbl_content_size" id="tbl_show_prod_1_1">
						<tr class="stickyTr">
							<th style="text-align: center;">#</th>
							<th>Size</th>
							<th style="text-align: center;">Edit</th>
							<th style="text-align: center;">Move</th>
						</tr>
						<?php
						$count_row = 1;
						foreach ($a_size[1] as $tmp_key => $row_content) {
						?>
							<tr>
								<td style="text-align: center;"><?php echo $count_row; ?></td>
								<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
								<td>
									<div class="iconBTn">
										<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
										<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(1,1,<?php echo $row_content["size_id"]; ?>);"></figure>
									</div>

								</td>
								<td style="text-align: center;" class="iconBTn border-right">
									<?php
									if ($row_content["previous_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(1,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>


									<?php
									}

									if ($row_content["next_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(1,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>


									<?php
									}
									?>
								</td>
							</tr>
						<?php
							$count_row++;
						}
						?>
					</table>
				</div>
				<div id="g_hockey_jersey_size" style="display: none;">
					<table class="tbl_content_size" id="tbl_show_prod_1_2">
						<tr class="stickyTr">
							<th style="text-align: center;">#</th>
							<th>Size</th>
							<th style="text-align: center;">Edit</th>
							<th style="text-align: center;">Move</th>
						</tr>
						<?php
						$count_row = 1;
						foreach ($a_size[2] as $tmp_key => $row_content) {
						?>
							<tr>
								<td style="text-align: center;"><?php echo $count_row; ?></td>
								<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
								<td style="text-align: center;">
									<div class="iconBTn">
										<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
										<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(1,2,<?php echo $row_content["size_id"]; ?>);"></figure>
									</div>

								</td>
								<td style="text-align: center;">
									<div class="iconBTn">
										<?php
										if ($row_content["previous_id"] != 0) {
										?>
											<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(1,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>

										<?php
										}

										if ($row_content["next_id"] != 0) {
										?>
											<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(1,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>

										<?php
										}
										?>
									</div>
								</td>
							</tr>
						<?php
							$count_row++;
						}
						?>
					</table>

				</div>
			</div>
		</div>

		<div class="col-12">
			<div class="singleTableItems">
				<div class="setting_sub_head">
					<div class="grid2 upper">
						<h6 class="m-0 XSmall">Sock size </h6>

					</div>
					<div class="column2 text-end">
						<button class="btn btn-dark iconBTn " onclick="return addNewSize2(1,3);">
							<figure class="m-0"><img src="images/vector/addWhite.png" alt=""></figure> Size
						</button>
					</div>


				</div>
				<div id="hockey_sock_size">
					<table class="tbl_content_size" id="tbl_show_prod_1_3">
						<tr class="stickyTr">
							<th style="text-align: center;">#</th>
							<th>Size</th>
							<th style="text-align: center;">Edit</th>
							<th style="text-align: center;">Move</th>
						</tr>
						<?php
						$count_row = 1;
						foreach ($a_size[3] as $tmp_key => $row_content) {
						?>
							<tr>
								<td style="text-align: center;"><?php echo $count_row; ?></td>
								<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
								<td style="text-align: center;">
									<div class="iconBTn">
										<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
										<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(1,3,<?php echo $row_content["size_id"]; ?>);"></figure>
									</div>

								</td>
								<td style="text-align: center;">
									<div class="iconBTn">
										<?php
										if ($row_content["previous_id"] != 0) {
										?>
											<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(1,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>
										<?php
										}

										if ($row_content["next_id"] != 0) {
										?>
											<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(1,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>
										<?php
										}
										?>
									</div>
								</td>
							</tr>
						<?php
							$count_row++;
						}
						?>
					</table>
				</div>
			</div>



		</div>
	</div>

	<script type="text/javascript">
		changeSelectPG(1);
	</script>
<?php
} else if ($prod_id == "2") {

	$sql_size = "SELECT * FROM tbl_size WHERE prod_id=2 AND enable=1 ORDER BY split_order ASC,sort_no ASC";
	$rs_size = $conn->query($sql_size);

	$a_size = array();

	while ($row_size = $rs_size->fetch_assoc()) {

		$a_size[($row_size["split_order"])][] = $row_size;
	}

	foreach ($a_size as $a_split_type => $a_tmp_row_size) {

		$num_size_row = sizeof($a_tmp_row_size);
		$tmp_index = 0;

		foreach ($a_tmp_row_size as $tmp_size_key => $a_row_size) {
			if ($tmp_index == 0) {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = 0;
			} else {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = $a_size[($a_row_size["split_order"])][($tmp_index - 1)]["size_id"];
			}

			if (($tmp_index + 1) == $num_size_row) {

				if (($tmp_index - 1) < 0) {
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				} else {
					$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				}
			} else if ($tmp_index > 0) {
				$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
			}

			$tmp_index++;
		}
	}

	$pg_selected = isset($_POST["select_pg"]) ? $_POST["select_pg"] : "";

?>

	<div class="col-12">
		<div class="singleTableItems">
			<div class="setting_sub_head">
				<div class="grid2 upper">
					<h6 class="m-0 XSmall"> Size of</h6>
					<select id="select_pg_1" onchange="return changeSelectPG(1);">
						<option value="1" <?php if ($pg_selected == "1") {
												echo "selected";
											} ?>>Player</option>
						<option value="2" <?php if ($pg_selected == "2") {
												echo "selected";
											} ?>>Goalie</option>
					</select>
				</div>
				<div class="column2 text-end">
					<button class="btn btn-dark iconBTn">
						<figure class="m-0"><img src="images/vector/addWhite.png" alt="" onclick="return addNewSize(2);"></figure> Size
					</button>
				</div>




			</div>
			<div id="p_shell_pant_size">
				<table class="tbl_content_size" id="tbl_show_prod_2_1">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Size</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_size[1] as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
									<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(2,1,<?php echo $row_content["size_id"]; ?>);"></figure>
								</div>
							</td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<?php
									if ($row_content["previous_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(2,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>
									<?php
									}

									if ($row_content["next_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(2,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>
									<?php
									}
									?>
								</div>
							</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>
			</div>
			<div id="g_shell_pant_size" style="display: none;">
				<table class="tbl_content_size" id="tbl_show_prod_2_2">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Size</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_size[2] as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
									<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(2,2,<?php echo $row_content["size_id"]; ?>);"></figure>
								</div>

							</td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<?php
									if ($row_content["previous_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(2,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>


									<?php
									}

									if ($row_content["next_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(2,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>

									<?php
									}
									?>
								</div>
							</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>

			</div>
		</div>

	</div>




	<script type="text/javascript">
		changeSelectPG(2);
	</script>
<?php
} else if ($prod_id == "3") {

	$sql_size = "SELECT * FROM tbl_size WHERE prod_id=3 AND enable=1 ORDER BY split_order ASC,sort_no ASC";
	$rs_size = $conn->query($sql_size);

	$a_size = array();

	while ($row_size = $rs_size->fetch_assoc()) {

		$a_size[($row_size["split_order"])][] = $row_size;
	}

	foreach ($a_size as $a_split_type => $a_tmp_row_size) {

		$num_size_row = sizeof($a_tmp_row_size);
		$tmp_index = 0;

		foreach ($a_tmp_row_size as $tmp_size_key => $a_row_size) {
			if ($tmp_index == 0) {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = 0;
			} else {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = $a_size[($a_row_size["split_order"])][($tmp_index - 1)]["size_id"];
			}

			if (($tmp_index + 1) == $num_size_row) {

				if (($tmp_index - 1) < 0) {
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				} else {
					$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				}
			} else if ($tmp_index > 0) {
				$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
			}

			$tmp_index++;
		}
	}


	$pg_selected = isset($_POST["select_pg"]) ? $_POST["select_pg"] : "";

?>


	<div class="col-12">
		<div class="singleTableItems">
			<div class="setting_sub_head">
				<div class="grid2 upper">
					<h6 class="m-0 XSmall"> Tops & Bottoms size of</h6>
					<select id="select_pg_3" onchange="return changeSelectPG(3);">
						<option value="1" <?php if ($pg_selected == "1") {
												echo "selected";
											} ?>>Adult</option>
						<option value="2" <?php if ($pg_selected == "2") {
												echo "selected";
											} ?>>Women</option>
						<option value="3" <?php if ($pg_selected == "3") {
												echo "selected";
											} ?>>Youth</option>
					</select>
				</div>
				<div class="column2 text-end">
					<button class="btn btn-dark iconBTn" onclick="return addNewSize(3);">
						<figure class="m-0"><img src="images/vector/addWhite.png" alt=""></figure> Size
					</button>
				</div>
			</div>
			<div id="p_tops_bottoms_size">
				<table class="tbl_content_size" id="tbl_show_prod_3_1">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Size</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_size[1] as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
									<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(3,1,<?php echo $row_content["size_id"]; ?>);"></figure>
								</div>

							</td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<?php
									if ($row_content["previous_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(3,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>

									<?php
									}

									if ($row_content["next_id"] != 0) {
									?>
										<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(3,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>


									<?php
									}
									?>
								</div>
							</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>
			</div>
			<div id="g_tops_bottoms_size" style="display: none;">
				<table class="tbl_content_size" id="tbl_show_prod_3_2">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Size</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_size[2] as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
									<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(3,2,<?php echo $row_content["size_id"]; ?>);"></figure>
								</div>

							</td>
							<td style="text-align: center;">
								<?php
								if ($row_content["previous_id"] != 0) {
								?>
									<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(3,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>


								<?php
								}

								if ($row_content["next_id"] != 0) {
								?>
									<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(3,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure>


								<?php
								}
								?>
							</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>

			</div>
			<div id="r_tops_bottoms_size" style="display: none;">
				<table class="tbl_content_size" id="tbl_show_prod_3_3">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Size</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_size[3] as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
									<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(3,2,<?php echo $row_content["size_id"]; ?>);"></figure>
								</div>
							</td>
							<td style="text-align: center;">
								<?php
								if ($row_content["previous_id"] != 0) {
								?>
									<figure class="m-0"><img src="images/vector/Arrow_up.png" alt="" onclick="return swapSorting(3,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"></figure>
								<?php
								}

								if ($row_content["next_id"] != 0) {
								?>
									<figure class="m-0"><img src="images/vector/Arrow_Down.png" alt="" onclick="return swapSorting(3,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"></figure


										<?php
									}
										?>
										</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>

			</div>
		</div>
	</div>




	<script type="text/javascript">
		changeSelectPG(3);
	</script>
<?php
} else if ($prod_id == "4") {

	$sql_choices = "SELECT * FROM tbl_product_choices WHERE prod_id=4 AND enable=1 ORDER BY sort_no ASC";
	$rs_choices = $conn->query($sql_choices);

	$a_choices = array();

	while ($row_choices = $rs_choices->fetch_assoc()) {

		$a_choices[] = $row_choices;
	}

	$num_choice_row = sizeof($a_choices);
	$tmp_index = 0;

	foreach ($a_choices as $tmp_c_key => $a_row_choice) {
		if ($tmp_index == 0) {
			$a_choices[$tmp_index]["previous_id"] = 0;
		} else {
			$a_choices[$tmp_index]["previous_id"] = $a_choices[($tmp_index - 1)]["pro_choice_id"];
		}

		if (($tmp_index + 1) == $num_choice_row) {

			if (($tmp_index - 1) < 0) {
				$a_choices[$tmp_index]["next_id"] = 0;
			} else {
				$a_choices[($tmp_index - 1)]["next_id"] = $a_row_choice["pro_choice_id"];
				$a_choices[$tmp_index]["next_id"] = 0;
			}
		} else if ($tmp_index > 0) {
			$a_choices[($tmp_index - 1)]["next_id"] = $a_row_choice["pro_choice_id"];
		}

		$tmp_index++;
	}

	/*echo "<pre>";
	print_r($a_choices);
	echo "</pre>";*/
	$sql_size = "SELECT * FROM tbl_size WHERE prod_id=4 AND enable=1 ORDER BY split_order ASC,sort_no ASC";
	$rs_size = $conn->query($sql_size);

	$a_size = array();

	while ($row_size = $rs_size->fetch_assoc()) {

		$a_size[($row_size["split_order"])][] = $row_size;
	}

	foreach ($a_size as $a_split_type => $a_tmp_row_size) {

		$num_size_row = sizeof($a_tmp_row_size);
		$tmp_index = 0;

		foreach ($a_tmp_row_size as $tmp_size_key => $a_row_size) {
			if ($tmp_index == 0) {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = 0;
			} else {
				$a_size[($a_row_size["split_order"])][$tmp_index]["previous_id"] = $a_size[($a_row_size["split_order"])][($tmp_index - 1)]["size_id"];
			}

			if (($tmp_index + 1) == $num_size_row) {

				if (($tmp_index - 1) < 0) {
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				} else {
					$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
					$a_size[($a_row_size["split_order"])][$tmp_index]["next_id"] = 0;
				}
			} else if ($tmp_index > 0) {
				$a_size[($a_row_size["split_order"])][($tmp_index - 1)]["next_id"] = $a_row_size["size_id"];
			}

			$tmp_index++;
		}
	}

?>

	<div class="col-6">
		<div class="singleTableItems">
			<div class="setting_sub_head">

				<div class="grid2 upper">
					<h6 class="m-0 XSmall"> Product choices </h6>

				</div>
				<div class="column2 text-end">
					<button class="btn btn-dark iconBTn" onclick="return addNewChoice(4);">
						<figure class=" m-0"><img src="images/vector/addWhite.png" alt=""></figure> Size
					</button>
				</div>


			</div>

			<div id="manage_product_choices">
				<table class="tbl_content_size" id="tbl_show_prod_4_0">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Choice</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_choices as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_choice<?php echo $row_content["pro_choice_id"]; ?>"><?php echo $row_content["choice_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editChoice(<?php echo $row_content["pro_choice_id"]; ?>);""></figure>
					<figure class=" m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteChoice(4,<?php echo $row_content["pro_choice_id"]; ?>);"></figure>
								</div>
							</td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<?php
									if ($row_content["previous_id"] != 0) {
									?>
										<figure class="m-0" onclick="return swapChoiceSorting(4,<?php echo $row_content["pro_choice_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"><img src="images/vector/Arrow_up.png" alt=""></figure>


									<?php
									}

									if ($row_content["next_id"] != 0) {
									?>
										<figure class="m-0" onclick="return swapChoiceSorting(4,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["pro_choice_id"]; ?>);"><img src="images/vector/Arrow_Down.png" alt=""></figure>


									<?php
									}
									?>
								</div>
							</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>

			</div>
		</div>
	</div>
	<div class="col-6">
		<div class="singleTableItems">
			<div class="setting_sub_head">

				<div class="grid2 upper">
					<h6 class="m-0 XSmall">Product size </h6>

				</div>
				<div class="column2 text-end">
					<button class="btn btn-dark iconBTn" onclick="return addNewSize2(4,1);">
						<figure class=" m-0"><img src="images/vector/addWhite.png" alt=""></figure> Size
					</button>
				</div>

			</div>

			<div id="manage_product_size">
				<table class="tbl_content_size" id="tbl_show_prod_4_1">
					<tr class="stickyTr">
						<th style="text-align: center;">#</th>
						<th>Size</th>
						<th style="text-align: center;">Edit</th>
						<th style="text-align: center;">Move</th>
					</tr>
					<?php
					$count_row = 1;
					foreach ($a_size[1] as $tmp_key => $row_content) {
					?>
						<tr>
							<td style="text-align: center;"><?php echo $count_row; ?></td>
							<td id="td_size<?php echo $row_content["size_id"]; ?>"><?php echo $row_content["size_name"]; ?></td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<figure class="m-0"><img src="images/vector/purpleEdit.png" alt="" onclick="return editSize(<?php echo $row_content["size_id"]; ?>);"></figure>
									<figure class="m-0"><img src="images/vector/deleteIcon.png" alt="" onclick="return deleteSize(4,1,<?php echo $row_content["size_id"]; ?>);"></figure>
								</div>
							</td>
							<td style="text-align: center;">
								<div class="iconBTn">
									<?php
									if ($row_content["previous_id"] != 0) {
									?>
										<figure class="m-0" onclick="return swapSorting(4,<?php echo $row_content["size_id"]; ?>,<?php echo $row_content["previous_id"]; ?>);"><img src="images/vector/Arrow_up.png" alt=""></figure>


									<?php
									}

									if ($row_content["next_id"] != 0) {
									?>
										<figure class="m-0" onclick="return swapSorting(4,<?php echo $row_content["next_id"]; ?>,<?php echo $row_content["size_id"]; ?>);"><img src="images/vector/Arrow_Down.png" alt=""></figure>

									<?php
									}
									?>
								</div>

							</td>
						</tr>
					<?php
						$count_row++;
					}
					?>
				</table>

			</div>
		</div>
	</div>
<?php
}
?>