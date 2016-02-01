<?php
    require 'js/parse/autoload.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;

    $func = $_POST['check'];

    if($func == 'register'){

        $name = $_POST['first']."_".$_POST['last'];
        $user = new ParseUser();
        $user->set("username", strtolower($name);
        $user->set("email", $_POST['email']);
        $user->set("password", $_POST['pass']);
        try {
            $user->signUp();
            $_SESSION['user'] = $user;
            echo 11;
        } catch (ParseException $ex) {
            echo $ex;
        }

    }
    
    if($func == 'forgot'){
        
    }
?>
     