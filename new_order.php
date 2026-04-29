<?php

include('check-session.php');

include('db.php');



$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;

$full_name = $obj_user->full_name;



$sql_select = "SELECT tbl_order_form.*,tbl_product.prod_name,tbl_product.split_name,COUNT(tbl_order_item.oi_id) AS item_num,SUM(tbl_order_item.qty_top1+tbl_order_item.qty_top2+tbl_order_item.qty_bottom1+tbl_order_item.qty_bottom2) AS qty_sum ";

$sql_select .= " FROM tbl_order_form LEFT JOIN tbl_product ON tbl_order_form.prod_id=tbl_product.prod_id ";

$sql_select .= " LEFT JOIN tbl_order_item ON tbl_order_form.of_id=tbl_order_item.of_id";

$sql_select .= " WHERE tbl_order_form.user_id='" . $user_id . "' AND tbl_order_form.enable=1 AND tbl_order_form.order_status<>'archived' AND tbl_order_form.is_reordered!= 1 GROUP BY tbl_order_form.of_id ORDER BY tbl_order_form.order_date DESC; ";

//echo $user_id;

//$rs_select = $conn->query($sql_select);





$rs_tmp = $conn->query($sql_select);



$a_row_select = array();



$a_order_main_id = array();

while ($row_tmp = $rs_tmp->fetch_assoc()) {



    $a_row_select[] = $row_tmp;



    if ($row_tmp["lkr_order_main_id"] != "") {

        if (!in_array($row_tmp["lkr_order_main_id"], $a_order_main_id)) {



            $a_order_main_id[] = $row_tmp["lkr_order_main_id"];
        }
    }



    if ($row_tmp["re_order_id"] != "") {

        if (!in_array($row_tmp["re_order_id"], $a_order_main_id)) {



            $a_order_main_id[] = $row_tmp["re_order_id"];
        }
    }
}



$design_found = array();

if (sizeof($a_order_main_id) > 0) {



    $conn3 = new mysqli($serverName, $userName, $userPassword, $dbName3);

    mysqli_set_charset($conn3, "utf8");



    $s_order_main_id = implode(",", $a_order_main_id);



    //echo "<hr>".$s_order_main_id."<hr>";

    

    $sql_chk_design = "SELECT order_main_id,order_main_code,order_design_file FROM order_main WHERE order_main_id IN (" . $s_order_main_id . "); ";

    $rs_chk_design = $conn3->query($sql_chk_design);



    while ($row_chk_design = $rs_chk_design->fetch_assoc()) {

        $design_found[($row_chk_design["order_main_id"])] = $row_chk_design;
    }
}



?>

<style>
    .orderNameImg {
        width: 26px;
        height: 26px;
        object-fit: contain;
    }

    .orderName {
        font-size: 13px;
        text-transform: capitalize;
        margin: auto 0;
    }

    .table-responsive td {
        padding: 5px 4px !important;
        font-size: 13px;
    }

    .orderInfoArea {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 0 10px;
    }

    .bg_status_info .themeBtn2grey {
        display: inline-flex;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 500;
    }

</style>

