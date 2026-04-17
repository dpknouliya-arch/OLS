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


	$sql_size = "SELECT * FROM tbl_size WHERE prod_id='".$prod_id."' AND enable=1 AND (size_of_person='youth' OR size_of_person='adult_youth') ORDER BY split_order ASC,sort_no ASC;";
	$rs_size = $conn->query($sql_size);

	$a_size = array();
	while($row_size = $rs_size->fetch_assoc()){
		$a_size[($row_size["split_order"])][] = $row_size;
		$spl_order = $row_size["split_order"];
	}

	//$draft_id = $_POST["draft_id"];
	$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
	$user_id = $obj_user->user_id;

	$a_sub_user = array();

	$sql_sub_user = "SELECT sub_user_id,nick_name FROM tbl_sub_user WHERE parent_user_id='".$user_id."' AND enable=1 ORDER BY nick_name ASC; ";
	$rs_sub_user = $conn->query($sql_sub_user);
	if($rs_sub_user->num_rows >0){
		while($row_sub_user = $rs_sub_user->fetch_assoc() ){
			$a_sub_user[($row_sub_user["sub_user_id"])] = $row_sub_user["nick_name"];
		}
		
	}


if($prod_id=="1"){
?>
<center>
<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>" style="border:10px solid #FFA; border-radius: 5px; background-color: #FFA; width: 100%;">
<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="1">
<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
<input type="hidden" id="on_team_name_list<?php echo $form_id; ?>" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">
<input type="hidden" id="on_year_list<?php echo $form_id; ?>" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">
<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new">
	<center>
		<h6>
			<span id="sp_outter_panel<?php echo $form_id; ?>">
				<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
				<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
					<!-- <i class="fa fa-pencil" style="cursor: pointer; color: #00F;" onclick="return editFormName(<?php echo $form_id; ?>);"></i> -->
				</span>
				<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
					<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
					<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
				</span>
			</span>
			<?php echo " (".$row_product["prod_name"].")"; ?>
			<i class="fa fa-minus-circle" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i>
		</h6>
	</center>
	<?php
	if(sizeof($a_sub_user)>0){
	?>
	<div id="d_assign_select_new<?php echo $form_id; ?>" class="assign_tag">Assign to 
		<select name="select_assign[<?php echo $form_id; ?>]">
			<option value="">==Select one==</option>
			<?php
			foreach($a_sub_user as $sub_user_id => $nick_name){
				echo '<option value="'.$sub_user_id.'">'.$nick_name.'</option>';
			}
			?>
		</select>
	</div>
	<?php
	}
	?>
	<table class="tbl_item_form" style="width: 100%;">
		<tr>
			<th style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #0A0; cursor: pointer; text-align: right;" onclick="return addItemRow(<?php echo $form_id; ?>,1);">
				<i class="fa fa-plus-circle"></i>
				<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="16">
			</th>
			<th style="width:150px;">Name on Jersey</th>
			<th style="width:65px;">Pattern Cut</th>
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
			<th style="width:125px;">Name For Packing</th>
			<th>Notes</th>
		</tr>
		<tbody id="prod_item_<?php echo $form_id; ?>">
		    <?php
		    for($tet=1;$tet<17;$tet++){
		    ?>
			<tr id="prod_item_<?php echo $form_id; ?>_<?=$tet?>">
				<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow('new',<?php echo $form_id; ?>,<?=$tet?>);">
					<i class="fa fa-minus-circle"></i>
					<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">
				</td>
				<td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120"></td>
				<td>
					<select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?=$tet?>','select_pg_<?php echo $form_id; ?>_<?=$tet?>','select_jsize_<?php echo $form_id; ?>_<?=$tet?>','select_ssize_<?php echo $form_id; ?>_<?=$tet?>','<?php echo $prod_id;?>');" id="select_mf_<?php echo $form_id; ?>_<?=$tet?>" name="select_mf[<?php echo $form_id; ?>][]" >
					    <option value="youth">YOUTH</option>
						<option value="male">ADULT</option>
						<option value="female">WOMEN-ADULT</option>
					</select>
				</td>
				<td>
					<select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?=$tet?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?=$tet?>','select_pg_<?php echo $form_id; ?>_<?=$tet?>','select_jsize_<?php echo $form_id; ?>_<?=$tet?>','select_ssize_<?php echo $form_id; ?>_<?=$tet?>','<?php echo $prod_id;?>');">
						<option value="player" title="Player">Player</option>
						<option value="goalie" title="Goalie">Goalie</option>
					</select>
				</td>
				<td >
					<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?=$tet?>" name="select_jsize[<?php echo $form_id; ?>][]">
						<option value="0"></option>
						<?php for($i=0; $i<sizeof($a_size["1"]);$i++){ ?>
						<option value="<?php echo $a_size["1"][$i]["size_id"]; ?>" ><?php echo $a_size["1"][$i]["size_name"]; ?></option>
						<?php } ?>
					</select>
				</td>
				<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>
				<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');"></td>
				<td><input class="white_in" name="jersey_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in jersey_qty2_<?php echo $form_id; ?>" name="jersey_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');"></td>
				<td>
					<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?=$tet?>" name="select_ssize[<?php echo $form_id; ?>][]">
						<option value="0"></option>
						<?php for($i=0; $i<sizeof($a_size["3"]);$i++){ ?>
						<option value="<?php echo $a_size["3"][$i]["size_id"]; ?>" ><?php echo $a_size["3"][$i]["size_name"]; ?></option>
						<?php } ?>
					</select>
				</td>
				<td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'sock_qty_<?php echo $form_id; ?>');"></td>
				<td><input class="white_in" name="sock_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in sock_qty2_<?php echo $form_id; ?>" name="sock_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'sock_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'sock_qty2_<?php echo $form_id; ?>');"></td>
				<td>
					<select class="white_in" name="select_ca[<?php echo $form_id; ?>][]">
						<option value=""></option>
						<option value="captain">Captain</option>
						<option value="assistant">Assistant</option>
					</select>
				</td>
				<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>
				<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>
			</tr>
			<?php
		    }
			?>
		</tbody>
		<tr>
			<th style="border-width: 0px; background-color: #FFA;"></th>
			<th>TOTAL ORDER</th><th></th><th></th><th></th><th></th><th></th>
			<th id="total_jersey_qty_<?php echo $form_id; ?>">0</th>
			<th></th>
			<th id="total_jersey_qty2_<?php echo $form_id; ?>">0</th>
			<th></th><th></th>
			<th id="total_sock_qty_<?php echo $form_id; ?>">0</th>
			<th></th>
			<th id="total_sock_qty2_<?php echo $form_id; ?>">0</th>
			<th></th><th></th><th></th>
		</tr>
	</table>

