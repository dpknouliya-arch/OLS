<?php
include('check-session_sub.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLSSUB"]));
$sub_user_id = $obj_user->sub_user_id;

$sql_select = "SELECT draft_id,order_date,req_due_date,customer_po,COUNT(prod_id) AS num_prod,COUNT(re_order_id) AS num_re_order FROM tbl_draft_of WHERE assign_user_id='" . $sub_user_id . "' AND enable=1 GROUP BY draft_id ORDER BY draft_id ASC;";
$rs_select = $conn->query($sql_select);

?>

<style>
	.defaultHeader {
		margin: 1.2vw;
		height: 100%;
		background: #FFF;
	}

	.boxes {
		padding: 1vw;
	}
</style>

<div class="teamMemberOrderFormPage">
	<div class="boxes">
		<div class=" ">
			<h2>Orders Information </h2>
			<p>Review, Edit, and Submit Your Orders </p>
		</div>
	</div>

	<div class="allOrders grid4">
		<?php
		if ($rs_select->num_rows == 0) {
			echo '<font color=red>No form assigned to you.</font>';
		}

		while ($row_draft = $rs_select->fetch_assoc()) {
		?>
			<span class="singleORder">
				<figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>
				<div class="orderDetails" id="draft_<?php echo $row_draft["draft_id"]; ?>">
					<div class="table-responsive" id="draft_card_<?php echo $row_draft["draft_id"]; ?>">
						<table class="table  border-none m-0" id="tbl_<?php echo $row_draft["draft_id"]; ?>" onclick="return showDraft('<?php echo $row_draft["draft_id"]; ?>');">
							<thead class=" ">
								<tr class="theader">
									<th colspan="2">
										<div class="d-inline text-black">Ref. # <br>
											<span class="jogCode themeColor XSmall"><?php echo $row_draft["draft_id"]; ?></span>
										</div>
										<div class="d-inline greenBadge">New</div>
									</th>
								</tr>
							</thead>
							<tbody id="tableBody">
								<tr>
									<td class="text-black-50">Ref.#</td>
									<td><?php echo $row_draft["draft_id"]; ?></td>
								</tr>
								<tr>
									<td class="text-black-50">Add date:</td>
									<td><?php echo (($row_draft["order_date"] == "0000-00-00") ? "" : date("F d, Y", strtotime($row_draft["order_date"]))); ?></td>
								</tr>
								<tr>
									<td class="text-black-50">Forms:</td>
									<td>
										<?php
										echo $row_draft["num_prod"];
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


<form action="?vp=<?php echo base64_encode('edit_order_sub'); ?>" id="form_edit" method="post">
	<input type="hidden" name="draft_id" id="edit_draft_id">
</form>

<script type="text/javascript">
	function showDraft(draft_id) {

		$('#edit_draft_id').val(draft_id);
		$('#form_edit').submit();

	}
</script>