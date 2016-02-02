<?php
    require 'js/parse/autoload.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseObject;
    use Parse\ParseQuery;

    session_start();

    /* Find user **/

    $query = new ParseQuery("_User");
    $query->equalTo("username",$_POST['username']);
    $user = $query->first();

    if(!$user){
        echo "Error: User please select valid username";
        return;
    }

    $result = false;
    $nice_thing = new ParseObject("NiceThing");
    $nice_thing->set("nice_thing", $_POST['content']);
    $nice_thing->set("location_name", $_POST['location']);
    $nice_thing->set("nice_thing", $_POST['content']);
    $nice_thing->set("whom", $_POST['who']);
    $nice_thing->set("feel", $_POST['feel']);
    $nice_thing->set("message", $_POST['message']);
    $nice_thing->set("feel", $_POST['feel']);
    $nice_thing->set("refered_user", $user);
    $nice_thing->set("privacy", 1);
    $nice_thing->set("status", 0);

    try {
        $nice_thing->save();
        $result = true;
    } catch (ParseException $ex) {  
        // Execute any logic that should take place if the save fails.
        // error is a ParseException object with an error code and message.
        echo 'Error: Failed to create new object, with error message: ' . $ex->getMessage();
        return;
    }

    class Response {};

    if($result){
        $response = new Response();
        $response->success = true;
        $response->message = "Your nice thing is added";
        $response->data = $nice_thing->getObjectId();
        $_SESSION['id'] = $nice_thing->getObjectId();
        echo json_encode($response); 
    }else{
        echo "Error: User please select valid username";
    }
 
?>