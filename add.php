<?php
    require 'js/parse/autoload.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseObject;
    use Parse\ParseQuery;

    $data = $_POST['data'];
    $privacy = $_POST['privacy'];

    /* Find user **/

    $query = new ParseQuery("_User");
    $query->equalTo("username",$data['username']);
    $user = $query->first();

    if(!$user){
        echo "Error: User please select valid username";
        return;
    }

    $result = false;
    $nice_thing = new ParseObject("NiceThing");
    $nice_thing->set("nice_thing", $data['content']);
    $nice_thing->set("location_name", $data['location']);
    $nice_thing->set("nice_thing", $data['content']);
    $nice_thing->set("whom", $data['who']);
    $nice_thing->set("feel", $data['feel']);
    $nice_thing->set("message", $data['message']);
    $nice_thing->set("feel", $data['feel']);
    $nice_thing->set("refered_user", $user);
    $nice_thing->set("privacy", (int)$privacy);
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

    if($result){
        echo $nice_thing->getObjectId();
    }else{
        echo "Error: User please select valid username";
    }
 
?>
     