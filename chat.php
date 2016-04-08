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
    $user = $_SESSION['user'];

    if($func == 'get_chatters'){

        $query = new ParseQuery("_User");
        $query->equalTo('objectId', $_POST['user']);
        $host = $query->first();
    
        $query = new ParseQuery("Chat");
        $query->equalTo('user1', $host);
        $query->equalTo('user2', $user);
        $chat = $query->first();

        if($chat){
            $response = new Response();
            $query = new ParseQuery("ChatLogs");
            $query->equalTo('Chat', $chat);
            $query->includeKey('speaker');

            $chatters = $query->find();
            $response->success = true;
            $response->type = 1;
            $response->data = $chatters;
            $response->chat = $chat->getObjectId();
            $response->user = $user->getObjectId();
            echo json_encode($response);

        }else{

            $chat = new ParseObject("Chat");
            $chat->set("user1", $host);
            $chat->set("user2", $user);

            try {
                $chat->save();
                $response = new Response();
                $response->success = true;
                $response->type = 2;
                $response->chat = $chat->getObjectId();
                $response->user = $user->getObjectId();
                echo json_encode($response);

            } catch (ParseException $ex) {
                $response->success = false;
                $response->message = 'Error: Failed to chat: ' . $ex;
                echo json_encode($response); 
            } 

        }
    }
    
?>
     
