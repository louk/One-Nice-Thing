
<?php
	require 'js/parse/autoload.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseQuery;

    session_start();

	class Response {};
    
	function add_user_report($report_id, $user){
	    
        $query = new ParseQuery("NiceThing");
        $query->equalTo("objectId",$report_id);
        $nice_thing = $query->first();    
        $nice_thing->set("User", $user);
        $response = new Response();
        try {
            $nice_thing->save();   
            $response->success = true;
            $response->message = "Your nice thing is added";
            $response->data = $nice_thing->getObjectId();
            echo json_encode($response); 
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

	        $query = new ParseQuery("NiceThing");
	        $query->equalTo("objectId",$report_id);
	        $nice_thing = $query->first();    
	        $nice_thing->set("User", $user);

	        try {
	            $nice_thing->save();   
	            $response->success = true;
	            $response->message = "Your nice thing is added";
	            $response->data = $nice_thing->getObjectId();
	            echo json_encode($response); 
	   		} catch (ParseException $ex) {  
	            // Execute any logic that should take place if the save fails.
	            // error is a ParseException object with an error code and message
	            $response->success = false;
	            $response->message = 'Error: Failed to create new object, with error message: ' . $ex->getMessage();
	            echo json_encode($response); 
	        }

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

?>