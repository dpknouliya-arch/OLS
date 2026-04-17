<?php
include('check-session.php');
include('db.php');
$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;

if ($obj_user->user_level != "admin") {
	echo "<font color=red><b>Not allow to access.</b></font>";
	exit();
}

?>
<style type="text/css">
	fieldset input {
		width: auto;
		border: 1px solid #DDDDDD;
		background: #FFFFFF;
		padding: 5px 20px;
		font-size: 13px;
	}

	fieldset label {

		font-size: var(--XSmall-size);
	}

	fieldset .border {
		border-radius: 4px;
		background: #F9F9F9;
		padding: 1vw;
		display: flex;
		align-content: center;
		justify-content: start;
		gap: 20px;
		position: relative;
	}

	fieldset .border::after {
		content: '';
		position: absolute;
		left: 0;
		top: 10px;
		width: 0.3vw;
		height: 2.8vw;
		background: #2E3236;
		border-radius: 8px;
	}



	.upperForm {
		margin: 15px 0;
		display: grid;
		grid-template-columns: 68vw auto;
	}

	.setting_head {
		border: 1px solid #EEEEEE;
		background: #FFFFFF;
		color: var(--blueColor);
		font-size: var(--XSmall-size);
		font-weight: 500;
		text-align: center;
		padding: 6px;
		margin-bottom: 20px;
	}

	table {
		width: 100%;
	}

	.setting_sub_head {
		background: #FFF;
		font-size: var(--XSmall-size);
		border-radius: 8px 8px 0 0;
		position: sticky;
		top: 0;
	}

	.border-right {
		border-right: none !important;
	}

	.setting_sub_head .column2 {
		padding: 10px;
	}

	.setting_sub_head .upper {
		border-bottom: 1px solid #E4E4E4;
		padding: 10px;
		min-height: 50px;
		align-items: center;

	}

	.items {
		background: #F9F9F9;
		padding: 1vw;
		margin: 0.7vw 0;
	}

	.setting_sub_head select {
		border: 1px solid #DDDDDD;
		background: #FFFFFF;
		padding: 5px 10px;
		border-radius: 4px;

	}

	.setting_sub_head button {
		font-size: 13px;
		padding: 4px 12px;
		border-radius: 4px;
	}

	.prod_panel .singleTableItems {
		box-shadow: 0px 2px 4px 0px #0000001A;
		height: 50vh;
		background: #FFFFFF;
		overflow: scroll;
		border-radius: 10px;
		scrollbar-width: none;
		border: 1px solid #DDDDDD;
	}

	.tbl_content_size th {
		background: var(--blueColor);
		color: #FFF;
		padding: 5px;
		font-weight: 400;
	}

	.tbl_content_size td {
		background: #FFF;
		border: 1px solid #DDDDDD;
		padding: 5px 10px;
		font-size: 13px;
		font-weight: 400;
		color: #111;
	}

	.actionBtns {
		display: flex;
		align-content: center;
		justify-content: center;
		border: 1px solid #DDD;
		border-left: 0;
	}

	.actionBtns .themeBtn2grey {
		height: 35px;
		margin: auto;
	}

	.iconBTn img {
		cursor: pointer;
	}

	.tbl_content_size .iconBTn {
		justify-content: center;
		display: flex;
	}

	.stickyTr {
		position: sticky;
		top: 100px;
	}

	.tbl_content_size th:nth-child(1) {
		width: 2vw;
	}

	.tbl_content_size th:nth-child(2) {
		width: 10vw;
	}
