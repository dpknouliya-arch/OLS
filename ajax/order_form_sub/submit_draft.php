<?php
session_start();

if( !isset($_SESSION["JOGOLSSUB"]) ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Your login session expired. Please login again.";
	echo json_encode($a_result);
	exit();
}

/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit();*/

include('../../db.php');

$add_date = date("Y-m-d H:i:s");

$draft_id = $_POST["edit_draft_id"];

if($_POST["oi_id_delete"]!=""){
	$sql_delete_oi = "DELETE FROM tbl_draft_oi WHERE oi_id IN (".$_POST["oi_id_delete"]."); ";
	$conn->query($sql_delete_oi);
}

foreach($_POST["edit_of_id"] as $form_id => $of_id){

	$prod_id = $_POST["prod_id_list"][$form_id];

	$loop = 0;
	if(isset($_POST["a_oi_id"][$form_id])){
		$loop = sizeof($_POST["a_oi_id"][$form_id]);
	}

	if($loop>0){
		for( $j=0; $j<$loop; $j++ ){

			$player_name = (isset($_POST["player_name"][$form_id][$j]))?$_POST["player_name"][$form_id][$j]:"";
			$p_or_g = (isset($_POST["select_pg"][$form_id][$j]))?$_POST["select_pg"][$form_id][$j]:"";
			$sex = (isset($_POST["select_mf"][$form_id][$j]))?$_POST["select_mf"][$form_id][$j]:"";
			$product_size = (isset($_POST["select_jsize"][$form_id][$j]))?$_POST["select_jsize"][$form_id][$j]:"";
			$jersey_number = (isset($_POST["jersey_number"][$form_id][$j]))?$_POST["jersey_number"][$form_id][$j]:"";
			$color_top1 = (isset($_POST["jersey_color"][$form_id][$j]))?$_POST["jersey_color"][$form_id][$j]:"";
			$qty_top1 = (isset($_POST["jersey_qty"][$form_id][$j]))?$_POST["jersey_qty"][$form_id][$j]:"";
			$color_top2 = (isset($_POST["jersey_color2"][$form_id][$j]))?$_POST["jersey_color2"][$form_id][$j]:"";
			$qty_top2 = (isset($_POST["jersey_qty2"][$form_id][$j]))?$_POST["jersey_qty2"][$form_id][$j]:"";
			$bottom_size = (isset($_POST["select_ssize"][$form_id][$j]))?$_POST["select_ssize"][$form_id][$j]:"";
			$color_bottom1 = (isset($_POST["sock_color"][$form_id][$j]))?$_POST["sock_color"][$form_id][$j]:"";
			$qty_bottom1 = (isset($_POST["sock_qty"][$form_id][$j]))?$_POST["sock_qty"][$form_id][$j]:"";
			$color_bottom2 = (isset($_POST["sock_color2"][$form_id][$j]))?$_POST["sock_color2"][$form_id][$j]:"";
			$qty_bottom2 = (isset($_POST["sock_qty2"][$form_id][$j]))?$_POST["sock_qty2"][$form_id][$j]:"";
			$c_or_a = (isset($_POST["select_ca"][$form_id][$j]))?$_POST["select_ca"][$form_id][$j]:"";
			$note = (isset($_POST["note"][$form_id][$j]))?$_POST["note"][$form_id][$j]:"";

			if($_POST["a_oi_id"][$form_id][$j]=="new"){

				$sql_insert2 = "INSERT INTO tbl_draft_oi (";
				$sql_insert2 .= "of_id,player_name,p_or_g,sex,product_size_id,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note) VALUES (";
				$sql_insert2 .= "'".$of_id."','".addslashes($player_name)."','".$p_or_g."','".$sex."','".$product_size."','".addslashes($jersey_number)."','".addslashes($color_top1)."','".$qty_top1."','".addslashes($color_top2)."'";
				$sql_insert2 .= ",'".$qty_top2."','".$bottom_size."','".addslashes($color_bottom1)."','".$qty_bottom1."','".addslashes($color_bottom2)."','".$qty_bottom2."','".$c_or_a."','".addslashes($note)."');";
				//of_id,player_name,p_or_g,sex,product_size,jersey_number,color_top1,qty_top1,color_top2,qty_top2,bottom_size,color_bottom1,qty_bottom1,color_bottom2,qty_bottom2,c_or_a,note
				$conn->query($sql_insert2);

			}else{

				$oi_id = $_POST["a_oi_id"][$form_id][$j];

				$sql_update2 = "UPDATE tbl_draft_oi SET player_name='".addslashes($player_name)."',p_or_g='".$p_or_g."',sex='".$sex."',product_size_id='".$product_size."',jersey_number='".addslashes($jersey_number)."'";
				$sql_update2 .= ",color_top1='".addslashes($color_top1)."',qty_top1='".$qty_top1."',color_top2='".addslashes($color_top2)."',qty_top2='".$qty_top2."',bottom_size='".$bottom_size."',color_bottom1='".addslashes($color_bottom1)."'";
				$sql_update2 .= ",qty_bottom1='".$qty_bottom1."',color_bottom2='".addslashes($color_bottom2)."',qty_bottom2='".$qty_bottom2."',c_or_a='".$c_or_a."',note='".addslashes($note)."'";
				$sql_update2 .= " WHERE oi_id='".$oi_id."'; ";
				$conn->query($sql_update2);

			}

		}
	}

}

$a_result["result"] = "success";

echo json_encode($a_result);
?>