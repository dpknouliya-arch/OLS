<?php

include('check-session.php');

include('db.php');



$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));

$user_id = $obj_user->user_id;

$full_name = $obj_user->full_name;

$user_email = $obj_user->user_email;

//echo $user_email;

$sql = "SELECT * FROM tbl_final_approvals WHERE customer_email='$user_email' ORDER BY approval_timestamp DESC";

// $sql = "SELECT t1.* FROM tbl_final_approvals t1

//   JOIN (SELECT * FROM tbl_final_approvals GROUP BY order_main_code ) t2

//     ON t1.order_main_code = t2.order_main_code AND t1.approval_timestamp = t2.approval_timestamp WHERE t1.customer_email='".$user_email."' ORDER BY approval_timestamp DESC";

$query = mysqli_query($conn, $sql);



?>
<style>
   .cke_notification{
        margin: 0;
    }
</style>

<div class="finalApprovalPage">

    <div class="innerMainContent">

        <div class="pageHeader">

            <h2>Final Approvals</h2>

            <p>This is where your Final Approvals Info will go.</p>

        </div>

        <div class="table-responsive">

            <table class="table table-striped">

                <thead class="themebg">

                    <tr>

                        <th class="text-center">#</th>

                        <th>Order name</th>

                        <th class="text-center">Order Date </th>

                        <th class="text-center">JOG Order Code</th>

                        <th class="text-center">Update Date</th>

                        <th class="text-center">View </th>

                        <th class="text-center">Chat With JOG</th>

                        <th class="text-center">Action</th>

                    </tr>

                </thead>

                <tbody id="tableBody">

                    <?php

                    $count = 1;

                    $list_of_id = "";

                    while ($row = mysqli_fetch_assoc($query)) {

                        if ($list_of_id != "") {

                            $list_of_id .= ",";

                        }

                        $list_of_id .= $row["order_main_id"];

                    ?>

                        <tr>

                            <td><?= $count ?></td>

                            <td><?= $row['order_main_name'] ?></td>

                            <td><?= $row['order_main_date'] ?></td>

                            <td><?= $row['order_main_code'] ?></td>

                            <td><?php echo date("F d, Y", strtotime($row["approval_timestamp"])); ?></td>

                            <td class="text-center"><a href="https://locker.jog-joinourgame.com/view/?id=<?= base64_encode($row['order_main_id']) ?>" target="_blank"><button class="btn iconBTn tableBTn   ">

                                        View <i class="fa fa-eye" aria-hidden="true"></i>

                                    </button>

                                </a>

                            </td>

                            <td class="text-center"><button class="btn chatBtn iconBTn tableBTn" onclick="return viewOrderForm(<?php echo $row['order_main_id']; ?>,1);"  ><i class='fa fa-comment'></i>

                                    <span id="sp_noti<?php echo $row["order_main_id"]; ?>" class="noti_msg" style="display:none;"></span>

                                </button></td>

                            <td class="text-center"><?php

                                                    if ($row['approval_from_customer'] == 1) {

                                                    ?>

                                    <button class="btn iconBTn tableBTn approve_ajax" order_id="<?= base64_encode($row['final_approval_id']) ?>">Approve <i class="fa fa-check" aria-hidden="true"></i></button>

                                    <button class="btn   iconBTn tableBTn reject_modal" order_id="<?= base64_encode($row['final_approval_id']) ?>">Reject <i class="fa fa-times" aria-hidden="true"></i>

                                    </button>

                                <?php

                                                    } elseif ($row['approval_from_customer'] == 2) {

                                ?>

                                    <button class="btn btn-success">Approved</button>

                                <?php

                                                    } else {

                                ?>

                                    <button class="btn btn-danger">Rejected</button>

                                <?php

                                                    }

                                ?>

                            </td>

                        </tr>

                    <?php

                        $count++;

                    } ?>

                </tbody>

                <tfoot>

                </tfoot>

            </table>

        </div>

    </div>

</div>









<!-- Modal -->

