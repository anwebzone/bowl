<?php
require __DIR__.'/config_with_app.php';

$di  = new \Anax\DI\CDIFactoryDefault();
$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_mysql.php');
    $db->connect();
    return $db;
});

$di->set('AccountController', function() use ($di) {
    $controller = new \Anax\Users\AccountController();
    $controller->setDI($di);
    return $controller;
});

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('TagsController', function() use ($di) {
    $controller = new \Anax\Tags\TagsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('QuestionsController', function() use ($di) {
    $controller = new \Anax\Ask\QuestionsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('HomeController', function() use ($di) {
    $controller = new \Anax\Home\HomeController();
    $controller->setDI($di);
    return $controller;
});

// Settings
$app = new \Anax\MVC\CApplicationBasic($di);
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);
$app->theme->configure(ANAX_APP_PATH . 'config/theme_bowling.php');
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar.php');

$flash_msg = $app->session->get('flash_msg');

if(!empty($flash_msg)){
	  $app->views->add('default/msg', [
        'content' => $flash_msg,
    ], 'flash');
		
		$app->session->set('flash_msg', null);
}

// Home route
$app->router->add('', function() use ($app){

	  $app->dispatcher->forward([
        'controller' => 'home',
        'action'     => 'index',
    ]);
		
});

// About route
$app->router->add('about', function() use ($app){

		$app->theme->setTitle("Om sidan");
 
    $content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');
		
	  $app->views->add('bowl/page', [
				'title'   => 'Om bowl',
        'content' => $content,
    ], 'main-large');

});

$app->router->handle();
$app->theme->render();