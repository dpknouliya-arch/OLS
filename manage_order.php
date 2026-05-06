<?php
include('check-session.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$user_id = $obj_user->user_id;


$sql_select = "SELECT 
    draft_id,
    order_date AS order_date,
     req_due_date AS req_due_date,
     customer_po AS customer_po,
     project_name AS project_name,
    prod_id AS num_prod,
    re_order_id AS num_re_order
FROM tbl_draft_of
WHERE user_id = ?
AND enable = 1 AND 	is_3dorder=0
GROUP BY draft_id
ORDER BY draft_id DESC
";

$stmt = $conn->prepare($sql_select);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rs_select = $stmt->get_result();

$a_row_draft = array();
while ($row_select = $rs_select->fetch_assoc()) {
	$a_row_draft[($row_select["draft_id"])]["row_normal"] = $row_select;
	$a_row_draft[($row_select["draft_id"])]["row_file"] = array();
}

?>


<div class=" manageOrder">
	<div class="innerMainContent">
		<div class="PageHeader">
			<h2>Manage Order</h2>
			<p>Review, Edit, and Submit Your Orders </p>
		</div>

		<div class="boxes">
			<div class="formTitle d-flex align-items-center flex-row">
				<h6 class="subHeading mb-4">Billing Information </h6>
			</div>
		</div>
		<div class="allOrders grid3">
			<?php
			foreach ($a_row_draft as $draft_id => $a_data) {

				$row_draft = array();
				if (sizeof($a_data["row_normal"]) > 0) {
					$row_draft = $a_data["row_normal"];
					if (sizeof($a_data["row_file"]) > 0) {
						$row_draft["num_prod"] = intval($a_data["row_normal"]["num_prod"]) + intval($a_data["row_file"]["num_prod"]);
						$row_draft["num_re_order"] = intval($a_data["row_normal"]["num_re_order"]) + intval($a_data["row_file"]["num_re_order"]);
					}
				} else {
					$row_draft = $a_data["row_file"];
					if (sizeof($a_data["row_normal"]) > 0) {
						$row_draft["num_prod"] = intval($a_data["row_normal"]["num_prod"]) + intval($a_data["row_file"]["num_prod"]);
						$row_draft["num_re_order"] = intval($a_data["row_normal"]["num_re_order"]) + intval($a_data["row_file"]["num_re_order"]);
					}
				}
			?>
				<span class="singleORder">
					<figure class="m-0" id="tbl_<?php echo $row_draft["draft_id"]; ?>" onclick="return showDraft('<?php echo $row_draft["draft_id"]; ?>');"><img src="images/vector/nextBtn.png" alt=""></figure>
					<div class="orderDetails" id="draft_<?php echo $row_draft["draft_id"]; ?>">
						<div class="table-responsive" id="draft_card_<?php echo $row_draft["draft_id"]; ?>">
							<table class="table  border-none m-0" id="tbl_<?php echo $row_draft["draft_id"]; ?>" onclick="return showDraft('<?php echo $row_draft["draft_id"]; ?>');">
								<thead class=" ">
									<tr class="theader">
										<th colspan="2">
											<div class="d-inline">Ref. # <br>
												<span class="jogCode themeColor XSmall"><?php echo $row_draft["draft_id"]; ?></span>
											</div>
											<div class="d-inline greenBadge">New</div>
										</th>
									</tr>

								</thead>
								<tbody id="tableBody">
									<tr>
										<td class="text-black-50"> Add date </td>
										<td><?php echo (($row_draft["order_date"] == "0000-00-00") ? "" : date("F d, Y", strtotime($row_draft["order_date"]))); ?></td>
									</tr>
									<tr>
										<td class="text-black-50"> Request Due </td>
										<td> <?php echo (($row_draft["req_due_date"] == "0000-00-00") ? "" : date("F d, Y", strtotime($row_draft["req_due_date"]))); ?> </td>
									</tr>
									<tr>
										<td class="text-black-50"> Customer PO </td>
										<td> <?php echo $row_draft["customer_po"]; ?> </td>
									</tr>
									<tr>
										<td class="text-black-50"> Project Name </td>
										<td> <?php echo $row_draft["project_name"]; ?> </td>
									</tr>
									<tr>
										<td class="text-black-50"> Order Forms </td>
										<td>
											<?php
											echo $row_draft["num_prod"];
											if ($row_draft["num_prod"] > $row_draft["num_re_order"] && $row_draft["num_re_order"] > 0) {
												echo '<div class="show_re_order_p">RE-ORDER <i>(PARTIAL)</i></div>';
											} else if ($row_draft["num_prod"] == $row_draft["num_re_order"]) {
												echo '<div class="show_re_order">RE-ORDER</div>';
											}
											?>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</span>
				
			<?php
			}
			?>
		</div>
	</div>
</div>



<form action="?vp=<?php echo base64_encode('edit_order'); ?>" id="form_edit" method="post">
	<input type="hidden" name="draft_id" id="edit_draft_id">
</form>

<script type="text/javascript">
	function showDraft(draft_id) {

		$('#edit_draft_id').val(draft_id);
		$('#form_edit').submit();


		$('.draft_box').hide();

		$('#draft_' + draft_id).show().css("max-width", "100%").addClass("col-md-12").addClass("col-sm-12").removeClass("col-md-3").removeClass("col-sm-3");


	
	}

	function minimizeDraft(draft_id) {

		$('.draft_card').animate({
			width: "100%",
			height: "130px"
		}).css("height", "130px").css("min-height", "130px");
		$('#draft_' + draft_id).css("max-width", "100%").removeClass("col-md-12").removeClass("col-sm-12").addClass("col-md-3").addClass("col-sm-3");

		$('#btn_mini_' + draft_id).hide();

		$('#d_edit_' + draft_id).html('');

		setTimeout(function() {
			$('.draft_box').show();
			$('#tbl_' + draft_id).show();
			$('#draft_card_' + draft_id).css("overflow-y", "hidden").css("overflow-x", "hidden");
		}, 450);

	}







</script>