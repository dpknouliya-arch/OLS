<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=UTF-8');

include __DIR__ . '/../../db.php';


 class authFile{ 
     // Signup new user 
     

     function SignUp(){
        global $conn ,$conn3;
        $input = json_decode(file_get_contents("php://input"), true);
        $name = $input['username'] ?? NULL; 
        $email = $input['email'] ??  NULL; 
        // $contact_name  = $input['contact_name'] ?? NULL; 
        $date = date('Y-m-d H:i:s');
     
    
        if(!$name || !$email){
            return ['status' => 404 , 'msg'=> 'Please add all data'];     
        }



        //  check in both(LR and OLS) if user already exisits 
        
        $sql_ols= "SELECT * FROM tbl_user Where user_email='$email'"; 
        $ols_user = $conn->query($sql_ols); 

        $sql_lr = "SELECT * FROM customer Where customer_email = '$email'"; 
        $lr_user = $conn3->query($sql_lr); 

        if($ols_user->num_rows > 0 || $lr_user->num_rows > 0){
             $data = $ols_user->fetch_assoc(); 
            //  $activate_key = $data['activate_key'] ??  $this->GetActivateKey(); 
             $activate_key =   $this->GetActivateKey(); 
            
             $update = $conn->query("UPDATE  tbl_user set activate_key='$activate_key' Where  user_id = '".$data['user_id']."'"); 

             if($activate_key):
                 $url = "https://ols-test.jog-joinourgame.com/confirm_email.php?key=$activate_key"; 
                  return ['status'=>503 , 'msg'=>'User already exists' ,'url'=>$url];
             else:
                return ['status'=>503 , 'msg'=>'User already exists' ]; 
             endif ; 
        }

        $date = date('Y-m-d H:i:s');
        $sql_lr =  "INSERT INTO customer (customer_name ,customer_email ,enable_ols) VALUES('$name' , '$email' ,1)" ; 
       
        if($conn3->query($sql_lr)){
            $customer_id = $conn3->insert_id;
            $sql_insert2 = "INSERT INTO tbl_customer_contact (customer_id,contact_name,contact_email) 
                               VALUES ('".$customer_id."','".addslashes($name)."','".addslashes($email)."')";
            $conn3->query($sql_insert2); 

           
            $activate_key =  $this->GetActivateKey(); 
            $sql = "INSERT INTO tbl_user (user_email, full_name, customer_id, date_add ,activate_key)
            VALUES ('$email', '$name', $customer_id, '$date' ,'$activate_key')";
            $result  = $conn->query($sql); 
            $user_id = $conn->insert_id ; 
            $url = "https://ols-test.jog-joinourgame.com/confirm_email.php?key=$activate_key"; 


            // $url =  "https://ols-test.jog-joinourgame.com/enable_user_from_lkr.php";
            // $params = ['customer_id' =>$customer_id , 'email'=>base64_encode($email) , 'customer_name'=>base64_encode($name)]; 

            //     // Initialize cURL
            //     $ch = curl_init($url);

            //     // Convert params to POST format
            //     curl_setopt($ch, CURLOPT_POST, true);
            //     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

            //     // Return response instead of printing
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //     // Execute
            //     $response = curl_exec($ch);
            return ['status'=> 200 , 'msg'=>'Signin successfully' , 'Url'=> $url , 'user_id' =>$user_id ];
        }
        else{
             return ['status'=>503 , 'msg'=>'Something went wrong. Can not create user' ] ; 
        }
        
       
     }

     function UserLogin(){

            global $conn ,$conn3;
            $input = json_decode(file_get_contents("php://input"), true);

            $email =  $input['email'] ?? NULL ;
            $password = base64_encode($input['password']) ?? NULL;

            $string_pass = md5(base64_decode($password));

            if(!$email || !$password){
                 return ['status'=>503 , 'msg' => 'Please add all data'];
            }

            $check_user_exists = "SELECT * FROM tbl_user Where user_email = '$email' AND user_password='$string_pass'";

            $data = $conn->query($check_user_exists);

            if($data->num_rows ==0){
                 return ['status'=>404 , 'msg'=> 'Can not found the user'];
            }

            $user    = $data->fetch_assoc();
            $user_id = $user['user_id'] ?? 0;

            require_once __DIR__ . '/JWTHelper.php';
            $access_token = JWTHelper::generate([
                'user_id' => $user_id,
                'email'   => $email,
                'level'   => $user['user_level'] ?? 0,
            ]);

            $refresh_token = $this->IssueRefreshToken($user_id);

            return [
                'status'        => 200,
                'msg'           => 'User loggedin successfully',
                'user_id'       => $user_id,
                'token'         => $access_token,
                'refresh_token' => $refresh_token,
            ];
     }

     function IssueRefreshToken($user_id) {
            global $conn;
            $token      = bin2hex(random_bytes(32));   // 64-char hex
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

            $stmt = $conn->prepare(
                "INSERT INTO tbl_refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)"
            );
            $stmt->bind_param('iss', $user_id, $token, $expires_at);
            $stmt->execute();
            $stmt->close();

            return $token;
     }

     function RefreshAccessToken() {
            global $conn;
            $input         = json_decode(file_get_contents("php://input"), true);
            $refresh_token = $input['refresh_token'] ?? null;

            if (!$refresh_token) {
                return ['status' => 400, 'msg' => 'Refresh token required'];
            }

            $stmt = $conn->prepare(
                "SELECT rt.*, u.user_email, u.user_level
                 FROM tbl_refresh_tokens rt
                 JOIN tbl_user u ON u.user_id = rt.user_id
                 WHERE rt.token = ? AND rt.revoked = 0"
            );
            $stmt->bind_param('s', $refresh_token);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$row) {
                return ['status' => 401, 'msg' => 'Invalid refresh token'];
            }

            if (strtotime($row['expires_at']) < time()) {
                return ['status' => 401, 'msg' => 'Refresh token expired'];
            }

            // Revoke the used token (rotation — one-time use)
            $stmt = $conn->prepare("UPDATE tbl_refresh_tokens SET revoked = 1 WHERE token = ?");
            $stmt->bind_param('s', $refresh_token);
            $stmt->execute();
            $stmt->close();

            require_once __DIR__ . '/JWTHelper.php';
            $new_access_token   = JWTHelper::generate([
                'user_id' => $row['user_id'],
                'email'   => $row['user_email'],
                'level'   => $row['user_level'] ?? 0,
            ]);
            $new_refresh_token  = $this->IssueRefreshToken($row['user_id']);

            return [
                'status'        => 200,
                'token'         => $new_access_token,
                'refresh_token' => $new_refresh_token,
            ];
     }

     function Logout() {
            global $conn;
            $input         = json_decode(file_get_contents("php://input"), true);
            $refresh_token = $input['refresh_token'] ?? null;

            if ($refresh_token) {
                $stmt = $conn->prepare("UPDATE tbl_refresh_tokens SET revoked = 1 WHERE token = ?");
                $stmt->bind_param('s', $refresh_token);
                $stmt->execute();
                $stmt->close();
            }

            return ['status' => 200, 'msg' => 'Logged out successfully'];
     }
 
     function ForgetPassword(){
         global $conn;
         $input = json_decode(file_get_contents("php://input"), true);

         $email = $input['email'] ?? NULL; 
         if(!$email){
             return ['status'=>404 , 'msg'=> 'Please enter email'];
         }

         $sql = "SELECT * FROM tbl_user Where user_email='$email'"; 
         $result = $conn->query($sql); 
         if($result->num_rows==0){
             return  ['status'=>404 , 'msg' =>'Can not find user'];
         }


         $currentURL = (isset($_SERVER['HTTPS']) ? "https" : "http")
              . "://$_SERVER[HTTP_HOST]";
         
        $url = $currentURL.'/request_reset_password.php'; 
        $params = ['email'=>base64_encode($email)]; 
        $ch = curl_init($url); 
        // Convert params to POST format
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // Return response instead of printing
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Execute
        $response = curl_exec($ch);
        $result = json_decode($response , true); 
        if($result['result']=='success'){
            return ['status'=>200 , 'msg'=> 'Please update your password'];
        }else{
             return ['status'=>200 , 'msg'=>'Can not sent mail'];
        }
        
     }
   

     function GetActivateKey(){
            $s_tmp = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $activate_key = "";
            for($i=0;$i<32;$i++){
            $ran_num = rand(0,61);
               $activate_key .= substr($s_tmp,$ran_num,1);
            }
            return $activate_key ; 
     }
 }



