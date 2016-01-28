<?php
    require 'js/parse/autoload.php';
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;

    session_start();

    $app_id = 'rVPT2Mws2ylIGYxH7pkxKsX0z0ORWDOoJebHe95f';
    $rest_key = 'ULNpwIX1AfnGHEP0cRX6brWDVTjyzeLJnQCYx5uZ';
    $master_key = 'Utp1QsroqE73YyXN42IgLubUhKe97XKqj5ciJ8iA';
    ParseClient::initialize( $app_id, $rest_key, $master_key );
    $storage = new ParseSessionStorage();
    ParseClient::setStorage($storage);
    
    $result =false;

    try {
        $user = ParseUser::logIn($_POST['email'], $_POST['password']);
        $user->save();
        $result = true;
    } catch (ParseException $error) {
        echo $error;
    }
    
    $user = ParseUser::getCurrentUser();
    $_SESSION['user'] = $user;

    if($result){
        header('location: index.php?dashboard');  
    }else{
        header('location: index.php?login');
    }
    
?>