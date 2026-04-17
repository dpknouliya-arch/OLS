<?php
	session_start();

	if(!isset($_SESSION["JOGOLS"])){
		echo '<center>Please re-login again.</center>';
		exit();
	}

	include('../../db.php');
	
	$pattern_cut = $_POST['pattern_cut'];
	$p_or_g = $_POST['p_or_g'];
	$prod_id  = $_POST['prod_id'];
	$flag = 0;
	
	if($pattern_cut=="youth"){
	    $sql = "SELECT * FROM tbl_size WHERE prod_id='".$prod_id."' AND enable=1 AND (size_of_person='youth' OR size_of_person='adult_youth') ORDER BY split_order ASC,sort_no ASC;";
	    $flag = 1;
	}
	elseif($pattern_cut=="female_youth"){
	    $sql = "SELECT * FROM tbl_size WHERE prod_id='".$prod_id."' AND enable=1 AND size_of_person='adult_female' ORDER BY split_order ASC,sort_no ASC;";
	    $flag = 1;
	}
	else{
	    $sql = "SELECT * FROM tbl_size WHERE prod_id='".$prod_id."' AND enable=1 AND (size_of_person='adult' OR size_of_person='adult_youth') ORDER BY split_order ASC,sort_no ASC;";
	}
	
	$query = $conn->query($sql);

	$jersey_size = array();
	$jersey_id = array();
	$sock_size = array();
	$sock_id = array();
	while($row_size = $query->fetch_assoc()){
		if($p_or_g=="player"){
		    if($row_size['split_order']=="1"){
		        $jersey_size[] = $row_size['size_name'];
		        $jersey_id[] = $row_size['size_id'];
		    }
		    if($row_size['split_order']=="3"){
		        $sock_size[] = $row_size['size_name'];
		        $sock_id[] = $row_size['size_id'];
		    }
		}
		elseif($p_or_g=="goalie"){
		    if($row_size['split_order']=="2"){
		        $jersey_size[] = $row_size['size_name'];
		        $jersey_id[] = $row_size['size_id'];
		    }
		    if($row_size['split_order']=="3"){
	            if($flag==0){
    		        $sock_size[] = $row_size['size_name'];
    		        $sock_id[] = $row_size['size_id'];
	            }
		      $flag = 0;
		    }
		}
		else{
		       $jersey_size[] = $row_size['size_name'];
		       $jersey_id[] = $row_size['size_id'];
    		   $sock_size[] = $row_size['size_name'];
    		   $sock_id[] = $row_size['size_id'];
		}
	}
	die(json_encode(array('status'=>'1','sock_id'=>$sock_id,'sock_size'=>$sock_size,'jersey_id'=>$jersey_id,'jersey_size'=>$jersey_size)));
?>