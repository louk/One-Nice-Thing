<?php

    require 'js/parse/autoload.php';
    require_once "common.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;

    $app_id = 'rVPT2Mws2ylIGYxH7pkxKsX0z0ORWDOoJebHe95f';
    $rest_key = 'ULNpwIX1AfnGHEP0cRX6brWDVTjyzeLJnQCYx5uZ';
    $master_key = 'Utp1QsroqE73YyXN42IgLubUhKe97XKqj5ciJ8iA';
    ParseClient::initialize( $app_id, $rest_key, $master_key );
    $storage = new ParseSessionStorage();
    ParseClient::setStorage($storage);

    echo user_login($_POST['username'], $_POST['password']);

?>
