<?php
    require_once ('../include/config.php');
    require_once ('../include/User.php');
    
    if(isset($_POST['tag']) && $_POST['tag'] != ''){
        
        $tag = $_POST['tag'];
    
        $user = new User();
        
        $response = array("tag" => $tag, "success" => 0, "error" => 0);
        
        if($tag == 'login'){
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $user_request = $user->authenticate_user($email, $password);
            if($user_request != false){ //user aunthentication
                $response['success'] = 1;
                $response['uid'] = $user_request['user_id'];
                $response['user']['user_id'] = $user_request['user_id'];
                $response['user']['email'] = $user_request['user_email'];
                $response['user']['user_type'] = $user_request['user_type'];
                $response['user']['created_at'] = $user_request['created_at'];
                
                echo json_encode($response);
            }else{
                $response['error'] = 1;
                $response['error_msg'] = "Incorrect email/password";
                
                echo json_encode($response);
            }
        }elseif($tag == 'register'){ //User Registration
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            if($user->is_user_registered($email)){
                $response['error'] = 2;
                $response['error_msg'] = "An account with that email has already been created";         
                echo json_encode($response);
            }else{
                $new_user = $user->create_user($email, $password, 1);
                if($new_user){
                    $response["success"] = 1;
                    $response['uid'] = $new_user['user_id'];
                    $response['user']['email'] = $new_user['email'];
                    echo json_encode($response);
                }else{
                    $response['error'] = 1;
                    $response['error_msg'] = "Problem creating new account";

                    echo json_encode($response);                    
                }
            }
        }else{
            echo "Invalid Request!!!";
        }
    } else {
        echo "Access Denied!!!";
    }

?>
