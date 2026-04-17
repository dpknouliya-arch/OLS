<?php
	session_start();

	if(!isset($_SESSION["JOGOLS"])){
		echo '<center>Please re-login again.</center>';
		exit();
	}

	include('../../db.php');

	$prod_id = $_POST["prod_id"];
    $form_id = $_POST["form_id"];

    $on_team_name = base64_decode($_POST["on_team_name"]);
    $on_year = base64_decode($_POST["on_year"]);
    $form_name = $on_team_name." ".$on_year;

    $sql_product = "SELECT * FROM tbl_product WHERE prod_id='".$prod_id."';";
	$rs_product = $conn->query($sql_product);
	$row_product = $rs_product->fetch_assoc();
?>
<center id="sameteam<?php echo $form_id; ?>">
<div class="tab-content" id="tab-content">
	<div class="tab-pane active" id="fill-tabpanel-<?php echo $form_id; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $form_id; ?>">
		<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>" style="border:10px solid #FFA; border-radius: 5px; background-color: #FFA; width: 100%;">

			<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id; ?>">
			<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
			<input type="hidden" id="on_team_name_list<?php echo $form_id; ?>" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">
			<input type="hidden" id="on_year_list<?php echo $form_id; ?>" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">
			<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new_file">
			<center>
				<h6>
					<span id="sp_outter_panel<?php echo $form_id; ?>">
						<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
						<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
							
						</span>
						<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
							<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
							<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
						</span>
					</span>
					<?php echo " (".$row_product["prod_name"].")"; ?>
					<i class="fa fa-minus-circle" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i>
				</h6>

				Upload order form: <input type="file" name="file_upload[<?php echo $form_id; ?>]" class="file_field">
			</center>
		</div>
	</div>
</div>
</center>