<div id="orderFormModal" class="modal fade" role="dialog">

    <div id="d_dialog_box" class="modal-dialog modal-lg" style="margin-top:15px;">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header" style="padding:15px 26px 1px 26px;">

                <h2 style="color:black;">Chat With JOG</h2>

                <button type="button" class="close btn-close" style="float: right;"  data-bs-dismiss="modal" aria-label="Close"></button>



            </div>

            <div class="modal-body" style="padding: 0px 26px;">

                <div id="d_chat_panel_outter">

                    <div id="d_chat_panel" class="row">



                    </div>

                    <span id="sp_bottom"></span>

                </div>

                <input type="hidden" id="max_chat_id" value="0">

            </div>

            <div class="modal-footer">

                <center style="width: 100%; display: block;" id="input_msg_zone">

                    <div class="row">

                        <div class="col-md-9" style="margin-bottom: 20px;">

                            <textarea style="width:100%;" id="msg_input"></textarea>

                        </div>

                        <div class="col-md-3 text-center" style="margin-top: -15px; padding-left: 0px;">

                            <div style="padding: 0px; color:#F00; font-size: 14px; font-weight: bold;">* English only.</div>

                            <div><button type="button" class="btn btn-dark send_msg" order_id="" style="width: 80%;">Send</button></div>

                        </div>

                    </div>

                </center>

            </div>

        </div>



    </div>

</div>

<input type="hidden" id="modal_is_close" value="yes">

<input type="hidden" id="chk_msg_read" value="">
<!-- <input type="hidden" name="" id="download_of_id" value=""> -->





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



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header" style="padding:15px 26px 1px 26px; cursor: move;">

                <button type="button" class="close" style="float: right;" data-dismiss="modal">&times;</button>

            </div>

            <div class="modal-body" style="padding: 0px 26px;" id="show_tracking_list">

                <iframe id="show_design_frame" src="" type=frame&vlink=xx&link=xx&css=xxx&bg=xx&bgcolor=xx marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scorlling=yes width=100% height=600></iframe>

            </div>

            <div class="modal-footer">



            </div>

        </div>



    </div>

</div>



<div id="rejectModal" class="modal fade" role="dialog">

    <div class="modal-dialog modal-lg">



        <!-- Modal content-->

        <div class="modal-content">

            <div class="modal-header" style="background-color:black;">

                <div class="container-fluid">

                    <h4 class="float-left">Reject</h4>

                    <button type="button" style="margin-top:-20px;" class="close float-right" data-dismiss="modal">&times;</button>

                </div>

            </div>

            <div class="modal-body" style="background-image: url('https://online.jog-joinourgame.com/assets/images/tutorial-background.jpg');">

                <form id="reject_form">

                    <div class="form-group">

                        <!--<label for="exampleInputEmail2" style="color:white;">DESIGN NAME</label>-->

                        <!--<input type="text" name="design_name" class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp" placeholder="Input Design Option">-->

                        <input type="hidden" name="dd_id" id="dd_id_reject">

                    </div>

                    <div class="form-group">

                        <label for="exampleFormControlTextarea2" style="color:white;">Reject Reason(Important)</label>

                        <textarea name="reject_textarea" class="form-control" id="exampleFormControlTextarea2" rows="3"></textarea>

                    </div>

                    <button type="submit" class="btn btn-danger">Reject</button>

                </form>

            </div>

        </div>



    </div>

</div>

<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>

<script>

    CKEDITOR.replace('reject_textarea');

</script>



<!-- <script type="text/javascript" src="https://code.jquery.com/ui/jquery-ui-git.js"></script> -->

