<?php
    require 'js/parse/autoload.php';
    require_once "config.php";
    require_once "common.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;

    $func = $_POST['check'];

    if($func == 'register'){
        echo user_register($_POST['first'], $_POST['last'],$_POST['pass'],$_POST['email']);
    }
    
?>
     