<?php
session_start();

if (!isset($_SESSION["JOGOLS"])) {
	echo '<tr><td colspan="5"><center>No data</center></td></tr>';
	exit();
}

include('../../db.php');

$obj_user = json_decode(base64_decode($_SESSION["JOGOLS"]));
$TOKEN_KEY = 'jogsports_secure_key_' . session_id();

$user_id = $obj_user->user_id;

function encryptAddrId($addr_id, $key)
{
	$iv = openssl_random_pseudo_bytes(16);
	$encrypted = openssl_encrypt($addr_id, 'AES-128-CBC', $key, 0, $iv);
	return base64_encode($encrypted . '::' . base64_encode($iv));
}

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM tbl_address WHERE user_id = ? AND enable = 1 ORDER BY date_add DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rs_select = $stmt->get_result();


if ($rs_select->num_rows == 0) {
	echo '<tr><td colspan="6"><center>No data.</center></td></tr>';
	exit();
}

$n_count_row = 1;
while ($row_addr = $rs_select->fetch_assoc()) {

	$addr_id = $row_addr["addr_id"];
	$encrypted_addr_id = htmlspecialchars(encryptAddrId($addr_id, $TOKEN_KEY), ENT_QUOTES, 'UTF-8');
?>

	<style>
		.radio-item {
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
		}
	</style>
	<div class="row infoBody">
		<div class="col-12 col-md-4     text-start borderRight borderLeft">
			<div class="card bg-none border-none">
				<div class="gridCustom2">
					<h6>Company</h6>
					<p id="td_addr_name<?php echo $row_addr["addr_id"]; ?>"><?php echo $row_addr["addr_name"]; ?></p>
				</div>
				<div class="gridCustom2">
					<h6 class="my-auto">Contact</h6>
					<p><?php echo $row_addr["contact_name"]; ?></p>
				</div>
				<div class="gridCustom2">
					<h6 class="my-auto">Tel.</h6>
					<p><?php echo $row_addr["tel"]; ?></p>
				</div>
			</div>
		</div>
		<div class="col-12 col-md-4  text-start borderRight  ">
			<div class="card  bg-none border-none d-flex justify-content-center h-100">
				<div class="gridCustom2">
					<h6 class="my-auto">Address</h6>
					<p><?php echo $row_addr["address"]; ?></p>
				</div>
				<div class="gridCustom2">
					<h6 class="my-auto">City</h6>
					<p><?php echo $row_addr["city"]; ?></p>
				</div>
				<div class="gridCustom2">
					<h6 class="my-auto">Country</h6>
					<p><?php echo $row_addr["country"]; ?></p>
				</div>
				<div class="gridCustom2">
					<h6 class="my-auto">Zip Code</h6>
					<p><?php echo $row_addr["zip_code"]; ?></p>
				</div>
			</div>

		</div>
		<div class="col-12 col-md-1 borderelative borderRight ">
			<div class="card bg-none border-none d-flex justify-content-center h-100">
				<p><?php echo $row_addr["tax_id"]; ?></p>
			</div>
		</div>
		<div class="col-12 col-md-1 position-relative bo borderelative borderRight">
			<div class="card bg-none border-none d-flex justify-content-center h-100">
				<div class="radio-item">
					<input class="is_billing_addr" id="billing_<?php echo $encrypted_addr_id; ?>" type="radio" name="is_billing_addr" name11="is_billing_addr_<?php echo $encrypted_addr_id; ?>"
						<?php if ($row_addr["is_billing_addr"] == "1") {
							echo "checked";
						} ?> onclick="return setDefaultBilling('<?php echo $encrypted_addr_id; ?>');">
					<label for="billing_<?php echo $encrypted_addr_id; ?>"> </label>
				</div>
			</div>
		</div>

		<div class="col-12 col-md-1 position-relative borderelative borderRight">
			<div class="card bg-none border-none d-flex justify-content-center h-100">
				<div class="radio-item">
					<input class="is_deliver_addr" id="deliver_<?php echo $encrypted_addr_id; ?>" type="radio" name="is_deliver_addr" name11="is_deliver_addr_<?php echo $encrypted_addr_id; ?>" <?php if ($row_addr["is_deliver_addr"] == "1") {
																																																		echo "checked";
																																																	} ?> onclick="return setDefaultDeliver('<?php echo $encrypted_addr_id; ?>');">
					<label for="deliver_<?php echo $encrypted_addr_id; ?>"> </label>
				</div>
			</div>
		</div>

		<div class="col-12 col-md-1  ">
			<div class="card  bg-none border-none d-flex justify-content-center h-100">
				<button type="button" class="btn themeBtn2grey iconBTn XSmall" onclick="return editAddrInfo('<?php echo $encrypted_addr_id; ?>');" style="    width: 5vw;">
					<figure class="m-0"><img src="images/vector/editBlack.png" alt="" style="width: 16px;"></figure> <span class="noneOnscreen">Edit</span>
				</button>
				<button type="button" class="btn themeBtn2grey iconBTn XSmall" onclick="return deleteAddrInfo('<?php echo $encrypted_addr_id; ?>');">
					<figure class="m-0"><img src="images/vector/delBlack.png" alt="" style="width: 16px;"></figure> <span class="noneOnscreen">Remove</span>
				</button>

			</div>
		</div>
	</div>

<?php
	$n_count_row++;
}
?>