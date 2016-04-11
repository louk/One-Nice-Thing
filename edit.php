<?php

    require 'js/parse/autoload.php';
    require_once 'includes/Twig/Autoloader.php';
    require_once "config.php";
    use Parse\ParseException;
    use Parse\ParseUser;
    use Parse\ParseSessionStorage;
    use Parse\ParseClient;
    use Parse\ParseObject;
    use Parse\ParseQuery;
    use Parse\ParseGeoPoint;


    //register autoloader
    Twig_Autoloader::register();
    //loader for template files
    $loader = new Twig_Loader_Filesystem('templates');
    //twig instance
    $twig = new Twig_Environment($loader, array(
        'cache' => 'cache',
    ));
    //load template file
    $twig->setCache(false);
    session_start();

    /* Find user **/

    if (isset($_POST['update'])) {
        $query = new ParseQuery("_User");
        $query->equalTo("username",$_POST['username']);
        $user = $query->first();

        if(!$user){
            echo "Error: User please select valid username";
            return;
        }

        $result = false;
        $query = new ParseQuery("NiceThing");
        $query->equalTo("objectId",$_POST['update']);
        $nice_thing = $query->first();

        $nice_thing->set("nice_thing", $_POST['content']);
        $nice_thing->set("location_name", $_POST['location']);
        $point = new ParseGeoPoint(floatval($_POST['lat']), floatval($_POST['lng']));
        $nice_thing->set("location", $point);
        $nice_thing->set("nice_thing", $_POST['content']);
        $nice_thing->set("whom", $_POST['who']);
        $nice_thing->set("feel", $_POST['feel']);
        $nice_thing->set("message", $_POST['message']);
        $nice_thing->set("feel", $_POST['feel']);
        $nice_thing->set("refered_user", $user);
        $nice_thing->set("User", $_SESSION['user']);
        $nice_thing->set("privacy", 1);
        $nice_thing->set("status", 0);

        $currentUser = $_SESSION['user'];
        $query = ParseUser::query();
        $userAgain = $query->get($currentUser->getObjectId());

        $connected = array();
        for ($i = 0; $i < count($userAgain->get('connected')); $i++) {
            array_push($connected, $userAgain->get('connected')[$i]);
        }
        $key = -1;
        $key = array_search($user->getObjectId(), $connected);
        if ($key == -1) {
            array_push($connected, $user->getObjectId());
            $userAgain->setArray("connected", $connected);
        }
        try {
            $userAgain->save(true);
            $nice_thing->save();
            $result = true;
        } catch (ParseException $ex) {  
            // Execute any logic that should take place if the save fails.
            // error is a ParseException object with an error code and message.
            echo 'Error: Failed to create new object, with error message: ' . $ex->getMessage();
            return;
        }

        class Response {};

        if($result){
            $query = new ParseQuery("NiceThing");
            $query->equalTo('User', $currentUser);
            $query->includeKey('refered_user');
            $query->includeKey('User');
            $nice_things = $query->find();

            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();

            $template = $twig->loadTemplate('my-things.html');
            echo $template->render(array('title' => 'My nice things', 'user' => $user, 'nav' => 5, 'nices' => $nice_things, 'users' => $users));
        }else{
            echo "Error: User please select valid username";
        }
    }

?>

