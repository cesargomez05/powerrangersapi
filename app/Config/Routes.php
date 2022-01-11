<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->post('oauth2', 'Home::getOAuth2');
$routes->post('jwt', 'Home::getJwt');
$routes->post('jwtuser', 'Home::getJwtUser');

// Actores
$routes->group('actors', ['filter' => 'auth:actor'], function ($routes) {
	$routes->get('', 'Actor::index');
	$routes->get('(:num)', 'Actor::show/$1');
	$routes->post('', 'Actor::create');
	$routes->put('(:num)', 'Actor::update/$1');
	$routes->patch('(:num)', 'Actor::update/$1');
	$routes->delete('(:num)', 'Actor::delete/$1');
});

// Eras
$routes->group('ages', ['filter' => 'auth:age'], function ($routes) {
	$routes->get('', 'Age::index');
	$routes->get('(:num)', 'Age::show/$1');
	$routes->post('', 'Age::create');
	$routes->put('(:num)', 'Age::update/$1');
	$routes->patch('(:num)', 'Age::update/$1');
	$routes->delete('(:num)', 'Age::delete/$1');
});

// Personajes
$routes->group('characters', ['filter' => 'auth:character'], function ($routes) {
	$routes->get('', 'Character::index');
	$routes->get('(:num)', 'Character::show/$1');
	$routes->post('', 'Character::create');
	$routes->put('(:num)', 'Character::update/$1');
	$routes->patch('(:num)', 'Character::update/$1');
	$routes->delete('(:num)', 'Character::delete/$1');
});

// Rangers
$routes->group('rangers', ['filter' => 'auth:ranger'], function ($routes) {
	$routes->get('', 'Ranger::index');
	$routes->get('(:num)', 'Ranger::show/$1');
	$routes->post('', 'Ranger::create');
	$routes->put('(:num)', 'Ranger::update/$1');
	$routes->patch('(:num)', 'Ranger::update/$1');
	$routes->delete('(:num)', 'Ranger::delete/$1');
});

// Series
$routes->group('series', ['filter' => 'auth:serie'], function ($routes) {
	$routes->get('', 'Serie::index');
	$routes->get('(:num)', 'Serie::show/$1');
	$routes->post('', 'Serie::create');
	$routes->put('(:num)', 'Serie::update/$1');
	$routes->patch('(:num)', 'Serie::update/$1');
	$routes->delete('(:num)', 'Serie::delete/$1');
});

// Temporadas
$routes->group('seasons', ['filter' => 'auth:season'], function ($routes) {
	$filter = ['filter' => 'serie_filter'];

	$routes->get('(:num)', 'Season::index/$1', $filter);
	$routes->get('(:num)/(:num)', 'Season::show/$1/$2', $filter);
	$routes->post('(:num)', 'Season::create/$1', $filter);
	$routes->put('(:num)/(:num)', 'Season::update/$1/$2', $filter);
	$routes->patch('(:num)/(:num)', 'Season::update/$1/$2', $filter);
	$routes->delete('(:num)/(:num)', 'Season::delete/$1/$2', $filter);
});

// Capítulos
$routes->group('chapters', ['filter' => 'auth:chapter'], function ($routes) {
	$filter = ['filter' => 'season_filter'];

	$routes->get('(:num)/(:num)', 'Chapter::index/$1/$2', $filter);
	$routes->get('(:num)/(:num)/(:num)', 'Chapter::show/$1/$2/$3', $filter);
	$routes->post('(:num)/(:num)', 'Chapter::create/$1/$2', $filter);
	$routes->put('(:num)/(:num)/(:num)', 'Chapter::update/$1/$2/$3', $filter);
	$routes->patch('(:num)/(:num)/(:num)', 'Chapter::update/$1/$2/$3', $filter);
	$routes->delete('(:num)/(:num)/(:num)', 'Chapter::delete/$1/$2/$3', $filter);
});

// Casting
$routes->group('casting', ['filter' => 'auth:casting'], function ($routes) {
	$routes->get('(:num)/(:num)', 'Casting::index/$1/$2');
	$routes->post('(:num)/(:num)', 'Casting::create/$1/$2');
	$routes->delete('(:num)/(:num)/(:num)/(:num)', 'Casting::delete/$1/$2/$3/$4');
	$routes->delete('(:num)/(:num)/(:num)/(:num)/(:num)', 'Casting::delete/$1/$2/$3/$4/$5');
});