<div class="newOrderPage">



    <div class="innerMainContent">

        <div class="pageHeader">

            <h2>Order Status</h2>

            <p>This is where your Order Info will go.</p>

        </div>

        <div class="table-responsive">

            <table class="table table-striped">

                <thead class="themebg">

                    <tr>

                        <th class="text-center">#</th>

                        <th>Order name</th>

                        <th class="text-center">Design</th>

                        <th class="text-center">Customer PO</th>

                        <th class="text-center">Order Date</th>

                        <th class="text-center">Items</th>

                        <th class="text-center">Total QTY</th>

                        <th class="text-center">Status</th>

                        <th class="text-center">Order Form</th>

                        <th class="text-center">Action</th>

                        <th class="text-center">Tracking</th>

                    </tr>

                </thead>

                <tbody id="tableBody">



                    <?php

                    $row_count = 1;

                    $list_of_id = "";

                    //while($row_order = $rs_select->fetch_assoc()){

                    for ($i = 0; $i < sizeof($a_row_select); $i++) {



                        $row_order = $a_row_select[$i];



                        if ($list_of_id != "") {

                            $list_of_id .= ",";
                        }

                        $list_of_id .= $row_order["of_id"];



                        $can_chat = 0;

                        if ($row_order["order_status"] == "new" || $row_order["order_status"] == "processing") {

                            $can_chat = 1;
                        }

                    ?>

                        <tr id="tr_show_row<?php echo $row_order["of_id"]; ?>">

                            <td class="bg_name_info text-left position-relative"><?php echo $row_count; ?></td>

                            <td class="bg_name_info text-left position-relative">
                                <div class="orderInfoArea">

                                    <img src="images/icons/<?php echo $row_order["split_name"]; ?>.png" class="orderNameImg">
                                    <h6 class="orderName">
                                        <?php echo $row_order["form_name"]; ?>
                                    </h6>

                                    <?php

                                    $b_show_reorder_design = false;

                                    if ($row_order["re_order_id"] != "") {

                                        echo '<div><div class="show_re_order">RE-ORDER</div></div>';

                                        $b_show_reorder_design = true;
                                    }

                                    ?>
                                </div>

                            </td>

                            <td class="bg_name_info text-center">

                                <?php

                                // echo '<pre>' ; 
                                // print_r($design_found); 


                                if (isset($design_found[($row_order["re_order_id"])]) && $design_found[($row_order["re_order_id"])]["order_design_file"] != "") {

                                    $row_design = $design_found[($row_order["re_order_id"])];



                                    echo '<i class="fa fa-image" style="color:#933; font-size:18px; cursor:pointer;" onclick="return showDesign(\'' . $row_design["order_main_code"] . '\',\'' . $row_design["order_design_file"] . '\');" data-toggle="modal" data-target="#showDesignModal"></i>';
                                } else if (isset($design_found[($row_order["lkr_order_main_id"])]) && $design_found[($row_order["lkr_order_main_id"])]["order_design_file"] != "") {

                                    $row_design = $design_found[($row_order["lkr_order_main_id"])];



                                    echo '<i class="fa fa-image" style="color:#933; font-size:18px; cursor:pointer;" onclick="return showDesign(\'' . $row_design["order_main_code"] . '\',\'' . $row_design["order_design_file"] . '\');" data-toggle="modal" data-target="#showDesignModal"></i>';
                                } else {

                                    echo '<i class="fa fa-image" style="color:#DDD; font-size:18px;"></i>';
                                }

                                ?>

                            </td>

                            <td class="bg_name_info text-center"><?php echo $row_order["customer_po"]; ?></td>

                            <td class="bg_name_info text-center"><?php echo date("F d, Y", strtotime($row_order["order_date"])); ?></td>

                            <td class="bg_number_info text-center"><?php echo $row_order["item_num"]; ?></td>

                            <td class="bg_number_info text-center"><?php echo $row_order["qty_sum"]; ?></td>

                            <td class="bg_status_info text-center" id="td_show_status<?php echo $row_order["of_id"]; ?>">

                                <div class="showing_status_<?php echo str_replace(" ", "_", $row_order["order_status"]); ?>"><i><?php echo strtoupper($row_order["order_status"]); ?> </i></div>

                            </td>

                            <td class="bg_status_info text-center">

                                <div>
                                    <!-- Button trigger modal -->

                                    <button type="button" class="btn iconBTn themeBtn2grey" data-bs-toggle="modal" onclick="return viewOrderForm(<?php echo $row_order["of_id"]; ?>,<?php echo $can_chat; ?>);" data-bs-target="#orderFormModal">

                                        <?php

                                        $is_excel_form = "no";

                                        if ($row_order["xls_name"] != "") {

                                            $is_excel_form = "yes";

                                        ?>

                                            <i class="fa fa-file-excel-o" aria-hidden="true" style="color: #FFF; background-color: #161; border-radius: 5px;"></i>



                                        <?php

                                        } else {

                                        ?>

                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>

                                        <?php

                                        }

                                        ?>

                                        View

                                    </button>

                                    <!-- <button style="width:100px; font-size: 14px;" type="button" class="btn btn-primary" onclick="return viewOrderForm(<?php echo $row_order['of_id']; ?>,<?php echo $can_chat; ?>);" data-toggle="modal" data-target="#orderFormModal">

                                    <?php

                                    $is_excel_form = "no";

                                    if ($row_order["xls_name"] != "") {

                                        $is_excel_form = "yes";

                                    ?>

                                        <i class="fa fa-file-excel-o" aria-hidden="true" style="color: #FFF; background-color: #161; border-radius: 5px;"></i>

                                        

                                    <?php

                                    } else {

                                    ?>

                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>

                                    <?php

                                    }

                                    ?>

                                    View

                                    <span id="sp_noti<?php echo $row_order["of_id"]; ?>" class="noti_msg" style="display:none;"></span>

                                </button> -->

                                    <input type="hidden" id="is_excel_form<?php echo $row_order["of_id"]; ?>" value="<?php echo $is_excel_form; ?>">

                                </div>
                            </td>

                            <td class="bg_status_info" id="td_show_action<?php echo $row_order["of_id"]; ?>">

                                <?php

                                if ($row_order["order_status"] == "shipped" || $row_order["order_status"] == "delivered") {

                                ?>

                                    <button style="font-size: 14px;" type="button" class="btn btn-success" onclick="return setReceived(<?php echo $row_order["of_id"]; ?>);">Received</button>

                                <?php

                                } else if ($row_order["order_status"] == "received") {

                                ?>

                                    <button style="font-size: 14px;" type="button" class="btn btn-secondary" onclick="return setArchived(<?php echo $row_order["of_id"]; ?>);">Archive</button>

                                <?php

                                }

                                ?>

                            </td>

                            <td class="bg_status_info">

                                <?php

                                $nsql = "SELECT order_main_dhl_link FROM order_main WHERE order_main_id='" . $row_order["lkr_order_main_id"] . "'";

                                $nquery = mysqli_query($conn3, $nsql);

                                if ($nquery->num_rows > 0) {



                                    $fetcher = mysqli_fetch_assoc($nquery);

                                    $ships = $fetcher['order_main_dhl_link'];

                                    if ($ships == "" || $ships == NULL) {
                                    } else {



                                        //if($row_order["ship_status"]=="yes"){



                                ?>

                                        <div class="tracking_link">

                                            <a href="<?= $ships ?>"><i class="fa fa-truck"></i></a>

                                        </div>

                                        <!--<div class="tracking_link" data-toggle="modal" data-target="#trackingLinkModal" onclick="return getTrackingList(<?php //echo $row_order["lkr_order_main_id"]; 

                                                                                                                                                            ?>);">-->

                                        <!--	<i class="fa fa-truck"></i>-->

                                        <!--</div>-->

                                <?php

                                    }
                                }

                                ?>

                            </td>

                        </tr>

                    <?php

                        $row_count++;
                    }

                    ?>

                </tbody>

                <tfoot>





                </tfoot>

            </table>







        </div>

    </div>

