<center id="sameteam<?php echo $_POST["teamno"]; ?>">

    <?php

    // session_start();

    include('../../db.php');



    $prod_id = $_POST["prod_id"];

    $form_id = $_POST["form_id"];

    $teamno = $_POST["teamno"];



    $on_team_name = base64_decode($_POST["on_team_name"]);

    $on_year = base64_decode($_POST["on_year"]);

    $form_name = $on_team_name . " " . $on_year;

    // Get Excel data if provided
    // $excel_data = [];
    // if (isset($_POST["excel_data"]) && !empty($_POST["excel_data"])) {
    //     $excel_data = json_decode(base64_decode($_POST["excel_data"]), true);
    //     if (!is_array($excel_data)) {
    //         $excel_data = [];
    //     }
    // }

    // Helper function to safely get Excel cell value
    function getExcelValue($row, $index)
    {
        return isset($row[$index]) ? htmlspecialchars($row[$index], ENT_QUOTES) : '';
    }



    // $sql_product = "SELECT * FROM tbl_product WHERE prod_id='" . $prod_id . "';"
    $sql = "SELECT * FROM tbl_product WHERE prod_id=?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $prod_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row_product = $result->fetch_assoc();



    $sql_size = "SELECT * FROM tbl_size WHERE prod_id= ?  AND enable=1 AND (size_of_person='youth' OR size_of_person='adult_youth') ORDER BY split_order ASC,sort_no ASC ";

    $stmt = $conn->prepare($sql_size);

    $stmt->bind_param("i", $prod_id);
    $stmt->execute();

    $rs_size = $stmt->get_result();
    $a_size = array();

    while ($row_size = $rs_size->fetch_assoc()) {

        $a_size[($row_size["split_order"])][] = $row_size;

        $spl_order = $row_size["split_order"];
    }

    ?>

    <div class="tab-content" id="tab-content">

        <?php

        if ($prod_id == "1") {

        ?>

            <div class="tab-pane active" id="fill-tabpanel-<?php echo $teamno; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $teamno; ?>">

                <div align="center" class="prod_card" id="prod_card<?php echo $form_id; ?> team<?php echo $teamno; ?>" card-id="<?php echo $form_id; ?>">

                    <input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">

                    <input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="1">

                    <input type="hidden" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">

                    <input type="hidden" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">

                    <input type="hidden" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">

                    <input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new">

                    <center>



                    </center>



                    <table class="table table-striped">

                        <thead class="themebg">

                            <tr class="theader">

                                <th class="tablecount">01</th>

                                <th colspan="17" class="text-center">

                                    <div class="d-inline">

                                        <h6 class="my-auto"><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>)

                                            <span href="#" class="d-inline deleteTable"

                                                onclick="removeTable(this)">



                                                <figure class="m-0 d-inline iconBTn"><img

                                                        src="images/vector/delter.png" alt="" style="width: 30px; background: #FFF; padding: 6px;margin-left: 20px;