// Zords
$routes->group('zords', ['filter' => 'auth:zord'], function ($routes) {
	$routes->get('', 'Zord::index');
	$routes->get('(:num)', 'Zord::show/$1');
	$routes->post('', 'Zord::create');
	$routes->put('(:num)', 'Zord::update/$1');
	$routes->patch('(:num)', 'Zord::update/$1');
	$routes->delete('(:num)', 'Zord::delete/$1');
});

// Temporada-Zord
$routes->group('seasonzord', ['filter' => 'auth:zord'], function ($routes) {
	$routes->get('(:num)/(:num)', 'SeasonZord::index/$1/$2');
	$routes->post('(:num)/(:num)', 'SeasonZord::create/$1/$2');
	$routes->delete('(:num)/(:num)/(:num)', 'SeasonZord::delete/$1/$2/$3');
});

// Megazords
$routes->group('megazords', ['filter' => 'auth:megazord'], function ($routes) {
	$routes->get('', 'Megazord::index');
	$routes->get('(:num)', 'Megazord::show/$1');
	$routes->post('', 'Megazord::create');
	$routes->put('(:num)', 'Megazord::update/$1');
	$routes->patch('(:num)', 'Megazord::update/$1');
	$routes->delete('(:num)', 'Megazord::delete/$1');
});

// Temporada-Megazord
$routes->group('seasonmegazord', ['filter' => 'auth:megazord'], function ($routes) {
	$routes->get('(:num)/(:num)', 'SeasonMegazord::index/$1/$2');
	$routes->post('(:num)/(:num)', 'SeasonMegazord::create/$1/$2');
	$routes->delete('(:num)/(:num)/(:num)', 'SeasonMegazord::delete/$1/$2/$3');
});

// Megazord-Zord
$routes->group('megazordzord', ['filter' => 'auth:megazord'], function ($routes) {
	$routes->get('(:num)', 'MegazordZord::index/$1');
	$routes->post('(:num)', 'MegazordZord::create/$1');
	$routes->delete('(:num)/(:num)', 'MegazordZord::delete/$1/$2');
});

// Transformaciones
$routes->group('transformations', ['filter' => 'auth:transformation'], function ($routes) {
	$routes->get('', 'Transformation::index');
	$routes->get('(:num)', 'Transformation::show/$1');
	$routes->post('', 'Transformation::create');
	$routes->put('(:num)', 'Transformation::update/$1');
	$routes->patch('(:num)', 'Transformation::update/$1');
	$routes->delete('(:num)', 'Transformation::delete/$1');
});

// Transformación-Ranger
$routes->group('transformationrangers', ['filter' => 'auth:transformation'], function ($routes) {
	$routes->get('(:num)', 'TransformationRanger::index/$1');
	$routes->get('(:num)/(:num)', 'TransformationRanger::show/$1/$2');
	$routes->post('(:num)', 'TransformationRanger::create/$1');
	$routes->put('(:num)/(:num)', 'TransformationRanger::update/$1/$2');
	$routes->patch('(:num)/(:num)', 'TransformationRanger::update/$1/$2');
	$routes->delete('(:num)/(:num)', 'TransformationRanger::delete/$1/$2');
});

// Morphers
$routes->group('morphers', ['filter' => 'auth:morpher'], function ($routes) {
	$routes->get('', 'Morpher::index');
	$routes->get('(:num)', 'Morpher::show/$1');
	$routes->post('', 'Morpher::create');
	$routes->put('(:num)', 'Morpher::update/$1');
	$routes->patch('(:num)', 'Morpher::update/$1');
	$routes->delete('(:num)', 'Morpher::delete/$1');
});

// Arsenal
$routes->group('arsenal', ['filter' => 'auth:arsenal'], function ($routes) {
	$routes->get('', 'Arsenal::index');
	$routes->get('(:num)', 'Arsenal::show/$1');
	$routes->post('', 'Arsenal::create');
	$routes->put('(:num)', 'Arsenal::update/$1');
	$routes->patch('(:num)', 'Arsenal::update/$1');
	$routes->delete('(:num)', 'Arsenal::delete/$1');
});

