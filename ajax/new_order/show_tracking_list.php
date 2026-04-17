<?php
include('../../db.php');

if( !isset($_POST["lkr_order_main_id"]) || ($_POST["lkr_order_main_id"]=="") ){

	$a_result["result"] = "fail";
	$a_result["msg"] = "Invalid parameter.";
	echo json_encode($a_result);
	exit();
}

$order_main_id = $_POST["lkr_order_main_id"];

$sql_select = "SELECT * FROM tbl_shipping WHERE order_main_id='".$order_main_id."'; ";
$rs_select = $conn->query($sql_select);

$a_result["result"] = "success";
$inner_content = '<table class="tbl_show_tracking">';
$inner_content .= '<tr>';
$inner_content .= '<th>#</th>';
$inner_content .= '<th>By</th>';
$inner_content .= '<th>Date</th>';
$inner_content .= '<th>Link</th>';
$inner_content .= '</tr>';

if($rs_select->num_rows > 0 ){

	$count_row = 0;

	while($row_select = $rs_select->fetch_assoc()){

		$count_row++;

		$tmp_link = "";
		if($row_select["ship_by"]=="UPS"){
			$tmp_link = '<a href="https://www.ups.com/track?loc=en_TH&tracknum='.$row_select["awb_number"].'&requester=WT/trackdetails" target="_blank">'.$row_select["awb_number"].'</a>';

		}else if($row_select["ship_by"]=="DHL"){
			$tmp_link = '<a href="https://www.dhl.com/content/g0/en/express/tracking.shtml?AWB='.$row_select["awb_number"].'&brand=DHL" target="_blank">'.$row_select["awb_number"].'</a>';
		
		}

		$inner_content .= '<tr>';
		$inner_content .= '<td>'.$count_row.'</td>';
		$inner_content .= '<td>'.$row_select["ship_by"].'</td>';
		$inner_content .= '<td>'.date("F d, Y",strtotime($row_select["ship_date"])).'</td>';
		$inner_content .= '<td>'.$tmp_link.'</td>';
		$inner_content .= '</tr>';
	}

}else{
	
	$inner_content .= '<tr><td colspan="4"><center>Empty!</center></td></tr>';

}

$inner_content .= '</table>';

$a_result["inner_content"] = $inner_content;

echo json_encode($a_result);

?>
