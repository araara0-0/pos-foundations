<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Pages::index');

$routes->get('/about', 'Pages::about');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attempt');
$routes->post('/logout', 'Auth::logout');

$routes->group('customers', ['filter' => 'auth'], static function ($routes) {
    $routes->get('', 'Customers::index');
    $routes->get('new', 'Customers::new');
    $routes->post('', 'Customers::create');
    $routes->get('(:num)/edit', 'Customers::edit/$1');
    $routes->post('(:num)', 'Customers::update/$1');
});

$routes->group('users', ['filter' => 'auth'], static function ($routes) {
    $routes->get('', 'Users::index');
    $routes->get('new', 'Users::new');
    $routes->post('', 'Users::create');
    $routes->get('(:num)/edit', 'Users::edit/$1');
    $routes->post('(:num)', 'Users::update/$1');
});