<script type="text/javascript">

    $(document).on('click', '.approve_ajax', function() {

        var order_id = $(this).attr('order_id');

        if (confirm('Do you really wish to approve it ?')) {

            $.ajax({

                type: 'POST',

                data: {

                    order_id: order_id,

                },

                url: 'ajax/final_approvals/approve.php',

                success: function(response) {

                    var response = JSON.parse(response);

                    if (response.result == "success") {

                        location.reload();

                    } else {

                        alert('Something Went Wrong');

                    }

                }

            })

        }

    })





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

        $('#modal_is_close').val("no");

        $('.send_msg').attr('order_id', of_id);

        getMessage(of_id);

    }



    function updateNotifyMessage() {



        var s_of_id = $('#s_list_of_id').val();



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/final_approvals/get_notify.php",

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



                setTimeout(function() {

                    updateNotifyMessage();

                }, 1000);



            }

        });



    }



    $(document).on('click', '.send_msg', function() {



        //var max_chat_id = $('#max_chat_id').val();

        var of_id = window.btoa($(this).attr('order_id'));

        var order_id = $(this).attr('order_id');

        var tmp_msg = $('#msg_input').val();



        if (tmp_msg == "") {

            return false;

        }



        $('#sending_msg').val("yes");



        var msg_input = tmp_msg;



        $.ajax({

            type: "POST",

            url: "ajax/final_approvals/save_chat_msg.php",

            data: {

                //"max_chat_id": max_chat_id,

                "of_id": of_id,

                "msg_input": msg_input

            },

            success: function(response) {

                var response = JSON.parse(response);

                if (response.result == "success") {

                    getMessage(order_id);

                    //console.log("chat_id="+resp.max_chat_id);



                    // 	var chk_msg_read = $('#chk_msg_read').val();

                    // 	if(chk_msg_read!=""){

                    // 		chk_msg_read += ","+resp.max_chat_id;

                    // 	}else{

                    // 		chk_msg_read = resp.max_chat_id;

                    // 	}

                    // 	$('#chk_msg_read').val(chk_msg_read);

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



    })



    function getMessage(of_id) {



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/final_approvals/get_chat_msg.php",

            data: {

                "of_id": of_id

            },

            success: function(resp) {
            //    console.log("Final approval"); 
            //    console.log(resp);
 

                if (resp.result == "success") {
            
                   $('#orderFormModal').modal('show');

                    $('#d_chat_panel').html(window.atob(resp.msg_box));

                    $('#max_chat_id').val(resp.max_chat_id);

                    $('#chk_msg_read').val(resp.chk_msg_read);
                    // $('#download_of_id').val(of_id);



                    setTimeout(function() {



                        $('#d_chat_panel_outter').animate({

                            scrollTop: $('#d_chat_panel').height()

                        }, 1000);



                    }, 1500);

                } else {



                    $('#d_chat_panel').html("");

                    $('#max_chat_id').val("0");



                }



                // autoUpdateChat();

            }

        });



    }



    function autoUpdateChat() {



        if ($('#modal_is_close').val() == "no") {
         updateChat();


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


        // console.log("max chat id" , max_chat_id); 
        // console.log("of id"  , of_id); 
        // console.log("check message read"  ,chk_msg_read); 
  
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



        
                //console.log("Read="+resp.read_chat_id+" | Unread="+resp.unread_chat_id);



                if (resp.read_chat_id != "" && resp.read_chat_id) {
                    var tmp_set_read = resp.read_chat_id;
                    alert(tmp_set_read);
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

            } else {

            ?>

                inner_src = 'https://locker.jog-joinourgame.com/files/' + order_main_code + '/' + order_design_file;

            <?php

            }

            ?>

        }



        $('#show_design_frame').attr("src", inner_src);

    }



    $(document).on('click', '.reject_modal', function() {

        var order_id = $(this).attr('order_id');

        $('#dd_id_reject').val(order_id);

        $('#rejectModal').modal('show');

    })



    $(document).on('submit', '#reject_form', function(e) {

        e.preventDefault();

        var form = $(this);

        var formdata = new FormData(form[0]);

        if (confirm("Confirm REJECT?")) {



            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/final_approvals/reject_draft.php",

                data: formdata,

                contentType: false,

                processData: false,

                success: function(resp) {

                    if (resp.result == "success") {

                        location.reload();

                    }

                }

            });



        }



    })

</script>