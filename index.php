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
        }else if (isset($_GET['explore']) && $_GET['explore'] != "") {
            $template = $twig->loadTemplate('detail.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("objectId", $_GET['explore']);
            $query->includeKey("refered_user");
            $query->includeKey("User");
            $nicethings = $query->first();

            echo $template->render(array('title' => 'Nicething detail page', 'nicething' => $nicethings));

        }else if (isset($_GET['change_password'])) {
            $template = $twig->loadTemplate('change_password.html');
            echo $template->render(array('title' => 'Change Password', 'user' => $user));
        }else if (isset($_GET['dashboard'])) {
            $template = $twig->loadTemplate('dashboard.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("User", $user);
            $query->includeKey("refered_user");
            $query->limit(100);
            $nicethings = $query->find();

            $userId = $user->getObjectId();
            $originated = originated($user->getObjectId());

            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $user->getObjectId());
            $user = $query->first();

            $query = new ParseQuery("NiceThing");
            $query->includeKey("refered_user");
            $query->descending("createdAt");
            $query->limit(3);
            $lastthreethings = $query->find();

            echo $template->render(array('title' => 'Dashboard', 'user' => $user, 'nav' => 1, 'nicethings' => $nicethings, 'last3' => $lastthreethings,
                'originated' => $originated));

        }else if (isset($_POST['viewTree'])) {

            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $_POST['viewTree']);
            $query->limit(100);
            $user = $query->first();
            $trees = array();
            $treesUser = array();

            for ($i = 0; $i < count($user->get('connected')); $i++) {
                $e = new Event();
                $key = array_search($user->get('connected')[$i],$treesUser);
                if(gettype($key) != "integer"){
                    array_push($treesUser, $user->get('connected')[$i]);
                    $query = new ParseQuery("_User");
                    $query->equalTo("objectId", $user->get('connected')[$i]);
                    $tree = $query->first();

                    $e->id = $tree->getObjectId();
                    $e->username = $tree->get('first');
                    $e->avatar = $tree->get('avatar');
                    $trees[] = $e;
                }
            }
            header('Content-Type: application/json');
            echo json_encode($trees);

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
                $e->name = $nicething->get('refered_user')->get('first');
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
                $e->avatar = $nicething->get('refered_user')->get('avatar');
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

            $chat = 0;
            $query = new ParseQuery("ChatLogs");
            $query->equalTo('speaker', $user);
            $query->descending('createdAt');
            $query->includeKey('Chat');
            $chat = $query->first();

            $chatters = "";
            $lastUserId = "";
            if(count($chat)>0){
                $query = new ParseQuery("Chat");
                $query->equalTo('objectId', $chat->get('Chat')->getObjectId());
                $last = $query->first();

                if ($user->getObjectId() == $last->get('users')[0]) {
                    $lastUserId = $last->get('users')[1];
                }else{
                    $lastUserId = $last->get('users')[0];
                }

                $query = new ParseQuery("_User");
                $query->equalTo('objectId', $lastUserId);
                $lastUserId = $query->first();

                $query = new ParseQuery("ChatLogs");
                $query->equalTo('Chat', $chat->get('Chat'));
                $query->ascending('createdAt');
                $query->includeKey('speaker');
                $chatters = $query->find();
                $chat = $chat->get('Chat')->getObjectId();
            }

            $template = $twig->loadTemplate('chat.html');
            echo $template->render(array('title' => 'Inbox', 'user' => $user, 'nav' => 3,'users' =>$users, 'chatters' =>$chatters, 
                'chat' => $chat, 'last' =>$lastUserId));
        }else if (isset($_GET['explore'])) {
            $template = $twig->loadTemplate('explore.html');

            $query = new ParseQuery("NiceThing");
            $query->limit(1000);
            $nice_things = $query->find();

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
            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $user->getObjectId());
            $query->limit(100);
            $user = $query->first();
            $trees = array();
            $treesUser = array();

            for ($i = 0; $i < count($user->get('connected')); $i++) {
                $e = new Event();
                $key = array_search($user->get('connected')[$i],$treesUser);
                if(gettype($key) != "integer"){
                    array_push($treesUser, $user->get('connected')[$i]);
                    $query = new ParseQuery("_User");
                    $query->equalTo("objectId", $user->get('connected')[$i]);
                    $tree = $query->first();

                    $e->id = $tree->getObjectId();
                    $e->username = $tree->get('first');
                    $e->avatar = $tree->get('avatar');
                    $trees[] = $e;
                }
            }
            echo $template->render(array('title' => 'My tree', 'user' => $user, 'nav' => 7, 'trees' => $trees));

        }else if (isset($_POST['like'])) {
            $query = new ParseQuery("NiceThing");
            $query->equalTo("objectId", $_POST['thing_id']);
            $favorite = $query->first();
            $result = false;
            $likes = array();
            for ($i = 0; $i < count($favorite->get('likes')); $i++) {
                array_push($likes, $favorite->get('likes')[$i]);
            }
            if ($_POST['like'] == 1) {
                $key = -1;
                $key = array_search($_POST['user_id'], $likes);
                if ($key == "") {
                    array_push($likes, $_POST['user_id']);
                    $favorite->setArray('likes',$likes);
                    $favorite->save(true);
                    $result = true;
                }
            }
            else{
                foreach (array_keys($likes, $_POST['user_id']) as $key) {
                    unset($likes[$key]);
                }
                $likes = array_values($likes);
                $favorite->setArray('likes',$likes);
                $favorite->save(true);
                $result = true;
            }

            $events = array();

            $e = new Event();
            $e->fav = count($likes);

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

            $friends = array();

            for ($i = 0; $i < count($currentUser->get('connected')); $i++) {
                $key = -1;
                $key = array_search($currentUser->get('connected')[$i], $friends);
                if(gettype($key) != "integer"){
                    array_push($friends, $currentUser->get('connected')[$i]);
                }
            }

            $events = array();

            for ($i = 0; $i < count($friends); $i++) {
                $query = new ParseQuery("_User");
                $query->equalTo("objectId", $friends[$i]);
                $friend = $query->first();

                $e = new Event();
                $e->id = $friend->getObjectId();
                $e->name = $friend->get('first')." ".$friend->get('last');
                $e->location_name = $friend->get('location_name');
                $e->favorite = $friend->get('favorite');
                $e->likes = $friend->get('likes');
                $e->avatar = $friend->get('avatar');
                $events[] = $e; 
            }
            echo $template->render(array('title' => 'My friends', 'user' => $currentUser, 'nav' => 8, 'friends' => $events));
        }else if (isset($_GET['settings'])) {
            $template = $twig->loadTemplate('settings.html');
            echo $template->render(array('title' => 'My settings', 'user' => $user));
        }else if (isset($_GET['success'])) {
            $template = $twig->loadTemplate('success.html');

            $query = new ParseQuery("NiceThing");
            $query->limit(1000);
            $nice_things = $query->find();

            echo $template->render(array('title' => 'Explore', 'user' => $user, 'nav' => 4, 'nicethings' => $nice_things));
        }else if (isset($_GET['contact'])) {
            $template = $twig->loadTemplate('contact.html');
            echo $template->render(array('title' => 'One nice thing contact us', 'user' => $user));
        }else if (isset($_GET['help'])) {
            $template = $twig->loadTemplate('help.html');
            echo $template->render(array('title' => 'One nice thing help', 'user' => $user));
        }else if (isset($_GET['about'])) {
            $template = $twig->loadTemplate('about.html');
            echo $template->render(array('title' => 'About us', 'user' => $user));
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

            $query = new ParseQuery("_User");
            $query->equalTo("objectId", $user->getObjectId());
            $user = $query->first();

            $query = new ParseQuery("NiceThing");
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

        }else if (isset($_GET['explore']) && $_GET['explore'] != "") {
            $template = $twig->loadTemplate('detail.html');
            $query = new ParseQuery("NiceThing");
            $query->equalTo("objectId", $_GET['explore']);
            $query->includeKey("refered_user");
            $query->includeKey("User");
            $nicethings = $query->first();

            echo $template->render(array('title' => 'Nicething detail page', 'nicething' => $nicethings));

        }else if (isset($_GET['reportnicething'])) {
            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();
            $template = $twig->loadTemplate('reportnicething.html');
            echo $template->render(array('title' => 'Report Nice Thing', 'users' =>$users));
        }else if (isset($_GET['success'])) {
            $template = $twig->loadTemplate('success.html');

            $query = new ParseQuery("NiceThing");
            $query->limit(1000);
            $nice_things = $query->find();

            echo $template->render(array('title' => 'Explore', 'nicethings' => $nice_things));

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
                $e->name = $nicething->get('refered_user')->get('first');
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
                $e->avatar = $nicething->get('refered_user')->get('avatar');
                $events[] = $e; 
            }
            header('Content-Type: application/json');
            echo json_encode($events);

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

    /* ORIGINATED NICE THING */
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
