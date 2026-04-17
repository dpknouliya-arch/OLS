<?php

session_start();



if (!isset($_SESSION["JOGOLS"])) {

    echo '<center>Please re-login again.</center>';

    exit();
}



include('../../db.php');



$prod_id = $_POST["prod_id"];

$form_id = $_POST["form_id"];

$teamno = $_POST["teamno"];



$on_team_name = base64_decode($_POST["on_team_name"]);

$on_year = base64_decode($_POST["on_year"]);

$form_name = $on_team_name . " " . $on_year;



$sql_product = "SELECT * FROM tbl_product WHERE prod_id='" . $prod_id . "';";

$rs_product = $conn->query($sql_product);

$row_product = $rs_product->fetch_assoc();





$sql_size = "SELECT * FROM tbl_size WHERE prod_id='" . $prod_id . "' AND enable=1 AND (size_of_person='youth' OR size_of_person='adult_youth') ORDER BY split_order ASC,sort_no ASC;";

$rs_size = $conn->query($sql_size);



$a_size = array();

while ($row_size = $rs_size->fetch_assoc()) {

    $a_size[($row_size["split_order"])][] = $row_size;

    $spl_order = $row_size["split_order"];
}

?>

<?php

if ($prod_id == "1") {

?>



    <div align="center" class="prod_card pt-6" id="prod_card<?php echo $form_id; ?> team<?php echo $teamno; ?>" card-id="<?php echo $form_id; ?>">

        <input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">

        <input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="1">

        <input type="hidden" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">

        <input type="hidden" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">

        <input type="hidden" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">

        <center>



        </center>



        <table class="table table-striped">

            <thead class="themebg">

                <tr class="theader">

                    <th class="tablecount">01</th>

                    <th colspan="17">

                        <div class="d-inline">

                            <h6><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>) <i class="fa fa-minus-circle" data-toggle="tooltip" title="Click to delete order form" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i></h6>

                            <a href="#" class="d-inline m-2">

                                <figure class="m-0 d-inline"><img

                                        src="images/vector/edit.png" alt="">

                                </figure>

                            </a>

                            <a href="#" class="d-inline deleteTable"

                                onclick="removeTable(this)">



                                <figure class="m-0 d-inline"><img

                                        src="images/vector/delter.png" alt="">

                                </figure>



                            </a>

                        </div>

                    </th>

                </tr>

                <tr>

                    <th class="text-center">#</th>

                    <th class="text-center">Name on Jersey</th>

                    <th class="text-center">Pattern Cut</th>

                    <th class="text-center">P or G</th>

                    <th class="text-center">Jersey Size</th>

                    <th class="text-center">Jersey No</th>

                    <th class="text-center">Jersey Color</th>

                    <th class="text-center">QTY</th>

                    <th class="text-center">Jersey Color</th>

                    <th class="text-center">QTY</th>

                    <th class="text-center">Sock Size</th>

                    <th class="text-center">Sock Color</th>

                    <th class="text-center">QTY</th>

                    <th class="text-center">Sock Color</th>

                    <th class="text-center">QTY</th>

                    <th class="text-center">C or A</th>

                    <th class="text-center">Name For Packing</th>

                    <th class="text-center">Notes</th>

                </tr>

            </thead>

            <tbody id="prod_item_<?php echo $form_id; ?>">

                <?php

                for ($tet = 1; $tet < 17; $tet++) {

                ?>

                    <tr id="prod_item_<?php echo $form_id; ?>_<?= $tet ?>">

                        <td>

                            <button class="deleteRow border-none bg-none" onclick="deleteRow(this)">

                                <figure class="m-0"><img src="../images/vector/deleteGrey.png" alt=""></figure>

                            </button>

                        </td>

                        <td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120"></td>

                        <td>

                            <select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');" id="select_mf_<?php echo $form_id; ?>_<?= $tet ?>" name="select_mf[<?php echo $form_id; ?>][]">

                                <option value="youth">YOUTH</option>

                                <option value="male">ADULT</option>

                                <option value="female">WOMEN-ADULT</option>

                            </select>

                        </td>

                        <td>

                            <select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');">

                                <option value="player" title="Player">Player</option>

                                <option value="goalie" title="Goalie">Goalie</option>

                            </select>

                        </td>

                        <td>

                            <select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_jsize[<?php echo $form_id; ?>][]">

                                <option value="0"></option>

                                <?php for ($i = 0; $i < sizeof($a_size["1"]); $i++) { ?>

                                    <option value="<?php echo $a_size["1"][$i]["size_id"]; ?>"><?php echo $a_size["1"][$i]["size_name"]; ?></option>

                                <?php } ?>

                            </select>

                        </td>

                        <td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>

                        <td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>

                        <td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');"></td>

                        <td><input class="white_in" name="jersey_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>

                        <td><input class="white_in jersey_qty2_<?php echo $form_id; ?>" name="jersey_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');"></td>

                        <td>

                            <select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_ssize[<?php echo $form_id; ?>][]">

                                <option value="0"></option>

                                <?php for ($i = 0; $i < sizeof($a_size["3"]); $i++) { ?>

                                    <option value="<?php echo $a_size["3"][$i]["size_id"]; ?>"><?php echo $a_size["3"][$i]["size_name"]; ?></option>

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

                <tr>

                    <td colspan="2">

                        <span id="addRowButton" class=" addRowButtoncls bg-none border-none d-flex justify-content-between w-100">

                            <figure class="m-0"><img src="../images/vector/add.png"

                                    alt="">

                        </span>

                        Add

                        </button>

                    </td>

                    <td colspan="18"></td>

                </tr>

            </tbody>

            <tr>

                <th></th>

                <th>TOTAL ORDER</th>

                <th></th>

                <th></th>

                <th></th>

                <th></th>

                <th></th>

                <th id="total_jersey_qty_<?php echo $form_id; ?>">0</th>

                <th></th>

                <th id="total_jersey_qty2_<?php echo $form_id; ?>">0</th>

                <th></th>

                <th></th>

                <th id="total_sock_qty_<?php echo $form_id; ?>">0</th>

                <th></th>

                <th id="total_sock_qty2_<?php echo $form_id; ?>">0</th>

                <th></th>

                <th></th>

                <th></th>

            </tr>

            <tr>

                <th></th>

                <th>Special Comments <br> (if any)</th>

                <th colspan="16" style="background-color: white;"><input type="text" name="special_comment[<?= $form_id ?>]" placeholder="Enter Special Comment here..." style="width:100%;"></th>

            </tr>

        </table>



    </div>



<?php

} else if ($row_product["split_type"] == "2") {



    $tmp_split = explode(",", $row_product["split_name"]);

    $split_name1 = $tmp_split[0];

    $split_name2 = $tmp_split[1];

?>



    <div align="center" class="prod_card" id="prod_card<?php echo $form_id; ?> team<?php echo $teamno; ?>" card-id="<?php echo $form_id; ?>">

        <input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">

        <input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">

        <input type="hidden" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">

        <input type="hidden" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">

        <input type="hidden" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">

        <center>



        </center>

        <table class="tbl_item_form" style="width:100%;" align="center">

            <tr class="theader">

                <th class="tablecount">01</th>

                <th colspan="17">

                    <div class="d-inline">

                        <h6><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>) <i class="fa fa-minus-circle" data-toggle="tooltip" title="Click to delete order form" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i></h6>

                        <a href="#" class="d-inline m-2">

                            <figure class="m-0 d-inline"><img

                                    src="images/vector/edit.png" alt="">

                            </figure>

                        </a>

                        <a href="#" class="d-inline deleteTable" onclick="removeTable(this)">

                            <figure class="m-0 d-inline"><img src="images/vector/delter.png" alt="">

                            </figure>

                        </a>

                    </div>

                </th>

            </tr>

            <tr>

                <th data-toggle="tooltip" title="Click to add rows" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">

                    <i class="fa fa-plus-circle"></i>

                    <input type="hidden" id="num_item_<?php echo $form_id; ?>" value="16">

                </th>

                <?php

                if ($row_product["have_name"] == "1") {

                ?>

                    <th>Name on <?php echo $split_name1; ?></th>

                <?php

                }



                if ($row_product["choose_pg"] == "1") {

                ?>

                    <th>P or G</th>

                <?php

                }



                if ($row_product["choose_mf"] == "1") {

                ?>

                    <th>Pattern Cut<span class="fa-stack " data-toggle="tooltip" title="Please note: Women’s cuts available only when there is a full team order of Adult women sizes only.">

                            <i class="fa fa-circle fa-stack-2x"></i>

                            <i class="fa fa-info fa-stack-1x fa-inverse"></i>

                        </span></th>

                <?php

                }

                ?>

                <th><?php echo $split_name1; ?> Size</th>

                <th><?php echo $split_name1; ?> #(Number)</th>

                <th><?php echo $split_name1; ?> Color</th>

                <th>QTY</th>

                <th><?php echo $split_name2; ?> Size</th>

                <th><?php echo $split_name2; ?> Color</th>

                <th>QTY</th>

                <th>Name For Packing

                    <span class="fa-stack " data-toggle="tooltip" title="Option to add a name or descriptor on packaging.">

                        <i class="fa fa-circle fa-stack-2x"></i>

                        <i class="fa fa-info fa-stack-1x fa-inverse"></i>

                    </span>

                </th>

                <th>Notes</th>

            </tr>

            <tbody id="prod_item_<?php echo $form_id; ?>">

                <?php

                for ($tet = 1; $tet < 17; $tet++) {

                ?>

                    <tr id="prod_item_<?php echo $form_id; ?>_<?= $tet ?>">

                        <td>

                            <button class="deleteRow border-none bg-none" onclick="deleteRow(this)">

                                <figure class="m-0"><img src="../images/vector/deleteGrey.png" alt=""></figure>

                            </button>

                        </td>

                        <?php

                        if ($row_product["have_name"] == "1") {

                        ?>

                            <td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120"></td>

                        <?php

                        }



                        if ($row_product["choose_pg"] == "1") {

                        ?>

                            <td>

                                <select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,<?= $tet ?>);">

                                    <option value="player" title="Player">Player</option>

                                    <option value="goalie" title="Goalie">Goalie</option>

                                </select>

                            </td>

                        <?php

                        }



                        if ($row_product["choose_mf"] == "1") {

                        ?>

                            <td>

                                <select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?= $tet ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');">

                                    <option value="youth">YOUTH</option>

                                    <option value="male">ADULT</option>

                                    <option value="female_youth">WOMEN-YOUTH</option>

                                    <option value="female">WOMEN-ADULT</option>

                                </select>

                            </td>

                            <input type="hidden" value="uni" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>">

                        <?php

                        }

                        ?>

                        <td>

                            <select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_jsize[<?php echo $form_id; ?>][]">

                                <option value="0"></option>

                                <?php for ($i = 0; $i < sizeof($a_size[$spl_order]); $i++) { ?>

                                    <option value="<?php echo $a_size[$spl_order][$i]["size_id"]; ?>"><?php echo $a_size[$spl_order][$i]["size_name"]; ?></option>

                                <?php } ?>

                            </select>

                        </td>

                        <td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10"></td>

                        <td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>

                        <td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');"></td>

                        <td>

                            <select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_ssize[<?php echo $form_id; ?>][]">

                                <option value="0"></option>

                                <?php for ($i = 0; $i < sizeof($a_size[$spl_order]); $i++) { ?>

                                    <option value="<?php echo $a_size[$spl_order][$i]["size_id"]; ?>"><?php echo $a_size[$spl_order][$i]["size_name"]; ?></option>

                                <?php } ?>

                            </select>

                        </td>

                        <td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>

                        <td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');"></td>

                        <td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>

                        <td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>



                    </tr>

                <?php

                }

                ?>

                <tr>

                    <td colspan="2">

                        <span id="addRowButton" class="addRowButtoncls bg-none border-none d-flex justify-content-between w-100">

                            <figure class="m-0"><img src="../images/vector/add.png"

                                    alt="">

                        </span>

                        Add

                        </button>

                    </td>

                    <td colspan="18"></td>

                </tr>

            </tbody>

            <tr>

                <th style="border-width: 0px; background-color: #FFA;"></th>

                <th>TOTAL ORDER</th>

                <?php

                if ($row_product["have_name"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["choose_pg"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["choose_mf"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["have_size"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["have_number"] == "1") {

                ?>

                    <th></th>

                <?php

                }

                ?>

                <th id="total_jersey_qty_<?php echo $form_id; ?>">0</th>

                <th></th>

                <th></th>

                <th id="total_sock_qty_<?php echo $form_id; ?>">0</th>

                <th></th>

                <th></th>

            </tr>

            <tr>

                <th style="border-width: 0px; background-color: #FFA;"></th>

                <th>Special Comments <br> (if any)</th>

                <th colspan="16" style="background-color: white;"><input type="text" name="special_comment[<?= $form_id ?>]" placeholder="Enter Special Comment here..." style="width:100%;"></th>

            </tr>

        </table>



    </div>



<?php

} else {



    $split_name = $row_product["split_name"];

?>



    <div class="prod_card" id="prod_card<?php echo $form_id; ?> team<?php echo $teamno; ?>" card-id="<?php echo $form_id; ?>">

        <input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">

        <input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">

        <input type="hidden" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">

        <input type="hidden" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">

        <input type="hidden" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">

        <center>



        </center>

        <table class="tbl_item_form" style="width:100%;" align="center">



            <tr class="theader">

                <th class="tablecount">01</th>

                <th colspan="17">

                    <div class="d-inline">

                        <h6><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>) <i class="fa fa-minus-circle" data-toggle="tooltip" title="Click to delete order form" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i></h6>

                        <a href="#" class="d-inline m-2">

                            <figure class="m-0 d-inline"><img

                                    src="images/vector/edit.png" alt="">

                            </figure>

                        </a>

                        <a href="#" class="d-inline deleteTable"

                            onclick="removeTable(this)">



                            <figure class="m-0 d-inline"><img

                                    src="images/vector/delter.png" alt="">

                            </figure>

                        </a>

                    </div>

                </th>

            </tr>



            <tr>

                <th data-toggle="tooltip" title="Click to add rows" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">

                    <i class="fa fa-plus-circle"></i>

                    <input type="hidden" id="num_item_<?php echo $form_id; ?>" value="16">

                    #

                </th>

                <?php

                if ($row_product["have_name"] == "1") {

                ?>

                    <th><?php echo $split_name; ?></th>

                <?php

                }



                if ($row_product["choose_pg"] == "1") {

                ?>

                    <th>P or G</th>

                <?php

                }



                if ($row_product["prod_id"] == "2") {

                ?>

                    <th>Pattern Cut<span class="fa-stack " data-toggle="tooltip" title="Please note: Women’s cuts available only when there is a full team order of Adult women sizes only.">

                            <i class="fa fa-circle fa-stack-2x"></i>

                            <i class="fa fa-info fa-stack-1x fa-inverse"></i>

                        </span></th>

                <?php

                }



                if ($row_product["choose_mf"] == "1") {

                ?>

                    <th>Sex</th>

                <?php

                }



                if ($row_product["have_size"] == "1" && $prod_id == "4") {

                ?>

                    <th class="glued_body">Glue</th>

                <?php

                }



                if ($row_product["have_size"] == "1") {

                ?>

                    <th><?php echo $split_name; ?> Size</th>

                <?php

                }



                if ($row_product["have_number"] == "1") {

                ?>

                    <th><?php echo $split_name; ?> #(Number)</th>

                <?php

                }

                ?>

                <th><?php echo $split_name; ?> Color</th>

                <th>QTY</th>

                <?php

                if ($prod_id == "4") {

                ?>

                    <th style="width:150px;" class="namebar_td">Name on Namebar</th>

                <?php } ?>

                <th>Name For Packing

                    <span class="fa-stack " data-toggle="tooltip" title="Option to add a name or descriptor on packaging.">

                        <i class="fa fa-circle fa-stack-2x"></i>

                        <i class="fa fa-info fa-stack-1x fa-inverse"></i>

                    </span>

                </th>

                <th>Notes</th>

            </tr>

            <tbody id="prod_item_<?php echo $form_id; ?>">

                <?php

                for ($tet = 1; $tet < 17; $tet++) {

                ?>

                    <tr id="prod_item_<?php echo $form_id; ?>_<?= $tet ?>">

                        <td>

                            <button class="deleteRow border-none bg-none" onclick="deleteRow(this)">

                                <figure class="m-0"><img src="../images/vector/deleteGrey.png" alt=""></figure>

                            </button>

                        </td>

                        <?php

                        if ($row_product["have_name"] == "1") {

                        ?>

                            <td>

                                <?php

                                $sql_choices = "SELECT * FROM tbl_product_choices WHERE prod_id='" . $prod_id . "' AND enable=1 ORDER BY sort_no ASC;";

                                $rs_choices = $conn->query($sql_choices);



                                if ($rs_choices->num_rows > 0) {

                                ?>

                                    <select class="white_in" id="product_id_<?php echo $form_id; ?>_<?= $tet ?>" onchange="return changePatternExtra('select_pg_<?php echo $form_id; ?>_<?= $tet ?>','product_id_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_glue_num_<?php echo $form_id; ?>_<?= $tet ?>','select_jersey_num_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');" name="player_name[<?php echo $form_id; ?>][]">

                                        <?php

                                        while ($row_choices = $rs_choices->fetch_assoc()) {

                                        ?>

                                            <option value="<?php echo $row_choices["choice_name"]; ?>"><?php echo $row_choices["choice_name"]; ?></option>

                                        <?php

                                        }

                                        ?>

                                    </select>

                                <?php

                                } else {

                                ?>

                                    <input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120">

                                <?php

                                }

                                ?>

                            </td>

                        <?php

                        }



                        if ($row_product["choose_pg"] == "1") {

                        ?>

                            <td>

                                <select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');">

                                    <option value="player" title="Player">Player</option>

                                    <option value="goalie" title="Goalie">Goalie</option>

                                </select>

                            </td>

                        <?php

                        }



                        if ($row_product["prod_id"] == "2") {

                        ?>

                            <td>

                                <select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');" id="select_mf_<?php echo $form_id; ?>_<?= $tet ?>" name="select_mf[<?php echo $form_id; ?>][]">

                                    <option value="youth">YOUTH</option>

                                    <option value="male">ADULT</option>

                                </select>

                            </td>

                        <?php

                        }



                        if ($row_product["choose_mf"] == "1") {

                        ?>

                            <td>

                                <select class="white_in" id="select_mf_<?php echo $form_id; ?>_1" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,1,1);">

                                    <option value="male">Male</option>

                                    <option value="female">Female</option>

                                </select>

                            </td>

                        <?php

                        }



                        if ($row_product["have_size"] == "1" && $prod_id == "4") {

                        ?>

                            <td class="glued_body">

                                <select class="white_in" name="select_mf[<?php echo $form_id; ?>][]" id="select_glue_num_<?php echo $form_id; ?>_<?= $tet ?>" disabled>

                                    <option value="na" selected>N/A</option>

                                    <option value="Yes">Yes</option>

                                    <option value="No">No</option>

                                </select>

                            </td>

                        <?php

                        }



                        if ($row_product["have_size"] == "1") {

                        ?>

                            <td>

                                <select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_jsize[<?php echo $form_id; ?>][]">

                                    <option value="0"></option>

                                    <?php for ($i = 0; $i < sizeof($a_size["1"]); $i++) { ?>

                                        <option value="<?php echo $a_size["1"][$i]["size_id"]; ?>"><?php echo $a_size["1"][$i]["size_name"]; ?></option>

                                    <?php } ?>

                                </select>

                            </td>

                        <?php

                        }



                        if ($row_product["have_number"] == "1") {

                        ?>

                            <td><input class="white_in" id="select_jersey_num_<?php echo $form_id; ?>_<?= $tet ?>" name="jersey_number[<?php echo $form_id; ?>][]" <?php if ($prod_id == "4") {

                                                                                                                                                                        echo "readonly";

                                                                                                                                                                        echo " placeholder='N/A'";
                                                                                                                                                                    } ?> type="text" maxlength="10"></td>

                        <?php

                        }

                        ?>

                        <td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100"></td>

                        <td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');"></td>

                        <?php

                        if ($row_product["have_size"] == "1" && $prod_id == "4") {

                        ?>

                            <td class="namebar_td">

                                <input type="text" readonly value="N/A" class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]">

                            </td>

                        <?php

                        } ?>

                        <td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text"></td>

                        <td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text"></td>



                    </tr>

                <?php

                }

                ?>

                <tr>

                    <td colspan="2">

                        <span id="addRowButton" class="addRowButtoncls bg-none border-none d-flex justify-content-between w-100">

                            <figure class="m-0"><img src="../images/vector/add.png"

                                    alt="">

                        </span>

                        Add

                        </button>

                    </td>

                    <td colspan="18"></td>

                </tr>

            </tbody>

            <tr>

                <th></th>

                <th>TOTAL ORDER</th>

                <?php

                if ($row_product["have_name"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["choose_pg"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["choose_mf"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["have_size"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["have_number"] == "1") {

                ?>

                    <th></th>

                <?php

                }



                if ($row_product["prod_id"] == "2") {

                ?>

                    <th>

                    <?php

                }

                if ($row_product["prod_id"] == "4") {

                    ?>

                    <th>

                    <?php

                }

                    ?>

                    <th id="total_jersey_qty_<?php echo $form_id; ?>">0</th>

                    <th></th>

                    <th></th>

                    <th></th>

            </tr>

            <tr>

                <th></th>

                <th>Special Comments <br> (if any)</th>

                <th colspan="16" style="background-color: white;"><input type="text" name="special_comment[<?= $form_id ?>]" placeholder="Enter Special Comment here..." style="width:100%;"></th>

            </tr>

        </table>



    </div>



<?php

}

?>