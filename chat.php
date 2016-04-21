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
        $userArray = $query->find();
        $chatId = '';

        for ($i = 0; $i < count($userArray); $i++) {
            $arr = $userArray[$i]->get('users');
            $ustr = $user->getObjectId().$_POST['user'];
            $ustr1 = $_POST['user'].$user->getObjectId();
            if (gettype(strpos(implode("",$arr), $ustr)) == "integer" || gettype(strpos(implode("",$arr), $ustr1)) == "integer") {
                $chatId = $userArray[$i]->getObjectId();
            }

        }
        $query = new ParseQuery("Chat");
        $query->equalTo('objectId', $chatId);
        $chat = $query->first();

        if($chat){
            $response = new Response();
            $query = new ParseQuery("ChatLogs");
            $query->equalTo('Chat', $chat);
            $query->includeKey('speaker');
            $query->ascending('createdAt');

            $chatters = $query->find();
            $msgArray = array();
            $spkArray = array();
            $dateArray = array();
            $imgArray = array();
            for ($i = 0; $i < count($chatters); $i++) {
                array_push($msgArray, $chatters[$i]->get('message'));
                array_push($spkArray, $chatters[$i]->get('speaker')->get('first'));
                array_push($imgArray, $chatters[$i]->get('speaker')->get('avatar'));
                array_push($dateArray, date_format($chatters[$i]->getCreatedAt(), 'F jS \a\t g:ia'));
            }
            $response->success = true;
            $response->type = 1;
            $response->data = $msgArray;
            $response->chat = $chat->getObjectId();
            $response->user = $user->getObjectId();
            $response->speakers = $spkArray;
            $response->avatar = $imgArray;
            $response->date = $dateArray;
            echo json_encode($response);

        }else{

            $chat = new ParseObject("Chat");
            $users = array();
            array_push($users, $host->getObjectId());
            array_push($users, $user->getObjectId());
            $chat->setArray('users', $users);

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

    if($func == 'get_user_all'){

        $query = new ParseQuery("_User");
        $query->equalTo('status', 1);
        $users = $query->find();

        $ids = array();
        $names = array();
        $avatars = array();

        foreach ($users as $user) {
            array_push($ids, $user->getObjectId());
            array_push($names, $user->get("first"));
            array_push($avatars, $user->get("avatar"));
        }

        $response = new Response();
        $response->id = $ids;
        $response->name = $names;
        $response->avatar = $avatars;
        echo json_encode($response);

    }

    if($func == 'get_user_direct'){

        $query = new ParseQuery("_User");
        $query->equalTo('objectId', $_POST['user']);
        $users = $query->first();

        $ids = array();
        $names = array();
        $avatars = array();

        foreach ($users->get('connected') as $user) {
            $query = new ParseQuery("_User");
            $query->equalTo('objectId', $user);
            $conn = $query->first();
            array_push($ids, $conn->getObjectId());
            array_push($names, $conn->get('first'));
            array_push($avatars, $conn->get('avatar'));
        }

        $response = new Response();
        $response->id = $ids;
        $response->name = $names;
        $response->avatar = $avatars;
        echo json_encode($response);

    }
    
?>
     
