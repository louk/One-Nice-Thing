
<?php
	require 'js/parse/autoload.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseQuery;
    use Parse\ParseObject;

    session_start();

	class Response {};
    
    function user_to_user($report_id){
        
        $query = new ParseQuery("NiceThing");
        $query->equalTo("objectId",$report_id);
        $query->includeKey('User');
        $query->includeKey('refered_user');
        $nice_thing = $query->first();  
        
        $ref_user = $nice_thing -> get('refered_user');
        $con_user = $nice_thing -> get('User');

        $ref_user_id = $ref_user->getObjectId();

        $connected =  $con_user->get("connected");

        array_push($connected,$ref_user_id);

        $con_user->setArray("connected",$connected);
        $response = new Response();
        try {
            $con_user->save();
            $response->success = true;
            $response->message = "Your nice thing is added";
            $response->data = $nice_thing->getObjectId();
            echo json_encode($response); 
        } catch (ParseException $ex) {  
            // Execute any logic that should take place if the save fails.
            // error is a ParseException object with an error code and message.
            echo 'Error: Failed to create new object, with error message: ' . $ex->getMessage();
            return;
        }
    }


	function add_user_report($report_id, $user){
	    
        $query = new ParseQuery("NiceThing");
        $query->equalTo("objectId",$report_id);
        $nice_thing = $query->first();    
        $nice_thing->set("User", $user);
        $response = new Response();
        try {
            $nice_thing->save();   
            echo user_to_user($report_id);

   		} catch (ParseException $ex) {  
            // Execute any logic that should take place if the save fails.
            // error is a ParseException object with an error code and message
            $response->success = false;
            $response->message = 'Error: Failed to create new object, with error message: ' . $ex->getMessage();
            echo $response;
		}
	}

	function user_login($username, $pass){
	    
	    $response = new Response();

	    try {
	        $user = ParseUser::logIn($username, $pass);
	        $user->save();
	        $user = ParseUser::getCurrentUser();
	        $_SESSION['user'] = $user;

            $response->success = true;
            $response->message = "Logged in";
            $response->data = $user->getObjectId();
            echo json_encode($response);

	    } catch (ParseException $error) {
	        $response->success = false;
            $response->message = 'Error: Failed to login: ' . $error;
            echo json_encode($response); 
	    } 
	}

	function user_register( $first, $last, $pass, $email ){
	    
	    $response = new Response();

	    $name = $first."_".$last;
        $user = new ParseUser();
        $user->set("username", strtolower($name));
        $user->set("email", $email );
        $user->set("password", $pass);

        try {
            $user->signUp();
            $_SESSION['user'] = $user;
            
            $response->success = true;
            $response->message = "Signed";
            $response->data = $user->getObjectId();
            echo json_encode($response); 
        } catch (ParseException $ex) {
            $response->success = false;
            $response->message = 'Error: Failed to register: ' . $error;
            echo json_encode($response); 
        } 
	}

	function user_login_report($username, $pass, $report_id){
	    
	    $response = new Response();

	    try {
	        $user = ParseUser::logIn($username, $pass);
	        $user->save();
	        $user = ParseUser::getCurrentUser();
	        $_SESSION['user'] = $user;
            echo add_user_report($report_id, $user);
	    } catch (ParseException $error) {
	        $response->success = false;
            $response->message = 'Error: Failed to login: ' . $error;
            echo json_encode($response); 
	    } 
	}

	function user_register_report($first, $last, $pass, $email, $report_id){
	    
	   	$response = new Response();

	    $name = $first."_".$last;
        $user = new ParseUser();
        $user->set("username", strtolower($name));
        $user->set("email", $email );
        $user->set("password", $pass);

        try {
            $user->signUp();
            $_SESSION['user'] = $user;
           	echo add_user_report($report_id, $user);
        } catch (ParseException $ex) {
            $response->success = false;
            $response->message = 'Error: Failed to register: ' . $error;
            echo json_encode($response); 
        }  
	}

    function mail_box($to, $header, $content, $type){
        
        $mail = new ParseObject("Mails");
        $mail->set("to", $to);
        $mail->set("header", $header);
        $mail->set("content", $content);
        $mail->set("type", $type);
        $mail->set("status", 0); 
        try {
            $mail->save();
        } catch (ParseException $ex) {  
            // Execute any logic that should take place if the save fails.
            // error is a ParseException object with an error code and message.
            echo 'Error: Failed to create new object, with error message: ' . $ex->getMessage();
            return;
        }
    }

?>