<?php

    require 'js/parse/autoload.php';
    require_once "config.php";
    require_once "common.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseQuery;
    use Parse\ParseObject;

    $func = $_POST['check'];

    if($func == 'register'){
        echo user_register($_POST['first'], $_POST['last'],$_POST['password'],$_POST['email'], $_POST['location'], $_POST['lat'], $_POST['lng']);
        mail_box($_POST['email'],'Register', 'Thank  you for registering', 'register');
    }

    if($func == 'guest'){

        (string)$uniq = uniqid();
        $name = "Guest-".$uniq;
        $user = new ParseUser();
        $user->set("username", $name);
        $user->set("password", $uniq);
        $user->setArray("connected", []);
        $user->set("status", 0);

        $response = new Response();

        try{
            $user->signUp();
            user_register_create_chat($user);
            $_SESSION['user'] = $user;
            $response->success = true;
            $response->message = "Logged in";
            $response->data = $user->getObjectId();
            echo json_encode($response);
        } catch (ParseException $ex) {
            $response->success = false;
            $response->message = 'Error: Failed to login: ' . $ex;
            echo json_encode($response);
        }
    }

    if($func == 'forgot'){
        (string)$pass = uniqid();
        $content = 'Here is the new password: ' . $pass;
        echo user_forgot($_POST['email'],$pass);
        mail_box($_POST['email'],'New password', $content, 'forgot');
    }

    if($func == 'message'){

        $response = new Response();

        $query = new ParseQuery("Chat");
        $query->equalTo('objectId', $_POST['chat']);
        $chat = $query->first();

        $query = new ParseQuery("_User");
        $query->equalTo('objectId', $_POST['speaker']);
        $user = $query->first();


        $chatter = new ParseObject("ChatLogs");
        $chatter->set("speaker", $user);
        $chatter->set("Chat", $chat);
        $chatter->set("message", $_POST['message']);

        try {
            $chatter->save();
            $response->success = true;
            $response->message = $_POST['message'];
            echo json_encode($response); 
        } catch (ParseException $ex) {
            $response->success = false;
            $response->message = 'Error: Failed to chatter: ' . $ex;
            echo json_encode($response); 
        }

    }

?>
