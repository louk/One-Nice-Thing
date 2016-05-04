<?php

    require 'js/parse/autoload.php';
    require_once "config.php";
    require_once "common.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseQuery;


    $func = $_POST['check'];

    if($func == 'guest'){

        (string)$uniq = uniqid();
        $name = "Guest-".$uniq;
        $user = new ParseUser();
        $user->set("username", $name);
        $user->set("password", $uniq);
        $user->setArray("connected", []);
        $user->set("status", 0);

        try{
            $user->signUp();
            $_SESSION['user'] = $user;
            $_SESSION['notification'] = true;

            $query = new ParseQuery("_Session");
            $query->equalTo("user", $user);
            $query->includeKey("user");
            $query->descending("createdAt");
            $query->limit(1);

            $new = $query->find(true);

            $_SESSION['last_date'] = date_format($new[0]->getCreatedAt(), 'Y-m-d\TH:i:s.u\Z');

            add_user_report($_SESSION['id'],$user);

        } catch (ParseException $ex) {
            echo $ex;
        }
    }

    if($func == 'login'){
        echo user_login_report($_POST['username'], $_POST['password'], $_SESSION['id']);
    }

    if($func == 'register'){
        echo user_register_report($_POST['first'], $_POST['last'], $_POST['password'], $_POST['email'], $_SESSION['id']);
    }

?>
