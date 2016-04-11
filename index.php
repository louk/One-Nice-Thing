<?php

    require_once 'includes/Twig/Autoloader.php';
    require_once "config.php";

    use Parse\ParseObject;
    use Parse\ParseClient;
    use Parse\ParseQuery;
    use Parse\ParseUser;

    session_start();
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

    $Conn_sum = 0; 
    $userId = 0;

    class Event {}
        $lists = array();

    if (isset($_SESSION['user'])) {
        $user = $_SESSION['user'];

        if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            $template = $twig->loadTemplate('main.html');
            //render a template
            echo $template->render(array('title' => 'See you agian'));
        }else if (isset($_GET['dashboard'])) {
            $template = $twig->loadTemplate('dashboard.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->limit(100);
            $nicethings = $query->find();

            $userId = $user->getObjectId();
            $originated = originated($user->getObjectId());

            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->descending("createdAt");
            $query->limit(3);
            $lastthreethings = $query->find();

            echo $template->render(array('title' => 'Dashboard', 'user' => $user, 'nav' => 1, 'nicethings' => $nicethings, 'last3' => $lastthreethings,
                'originated' => $originated));

        }else if (isset($_POST['viewThings'])) {

            $query = new ParseQuery("_User");
            $query->equalTo('objectId', $_POST['viewThings']);
            $userThings = $query->first();

            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $userThings);
            $query->includeKey("refered_user");
            $query->includeKey("User");
            $query->limit(1000);
            $nicethings = $query->find();

            $events = array();

            foreach ($nicethings as $nicething) {
                $e = new Event();
                $e->id = $nicething->getObjectId();
                $e->user = $nicething->get('User')->getObjectId();
                $e->name = $nicething->get('refered_user')->get('username');
                $e->date = $nicething->get('date');
                $e->feel = $nicething->get('feel');
                $e->location_name = $nicething->get('location_name');
                $e->message = $nicething->get('message');
                $e->nice_thing = $nicething->get('nice_thing');
                $e->privacy = $nicething->get('privacy');
                $e->refered_user = $nicething->get('refered_user')->getObjectId();
                $e->status = $nicething->get('status');
                $e->whom = $nicething->get('whom');
                $e->lat = $nicething->get('location')->getLatitude();
                $e->lng = $nicething->get('location')->getLongitude();
                $e->createdAt = $nicething->getCreatedAt();
                $events[] = $e; 
            }
            header('Content-Type: application/json');
            echo json_encode($events);


        }else if (isset($_GET['reportnicething'])) {
            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();
            $template = $twig->loadTemplate('reportnicething.html');
            echo $template->render(array('title' => 'Report Nice Thing', 'users' =>$users, 'user' => $user, 'nav' => 2));

        }else if (isset($_GET['chat'])) {

            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();

            $query = new ParseQuery("_User");
            $query->equalTo('username', 'admin');
            $admin = $query->first();

            $query = new ParseQuery("Chat");
            $query->equalTo('user1', $admin);
            $query->equalTo('user2', $user);
            $chat = $query->first();

            $query = new ParseQuery("ChatLogs");
            $query->equalTo('Chat', $chat);
            $query->includeKey('speaker');
            $chatters = $query->find();

            $template = $twig->loadTemplate('chat.html');
            echo $template->render(array('title' => 'Inbox', 'user' => $user, 'nav' => 3,'users' =>$users, 'chatters' =>$chatters, 'chat' => $chat ));
        }else if (isset($_GET['explore'])) {
            $template = $twig->loadTemplate('explore.html');

            $query = new ParseQuery("NiceThing");
            $query->equalTo('User', $user);
            $nice_things1 = $query->find();

            $query = new ParseQuery("NiceThing");
            $query->equalTo('refered_user', $user);
            $nice_things2 = $query->find();

            $nice_things = array_merge($nice_things1, $nice_things2);

            echo $template->render(array('title' => 'Explore', 'user' => $user, 'nav' => 4, 'nicethings' => $nice_things));
        }else if (isset($_GET['nicethings'])) {
            $query = new ParseQuery("NiceThing");
            $query->equalTo('User', $user);
            $query->includeKey('refered_user');
            $query->includeKey('User');
            $nice_things = $query->find();

            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();

            $template = $twig->loadTemplate('my-things.html');
            echo $template->render(array('title' => 'My nice things', 'user' => $user, 'nav' => 5, 'nices' => $nice_things, 'users' => $users));
        }else if (isset($_GET['mymap'])) {
            $template = $twig->loadTemplate('my-map.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->limit(100);
            $nicethings = $query->find();
            echo $template->render(array('title' => 'My map', 'user' => $user, 'nav' => 6, 'nicethings' => $nicethings));

        }else if (isset($_GET['tree'])) {
            $template = $twig->loadTemplate('my-tree.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->limit(100);
            $trees = $query->find();
            echo $template->render(array('title' => 'My tree', 'user' => $user, 'nav' => 7, 'trees' => $trees));
        }else if (isset($_POST['like'])) {
            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $_POST['user_id']);
            $favorite = $query->first();
            $result = false;
            $likes = array();
            for ($i = 0; $i < count($favorite->get('likes')); $i++) {
                array_push($likes, $favorite->get('likes')[$i]);
            }
            if ($_POST['like'] == 1) {
                array_push($likes, $_POST['friend_id']);
                $favorite->setArray('likes',$likes);
                $favorite->save(true);
                $query = new ParseQuery("_User");
                $query->equalTo("objectId", $_POST['friend_id']);
                $friendfav = $query->first();
                $friendfav->set('favorite',$friendfav->get('favorite')+1);
                $friendfav->save(true);
                $result = true;
            }
            else{
                foreach (array_keys($likes, $_POST['friend_id']) as $key) {
                    unset($likes[$key]);
                }
                $likes = array_values($likes);
                $favorite->setArray('likes',$likes);
                $favorite->save(true);
                $query = new ParseQuery("_User");
                $query->equalTo("objectId", $_POST['friend_id']);
                $friendfav = $query->first();
                $friendfav->set('favorite',$friendfav->get('favorite')-1);
                $friendfav->save(true);
                $result = true;
                $result = true;
            }

            $events = array();

            $e = new Event();
            $e->fav = $friendfav->get('favorite');

            if ($result) {
                $e->msg = 1;
                $events[] = $e; 
                header('Content-Type: application/json');
                echo json_encode($events);
            }
            else{
                $e->msg = 1;
                $events[] = $e; 
                header('Content-Type: application/json');
                echo json_encode($events);
            }

        }else if (isset($_GET['friends'])) {
            $template = $twig->loadTemplate('friends.html');
            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $user->getObjectId());
            $query->limit(1000);
            $currentUser = $query->first();

            $events = array();

            for ($i = 0; $i < count($currentUser->get('connected')); $i++) {
                $query = new ParseQuery("_User");
                $query->equalTo("objectId", $currentUser->get('connected')[$i]);
                $friends = $query->first();

                $e = new Event();
                $e->id = $friends->getObjectId();
                $e->name = $friends->get('first')." ".$friends->get('last');
                $e->location_name = $friends->get('location_name');
                $e->favorite = $friends->get('favorite');
                $e->likes = $friends->get('likes');
                $events[] = $e; 
            }
            echo $template->render(array('title' => 'My friends', 'user' => $currentUser, 'nav' => 8, 'friends' => $events));
        }else if (isset($_GET['settings'])) {
            $template = $twig->loadTemplate('settings.html');
            echo $template->render(array('title' => 'My settings', 'user' => $user));
        }else if (isset($_GET['contact'])) {
            $template = $twig->loadTemplate('contact.html');
            echo $template->render(array('title' => 'One nice thing contact us', 'user' => $user));
        }else if (isset($_GET['help'])) {
            $template = $twig->loadTemplate('help.html');
            echo $template->render(array('title' => 'One nice thing help', 'user' => $user));
        }else if (isset($_GET['about'])) {
            $template = $twig->loadTemplate('about.html');
            echo $template->render(array('title' => 'About us', 'user' => $user));
        }else if (isset($_GET['success'])) {
            $template = $twig->loadTemplate('success.html');
            echo $template->render(array('title' => 'Success stories'));
        }else if (isset($_GET['help'])) {
            $template = $twig->loadTemplate('help.html');
            echo $template->render(array('title' => 'Help'));
        }else if(isset($_GET['about'])){
            $template = $twig->loadTemplate('about.html');
            echo $template->render(array('title' => 'About us')); 
        }else{
            $template = $twig->loadTemplate('dashboard.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->limit(100);
            $nicethings = $query->find();

            $userId = $user->getObjectId();
            $originated = originated($user->getObjectId());

            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->descending("createdAt");
            $query->limit(3);
            $lastthreethings = $query->find();
            echo $template->render(array('title' => 'Dashboard', 'user' => $user, 'nav' => 1, 'nicethings' => $nicethings, 'last3' => $lastthreethings,
                'originated' => $originated));
        }
    } else {
        if (isset($_GET['login'])) {
            $template = $twig->loadTemplate('login.html');
            echo $template->render(array('title' => 'Login'));
        }else if (isset($_GET['reportnicething'])) {
            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();
            $template = $twig->loadTemplate('reportnicething.html');
            echo $template->render(array('title' => 'Report Nice Thing', 'users' =>$users));
        }else if (isset($_GET['success'])) {
            $template = $twig->loadTemplate('success.html');
            echo $template->render(array('title' => 'Success stories'));
        }else if (isset($_GET['help'])) {
            $template = $twig->loadTemplate('help.html');
            echo $template->render(array('title' => 'Help'));
        }else if(isset($_GET['choose'])){
            $template = $twig->loadTemplate('choose.html');
            echo $template->render(array('title' => 'Choose user type')); 
        }
        else if (isset($_GET['contact'])) {
            $template = $twig->loadTemplate('contact.html');
            echo $template->render(array('title' => 'One nice thing contact us'));
        }
        else if(isset($_GET['about'])){
            $template = $twig->loadTemplate('about.html');
            echo $template->render(array('title' => 'About us')); 
        }
        else {
            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();
            $template = $twig->loadTemplate('main.html');
            //$template = $twig->loadTemplate('my-things.html');
            echo $template->render(array('title' => 'Start', 'users' => $users));
        }
    }

    function originated($id){

        global $Conn_sum, $userId, $lists;
        $key = -1;
        $key = array_search($id, $lists);

        if ($key == null) {

            array_push($lists,$id);
            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $id);
            $connect = $query->first();

            if(count($connect->get('connected')) == 0){
                return $Conn_sum;
            }
            else{
                for ($i = 0; $i < count($connect->get('connected')); $i++) {
                    if ($connect->get('connected')[$i] != $userId && $connect->get('connected')[$i] != $id) {
                        $Conn_sum++;
                        originated($connect->get('connected')[$i]);
                    }
                }
                return $Conn_sum;
            }
        }
    }
?>