// Temporada-Arsenal
$routes->group('seasonarsenal', ['filter' => 'auth:arsenal'], function ($routes) {
	$routes->get('(:num)/(:num)', 'SeasonArsenal::index/$1/$2');
	$routes->post('(:num)/(:num)', 'SeasonArsenal::create/$1/$2');
	$routes->delete('(:num)/(:num)/(:num)', 'SeasonArsenal::delete/$1/$2/$3');
});

// Villanos
$routes->group('villains', ['filter' => 'auth:villain'], function ($routes) {
	$routes->get('', 'Villain::index');
	$routes->get('(:num)', 'Villain::show/$1');
	$routes->post('', 'Villain::create');
	$routes->put('(:num)', 'Villain::update/$1');
	$routes->patch('(:num)', 'Villain::update/$1');
	$routes->delete('(:num)', 'Villain::delete/$1');
});

// Temporada-Villano
$routes->group('seasonvillain', ['filter' => 'auth:villain'], function ($routes) {
	$routes->get('(:num)/(:num)', 'SeasonVillain::index/$1/$2');
	$routes->post('(:num)/(:num)', 'SeasonVillain::create/$1/$2');
	$routes->delete('(:num)/(:num)/(:num)', 'SeasonVillain::delete/$1/$2/$3');
});

// Módulos
$routes->group('modules', ['filter' => 'auth:module'], function ($routes) {
	$routes->get('', 'Module::index');
	$routes->get('(:alpha)', 'Module::show/$1');
	$routes->post('', 'Module::create');
	$routes->put('(:alpha)', 'Module::update/$1');
	$routes->patch('(:alpha)', 'Module::update/$1');
	$routes->delete('(:alpha)', 'Module::delete/$1');
});

// Usuarios
$routes->group('users', ['filter' => 'auth:user'], function ($routes) {
	$routes->get('', 'User::index');
	$routes->get('(:num)', 'User::show/$1');
	$routes->post('', 'User::create');
	$routes->put('(:num)', 'User::update/$1');
	$routes->patch('(:num)', 'User::update/$1');
	$routes->delete('(:num)', 'User::delete/$1');
});

// Permisos
$routes->group('permissions', ['filter' => 'auth:permission'], function ($routes) {
	$routes->get('(:num)', 'Permission::index/$1');
	$routes->post('(:num)', 'Permission::create/$1');
	$routes->delete('(:num)/(:alpha)', 'Permission::delete/$1/$2');
});

// API
$routes->group('api', function ($routes) {
	/*$routes->get('actors', 'Actor::getList');
	$routes->get('actors/(:segment)', 'Actor::getRecordByURI/$1');
	$routes->get('ages', 'Age::getList');
	$routes->get('ages/(:segment)', 'Age::getRecordByURI/$1');
	$routes->get('characters', 'Character::getList');
	$routes->get('characters/(:segment)', 'Character::getRecordByURI/$1');
	$routes->get('rangers', 'Ranger::getList');
	$routes->get('rangers/(:segment)', 'Ranger::getRecordByURI/$1');
	$routes->get('series', 'Serie::getList');
	$routes->get('series/(:segment)', 'Serie::getRecordByURI/$1');
	$routes->get('zords', 'Zord::getList');
	$routes->get('zords/(:segment)', 'Zord::getRecordByURI/$1');
	$routes->get('seasons/(:segment)', 'Season::getList/$1');
	$routes->get('seasons/(:segment)/(:segment)', 'Season::getRecordByURI/$1/$2');
	$routes->get('chapters/(:segment)/(:segment)', 'Chapter::getList/$1/$2');
	$routes->get('chapters/(:segment)/(:segment)/(:segment)', 'Chapter::getRecordByURI/$1/$2/$3');
	$routes->get('casting/(:segment)/(:segment)', 'Casting::getList/$1/$2/0');
	$routes->get('teamup/(:segment)/(:segment)', 'Casting::getList/$1/$2/1');*/
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