</div>



<!-- Modal -->

<div class="modal fade" id="orderFormModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog smallModal">

        <div class="modal-content">



            <div class="modal-header">

                <h1 class="modal-title fs-5" id="modal_form_title">Order Form</h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>



            </div>

            <div class="modal-body" style="padding: 0px 26px;">

                <div id="order_form_content" class="table-responsive">

                </div>

                <div class="text-center" style="padding: 5px;">

                    <button id="btn_mini" class="btn btn-info" onclick="$('#order_form_content').css('max-height','220px').css('overflow-y','scroll'); $(this).hide(); $('#btn_maxi').show();">Minimize</button>

                    <button id="btn_maxi" style="display: none;" class="btn btn-info" onclick="$('#order_form_content').css('max-height','').css('overflow-y',''); $(this).hide(); $('#btn_mini').show();">Maximize</button>

                    <input type="hidden" id="download_of_id">

                    <input type="hidden" id="sending_msg" value="no">

                </div>

                <div id="d_chat_panel_outter">

                    <div id="d_chat_panel" class="row">



                    </div>

                    <span id="sp_bottom"></span>

                </div>

                <input type="hidden" id="max_chat_id" value="0">

            </div>

            <div class="modal-footer">

                <div style="width: 100%; display: none;" id="input_msg_zone">

                    <div class="row">

                        <div class="col-md-12" style="margin-bottom: 20px;">

                            <div class="d-flex gap-2">

                                <textarea style="width:100%;" id="msg_input"></textarea>

                                <div>

                                    <div style="padding: 5px 0px; color:#F00; font-size: 14px; font-weight: bold;">* English only.</div>

                                    <div><button type="button" class="btn btn-dark" style="width: 15vw ;" onclick="return sendMessage();">Send</button></div>

                                </div>



                            </div>



                        </div>

                    </div>

                    <div class="col-md-3 text-center" style="margin-top: -15px; padding-left: 0px;">



                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<!-- Modal -->


<input type="hidden" id="modal_is_close" value="yes">

<input type="hidden" id="chk_msg_read" value="">





<!-- Modal -->

<div id="trackingLinkModal" class="modal fade" role="dialog">

    <div class="modal-dialog modal-md">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header" style="padding:15px 26px 1px 26px;">

                <h5>Tracking</h5>

                <button type="button" class="close" style="float: right;" data-dismiss="modal">&times;</button>



            </div>

            <div class="modal-body" style="padding: 0px 26px;" id="show_tracking_list">



            </div>

            <div class="modal-footer">



            </div>

        </div>



    </div>

</div>





<!-- Modal -->

