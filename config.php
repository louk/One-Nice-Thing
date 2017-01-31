<?php
require 'js/parse/autoload.php';
use Parse\ParseClient as ParseClient;
use Parse\ParseObject as ParseObject;

	
    $app_id = 'rVPT2Mws2ylIGYxH7pkxKsX0z0ORWDOoJebHe95f';
    $rest_key = 'ULNpwIX1AfnGHEP0cRX6brWDVTjyzeLJnQCYx5uZ';
    $master_key = 'Utp1QsroqE73YyXN42IgLubUhKe97XKqj5ciJ8iA';
    ParseClient::initialize( 'rVPT2Mws2ylIGYxH7pkxKsX0z0ORWDOoJebHe95f', 'ULNpwIX1AfnGHEP0cRX6brWDVTjyzeLJnQCYx5uZ', 'Utp1QsroqE73YyXN42IgLubUhKe97XKqj5ciJ8iA' );
    ParseClient::setServerURL('https://parseapi.back4app.com','/');

	$testObject = ParseObject::create("TestObject");
	$testObject->set("foo", "bar");
	$testObject->save();

?>
