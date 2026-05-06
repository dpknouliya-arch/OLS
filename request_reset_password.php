<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php';

if (!isset($_POST["email"])) {

	$a_result["result"] = "fail";
	$a_result["msg"] = "Fail to register.";
	echo json_encode($a_result);

	exit();
}



$email = base64_decode($_POST["email"]);

  
$sql_chk = "SELECT user_id,user_email,full_name FROM tbl_user WHERE user_email='" . $email . "' ; ";
$rs_chk = $conn->query($sql_chk);
if ($rs_chk->num_rows == 0) {
	$a_result["result"] = "fail";
	$a_result["msg"] = "Make sure you input correctly email.";
} else {

	$row_chk = $rs_chk->fetch_assoc();
	$user_id = $row_chk["user_id"];
	$user_email = $row_chk["user_email"];
	$full_name = $row_chk["full_name"];

	$s_tmp = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$activate_key = "";
	for ($i = 0; $i < 32; $i++) {
		$ran_num = rand(0, 61);
		$activate_key .= substr($s_tmp, $ran_num, 1);
	}

	$sql_update = "UPDATE tbl_user SET activate_key='" . $activate_key . "' WHERE user_id='" . $user_id . "'; ";

	if ($conn->query($sql_update)) {

		$sql_sender = "SELECT * FROM tbl_email_setting WHERE use_for='gmail_SMTP' AND enable=1 LIMIT 0,1; "; //sendgrid_SMTP
		$rs_sender = $conn->query($sql_sender);

		if ($rs_sender->num_rows > 0) {

			$row_sender = $rs_sender->fetch_assoc();
			$email_name = $row_sender["email_name"];
			$email_password = $row_sender["email_password"];
			$host_name = $row_sender["server_name"];
			$port_num = $row_sender["port_name"];

			$smtp_secure = $row_sender["smtp_secure"];

			$html='<!DOCTYPE html>
					<html>

						<head>
							<title></title>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<meta name="viewport" content="width=device-width, initial-scale=1">
							<meta http-equiv="X-UA-Compatible" content="IE=edge" />
						</head>

						<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">
							<div style="background-color: #f4f4f4; margin: 0!important; padding: 0!important">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tbody>
										<tr>
											<td bgcolor="#000000" align="center">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px">
													<tbody>
														<tr>
															<td align="center" valign="top" style="padding: 40px 10px 40px 10px"> </td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td bgcolor="#000000" align="center" style="padding: 0px 10px 0px 10px">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px">
													<tbody>
														<tr>
															<td bgcolor="#ffffff" align="center" valign="top">
																<img src="https://online.jog-joinourgame.com/assets/images/logo.png" width="125" height="100"
																	style="display: block; border: 0px; object-fit: contain;">
																<h1
																	style="font-size: 30px; font-weight: 700; margin-bottom: 20px;padding: 0 30px; text-align: center;">
																	Forgot Password!
																</h1>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px">
												<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px">
													<tbody>
														<tr>
															<td style="padding: 10px; border: 1px solid #E3EBF2;">
																<p
																	style="font-size: 20px; font-weight: 600;color: #403d3d; margin: 0; text-align: left; padding-left: 20px;">
																	Hello '.$full_name.',</p>
															</td>
														</tr>
														<tr>
															<td bgcolor="#ffffff" align="left">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tbody>
																		<tr>
																			<td bgcolor="#ffffff" align="center" style="padding: 20px ">
																				<table border="0" cellspacing="0" cellpadding="0"
																					style="width: 100%;">
																					<tbody>
																						<tr>
																							<td style="padding: 10px   ; text-align: left; border: 1px solid #E3EBF2;">
																								<p style="margin: 0">You have requested to reset your password. Click the button below to reset your password.</p>
																							</td>
																						</tr>
																						<tr>
																							<td
																								style="padding: 10px   ; text-align: left;border: 1px solid #E3EBF2;">
																								<a href="https://ols-test.jog-joinourgame.com/confirm_email.php?key=' . $activate_key . '"
																									style="display: inline-block; background-color: #000000; color: #ffffff; padding: 8px 20px; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: 500;">Change
																									Password</a>
																							</td>
																						</tr>
																						<tr>
																							<td
																								style="padding: 10px   ; text-align: left;border: 1px solid #E3EBF2;">
																								<p style="margin: 0">Cheers,<br>JOG Team</p>
																							</td>
																						</tr>
																						<tr>
																							<td style="padding: 10px; text-align: left; font-size: 14px; color: #F00; background: #FFE1E2 ; text-align: center; border: 1px solid #E3EBF2;">
																								<p style="margin: 0;">Note: If this action was
																									not initiated by you, you can ignore this
																									message.</p>
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
														<tr>
															<td bgcolor="#000000" align="center" style="padding: 20px;">
																<h2 style="font-size: 20px; font-weight: 400; color: #ffffff; margin: 0">Need
																	more help?</h2>
																<p style="margin: 0"><a href="https://ols-test.jog-joinourgame.com" style="color: #ffffff"
																		target="_blank" rel="noreferrer">We re here to help you out</a></p>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</body>
					</html>';

			$mail = new PHPMailer;
			//$mail->isSMTP();
			//$mail->SMTPDebug = 2;
			$mail->Host = "mail.jog-joinourgame.com";
			$mail->Port = $port_num; //465
			$mail->SMTPSecure = "tls";
			$mail->SMTPAuth = true;
			$mail->Username = "no-reply@jog-joinourgame.com";
			$mail->Password = "demo@9090";

			$mail->setFrom('no-reply@jog-joinourgame.com', 'JOG Sports');
			$mail->addBcc("ravish@jogsportswear.com");
			$mail->addAddress($email);
			$mail->isHTML(true);
			$mail->CharSet = 'UTF-8';
			$mail->Subject = 'Reset your password (JOG Online Services)';
			$mail->msgHTML($html);
			if (!$mail->send()) {
				//echo "Mailer Error: " . $mail->ErrorInfo;
				$a_result["result"] = "fail";
				$a_result["msg"] = "Mailer Error: " . $mail->ErrorInfo;
			} else {
				$a_result["result"] = "success";
			}
		} else {
			$a_result["result"] = "fail";
			$a_result["msg"] = "Error: Sender email account not set.";
		}
	} else {
		$a_result["result"] = "fail";
		$a_result["msg"] = "Fail to connect DB.";
	}
}

echo json_encode($a_result);
