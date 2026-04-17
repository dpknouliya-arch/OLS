<?php
	session_start();

	if(!isset($_SESSION["JOGOLS"])){
		echo '<center>Please re-login again.</center>';
		exit();
	}

	include('../../db.php');

    $prod_id = $_POST["prod_id"];
    $form_id = $_POST["form_id"];

	$sql_product = "SELECT * FROM tbl_product WHERE prod_id='".$prod_id."';";
	$rs_product = $conn->query($sql_product);
	$row_product = $rs_product->fetch_assoc();

	$sql_size = "SELECT * FROM tbl_size WHERE prod_id='".$prod_id."' AND enable=1 AND (size_of_person='youth' OR size_of_person='adult_youth') ORDER BY split_order ASC,sort_no ASC;";
	$rs_size = $conn->query($sql_size);

	$a_size = array();
	while($row_size = $rs_size->fetch_assoc()){
		$a_size[($row_size["split_order"])][] = $row_size;
		$split_name = $row_size["split_order"];
	}

	$row_id = intval($_POST["num_item"])+1;
	if($prod_id==2){
	    $split_name=1;
	}

if($prod_id=="1"){	
?>
	<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_id; ?>">
		<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow(<?php echo $form_id; ?>,'new',1,<?php echo $row_id; ?>);">
			<i class="fa fa-minus-circle"></i>
			<input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">
		</td>
		<td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120"></td>
		<td>
			<select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','<?php echo $prod_id;?>');" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_mf[<?php echo $form_id; ?>][]" >
			    <option value="youth">YOUTH</option>
				<option value="male">ADULT</option>
				<option value="female">WOMEN-ADULT</option>
			</select>
		</td>
		<td>
			<select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','<?php echo $prod_id;?>');">
				<option value="player" title="Player">Player</option>
				<option value="goalie" title="Goalie">Goalie</option>
			</select>
		</td>
		<td >
			<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
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
			<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_ssize[<?php echo $form_id; ?>][]">
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
}else if($row_product["split_type"]=="2"){

	/*$tmp_split = explode(",", $row_product["split_name"]);
	$split_name1 = $tmp_split[0];
	$split_name2 = $tmp_split[1];*/
?>
	<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_id; ?>">
		<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow(<?php echo $form_id; ?>,'new',<?php echo $prod_id; ?>,<?php echo $row_id; ?>,2);">
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
			<select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,<?php echo $row_id; ?>);">
				<option value="player" title="Player">Player</option>
				<option value="goalie" title="Goalie">Goalie</option>
			</select>
		</td>
		<?php 
			}
			
			if($row_product["choose_mf"]=="1"){ 
		?>
		<td>
			<select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','<?php echo $prod_id;?>');">
				<option value="youth">YOUTH</option>
				<option value="male">ADULT</option>
				<option value="female_youth">WOMEN-YOUTH</option>
				<option value="female">WOMEN-ADULT</option>
			</select>
		</td>
		<input type="hidden" value="uni" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_id;?>">
		<?php 
			}
		?>
		<td >
			<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
				<option value="0"></option>
				<?php for($i=0; $i<sizeof($a_size[$split_name]);$i++){ ?>
				<option value="<?php echo $a_size[$split_name][$i]["size_id"]; ?>" ><?php echo $a_size[$split_name][$i]["size_name"]; ?></option>
				<?php } ?>
			</select>
		</td>
		<td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>
		<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
		<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');"></td>
		<td>
			<select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_ssize[<?php echo $form_id; ?>][]">
				<option value="0"></option>
				<?php for($i=0; $i<sizeof($a_size[$split_name]);$i++){ ?>
				<option value="<?php echo $a_size[$split_name][$i]["size_id"]; ?>" ><?php echo $a_size[$split_name][$i]["size_name"]; ?></option>
				<?php } ?>
			</select>
		</td>
		<td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
		<td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');"></td>
		<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>
		<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>
	</tr>
<?php
}else{

?>
	<tr id="prod_item_<?php echo $form_id; ?>_<?php echo $row_id; ?>">
		<td style="border-width: 0px; background-color: #FFA; font-size: 16px; color: #F00; cursor: pointer; text-align: right;" onclick="return deleteItemRow(<?php echo $form_id; ?>,'new',<?php echo $prod_id; ?>,<?php echo $row_id; ?>,1);">
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
				<select class="white_in" id="product_id_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="player_name[<?php echo $form_id; ?>][]" onchange="return changePatternExtra('select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>','product_id_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_glue_num_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jersey_num_<?php echo $form_id; ?>_<?php echo $row_id; ?>','<?php echo $prod_id;?>');">
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
			<select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','<?php echo $prod_id;?>');">
				<option value="player" title="Player">Player</option>
				<option value="goalie" title="Goalie">Goalie</option>
			</select>
		</td>
		<?php 
			}
			
			if($row_product["choose_mf"]=="1"){ 
		?>
		<td>
			<select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,<?php echo $row_id; ?>);">
				<option value="male">Adult</option>
				<option value="female">Women</option>
				<option value="youth">Youth</option>
			</select>
		</td>
				<?php 
			}
			
			if($row_product["prod_id"]=="2"){ 
		?>
		<td>
			<select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','select_ssize_<?php echo $form_id; ?>_<?php echo $row_id; ?>','<?php echo $prod_id;?>');" name="select_mf[<?php echo $form_id; ?>][]" >
			    <option value="youth">YOUTH</option>
			    <option value="male">ADULT</option>
		    </select>
		</td>
		<?php 
			}
			
			if($row_product["have_size"]=="1" && $prod_id=="4"){ 
				?>
				<td class="glued_body">
					<select class="white_in" name="select_mf[<?php echo $form_id; ?>][]" id="select_glue_num_<?php echo $form_id; ?>_<?php echo $row_id; ?>" disabled>
					    <option value="na" selected>N/A</option>
						<option value="Yes">Yes</option>
						<option value="No">No</option>
					</select>
				</td>
				<?php 
					}

			if($row_product["have_size"]=="1"){ 
		?>
		<td>
			<select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_jsize[<?php echo $form_id; ?>][]">
				<option value="0"></option>
				<?php for($i=0; $i<sizeof($a_size[$split_name]);$i++){ ?>
				<option value="<?php echo $a_size[$split_name][$i]["size_id"]; ?>" ><?php echo $a_size[$split_name][$i]["size_name"]; ?></option>
				<?php } ?>
			</select>
		</td>
		<?php 
			}

			if($row_product["have_number"]=="1"){ 
		?>
		<td><input class="white_in" id="select_jersey_num_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>
		<?php 
			}
		?>
		<td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>
		<td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');"></td>
		<?php
				if($row_product["have_size"]=="1" && $prod_id=="4"){ 
				?>
				<td class="namebar_td">
					<input type="text" readonly value="N/A" class="white_in" id="select_pg_<?php echo $form_id; ?>_<?php echo $row_id; ?>" name="select_pg[<?php echo $form_id; ?>][]">
				</td>
				<?php 
					}?>
		<td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>
		<td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>
		
	</tr>
<?php
}
?>