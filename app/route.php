<?php
/**
 * Created by PhpStorm.
 * User: liubingxia
 * Date: 15-8-21
 * Time: 下午6:45
 */
use Scaffold\Routing\Router;


Router::get("test",  function(){
    echo "Hello World";
});

Router::get("hello/:id", function($id){
    echo "Hello to $id";
});

Router::get("list/:id",  "MainController@getList");

Router::get("session", function(){
    $session=new \Scaffold\Session\Session();
    $_SESSION['aa']=12;
    echo $_SESSION['aa'],"\n";
    echo session_id();
});