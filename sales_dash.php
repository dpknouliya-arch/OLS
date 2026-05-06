<?php

include('check-session.php');



$obj_user = json_decode(base64_decode($_SESSION["JOGOLSSALE"]));
$sales_user_id = $obj_user->sales_user_id ; 

$sql  = "SELECT COUNT(*) AS count FROM tbl_sales_assignments   Where sales_user_id='$sales_user_id'";
$all_customer_count = $conn->query($sql)->fetch_assoc(); 
$active_sql_count =$conn->query($sql." AND enable=1")->fetch_assoc(); 
$inactive_sql_count = $conn->query($sql ."AND enable=0")->fetch_assoc();


?>





<div class=" salesDashBoard innerMainContent ">

    <div class="row  ">

        <div class="col-md-12">



            <div class="card border-none ">

                <div class="boxes ">

                    <div class="grid2">

                        <div class="contentSide">

                            <h1>Hello, <?php echo $obj_user->s_user_name; ?></h1>

                            <h4>Welcome to Your Dashboard !</h4>

                            <p><?= date('d, F Y') ?> </p>

                        </div>

                        <div class="ImgSide">

                            <figure class="m-0"><img src="images/default/adminDashBg.png" alt=""></figure>



                        </div>

                    </div>

                </div>

                <span class="dottedBorder">

                </span>

                <div class="adminOverview">

                    <div class="grid3">

                        <div class="items totalCus">

                            <div class="inner position-relative">

                                <div class="d-flex  leftSide">

                                    <figure class="my-auto ">

                                        <img src="images/icons/Tuser.png" alt="" class="iconImg">

                                    </figure>

                                    <div class="history">

                                        <h5>Total Customer</h5>

                                        <h1><?=$all_customer_count['count']?></h1>

                                    </div>

                                </div>

                                <figure class="m-0 bgImg">

                                    <img src="images/default/totalBg.png" alt="" class=" ">

                                </figure>

                            </div>

                        </div>



                        <div class="items ActiveCus">

                            <div class="inner position-relative">

                                <div class="d-flex  leftSide">

                                    <figure class="my-auto ">

                                        <img src="images/icons/aCustomer.png" alt="" class="iconImg">

                                    </figure>

                                    <div class="history">

                                        <h5>Active Customer</h5>

                                        <h1><?=$active_sql_count['count']?></h1>

                                    </div>

                                </div>

                                <figure class="m-0 bgImg">

                                    <img src="images/default/activeBg.png" alt="" class=" ">

                                </figure>

                            </div>

                        </div>



                        <div class="items inActiveCus">

                            <div class="inner position-relative">

                                <div class="d-flex  leftSide">

                                    <figure class="my-auto ">

                                        <img src="images/icons/inActiveUser.png" alt="" class="iconImg">

                                    </figure>

                                    <div class="history">

                                        <h5>In-Active Customer</h5>

                                        <h1><?=$inactive_sql_count['count']?></h1>

                                    </div>

                                </div>

                                <figure class="m-0 bgImg">

                                    <img src="images/default/inActive.png" alt="" class=" ">

                                </figure>

                            </div>

                        </div>



                    </div>

                </div>

            </div>



        </div>



    </div>

</div>