">

                                                </figure>
                                            </span>
                                        </h6>
                                    </div>

                                 </th>

                            </tr>

                            <tr>

                                <th class="text-center"># 1</th>

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

                            // Calculate number of rows needed - use Excel data count or default to 16
                            $row_count = !empty($excel_data) ? count($excel_data) : 10;



                            function getSelected($value, $compare)
                            {
                                return (strcasecmp($value, $compare) === 0) ? 'selected' : '';
                            }

                            // Helper to find size option by name
                            // function getSizeSelected($sizes, $value)
                            // {
                            //     if (empty($value)) return '';
                            //     for ($i = 0; $i < sizeof($sizes); $i++) {
                            //         if (strcasecmp($sizes[$i]["size_name"], $value) === 0) {
                            //             return 'selected';
                            //         }
                            //     }
                            //     return '';
                            // }


                            function getSizeSelected($sizes, $value)
                            {
                                if (empty($value)) return '';
                                $select = $sizes == $value ? "selected" : "";
                                return $select;
                            }


                            // Helper to find size value by name
                            function getSizeValue($sizes, $value)
                            {
                                if (empty($value)) return '0';
                                for ($i = 0; $i < sizeof($sizes); $i++) {
                                    if (strcasecmp($sizes[$i]["size_name"], $value) === 0) {
                                        return $sizes[$i]["size_id"];
                                    }
                                }
                                return '0';
                            }

                            for ($tet = 1; $tet <= $row_count; $tet++) {
                                // Get Excel row data (0-indexed)
                                $excel_row = isset($excel_data[$tet - 1]) ? $excel_data[$tet - 1] : [];

                                // Helper to get selected attribute for select fields




                                $mf_val = strtolower(getExcelValue($excel_row, 1));
                                $pg_val = strtolower(getExcelValue($excel_row, 2));
                                $ca_val = strtolower(getExcelValue($excel_row, 15));



                            ?>

                                <tr id="prod_item_<?php echo $form_id; ?>_<?= $tet ?>">

                                    <td>

                                        <input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">

                                        <button class="deleteRow border-none bg-none" onclick="deleteRow(this)">

                                            <figure class="m-0"><img src="images/vector/deleteGrey.png" alt=""></figure>

                                        </button>

                                    </td>

                                    <td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo getExcelValue($excel_row, 0); ?>"></td>

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

                                                <option value="<?php echo $a_size["1"][$i]["size_id"]; ?>" <?php echo getSizeSelected($a_size["1"][$i]["size_name"], getExcelValue($excel_row, 3)); ?>><?php echo $a_size["1"][$i]["size_name"]; ?></option>

                                            <?php } ?>

                                        </select>

                                    </td>

                                    <td>
                                        <input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo getExcelValue($excel_row, 4); ?>">
                                    </td>

                                    <td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, 5); ?>"></td>

                                    <td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, 6); ?>"></td>

                                    <td><input class="white_in" name="jersey_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, 7); ?>"></td>

                                    <td><input class="white_in jersey_qty2_<?php echo $form_id; ?>" name="jersey_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'jersey_qty2_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, 8); ?>"></td>

                                    <td>

                                        <select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_ssize[<?php echo $form_id; ?>][]">

                                            <option value="0"></option>

                                            <?php for ($i = 0; $i < sizeof($a_size["3"]); $i++) { ?>

                                                <option value="<?php echo $a_size["3"][$i]["size_id"]; ?>" <?php echo getSizeSelected($a_size["3"], getExcelValue($excel_row, 9)); ?>><?php echo $a_size["3"][$i]["size_name"]; ?></option>

                                            <?php } ?>

                                        </select>

                                    </td>

                                    <td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, 10); ?>"></td>

                                    <td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'sock_qty_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, 11); ?>"></td>

                                    <td><input class="white_in" name="sock_color2[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, 11); ?>"></td>

                                    <td><input class="white_in sock_qty2_<?php echo $form_id; ?>" name="sock_qty2[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(1,'sock_qty2_<?php echo $form_id; ?>');" onkeyup="calculateQTY(1,'sock_qty2_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, 13); ?>"></td>

                                    <td>

                                        <select class="white_in" name="select_ca[<?php echo $form_id; ?>][]">

                                            <option value="" <?php echo getSelected($ca_val, ''); ?>></option>

                                            <option value="captain" <?php echo getSelected($ca_val, 'captain') . getSelected($ca_val, getExcelValue($excel_row, 14)); ?>>Captain</option>

                                            <option value="assistant" <?php echo getSelected($ca_val, 'assistant') . getSelected($ca_val,  getExcelValue($excel_row, 15)); ?>>Assistant</option>

                                        </select>

                                    </td>

                                    <td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text" value="<?php echo getExcelValue($excel_row, 16); ?>"></td>

                                    <td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value=""></td>

                                </tr>

                            <?php

                            }

                            ?>
                        </tbody>
                        <tr>

                            <td colspan="2">

                                <span id="addRowButton" class=" addRowButtoncls bg-none border-none d-flex justify-content-between w-100" onclick="return addItemRow(<?php echo $form_id; ?>,1);">
                                    <figure class="m-0"><img src="images/vector/add.png" alt=""> </figure>
                                </span>



                                <input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $tet; ?>">

                            </td>

                            <td colspan="18"></td>

                        </tr>



                        <tr>

                            <th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>

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

                    </table>



                </div>

            </div>

        <?php

        } else if ($row_product["split_type"] == "2") {



            $tmp_split = explode(",", $row_product["split_name"]);

            $split_name1 = $tmp_split[0];

            $split_name2 = $tmp_split[1];

        ?>

            <div class="tab-pane active" id="fill-tabpanel-<?php echo $teamno; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $teamno; ?>">

                <div align="center" class="prod_card" id="prod_card<?php echo $form_id; ?> team<?php echo $teamno; ?>" card-id="<?php echo $form_id; ?>">

                    <input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">

                    <input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">

                    <input type="hidden" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">

                    <input type="hidden" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">

                    <input type="hidden" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">

                    <input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new">

                    <center>



                    </center>

                    <table class="tbl_item_form" style="width:100%;" align="center">

                        <tr class="theader">

                            <th class="tablecount">01</th>

                            <th colspan="15" class="text-center">

                                <div class="d-inline">

                                    <!-- <h6><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>) <i class="fa fa-minus-circle" data-toggle="tooltip" title="Click to delete order form" style="font-size: 16px; color: #F00; cursor: pointer;" onclick="return deleteProductCard(<?php echo $form_id; ?>);"></i></h6> -->
                                    <h6 class="my-auto"><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>) <span href="#" class="d-inline deleteTable" onclick="removeTable(this)">
                                            <figure class="m-0 d-inline"><img src="images/vector/delter.png" alt="" style="width: 30px; background: #FFF; padding: 6px;margin-left: 20px;