</style>
<div class="rightInnerDefault">

	<div class="pageHeader">
		<h2>Setting</h2>
		<p>This is where your Billing Info will go.</p>
	</div>
	<div class="col-md-12">
		<fieldset>
			<label for="">Google SMTP Connection:</label> <br>
			<?php
			$sql_email_setting = "SELECT * FROM tbl_email_setting WHERE use_for='gmail_SMTP' AND enable=1; ";
			$rs_email_setting = $conn->query($sql_email_setting);
			$row_email_setting = $rs_email_setting->fetch_assoc();
			$smtp_user = $row_email_setting["email_name"];
			$smtp_password = $row_email_setting["email_password"];
			?>

			<div class="upperForm">
				<div class="border">
					<div>
						<label for=""> Email account: </label>
						<input type="text" id="input_email" disabled value="<?php echo $smtp_user; ?>">
					</div>
					<div>
						<label for=""> Password: </label>
						<input type="password" id="input_password" disabled value="<?php echo $smtp_password; ?>">
					</div>
				</div>
				<div class="actionBtns">
					<div class=" d-flex gap-2">
						<button class=" btn themeBtn2grey d-flex  iconBTn" type=" button" onclick="return editSMTPInfo();" id="btn_edit_smtp">
							<figure class="m-0"><img src="images/vector/ediBlue.png" alt=""></figure> Edit
						</button>
						<button class="btn  themeBtn2grey d-flex iconBTn " type="button" onclick="return saveSMTPInfo();" disabled id="btn_save_smtp">
							<figure class="m-0"><img src="images/vector/saveGreen.png" alt=""></figure> Save
						</button>

					</div>
				</div>
			</div>
		</fieldset>
	</div>
	<div class="col-md-12">
		<fieldset>
			<div class="container-fluid p-0">

				<div class="row">
					<legend class="mSize">Product Size:</legend>

					<div class=" col-md-8 col-12 show_on_left">
						<div class="items">
							<div class="setting_head iconBTn d-flex align-items-center justify-content-center">
								<figure class="m-0"><img src="images/vector/socks.png" alt=""></figure> Hockey Jersey & Socks
							</div>
							<div class="row prod_panel" id="prod_content1">

							</div>
						</div>
					</div>
					<div class="col-md-4 col-12 show_on_right">
						<div class="items">
							<div class="setting_head iconBTn d-flex align-items-center justify-content-center">
								<figure class="m-0"><img src="images/vector/socks.png" alt=""></figure> Sport & Apparel
							</div>

							<div class="row prod_panel" id="prod_content3">

							</div>
						</div>
					</div>
					<div class="col-8 show_on_right">
						<div class="items">
							<div class="setting_head iconBTn d-flex align-items-center justify-content-center">
								<figure class="m-0"><img src="images/vector/socks.png" alt=""></figure> Bag/Hat/Accessories
							</div>
							<div class="row prod_panel" id="prod_content4">

							</div>
						</div>
					</div>
					<div class="col-4 show_on_left">
						<div class="items">
							<div class="setting_head iconBTn d-flex align-items-center justify-content-center">
								<figure class="m-0"><img src="images/vector/socks.png" alt=""></figure> Hockey Shell Pants
							</div>
							<div class="row prod_panel" id="prod_content2">

							</div>
						</div>

					</div>

				</div>

			</div>

		</fieldset>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
	showInnerContent(1);
	showInnerContent(2);
	showInnerContent(3);
	showInnerContent(4);

	function showInnerContent(prod_id, select_pg = 1) {

		$('#prod_content' + prod_id).html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Loading...');

		$.ajax({
			type: "POST",
			dataType: "html",
			url: "ajax/setting/show_content.php",
			data: {
				"prod_id": prod_id,
				"select_pg": select_pg
			},
			success: function(resp) {

				$('#prod_content' + prod_id).html(resp);

			}
		});

	}

	function changeSelectPG(prod_id) {

		if (prod_id == "1") {
			if ($('#select_pg_1').val() == "1") {
				$('#g_hockey_jersey_size').hide();
				$('#p_hockey_jersey_size').show();
			} else {
				$('#p_hockey_jersey_size').hide();
				$('#g_hockey_jersey_size').show();
			}
		} else if (prod_id == "2") {
			if ($('#select_pg_2').val() == "1") {
				$('#g_shell_pant_size').hide();
				$('#p_shell_pant_size').show();
			} else {
				$('#p_shell_pant_size').hide();
				$('#g_shell_pant_size').show();
			}
		} else if (prod_id == "3") {
			if ($('#select_pg_3').val() == "1") {
				$('#r_tops_bottoms_size').hide();
				$('#g_tops_bottoms_size').hide();
				$('#p_tops_bottoms_size').show();
			} else if ($('#select_pg_3').val() == "2") {
				$('#p_tops_bottoms_size').hide();
				$('#g_tops_bottoms_size').show();
				$('#r_tops_bottoms_size').hide();
			} else {
				$('#p_tops_bottoms_size').hide();
				$('#g_tops_bottoms_size').hide();
				$('#r_tops_bottoms_size').show();
			}
		}

	}

	function addNewChoice(prod_id) {

		var choice_name = prompt("Choice name:", "");

		if (choice_name != null && choice_name != "") {

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/add_new_choice.php",
				data: {
					"prod_id": prod_id,
					"choice_name": window.btoa(choice_name)
				},
				success: function(resp) {

					if (resp.result == "success") {

						showInnerContent(prod_id);

					} else {
						alert(resp.msg);
					}

				}
			});
		}

	}

	function addNewSize(prod_id) {

		var size_name = prompt("Size name:", "");

		if (size_name != null && size_name != "") {

			var select_pg = $('#select_pg_' + prod_id).val();

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/add_new_size.php",
				data: {
					"prod_id": prod_id,
					"split_order": select_pg,
					"size_name": window.btoa(size_name)
				},
				success: function(resp) {

					if (resp.result == "success") {

						showInnerContent(prod_id, $('#select_pg_' + prod_id).val());

					} else {
						alert(resp.msg);
					}

				}
			});
		}

	}

	function addNewSize2(prod_id, split_order) {

		var size_name = prompt("Size name:", "");

		if (size_name != null && size_name != "") {

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/add_new_size.php",
				data: {
					"prod_id": prod_id,
					"split_order": split_order,
					"size_name": window.btoa(size_name)
				},
				success: function(resp) {

					if (resp.result == "success") {

						if (prod_id == "4") {
							showInnerContent(prod_id);
						} else {
							showInnerContent(prod_id, $('#select_pg_' + prod_id).val());
						}


					} else {
						alert(resp.msg);
					}

				}
			});
		}

	}

	function swapChoiceSorting(prod_id, pro_choice_id_up, pro_choice_id_down) {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "ajax/setting/swap_choice_sort.php",
			data: {
				"pro_choice_id_up": pro_choice_id_up,
				"pro_choice_id_down": pro_choice_id_down
			},
			success: function(resp) {

				if (resp.result == "success") {

					showInnerContent(prod_id);

				} else {
					alert(resp.msg);
				}

			}
		});
	}

	function swapSorting(prod_id, size_id_up, size_id_down) {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "ajax/setting/swap_size_sort.php",
			data: {
				"size_id_up": size_id_up,
				"size_id_down": size_id_down
			},
			success: function(resp) {

				if (resp.result == "success") {

					showInnerContent(prod_id, $('#select_pg_' + prod_id).val());

				} else {
					alert(resp.msg);
				}

			}
		});
	}

	function editChoice(pro_choice_id) {

		var choice_name = $('#td_choice' + pro_choice_id).html();

		var edit_choice_name = prompt("Edit choice name:", choice_name);

		if (edit_choice_name != "" && edit_choice_name != null) {

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/edit_choice_name.php",
				data: {
					"pro_choice_id": pro_choice_id,
					"choice_name": window.btoa(edit_choice_name)
				},
				success: function(resp) {

					if (resp.result == "success") {

						$('#td_choice' + pro_choice_id).html(edit_choice_name);

					} else {
						alert(resp.msg);
					}

				}
			});

		}
	}

	function editSize(size_id) {

		var size_name = $('#td_size' + size_id).html();

		var edit_size_name = prompt("Edit size name:", size_name);

		if (edit_size_name != "" && edit_size_name != null) {

			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/edit_size_name.php",
				data: {
					"size_id": size_id,
					"size_name": window.btoa(edit_size_name)
				},
				success: function(resp) {

					if (resp.result == "success") {

						$('#td_size' + size_id).html(edit_size_name);

					} else {
						alert(resp.msg);
					}

				}
			});

		}

	}

	function deleteChoice(prod_id, pro_choice_id) {

		var choice_name = $('#td_choice' + pro_choice_id).html();

		if (confirm("Confirm to delete \'" + choice_name + "\' ?")) {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/delete_choice.php",
				data: {
					"prod_id": prod_id,
					"pro_choice_id": pro_choice_id
				},
				success: function(resp) {

					if (resp.result == "success") {

						showInnerContent(prod_id);

					} else {
						alert(resp.msg);
					}

				}
			});
		}

	}

	function deleteSize(prod_id, split_order, size_id) {

		var size_name = $('#td_size' + size_id).html();

		if (confirm("Confirm to delete \'" + size_name + "\' ?")) {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "ajax/setting/delete_size.php",
				data: {
					"prod_id": prod_id,
					"split_order": split_order,
					"size_id": size_id
				},
				success: function(resp) {

					if (resp.result == "success") {

						showInnerContent(prod_id, $('#select_pg_' + prod_id).val());

					} else {
						alert(resp.msg);
					}

				}
			});
		}

	}

	function editSMTPInfo() {

		$('#input_email').attr("disabled", false);
		$('#input_password').attr("disabled", false).attr("type", "text");

		$('#btn_edit_smtp').attr("disabled", true);
		$('#btn_save_smtp').attr("disabled", false);
		/*var input_email = $('#input_email').val();
		var input_password = $('#input_password').val();

		alert(input_password);*/
	}

	function saveSMTPInfo() {

		var input_email = $('#input_email').val();
		var input_password = $('#input_password').val();
		if (input_email == "" || input_password == "") {
			alert("Please input account and password");
			return false;
		}

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "ajax/setting/submit_smtp_info.php",
			data: {
				"s_email": window.btoa(input_email),
				"s_password": window.btoa(input_password)
			},
			success: function(resp) {

				if (resp.result == "success") {

					$('#input_email').attr("disabled", true);
					$('#input_password').attr("disabled", true).attr("type", "password");

					$('#btn_edit_smtp').attr("disabled", false);
					$('#btn_save_smtp').attr("disabled", true);

				} else {
					alert(resp.msg);
				}

			}
		});

	}
</script>