<div id="showDesignModal" class="modal draggable fade" role="dialog">

    <div class="modal-dialog modal-lg">

   <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Final Design</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        <!-- Modal content-->

        <div class="modal-content">
            <div class="modal-body" style="padding: 0px 26px;" id="show_tracking_list">

                <iframe id="show_design_frame" src="" type=frame&vlink=xx&link=xx&css=xxx&bg=xx&bgcolor=xx marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scorlling=yes width=100% height=600></iframe>

            </div>
        </div>



    </div>

</div>



<script type="text/javascript" src="https://code.jquery.com/ui/jquery-ui-git.js"></script>

<script type="text/javascript">
    $('#orderFormModal').on('hidden.bs.modal', function() {

        $('#modal_is_close').val("yes");

        $('#chk_msg_read').val("");

    });



    /*



        $('#showDesignModal').draggable({

    	    cursor: 'move',

    	    handle: '.modal-header'

    	});

    	$('.modal.draggable>.modal-dialog>.modal-content>.modal-header').css('cursor', 'move');

    */





    updateNotifyMessage();



    function viewOrderForm(of_id, can_chat = 0) {



        if ($('#is_excel_form' + of_id).val() == "yes") {

            $('#d_dialog_box').css('min-width', '1200px');

        } else {

            $('#d_dialog_box').css('min-width', '');

        }



        if (can_chat == 1) {

            $('#input_msg_zone').show();

        } else {

            $('#input_msg_zone').show();

        }



        $('#order_form_content').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Loading...');



        $.ajax({

            type: "POST",

            dataType: "html",

            url: "ajax/new_order/show_order_form.php",

            data: {

                "of_id": of_id

            },

            success: function(resp) {



                $('#download_of_id').val(of_id);

                $('#order_form_content').html(resp);



                $('#modal_is_close').val("no");



                getMessage(of_id);



            }

        });



    }



    function updateNotifyMessage() {



        var s_of_id = $('#s_list_of_id').val();



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/new_order/get_notify.php",

            data: {

                "s_of_id": s_of_id,

                "msg_type": 'A'

            },

            success: function(resp) {



                if (resp.result == "success") {



                    var s_noti = resp.s_notify;

                    //console.log(s_noti);

                    var a_noti = s_noti.split(",");

                    var tmp_of_id = "";



                    for (var i = 0; i < a_noti.length; i++) {

                        tmp_of_id = a_noti[i].split("=");

                        if (parseInt(tmp_of_id[1]) > 0) {



                            $('#sp_noti' + tmp_of_id[0]).html(tmp_of_id[1]);

                            $('#sp_noti' + tmp_of_id[0]).show();



                        } else {

                            $('#sp_noti' + tmp_of_id[0]).hide();



                        }

                    }



                } else {

                    //alert("Error: Connection fail please try again.");

                }



               // setTimeout(function() {

                 //   updateNotifyMessage();

              //  }, 1000);



            }

        });



    }



    function sendMessage() {



        //var max_chat_id = $('#max_chat_id').val();

        var of_id = $('#download_of_id').val();

        var tmp_msg = $('#msg_input').val();



        if (tmp_msg == "") {

            return false;

        }



        $('#sending_msg').val("yes");



        var msg_input = window.btoa(tmp_msg);



        // alert("["+max_chat_id+"]["+of_id+"][<?php echo intval($user_id); ?>][<?php echo base64_encode($full_name); ?>]["+msg_input+"]");

        // return false;



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/new_order/save_chat_msg.php",

            data: {

                //"max_chat_id": max_chat_id,

                "of_id": of_id,

                "person_id": <?php echo intval($user_id); ?>,

                "person_name": "<?php echo base64_encode($full_name); ?>",

                "msg_input": msg_input

            },

            success: function(resp) {



                if (resp.result == "success") {



                    //console.log("chat_id="+resp.max_chat_id);



                    var chk_msg_read = $('#chk_msg_read').val();

                    if (chk_msg_read != "") {

                        chk_msg_read += "," + resp.max_chat_id;

                    } else {

                        chk_msg_read = resp.max_chat_id;

                    }

                    $('#chk_msg_read').val(chk_msg_read);

                    //$('#max_chat_id').val(resp.max_chat_id);



                    //$('#d_chat_panel').append(window.atob(resp.msg_box));



                    $('#msg_input').val('').focus();

                    $('#sending_msg').val("no");



                    $('#d_chat_panel_outter').animate({

                        scrollTop: $('#d_chat_panel').height()

                    }, 1000);



                } else {

                    alert(resp.msg);

                }



            }

        });



    }



    function getMessage(of_id) {



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/new_order/get_chat_msg.php",

            data: {

                "of_id": of_id

            },

            success: function(resp) {



                if (resp.result == "success") {



                    $('#d_chat_panel').html(window.atob(resp.msg_box));

                    $('#max_chat_id').val(resp.max_chat_id);

                    $('#chk_msg_read').val(resp.chk_msg_read);



                    setTimeout(function() {



                        $('#d_chat_panel_outter').animate({

                            scrollTop: $('#d_chat_panel').height()

                        }, 1000);



                    }, 1500);

                } else {



                    $('#d_chat_panel').html("");

                    $('#max_chat_id').val("0");



                }



                autoUpdateChat();

            }

        });



    }



    function autoUpdateChat() {



        if ($('#modal_is_close').val() == "no") {



            setTimeout(function() {



                updateChat();



            }, 500);

        }

    }



    function updateChat() {



        if ($('#sending_msg').val() == "yes") {

            autoUpdateChat();

            return false;

        }



        var max_chat_id = $('#max_chat_id').val();

        var of_id = $('#download_of_id').val();

        var chk_msg_read = $('#chk_msg_read').val();



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/new_order/update_chat_msg.php",

            data: {

                "max_chat_id": max_chat_id,

                "of_id": of_id,

                "chk_msg_read": chk_msg_read

            },

            success: function(resp) {



                //console.log(resp.result);

                //console.log("Read="+resp.read_chat_id+" | Unread="+resp.unread_chat_id);



                if (resp.read_chat_id != "") {

                    var tmp_set_read = resp.read_chat_id;

                    var a_set_read = tmp_set_read.split(",");



                    for (var i = 0; i < a_set_read.length; i++) {

                        $('#msg_chat' + a_set_read[i]).html('Read');

                    }



                }

                $('#chk_msg_read').val(resp.unread_chat_id);



                if (resp.result == "success") {



                    if ($('#msg_box_no' + resp.max_chat_id).html() == null) {



                        $('#d_chat_panel').append(window.atob(resp.msg_box));

                        $('#max_chat_id').val(resp.max_chat_id);

                        $('#d_chat_panel_outter').animate({

                            scrollTop: $('#d_chat_panel').height()

                        }, 1000);



                        autoUpdateChat();



                    }



                } else {

                    autoUpdateChat();

                }



            }

        });



    }



    function setReceived(of_id) {



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/new_order/set_received.php",

            data: {

                "of_id": of_id

            },

            success: function(resp) {



                if (resp.result == "success") {



                    var show_action = '<button style="font-size: 14px;" type="button" class="btn btn-secondary" onclick="return setArchived(' + of_id + ');">Archive</button>';



                    $('#td_show_action' + of_id).html(show_action);



                    var show_status = '<div class="showing_status_received"><i>RECEIVED</i></div>';



                    $('#td_show_status' + of_id).html(show_status);



                } else {



                    alert(resp.msg);



                }



            }

        });



    }



    function setArchived(of_id) {



        if (confirm("This order will move to Archived Orders. Confirm?")) {

            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/new_order/set_archived.php",

                data: {

                    "of_id": of_id

                },

                success: function(resp) {



                    if (resp.result == "success") {



                        $('#tr_show_row' + of_id).remove();



                    } else {



                        alert(resp.msg);



                    }



                }

            });

        }

    }



    function getTrackingList(lkr_order_main_id) {



        $('#show_tracking_list').html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Loading...');



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/new_order/show_tracking_list.php",

            data: {

                "lkr_order_main_id": lkr_order_main_id

            },

            success: function(resp) {



                if (resp.result == "success") {



                    $('#show_tracking_list').html(resp.inner_content);



                } else {



                    alert(resp.msg);



                }



            }

        });

    }



    function showDesign(order_main_code, order_design_file) {



        var inner_src = '';



        if (order_design_file != "") {

            <?php


            if ($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "192.168.88.190") {

            ?>

                inner_src = 'http://192.168.88.190/internal/files/' + order_main_code + '/' + order_design_file;

            <?php

            } elseif($_SERVER["HTTP_HOST"] == "ols-test.jog-joinourgame.com" || $_SERVER["HTTP_HOST"] == "ols-test.jog-joinourgame.com"){ 
                 ?>
                     inner_src = 'https://locker-test.jog-joinourgame.com/files/' + order_main_code + '/' + order_design_file;
              <?
            } else {

            ?>

                inner_src = 'https://locker.jog-joinourgame.com/files/' + order_main_code + '/' + order_design_file;

            <?php

            }

            ?>

        }


 
        $('#showDesignModal').modal('show'); 

        $('#show_design_frame').attr("src", inner_src);

    }
</script>