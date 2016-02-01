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
    

    /*if($_POST['check']=="true" ){
        (string)$uniq = uniqid();
        $name = "Guest-".$uniq;
        $user = new ParseUser();
        $user->set("username", $name);
        $user->set("password", $uniq);

        try {
            $user->signUp();
            $_SESSION['user'] = $user;
            echo 11;
        } catch (ParseException $ex) {
            echo 0;
        }

    }else{*/
    try {
        $user = ParseUser::logIn($_POST['username'], $_POST['password']);
        $user->save();
        $user = ParseUser::getCurrentUser();
        $_SESSION['user'] = $user;
        echo 12;
    } catch (ParseException $error) {
        echo $error;
    } 
   //}

?>