</div>
</center>
<?php
}else if($row_product["split_type"]=="2"){

	$tmp_split = explode(",", $row_product["split_name"]);
	$split_name1 = $tmp_split[0];
	$split_name2 = $tmp_split[1];
?>
<center>
<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>" style="border:10px solid #FFA; border-radius: 5px; background-color: #FFA; width: 100%;">
<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id?>">
<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
<input type="hidden" id="on_team_name_list<?php echo $form_id; ?>" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">
<input type="hidden" id="on_year_list<?php echo $form_id; ?>" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">
<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new">
	<center>
		<h6>
			<span id="sp_outter_panel<?php echo $form_id; ?>">
				<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
				<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
					<i class="fa fa-pencil" style="cursor: pointer; color: #00F;" onclick="return editFormName(<?php echo $form_id; ?>);"></i>
				</span>
				<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
					<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
					<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
				</span>
			</span>
			<?php echo " (".$row_product["prod_name"].")"; ?>
			<i class="fa fa-minus-circle" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i>
		</h6>
	</center>
	<?php
	if(sizeof($a_sub_user)>0){
	?>
	<div id="d_assign_select_new<?php echo $form_id; ?>" class="assign_tag">Assign to 
		<select name="select_assign[<?php echo $form_id; ?>]">
			<option value="">==Select one==</option>
			<?php
			foreach($a_sub_user as $sub_user_id => $nick_name){
				echo '<option value="'.$sub_user_id.'">'.$nick_name.'</option>';
			}
			?>
		</select>
	</div>
	<?php
	}
	?>
	<table class="tbl_item_form" style="width:100%">
		<tr>
			<th style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #0A0; cursor: pointer; text-align: right;" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">
				<i class="fa fa-plus-circle"></i>
				<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="1">
			</th>
			<?php 
				if($row_product["have_name"]=="1"){ 
			?>
			<th style="width:150px;">Name on <?php echo $split_name1; ?></th>
			<?php 
				}
				
				if($row_product["choose_pg"]=="1"){ 
			?>
			<th style="width:65px;">P or G</th>
			<?php 
				}
				
				if($row_product["choose_mf"]=="1"){ 
			?>
			<th style="width:90px;">Pattern Cut</th>
			<?php 
				}
			?>
			<th style="width:80px;"><?php echo $split_name1; ?> Size</th>
			<th><?php echo $split_name1; ?> #</th>
			<th><?php echo $split_name1; ?> Color</th>
			<th style="width:50px;">QTY</th>
			<th style="width:80px;"><?php echo $split_name2; ?> Size</th>
			<th><?php echo $split_name2; ?> Color</th>
			<th style="width:50px;">QTY</th>
			<th style="width:100px">Name For Packing</th>
			<th>Notes</th>
		</tr>
		<tbody id="prod_item_<?php echo $form_id; ?>">
			<tr id="prod_item_<?php echo $form_id; ?>_1">
				<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow('new',<?php echo $form_id; ?>,1,2);">
					<i class="fa fa-minus-circle"></i>
					<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">
				</td>
				<?php 
					if($row_product["have_name"]=="1"){ 
				?>
				<td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120"></td>
				<?php 
					}
					
					if($row_product["choose_pg"]=="1"){ 
				?>
				<td>
					<select class="white_in" id="select_pg_<?php echo $form_id; ?>_1" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,1);">
						<option value="player" title="Player">Player</option>
						<option value="goalie" title="Goalie">Goalie</option>
					</select>
				</td>
				<?php 
					}
					
					if($row_product["choose_mf"]=="1"){ 
				?>
				<td>
					<select class="white_in" id="select_mf_<?php echo $form_id; ?>_1" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_1','select_pg_<?php echo $form_id; ?>_1','select_jsize_<?php echo $form_id; ?>_1','select_ssize_<?php echo $form_id; ?>_1','<?php echo $prod_id;?>');">
						<option value="youth">YOUTH</option>
						<option value="male">ADULT</option>
						<option value="female_youth">WOMEN-YOUTH</option>
						<option value="female">WOMEN-ADULT</option>
					</select>
				</td>
				<input type="hidden" value="uni" id="select_pg_<?php echo $form_id; ?>_1">
				<?php 
					}
				?>
				<td >
					<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_1" name="select_jsize[<?php echo $form_id; ?>][]">
						<option value="0"></option>
						<?php for($i=0; $i<sizeof($a_size[$spl_order]);$i++){ ?>
						<option value="<?php echo $a_size[$spl_order][$i]["size_id"]; ?>" ><?php echo $a_size[$spl_order][$i]["size_name"]; ?></option>
						<?php } ?>
					</select>
				</td>
				<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>
				<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');"></td>
				<td>
					<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_1" name="select_ssize[<?php echo $form_id; ?>][]">
						<option value="0"></option>
						<?php for($i=0; $i<sizeof($a_size[$spl_order]);$i++){ ?>
						<option value="<?php echo $a_size[$spl_order][$i]["size_id"]; ?>" ><?php echo $a_size[$spl_order][$i]["size_name"]; ?></option>
						<?php } ?>
					</select>
				</td>
				<td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');"></td>
				<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>
				<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>
				
			</tr>
		</tbody>
		<tr>
			<th style="border-width: 0px; background-color: #FFA;"></th>
			<th>TOTAL ORDER</th>
			<?php 
				if($row_product["have_name"]=="1"){ 
			?>
			<th></th>
			<?php 
				}
				
				if($row_product["choose_pg"]=="1"){ 
			?>
			<th></th>
			<?php 
				}
				
				if($row_product["choose_mf"]=="1"){ 
			?>
			<th></th>
			<?php 
				}

				if($row_product["have_size"]=="1"){ 
			?>
			<th></th>
			<?php 
				}

				if($row_product["have_number"]=="1"){ 
			?>
			<th></th>
			<?php 
				}
			?>
			<th id="total_jersey_qty_<?php echo $form_id; ?>">0</th>
			<th></th><th></th>
			<th id="total_sock_qty_<?php echo $form_id; ?>">0</th>
			<th></th><th></th><th></th>
		</tr>
		<tr>
			<th style="border-width: 0px; background-color: #FFA;"></th>
			<th>Special Comments <br> (if any)</th><th colspan="16" style="background-color: white;"><input type="text" name="special_comment[<?=$form_id?>]" placeholder="Enter Special Comment here..." style="width:100%;"></th>
		</tr>
	</table>