">
                                            </figure>
                                        </span></h6>

                                    <!-- <a href="#" class="d-inline m-2">

                                    <figure class="m-0 d-inline"><img

                                    src="images/vector/edit.png" alt="">

                                    </figure>

                                </a> -->


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

                                <th style="width:150px;">Name on <?php echo $split_name1; ?></th>

                            <?php

                            }



                            if ($row_product["choose_pg"] == "1") {

                            ?>

                                <th style="width:65px;">P or G</th>

                            <?php

                            }



                            if ($row_product["choose_mf"] == "1") {

                            ?>

                                <th style="width:110px;">Pattern Cut
                                    <span class="fa-stack " data-toggle="tooltip" title="Please note: Women’s cuts available only when there is a full team order of Adult women sizes only.">
                                        <i class="fa fa-info fa-stack-1x fa-inverse" style="cursor:pointer;"></i>
                                    </span>
                                </th>

                            <?php

                            }

                            ?>

                            <th style="width:80px;"><?php echo $split_name1; ?> Size</th>

                            <th><?php echo $split_name1; ?> #(Number)</th>

                            <th><?php echo $split_name1; ?> Color</th>

                            <th style="width:50px;">QTY</th>

                            <th style="width:80px;"><?php echo $split_name2; ?> Size</th>

                            <th><?php echo $split_name2; ?> Color</th>

                            <th style="width:50px;">QTY</th>

                            <th style="width:125px">Name For Packing

                                <span class="fa-stack " data-toggle="tooltip" title="Option to add a name or descriptor on packaging.">
                                    <i class="fa fa-info fa-stack-1x fa-inverse" style="cursor:pointer;"></i> </span>

                            </th>

                            <th>Notes</th>

                        </tr>

                        <tbody id="prod_item_<?php echo $form_id; ?>">

                            <?php

                            // Calculate number of rows needed - use Excel data count or default to 16
                            $row_count = !empty($excel_data) ? max(count($excel_data), 16) : 16;


                            // Helper to get selected attribute
                            function getSelectedAttr($value, $compare)
                            {
                                return (strcasecmp($value, $compare) === 0) ? 'selected' : '';
                            }

                            // Helper to find size option by name
                            function getSizeOptSelected($sizes, $value)
                            {
                                if (empty($value)) return '';
                                for ($i = 0; $i < sizeof($sizes); $i++) {
                                    if (strcasecmp($sizes[$i]["size_name"], $value) === 0) {
                                        return 'selected';
                                    }
                                }
                                return '';
                            }

                            for ($tet = 1; $tet <= $row_count; $tet++) {
                                // Get Excel row data (0-indexed)
                                $excel_row = isset($excel_data[$tet - 1]) ? $excel_data[$tet - 1] : [];
                                $col_index = 0; // Track column index for Excel data mapping


                                $mf_val = strtolower(getExcelValue($excel_row, $row_product["choose_mf"] == "1" ? ($row_product["choose_pg"] == "1" ? 2 : 1) : ($row_product["choose_pg"] == "1" ? 1 : 0)));
                                $pg_val = strtolower(getExcelValue($excel_row, $row_product["choose_pg"] == "1" ? ($row_product["have_name"] == "1" ? 2 : 1) : 0));
                            ?>

                                <tr id="prod_item_<?php echo $form_id; ?>_<?= $tet ?>">

                                    <td>

                                        <button class="deleteRow border-none bg-none" onclick="deleteRow(this)">

                                            <figure class="m-0"><img src="images/vector/deleteGrey.png" alt=""></figure>

                                        </button>

                                        <input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">

                                    </td>

                                    <?php

                                    if ($row_product["have_name"] == "1") {
                                        $col_idx = $col_index++;
                                    ?>

                                        <td><input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo getExcelValue($excel_row, $col_idx); ?>"></td>

                                    <?php

                                    }



                                    if ($row_product["choose_pg"] == "1") {
                                        $col_idx = $col_index++;
                                    ?>

                                        <td>

                                            <select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePG(<?php echo $form_id; ?>,<?= $tet ?>);">

                                                <option value="player" <?php echo getSelectedAttr(strtolower(getExcelValue($excel_row, $col_idx)), 'player'); ?> title="Player">Player</option>

                                                <option value="goalie" <?php echo getSelectedAttr(strtolower(getExcelValue($excel_row, $col_idx)), 'goalie'); ?> title="Goalie">Goalie</option>

                                            </select>

                                        </td>

                                    <?php

                                    }



                                    if ($row_product["choose_mf"] == "1") {
                                        $col_idx = $col_index++;
                                        $mf_cell = strtolower(getExcelValue($excel_row, $col_idx));
                                    ?>

                                        <td>

                                            <select class="white_in" id="select_mf_<?php echo $form_id; ?>_<?= $tet ?>" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');">

                                                <option value="youth" <?php echo getSelectedAttr($mf_cell, 'youth'); ?>>YOUTH</option>

                                                <option value="male" <?php echo getSelectedAttr($mf_cell, 'male') . getSelectedAttr($mf_cell, 'adult'); ?>>ADULT</option>

                                                <option value="female_youth" <?php echo getSelectedAttr($mf_cell, 'female_youth') . getSelectedAttr($mf_cell, 'women-youth'); ?>>WOMEN-YOUTH</option>

                                                <option value="female" <?php echo getSelectedAttr($mf_cell, 'female') . getSelectedAttr($mf_cell, 'women-adult'); ?>>WOMEN-ADULT</option>

                                            </select>

                                        </td>

                                        <input type="hidden" value="uni" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>">

                                    <?php

                                    }

                                    ?>

                                    <td>

                                        <select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_jsize[<?php echo $form_id; ?>][]">

                                            <option value="0"></option>

                                            <?php
                                            $jsize_col = $col_index++;
                                            for ($i = 0; $i < sizeof($a_size[$spl_order]); $i++) {
                                            ?>

                                                <option value="<?php echo $a_size[$spl_order][$i]["size_id"]; ?>" <?php echo getSizeOptSelected($a_size[$spl_order], getExcelValue($excel_row, $jsize_col)); ?>><?php echo $a_size[$spl_order][$i]["size_name"]; ?></option>

                                            <?php } ?>

                                        </select>

                                    </td>

                                    <td><input class="white_in" name="jersey_number[<?php echo $form_id; ?>][]" type="text" maxlength="10" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td>

                                        <select class="white_in" id="select_ssize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_ssize[<?php echo $form_id; ?>][]">

                                            <option value="0"></option>

                                            <?php
                                            $ssize_col = $col_index++;
                                            for ($i = 0; $i < sizeof($a_size[$spl_order]); $i++) {
                                            ?>

                                                <option value="<?php echo $a_size[$spl_order][$i]["size_id"]; ?>" <?php echo getSizeOptSelected($a_size[$spl_order], getExcelValue($excel_row, $ssize_col)); ?>><?php echo $a_size[$spl_order][$i]["size_name"]; ?></option>

                                            <?php } ?>

                                        </select>

                                    </td>

                                    <td><input class="white_in" name="sock_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td><input class="white_in sock_qty_<?php echo $form_id; ?>" name="sock_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'sock_qty_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td><input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td><input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>



                                </tr>

                            <?php

                            }

                            ?>
                        </tbody>

                        <tfoot> 
                                <tr>

                                    <td colspan="2">
                                        <span id="addRowButton" class="addRowButtoncls bg-none border-none d-flex justify-content-between w-100" onclick="return addItemRow(<?php echo $form_id; ?>,1);">
                                            <figure class="m-0"><img src="../../images/vector/add.png" alt=""></figure>
                                        </span>
                                        <input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $tet; ?>">

                                    </td>

                                    <td colspan="18"></td>

                                </tr>



                                <tr>


                                    <th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>

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

                                    <!-- <th style="border-width: 0px; background-color: #FFA;"></th> -->

                                    <th colspan="2" style="background: #F9F9F9 !important;   padding: 10px 0 10px 10px !important;">
                                        <p class="mb-0" style="background: #222222;  text-align: center;  font-size: 13px;  font-weight: 500;  padding: 10px !important;  border-radius: 15px 0 0 15px;">
                                            Special Comments (if any)
                                        </p>
                                    </th>

                                    <th colspan="16" style="background-color: #F9F9F9 !important; padding: 10px  10px 10px 0 !important;">
                                        <input type="text" name="special_comment[<?= $form_id ?>]" placeholder="Enter Special Comment here..." style=" width: 100%; background:#FFF !important; border: 1px solid #eee; padding: 8px; border-radius: 0 20px 20px 0;">
                                    </th>

                                </tr>

                        </tfoot>
                    </table>



                </div>

            </div>

        <?php

        } else {



            $split_name = $row_product["split_name"];

        ?>

            <div class="tab-pane active" id="fill-tabpanel-<?php echo $teamno; ?>" role="tabpanel" aria-labelledby="fill-tab-<?php echo $teamno; ?>">

                <div class="prod_card" id="prod_card<?php echo $form_id; ?> team<?php echo $teamno; ?>" card-id="<?php echo $form_id; ?>">

                    <input type="hidden" id="obj_size<?php echo $form_id; ?>" value="<?php echo base64_encode(json_encode($a_size)); ?>">

                    <input type="hidden" name="prod_id_list[<?php echo $form_id; ?>]" value="<?php echo $prod_id ?>">

                    <input type="hidden" name="form_name_list[<?php echo $form_id; ?>]" value="<?php echo $form_name; ?>">

                    <input type="hidden" name="on_team_name_list[<?php echo $form_id; ?>]" value="<?php echo $on_team_name; ?>">

                    <input type="hidden" name="on_year_list[<?php echo $form_id; ?>]" value="<?php echo $on_year; ?>">

                    <input type="hidden" id="edit_of_id<?php echo $form_id; ?>" name="edit_of_id[<?php echo $form_id; ?>]" value="new">

                    <center>



                    </center>

                    <table class="tbl_item_form" style="width:100%;" align="center">
                        <thead>
                            <tr class="theader">

                                <th class="tablecount">01</th>

                                <th colspan="15" class="text-center">

                                    <div class="d-inline">

                                        <h6 class="my-auto"><?php echo $form_name; ?>(<?php echo $row_product["prod_name"]; ?>)
                                            <span href="#" class="d-inline deleteTable"
                                                onclick="removeTable(this)">
                                                <figure class="m-0 d-inline"><img
                                                        src="images/vector/delter.png" alt="" style="width: 30px; background: #FFF; padding: 6px;margin-left: 20px;
">
                                                </figure>
                                            </span>
                                        </h6>





                                    </div>

                                </th>

                            </tr>
                        </thead>



                        <tr>

                            <td data-toggle="tooltip" title="Click to add rows" onclick="return addItemRow(<?php echo $form_id; ?>,<?php echo $prod_id; ?>);">

                                <i class="fa fa-plus-circle"></i>

                                <input type="hidden" id="num_item_<?php echo $form_id; ?>" value="16">



                            </td>

                            <?php

                            if ($row_product["have_name"] == "1") {

                            ?>

                                <th style="width:150px;"><?php echo $split_name; ?></th>

                            <?php

                            }



                            if ($row_product["choose_pg"] == "1") {

                            ?>

                                <th style="width:65px;">P or G</th>

                            <?php

                            }



                            if ($row_product["prod_id"] == "2") {

                            ?>

                                <th style="width:65px;">Pattern Cut<span class="fa-stack " data-toggle="tooltip" title="Please note: Women’s cuts available only when there is a full team order of Adult women sizes only.">
                                        <i class="fa fa-info fa-stack-1x fa-inverse" style="cursor:pointer;"></i></span></th>

                            <?php

                            }



                            if ($row_product["choose_mf"] == "1") {

                            ?>

                                <th style="width:90px;">Sex</th>

                            <?php

                            }



                            if ($row_product["have_size"] == "1" && $prod_id == "4") {

                            ?>

                                <th style="width:80px;" class="glued_body">Glue</th>

                            <?php

                            }



                            if ($row_product["have_size"] == "1") {

                            ?>

                                <th style="width:80px;"><?php echo $split_name; ?> Size</th>

                            <?php

                            }



                            if ($row_product["have_number"] == "1") {

                            ?>

                                <th><?php echo $split_name; ?> #(Number)</th>

                            <?php

                            }

                            ?>

                            <th><?php echo $split_name; ?> Color</th>

                            <th style="width:50px;">QTY</th>

                            <?php

                            if ($prod_id == "4") {

                            ?>

                                <th style="width:150px;" class="namebar_td">Name on Namebar</th>

                            <?php } ?>

                            <th style="width:125px;">Name For Packing

                                <span class="fa-stack " data-toggle="tooltip" title="Option to add a name or descriptor on packaging.">
                                    <i class="fa fa-info fa-stack-1x fa-inverse" style="cursor:pointer;"></i>
                                </span>

                            </th>

                            <th>Notes</th>

                        </tr>

                        <tbody id="prod_item_<?php echo $form_id; ?>">

                            <?php

                            // Calculate number of rows needed - use Excel data count or default to 16
                            $row_count = !empty($excel_data) ? max(count($excel_data), 16) : 16;

                            // Get choices once outside the loop for this product
                            static $product_choices = null;
                            static $choices_data = [];
                            if ($product_choices === null && $row_product["have_name"] == "1") {
                                $sql_choices = "SELECT * FROM tbl_product_choices WHERE prod_id='" . $prod_id . "' AND enable=1 ORDER BY sort_no ASC;";
                                $product_choices = $conn->query($sql_choices);
                                if ($product_choices && $product_choices->num_rows > 0) {
                                    while ($c = $product_choices->fetch_assoc()) {
                                        $choices_data[] = $c;
                                    }
                                }
                            }

                            // Helper functions
                            if (!function_exists('getSelAttr2')) {
                                function getSelAttr2($value, $compare)
                                {
                                    return (strcasecmp($value, $compare) === 0) ? 'selected' : '';
                                }
                            }
                            if (!function_exists('getSizeOptSel2')) {
                                function getSizeOptSel2($sizes, $value)
                                {
                                    if (empty($value)) return '';
                                    for ($i = 0; $i < sizeof($sizes); $i++) {
                                        if (strcasecmp($sizes[$i]["size_name"], $value) === 0) {
                                            return 'selected';
                                        }
                                    }
                                    return '';
                                }
                            }

                            for ($tet = 1; $tet <= $row_count; $tet++) {
                                // Get Excel row data (0-indexed)
                                $excel_row = isset($excel_data[$tet - 1]) ? $excel_data[$tet - 1] : [];
                                $col_index = 0; // Track column index for Excel data mapping

                            ?>

                                <tr id="prod_item_<?php echo $form_id; ?>_<?= $tet ?>">

                                    <td>

                                        <button class="deleteRow border-none bg-none" onclick="deleteRow(this)">

                                            <figure class="m-0"><img src="images/vector/deleteGrey.png" alt=""></figure>

                                        </button>

                                        <input type="hidden" name="a_oi_id[<?php echo $form_id; ?>][]" value="new">

                                    </td>

                                    <?php

                                    if ($row_product["have_name"] == "1") {
                                        $col_idx = $col_index++;
                                        $player_name_val = getExcelValue($excel_row, $col_idx);
                                    ?>

                                        <td>

                                            <?php

                                            if (!empty($choices_data)) {
                                            ?>

                                                <select class="white_in" id="product_id_<?php echo $form_id; ?>_<?= $tet ?>" onchange="return changePatternExtra('select_pg_<?php echo $form_id; ?>_<?= $tet ?>','product_id_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_glue_num_<?php echo $form_id; ?>_<?= $tet ?>','select_jersey_num_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');" name="player_name[<?php echo $form_id; ?>][]">

                                                    <?php foreach ($choices_data as $row_choices) { ?>

                                                        <option value="<?php echo $row_choices["choice_name"]; ?>" <?php echo getSelAttr2($player_name_val, $row_choices["choice_name"]); ?>><?php echo $row_choices["choice_name"]; ?></option>

                                                    <?php } ?>

                                                </select>

                                            <?php

                                            } else {

                                            ?>

                                                <input class="white_in" name="player_name[<?php echo $form_id; ?>][]" type="text" maxlength="120" value="<?php echo $player_name_val; ?>">

                                            <?php

                                            }

                                            ?>

                                        </td>

                                    <?php

                                    }



                                    if ($row_product["choose_pg"] == "1") {
                                        $col_idx = $col_index++;
                                        $pg_cell = strtolower(getExcelValue($excel_row, $col_idx));
                                    ?>

                                        <td>

                                            <select class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');">

                                                <option value="player" <?php echo getSelAttr2($pg_cell, 'player'); ?> title="Player">Player</option>

                                                <option value="goalie" <?php echo getSelAttr2($pg_cell, 'goalie'); ?> title="Goalie">Goalie</option>

                                            </select>

                                        </td>

                                    <?php

                                    }



                                    if ($row_product["prod_id"] == "2") {
                                        $col_idx = $col_index++;
                                        $mf_cell = strtolower(getExcelValue($excel_row, $col_idx));
                                    ?>

                                        <td>

                                            <select class="white_in" onchange="return changePattern('select_mf_<?php echo $form_id; ?>_<?= $tet ?>','select_pg_<?php echo $form_id; ?>_<?= $tet ?>','select_jsize_<?php echo $form_id; ?>_<?= $tet ?>','select_ssize_<?php echo $form_id; ?>_<?= $tet ?>','<?php echo $prod_id; ?>');" id="select_mf_<?php echo $form_id; ?>_<?= $tet ?>" name="select_mf[<?php echo $form_id; ?>][]">

                                                <option value="youth" <?php echo getSelAttr2($mf_cell, 'youth'); ?>>YOUTH</option>

                                                <option value="male" <?php echo getSelAttr2($mf_cell, 'male') . getSelAttr2($mf_cell, 'adult'); ?>>ADULT</option>

                                            </select>

                                        </td>

                                    <?php

                                    }



                                    if ($row_product["choose_mf"] == "1") {
                                        $col_idx = $col_index++;
                                        $mf_cell = strtolower(getExcelValue($excel_row, $col_idx));
                                    ?>

                                        <td>

                                            <select class="white_in" id="select_mf_<?php echo $form_id; ?>_1" name="select_mf[<?php echo $form_id; ?>][]" onchange="return changeMF(<?php echo $form_id; ?>,1,1);">

                                                <option value="male" <?php echo getSelAttr2($mf_cell, 'male'); ?>>Male</option>

                                                <option value="female" <?php echo getSelAttr2($mf_cell, 'female'); ?>>Female</option>

                                            </select>

                                        </td>

                                    <?php

                                    }



                                    if ($row_product["have_size"] == "1" && $prod_id == "4") {
                                        $col_idx = $col_index++;
                                        $glue_val = strtolower(getExcelValue($excel_row, $col_idx));
                                    ?>

                                        <td class="glued_body">

                                            <select class="white_in" name="select_mf[<?php echo $form_id; ?>][]" id="select_glue_num_<?php echo $form_id; ?>_<?= $tet ?>" disabled>

                                                <option value="na" <?php echo (empty($glue_val) || $glue_val == 'na') ? 'selected' : ''; ?>>N/A</option>

                                                <option value="Yes" <?php echo getSelAttr2($glue_val, 'yes'); ?>>Yes</option>

                                                <option value="No" <?php echo getSelAttr2($glue_val, 'no'); ?>>No</option>

                                            </select>

                                        </td>

                                    <?php

                                    }



                                    if ($row_product["have_size"] == "1") {
                                        $col_idx = $col_index++;
                                    ?>

                                        <td>

                                            <select class="white_in" id="select_jsize_<?php echo $form_id; ?>_<?= $tet ?>" name="select_jsize[<?php echo $form_id; ?>][]">

                                                <option value="0"></option>

                                                <?php for ($i = 0; $i < sizeof($a_size["1"]); $i++) { ?>

                                                    <option value="<?php echo $a_size["1"][$i]["size_id"]; ?>" <?php echo getSizeOptSel2($a_size["1"], getExcelValue($excel_row, $col_idx)); ?>><?php echo $a_size["1"][$i]["size_name"]; ?></option>

                                                <?php } ?>

                                            </select>

                                        </td>

                                    <?php
                                    }



                                    if ($row_product["have_number"] == "1") {
                                        $col_idx = $col_index++;
                                    ?>

                                        <td>

                                            <input class="white_in" id="select_jersey_num_<?php echo $form_id; ?>_<?= $tet ?>" name="jersey_number[<?php echo $form_id; ?>][]"
                                                <?php if ($prod_id == "4") {
                                                    echo "readonly";
                                                    echo " placeholder='N/A'";
                                                } ?> type="text" maxlength="10" value="<?php echo getExcelValue($excel_row, $col_idx); ?>">
                                        </td>

                                    <?php

                                    }

                                    ?>

                                    <td><input class="white_in" name="jersey_color[<?php echo $form_id; ?>][]" type="text" maxlength="100" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <td><input class="white_in jersey_qty_<?php echo $form_id; ?>" name="jersey_qty[<?php echo $form_id; ?>][]" type="number" min="0" max="9999" onchange="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" onkeyup="calculateQTY(<?php echo $prod_id; ?>,'jersey_qty_<?php echo $form_id; ?>');" value="<?php echo getExcelValue($excel_row, $col_index++); ?>"></td>

                                    <?php

                                    if ($row_product["have_size"] == "1" && $prod_id == "4") {
                                        $col_idx = $col_index++;
                                    ?>

                                        <td class="namebar_td">

                                            <input type="text" readonly value="N/A" class="white_in" id="select_pg_<?php echo $form_id; ?>_<?= $tet ?>" name="select_pg[<?php echo $form_id; ?>][]">

                                        </td>

                                    <?php

                                    } ?>

                                    <td>
                                        <input class="white_in" name="name_for_packing[<?php echo $form_id; ?>][]" type="text" value="<?php echo getExcelValue($excel_row, $col_index++); ?>">
                                    </td>

                                    <td>
                                        <input class="white_in" name="note[<?php echo $form_id; ?>][]" type="text" value="<?php echo getExcelValue($excel_row, $col_index++); ?>">
                                    </td>


                                </tr>

                            <?php

                            }

                            ?>
                        </tbody>
                        <tr>
                            <td colspan="2">
                                <span id="addRowButton" class="addRowButtoncls bg-none border-none d-flex justify-content-between w-100" onclick="return addItemRow(<?php echo $form_id; ?>,1);">
                                    <figure class="m-0"><img src="../../images/vector/add.png" alt=""></figure>
                                </span>
                                <input type="hidden" id="num_item_<?php echo $form_id; ?>" value="<?php echo $tet; ?>">

                            </td>
                            <td colspan="18"></td>
                        </tr>

                        <tr>
                            <th colspan="2" style="text-align: center; font-size: 14px; font-weight: 500;">TOTAL ORDER</th>
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

                            <th colspan="2" style="background: #F9F9F9 !important;   padding: 10px 0 10px 10px !important;">
                                <p class="mb-0" style="background: #222222;  text-align: center;  font-size: 13px;  font-weight: 500;  padding: 10px !important;  border-radius: 15px 0 0 15px;">
                                    Special Comments (if any)
                                </p>
                            </th>
                            <th colspan="16" style="background-color: #F9F9F9 !important; padding: 10px  10px 10px 0 !important;">
                                <input type="text" name="special_comment[<?= $form_id ?>]" placeholder="Enter Special Comment here..." style=" width: 100%; background:#FFF !important; border: 1px solid #eee; padding: 8px; border-radius: 0 20px 20px 0;">
                            </th>
                        </tr>

                    </table>



                </div>

            </div>

        <?php

        }

        ?>

    </div>

