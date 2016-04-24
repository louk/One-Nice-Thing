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
    
    if (isset($_POST['name'])) {
        $message = "Name: ".$_POST['name']."<br/>Email: ".$_POST['email']."<br/>Message: ".$_POST['message'];
        sendmail("khangai@alcehmist.mn", $message);
        sendmail("travis@serenogroup.com", $message);
    }
?>