</div>
</center>
<?php
}else{

	$split_name = $row_product["split_name"];
?>
<center>
<div class="prod_card" id="prod_card<?php echo $form_id; ?>" card-id="<?php echo $form_id; ?>" style="border:10px solid #FFA; border-radius: 5px; background-color: #FFA; width: 100%;">
<input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">
<input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id?>">
<input type="hidden" id="form_name_list<?php echo $form_id; ?>" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">
<input type="hidden" id="on_team_name_list<?php echo $form_id; ?>" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">
<input type="hidden" id="on_year_list<?php echo $form_id; ?>" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">
<input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new">
	<center>
		<h6>
			<span id="sp_outter_panel<?php echo $form_id; ?>">
				<span id="show_edit_form_name<?php echo $form_id; ?>"><?php echo $form_name; ?></span>
				<span id="sp_edit_fn_panel<?php echo $form_id; ?>">
					<i class="fa fa-pencil" style="cursor: pointer; color: #00F;" onclick="return editFormName(<?php echo $form_id; ?>);"></i>
				</span>
				<span id="sp_save_edit_fn_panel<?php echo $form_id; ?>" style="display:none;">
					<i class="fa fa-check" style="cursor: pointer; color: #0F0;" onclick="return editFormNameDone(<?php echo $form_id; ?>);"></i>
					<i class="fa fa-times" style="cursor: pointer; color: #F00; margin-left: 5px;" onclick="return editFormNameCancel(<?php echo $form_id; ?>);"></i>
				</span>
			</span>
			<?php echo " (".$row_product["prod_name"].")"; ?>
			<i class="fa fa-minus-circle" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i>
		</h6>
	</center>
	<?php
	if(sizeof($a_sub_user)>0){
	?>
	<div id="d_assign_select_new<?php echo $form_id; ?>" class="assign_tag">Assign to 
		<select name="select_assign[<?php echo $form_id; ?>]">
			<option value="">==Select one==</option>
			<?php
			foreach($a_sub_user as $sub_user_id => $nick_name){
				echo '<option value="'.$sub_user_id.'">'.$nick_name.'</option>';
			}
			?>
		</select>
	</div>
	<?php
	}
	?>
	<table class="tbl_item_form" style="width:100%">
		<tr>
			<th style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #0A0; cursor: pointer; text-align: right;" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">
				<i class="fa fa-plus-circle"></i>
				<input type="hidden" id="num_item_<?php echo $form_id; ?>" value="1">
			</th>
			<?php 
				if($row_product["have_name"]=="1"){ 
			?>
			<th style="width:150px;"><?php echo $split_name; ?></th>
			<?php 
				}
				
				if($row_product["choose_pg"]=="1"){ 
			?>
			<th style="width:65px;">P or G</th>
			<?php 
				}
				if($row_product["prod_id"]=="2"){ 
			?>
			<th style="width:65px;">Pattern Cut</th>
			<?php 
				}
				
				if($row_product["have_size"]=="1" && $prod_id=="4"){ 
			?>
			<th style="width:80px;" class="glued_body">Glue</th>
			<?php 
				}
				
				if($row_product["choose_mf"]=="1"){ 
			?>
			<th style="width:90px;">Pattern Cut</th>
			<?php 
				}

				if($row_product["have_size"]=="1"){ 
			?>
			<th style="width:80px;"><?php echo $split_name; ?> Size</th>
			<?php 
				}
				
				if($row_product["have_number"]=="1"){ 
			?>
			<th><?php echo $split_name; ?> #</th>
			<?php 
				}
			?>
			<th><?php echo $split_name; ?> Color</th>
			<th style="width:50px;">QTY</th>
			<th style="width:100px">Name For Packing</th>
			<th>Notes</th>
		</tr>
		<tbody id="prod_item_<?php echo $form_id; ?>">
			<tr id="prod_item_<?php echo $form_id; ?>_1">
				<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow('new',<?php echo $form_id; ?>,1,1);">
					<i class="fa fa-minus-circle"></i>
					<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">
				</td>
				<?php 
					if($row_product["have_name"]=="1"){ 
				?>
				<td>
					<?php
					$sql_choices = "SELECT * FROM tbl_product_choices WHERE prod_id='".$prod_id."' AND enable=1 ORDER BY sort_no ASC;";
					$rs_choices = $conn->query($sql_choices);

					if( $rs_choices->num_rows > 0 ){
						?>
						<select class="white_in" id="product_id_<?php echo $form_id; ?>_1" onchange="return changePatternExtra('product_id_<?php echo $form_id; ?>_1','select_jsize_<?php echo $form_id; ?>_1','select_glue_num_<?php echo $form_id; ?>_1','select_jersey_num_<?php echo $form_id; ?>_1','<?php echo $prod_id;?>');" name="player_name[<?php echo $form_id; ?>][]" >
						<?php
						while($row_choices = $rs_choices->fetch_assoc()){
							?>
							<option value="<?php echo $row_choices["choice_name"]; ?>"><?php echo $row_choices["choice_name"]; ?></option>
							<?php
						}
						?>
						</select>
						<?php
					}else{
						?>
						<input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120">
						<?php
					}
					?>
				</td>
				<?php 
					}
					
					if($row_product["choose_pg"]=="1"){ 
				?>
				<td>
					<select class="white_in" id="select_pg_<?php echo $form_id; ?>_1" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_1','select_pg_<?php echo $form_id; ?>_1','select_jsize_<?php echo $form_id; ?>_1','select_ssize_<?php echo $form_id; ?>_1','<?php echo $prod_id;?>');">
						<option value="player" title="Player">Player</option>
						<option value="goalie" title="Goalie">Goalie</option>
					</select>
				</td>
				<?php 
					}
					
					if($row_product["prod_id"]=="2"){ 
			?>
			    <td>
					<select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_1','select_pg_<?php echo $form_id; ?>_1','select_jsize_<?php echo $form_id; ?>_1','select_ssize_<?php echo $form_id; ?>_1','<?php echo $prod_id;?>');" id="select_mf_<?php echo $form_id; ?>_1" name="select_mf[<?php echo $form_id; ?>][]" >
					    <option value="youth">YOUTH</option>
						<option value="male">ADULT</option>
					</select>
				</td>
			<?php 
				}
					
					if($row_product["choose_mf"]=="1"){ 
				?>
				<td>
					<select class="white_in" id="select_mf_<?php echo $form_id; ?>_1" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,1,1);">
						<option value="male">Male</option>
						<option value="female">Female</option>
					</select>
				</td>
				<?php 
					}
					if($row_product["have_size"]=="1" && $prod_id=="4"){ 
				?>
				<td class="glued_body">
					<select class="white_in" name="select_mf[<?php echo $form_id; ?>][]" id="select_glue_num_<?php echo $form_id; ?>_1" disabled>
					    <option value="na" selected>N/A</option>
						<option value="Yes">Yes</option>
						<option value="No">No</option>
					</select>
				</td>
				<?php 
					}

					if($row_product["have_size"]=="1"){ 
				?>
				<td >
					<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_1" name="select_jsize[<?php echo $form_id; ?>][]">
						<option value="0"></option>
						<?php for($i=0; $i<sizeof($a_size["1"]);$i++){ ?>
						<option value="<?php echo $a_size["1"][$i]["size_id"]; ?>" ><?php echo $a_size["1"][$i]["size_name"]; ?></option>
						<?php } ?>
					</select>
				</td>
				<?php 
					}

					if($row_product["have_number"]=="1"){ 
				?>
				<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>
				<?php 
					}
				?>
				<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
				<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');"></td>
				<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>
				<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>
				
			</tr>
		</tbody>
		<tr>
			<th style="border-width: 0px; background-color: #FFA;"></th>
			<th>TOTAL ORDER</th>
			<?php 
				if($row_product["have_name"]=="1"){ 
			?>
			<th></th>
			<?php 
				}
				
				if($row_product["choose_pg"]=="1"){ 
			?>
			<th></th>
			<?php 
				}
				
				if($row_product["choose_mf"]=="1"){ 
			?>
			<th></th>
			<?php 
				}

				if($row_product["have_size"]=="1"){ 
			?>
			<th></th>
			<?php 
				}

				if($row_product["have_number"]=="1"){ 
			?>
			<th></th>
			<?php 
				}

				if($row_product["prod_id"]=="2"){ 
			?>
			<th>
			<?php 
				}
				if($row_product["prod_id"]=="4"){ 
			?>
			<th>
			<?php 
				}
			?>
			<th id="total_jersey_qty_<?php echo $form_id; ?>">0</th>
			<th></th><th></th><?php
			if($row_product["prod_id"]!="4"){
			?><th></th>
			<?php } ?>
		</tr>
		<tr>
			<th style="border-width: 0px; background-color: #FFA;"></th>
			<th>Special Comments <br> (if any)</th><th colspan="16" style="background-color: white;"><input type="text" name="special_comment[<?=$form_id?>]" placeholder="Enter Special Comment here..." style="width:100%;"></th>
		</tr>
		</tr>
	</table>

</div>
</center>
<?php
}
?>