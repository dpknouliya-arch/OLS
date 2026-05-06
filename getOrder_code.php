<?php
session_start();




$q_main_code = 'SELECT order_main_id FROM order_main WHERE order_main_code="'.$_POST['order_main_code'].'"';
$query_main_code = $conn3->query($q_main_code);
$main_code_count = $query_main_code->num_rows;

// if($is_appreal && $main_code_count==0){
// 	$q_main_code = 'SELECT ex_code FROM apparel_order WHERE ex_code="'.$_POST['order_main_code'].'"';
// 	$query_main_code = $conn->query($q_main_code);
// 	$main_code_count = $query_main_code->num_rows;
// }
while ($row_selection = $query_main_code->fetch_assoc()) {  
	$order_main_id=$row_selection['order_main_id'];
}
echo base64_encode($order_main_id);



 ?>
