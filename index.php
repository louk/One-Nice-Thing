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

    if(isset($_SESSION['user'])){
    }
    else{
        if(isset($_GET['login'])){
            $template = $twig->loadTemplate('login.html');
            echo $template->render(array('title' => 'Login')); 
        }else if(isset($_GET['add'])){
            $template = $twig->loadTemplate('add.html');
            echo $template->render(array('title' => 'Add')); 
        }
        else{
            $template = $twig->loadTemplate('main.html');
            echo $template->render(array('title' => 'Start')); 
        }
        
    }
?>

