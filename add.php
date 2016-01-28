<?php
    require 'js/parse/autoload.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseUser;

    $data = $_POST['data'];

    if(isset($data['anom'])){

    }else{
        $email = $_POST['email'];
        $length = 8;
        $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

        $user = new ParseUser();
        $user->set("username", $email);
        $user->set("email", $email);
        $user->set("password", $pass);
        $user->set("status", 0);

        try {
            $user->signUp();

            $nice = new ParseObject("NiceThing");
            $nice->set("user", $user);
            $nice->set("nice_thing", $_POST['nice']);
            $nice->set("date", $_POST['date']);
            
            $query = new ParseQuery("_User");
            $query->equalTo('username', $_POST['friend']);
            $users = $query->find();

            $result =true;
        } catch (ParseException $ex) {
            echo "Error: " . $ex->getCode() . " " . $ex->getMessage();
        }




    }
    
?>
     