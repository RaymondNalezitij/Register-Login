<?php

require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'App\Controllers\WebpageController@index');
    $r->addRoute('POST', '/', 'App\Controllers\WebpageController@endSession');

    $r->addRoute('GET', '/login', 'App\Controllers\LoginController@index');
    $r->addRoute('POST', '/login', 'App\Controllers\LoginController@validate');

    $r->addRoute('GET', '/register', 'App\Controllers\RegisterController@register');
    $r->addRoute('POST', '/register', 'App\Controllers\RegisterController@store');
});

//Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        print_r("404 Not Found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        print_r("405 Method Not Allowed");
        break;
    case FastRoute\Dispatcher::FOUND:

        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = explode("@", $handler);

        $loader = new \Twig\Loader\FilesystemLoader('app/Views');
        $twig = new \Twig\Environment($loader);

        $twig->addGlobal('errors', $_SESSION['errors'] ?? []);

        $container = new DI\Container();
        $container->set(\App\Repositories\UserRepositoryInterface::class, DI\create(\App\Repositories\UserInfoRepositoryMYSQL::class));

        $response = ($container->get($controller))->$method($vars);

        if ($response instanceof \App\View) {
            unset($_SESSION['errors']);

            $template = $twig->load($response->getTemplatePath());
            echo $template->render($response->getData());
            exit;
        }

        if ($response instanceof \App\Redirect) {
            header('Location: ' . $response->getLocation());
        }

        break;
}