</center>

<script>
    let hasModalBeenShown = false;

    function changePattern(pattern_id, p_g_id, jersey_size_id, sock_size_id, prod_id) {
        var select_id = pattern_id;
        var select = '#' + select_id;
        var pattern_cut = $(select).val();
        var p_or_g = $('#' + p_g_id).val();
        var prod_id = prod_id;
        if (prod_id == "1") {
            var htmls = '';
            if (p_or_g == "player") {
                htmls += '<option value="player" selected title="Player">Player</option>';
                htmls += '<option value="goalie" title="Goalie">Goalie</option>';
                $('#' + p_g_id).empty().append(htmls);
            } else {
                htmls += '<option value="player" title="Player">Player</option>';
                htmls += '<option value="goalie" selected title="Goalie">Goalie</option>';
                $('#' + p_g_id).empty().append(htmls);
            }
        }
        if (pattern_cut == 'female' && prod_id == 1 && !hasModalBeenShown) {
            $('#exampleModal').modal('show');
            hasModalBeenShown = true;
        }
        // else if(prod_id=="1" && pattern_cut=="youth"){
        //     var htmls='';
        //     htmls+='<option value="player" title="Player">Player</option>';
        //     $('#'+p_g_id).empty();
        //     $('#'+p_g_id).append(htmls);
        // }
        pattern_cut = $(select).val();
        p_or_g = $('#' + p_g_id).val();
        $.ajax({
            type: 'POST',
            data: {
                pattern_cut: pattern_cut,
                p_or_g: p_or_g,
                prod_id: prod_id
            },
            url: 'ajax/add_order/change_pattern.php',
            success: function(response) {
                var response = JSON.parse(response);
                if (response.status == 1) {
                    var html = '';
                    html += '<option value="0"></option>';
                    for (var i = 0; i < response.jersey_id.length; i++) {
                        html += '<option value="' + response.jersey_id[i] + '">' + response.jersey_size[i] + '</option>';
                    }

                    var sock = '';
                    sock += '<option value="0"></option>';
                    for (var z = 0; z < response.sock_id.length; z++) {
                        sock += '<option value="' + response.sock_id[z] + '">' + response.sock_size[z] + '</option>';
                    }
                    $('#' + jersey_size_id).empty();
                    $('#' + jersey_size_id).append(html);

                    $('#' + sock_size_id).empty();
                    $('#' + sock_size_id).append(sock);

                } else {
                    alert('Something Went Wrong');
                }

            }
        })
    }

    function changePatternExtra(select_p_g, product_id, product_size_id, glue_id, jersey_num_id, prod_id) {
        var select_p_g = select_p_g;
        var product_id = product_id;
        var product_size_id = product_size_id;
        var glue_id = glue_id;
        var jersey_num_id = jersey_num_id;
        var prod_id = prod_id;
        var product_name = $('#' + product_id).val();
        var html = '';
        if (product_name == "Hats" || product_name == "Beanies" || product_name == "Pom Poms") {
            $('#' + glue_id).prop("disabled", true);
            html = '';
            html += '<option value="0" selected>N/A</option>';
            $('#' + glue_id).empty();
            $('#' + glue_id).append(html);

            $('#' + jersey_num_id).prop("readonly", true);
            $('#' + jersey_num_id).val("N/A");

            html = '';
            html += '<option value="67">OSFA</option>';
            $('#' + product_size_id).empty();
            $('#' + product_size_id).append(html);

            $('#' + select_p_g).prop("readonly", true);
            $('#' + select_p_g).val("N/A");

            // var glue_name = $('#'+glue_id).removeAttr("disabled");
            // var html = '';
            // html+='<option value="Yes">Yes</option>';
            // html+='<option value="No>No</option>';
            // $('#'+glue_id).empty();
            // $('#'+glue_id).append(html);
        } else if (product_name == "Fight Strap") {
            $('#' + glue_id).prop("disabled", true);
            html = '';
            html += '<option value="0" selected>N/A</option>';
            $('#' + glue_id).empty();
            $('#' + glue_id).append(html);

            $('#' + jersey_num_id).prop("readonly", true);
            $('#' + jersey_num_id).val("N/A");

            $('#' + product_size_id).prop("disabled", true);
            html = '';
            html += '<option value="0" selected>N/A</option>';
            $('#' + product_size_id).empty();
            $('#' + product_size_id).append(html);

            $('#' + select_p_g).prop("readonly", true);
            $('#' + select_p_g).val("N/A");
        } else if (product_name == "Namebars") {
            $('#' + jersey_num_id).prop("readonly", true);
            $('#' + jersey_num_id).val("N/A");

            $('#' + product_size_id).prop("disabled", false);
            html = '';
            html += '<option value="68" selected>Youth</option>';
            html += '<option value="97">Adult</option>';
            $('#' + product_size_id).empty();
            $('#' + product_size_id).append(html);

            var glue_name = $('#' + glue_id).removeAttr("disabled");
            html = '';
            html += '<option value="Yes">Yes</option>';
            html += '<option value="No">No</option>';
            $('#' + glue_id).empty();
            $('#' + glue_id).append(html);

            $('#' + select_p_g).prop("readonly", false);
            $('#' + select_p_g).val("");
            $('#' + select_p_g).attr("placeholder", "Input Nameber Name...");
        } else if (product_name == "Captain Letters" || product_name == "Assistant Letters") {
            $('#' + product_size_id).prop("disabled", false);
            html = '';
            html += '<option value="68" selected>Youth</option>';
            html += '<option value="97">Adult</option>';
            $('#' + product_size_id).empty();
            $('#' + product_size_id).append(html);

            var glue_name = $('#' + glue_id).removeAttr("disabled");
            html = '';
            html += '<option value="Yes">Yes</option>';
            html += '<option value="No">No</option>';
            $('#' + glue_id).empty();
            $('#' + glue_id).append(html);

            $('#' + jersey_num_id).prop("readonly", true);
            $('#' + jersey_num_id).val("N/A");

            $('#' + select_p_g).prop("readonly", true);
            $('#' + select_p_g).val("N/A");
        } else if (product_name == "Sports Bags" || product_name == "Garment Bags") {
            $('#' + glue_id).prop("disabled", true);
            html = '';
            html += '<option value="0" selected>N/A</option>';
            $('#' + glue_id).empty();
            $('#' + glue_id).append(html);

            $('#' + product_size_id).prop("disabled", true);
            html = '';
            html += '<option value="0" selected>N/A</option>';
            $('#' + product_size_id).empty();
            $('#' + product_size_id).append(html);

            $('#' + jersey_num_id).removeAttr("readonly");
            $('#' + jersey_num_id).val("");
            $('#' + jersey_num_id).attr("placeholder", "Input Number Here...");

            $('#' + select_p_g).prop("readonly", true);
            $('#' + select_p_g).val("N/A");
        } else if (product_name == "Hockey Bags") {
            $('#' + product_size_id).prop("disabled", false);
            html = '';
            html += '<option value="98" selected>Junior</option>';
            html += '<option value="99">Senior</option>';
            html += '<option value="100">Goalie</option>';
            html += '<option value="101">Coach</option>';
            $('#' + product_size_id).empty();
            $('#' + product_size_id).append(html);

            $('#' + glue_id).prop("disabled", true);
            html = '';
            html += '<option value="0" selected>N/A</option>';
            $('#' + glue_id).empty();
            $('#' + glue_id).append(html);

            $('#' + jersey_num_id).removeAttr("readonly");
            $('#' + jersey_num_id).val("");
            $('#' + jersey_num_id).attr("placeholder", "Input Number Here...");

            $('#' + select_p_g).prop("readonly", true);
            $('#' + select_p_g).val("N/A");
        }
    }

    function changeMF(form_id, row_id, split_type) {

        var obj_size = $.parseJSON(window.atob($('#obj_size' + form_id).val()));

        var inner_select = '';

        if ($('#select_mf_' + form_id + '_' + row_id).val() == "male") {

            inner_select += '<option value="0"></option>';
            for (var i = 0; i < obj_size[1].length; i++) {
                inner_select += '<option value="' + obj_size[1][i].size_id + '">' + obj_size[1][i].size_name + '</option>';
            }

        } else if ($('#select_mf_' + form_id + '_' + row_id).val() == "female") {

            inner_select += '<option value="0"></option>';
            for (var i = 0; i < obj_size[2].length; i++) {
                inner_select += '<option value="' + obj_size[2][i].size_id + '">' + obj_size[2][i].size_name + '</option>';
            }

        } else {
            inner_select += '<option value="0"></option>';
            for (var i = 0; i < obj_size[3].length; i++) {
                inner_select += '<option value="' + obj_size[3][i].size_id + '">' + obj_size[3][i].size_name + '</option>';
            }
        }

        if (inner_select != '') {
            $('#select_jsize_' + form_id + '_' + row_id).html(inner_select);
            $('#select_ssize_' + form_id + '_' + row_id).html(inner_select);
        }

    }
</script>