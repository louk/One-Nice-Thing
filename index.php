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

    if (isset($_SESSION['user'])) {
        
        $user = $_SESSION['user'];

        if (isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            $template = $twig->loadTemplate('main.html');
            //render a template
            echo $template->render(array('title' => 'See you agian'));
        }if (isset($_GET['dashboard'])) {
            $template = $twig->loadTemplate('dashboard.html');
            echo $template->render(array('title' => 'Dashboard', 'user' => $user, 'nav' => 1));
        }if (isset($_GET['reportnicething'])) {
            $query = new ParseQuery("_User");
            $query->equalTo('status', 1);
            $users = $query->find();
            $template = $twig->loadTemplate('reportnicething.html');
            echo $template->render(array('title' => 'Report Nice Thing', 'users' =>$users, 'user' => $user, 'nav' => 2));
        }if (isset($_GET['chat'])) {
            $template = $twig->loadTemplate('chat.html');
            echo $template->render(array('title' => 'Inbox', 'user' => $user, 'nav' => 3));
        }if (isset($_GET['explore'])) {
            $template = $twig->loadTemplate('explore.html');
            echo $template->render(array('title' => 'Explore', 'user' => $user, 'nav' => 4));
        }if (isset($_GET['nicethings'])) {
            $template = $twig->loadTemplate('my-things.html');
            echo $template->render(array('title' => 'My nice things', 'user' => $user, 'nav' => 5));
        }if (isset($_GET['mymap'])) {
            $template = $twig->loadTemplate('my-map.html');
            echo $template->render(array('title' => 'My map', 'user' => $user, 'nav' => 6));
        }if (isset($_GET['tree'])) {
            $template = $twig->loadTemplate('my-tree.html');
            echo $template->render(array('title' => 'My tree', 'user' => $user, 'nav' => 7));
        }if (isset($_GET['friends'])) {
            $template = $twig->loadTemplate('friends.html');
            echo $template->render(array('title' => 'My friends', 'user' => $user, 'nav' => 8));
        }if (isset($_GET['settings'])) {
            $template = $twig->loadTemplate('settings.html');
            echo $template->render(array('title' => 'My settings', 'user' => $user));
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
?>

