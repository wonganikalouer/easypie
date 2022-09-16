<?php
use EaseRoutes\Routes;
use EaseRoutes\Response;
use EaseRoutes\Settings;

$router         = new Routes();

//Load Your Home Page
$router->get('/home', 'views/home.php');

//Route that returns a view php or html layout in views/test/about.php
//views folder is automatically selected by the framework
$router->get('/about', function ($request) {
    return Response::view("test.about");
});

//print settings, try this code on a hosted server, the results will be different
$router->get('/settings', function ($request) {
    return Settings::get("APP_NAME"); 
});

//Route that returns a json response
$router->get('/json', function ($request) {
    return Response::json(["name"=>"Wongani","age"=>25]);
});


//if all routes were not satisfied, call this 404 page
$router->load404("views/index.php");//force homepage incase this happens