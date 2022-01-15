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
	$filter = ['filter' => 'actor_filter'];
	$routes->get('', 'Actor::index', $filter);
	$routes->get('(:alphanum)', 'Actor::show/$1', $filter);
	$routes->post('', 'Actor::create', $filter);
	$routes->put('(:alphanum)', 'Actor::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Actor::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Actor::delete/$1', $filter);
});

// Eras
$routes->group('ages', ['filter' => 'auth:age'], function ($routes) {
	$filter = ['filter' => 'age_filter'];
	$routes->get('', 'Age::index', $filter);
	$routes->get('(:alphanum)', 'Age::show/$1', $filter);
	$routes->post('', 'Age::create', $filter);
	$routes->put('(:alphanum)', 'Age::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Age::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Age::delete/$1', $filter);
});

// Personajes
$routes->group('characters', ['filter' => 'auth:character'], function ($routes) {
	$filter = ['filter' => 'character_filter'];
	$routes->get('', 'Character::index', $filter);
	$routes->get('(:alphanum)', 'Character::show/$1', $filter);
	$routes->post('', 'Character::create', $filter);
	$routes->put('(:alphanum)', 'Character::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Character::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Character::delete/$1', $filter);
});

// Rangers
$routes->group('rangers', ['filter' => 'auth:ranger'], function ($routes) {
	$filter = ['filter' => 'ranger_filter'];
	$routes->get('', 'Ranger::index', $filter);
	$routes->get('(:alphanum)', 'Ranger::show/$1', $filter);
	$routes->post('', 'Ranger::create', $filter);
	$routes->put('(:alphanum)', 'Ranger::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Ranger::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Ranger::delete/$1', $filter);
});

// Series
$routes->group('series', ['filter' => 'auth:serie'], function ($routes) {
	$filter = ['filter' => 'serie_filter'];
	$routes->get('', 'Serie::index', $filter);
	$routes->get('(:alphanum)', 'Serie::show/$1', $filter);
	$routes->post('', 'Serie::create', $filter);
	$routes->put('(:alphanum)', 'Serie::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Serie::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Serie::delete/$1', $filter);
});

// Temporadas
$routes->group('seasons', ['filter' => 'auth:season'], function ($routes) {
	$filter = ['filter' => 'season_filter'];

	$routes->get('(:alphanum)', 'Season::index/$1', $filter);
	$routes->get('(:alphanum)/(:alphanum)', 'Season::show/$1/$2', $filter);
	$routes->post('(:alphanum)', 'Season::create/$1', $filter);
	$routes->put('(:alphanum)/(:alphanum)', 'Season::update/$1/$2', $filter);
	$routes->patch('(:alphanum)/(:alphanum)', 'Season::update/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)', 'Season::delete/$1/$2', $filter);
});

// Capítulos
$routes->group('chapters', ['filter' => 'auth:chapter'], function ($routes) {
	$filter = ['filter' => 'chapter_filter'];
	$routes->get('(:alphanum)/(:alphanum)', 'Chapter::index/$1/$2', $filter);
	$routes->get('(:alphanum)/(:alphanum)/(:alphanum)', 'Chapter::show/$1/$2/$3', $filter);
	$routes->post('(:alphanum)/(:alphanum)', 'Chapter::create/$1/$2', $filter);
	$routes->put('(:alphanum)/(:alphanum)/(:alphanum)', 'Chapter::update/$1/$2/$3', $filter);
	$routes->patch('(:alphanum)/(:alphanum)/(:alphanum)', 'Chapter::update/$1/$2/$3', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)', 'Chapter::delete/$1/$2/$3', $filter);
});

// Casting
$routes->group('casting', ['filter' => 'auth:casting'], function ($routes) {
	$filter = ['filter' => 'casting_filter'];
	$routes->get('(:alphanum)/(:alphanum)', 'Casting::index/$1/$2', $filter);
	$routes->post('(:alphanum)/(:alphanum)', 'Casting::create/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)/(:alphanum)', 'Casting::delete/$1/$2/$3/$4', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)/(:alphanum)/(:alphanum)', 'Casting::delete/$1/$2/$3/$4/$5', $filter);
});

// Zords
$routes->group('zords', ['filter' => 'auth:zord'], function ($routes) {
	$filter = ['filter' => 'zord_filter'];
	$routes->get('', 'Zord::index', $filter);
	$routes->get('(:alphanum)', 'Zord::show/$1', $filter);
	$routes->post('', 'Zord::create', $filter);
	$routes->put('(:alphanum)', 'Zord::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Zord::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Zord::delete/$1', $filter);
});

// Temporada-Zord
$routes->group('seasonzord', ['filter' => 'auth:zord'], function ($routes) {
	$filter = ['filter' => 'seasonzord_filter'];
	$routes->get('(:alphanum)/(:alphanum)', 'SeasonZord::index/$1/$2', $filter);
	$routes->post('(:alphanum)/(:alphanum)', 'SeasonZord::create/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)', 'SeasonZord::delete/$1/$2/$3', $filter);
});

// Megazords
$routes->group('megazords', ['filter' => 'auth:megazord'], function ($routes) {
	$filter = ['filter' => 'megazord_filter'];
	$routes->get('', 'Megazord::index', $filter);
	$routes->get('(:alphanum)', 'Megazord::show/$1', $filter);
	$routes->post('', 'Megazord::create', $filter);
	$routes->put('(:alphanum)', 'Megazord::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Megazord::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Megazord::delete/$1', $filter);
});

// Temporada-Megazord
$routes->group('seasonmegazord', ['filter' => 'auth:megazord'], function ($routes) {
	$filter = ['filter' => 'seasonmegazord_filter'];
	$routes->get('(:alphanum)/(:alphanum)', 'SeasonMegazord::index/$1/$2', $filter);
	$routes->post('(:alphanum)/(:alphanum)', 'SeasonMegazord::create/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)', 'SeasonMegazord::delete/$1/$2/$3', $filter);
});

// Megazord-Zord
$routes->group('megazordzord', ['filter' => 'auth:megazord'], function ($routes) {
	$filter = ['filter' => 'megazordzord_filter'];
	$routes->get('(:alphanum)', 'MegazordZord::index/$1', $filter);
	$routes->post('(:alphanum)', 'MegazordZord::create/$1', $filter);
	$routes->delete('(:alphanum)/(:alphanum)', 'MegazordZord::delete/$1/$2', $filter);
});

// Transformaciones
$routes->group('transformations', ['filter' => 'auth:transformation'], function ($routes) {
	$filter = ['filter' => 'transformation_filter'];
	$routes->get('', 'Transformation::index', $filter);
	$routes->get('(:alphanum)', 'Transformation::show/$1', $filter);
	$routes->post('', 'Transformation::create', $filter);
	$routes->put('(:alphanum)', 'Transformation::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Transformation::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Transformation::delete/$1', $filter);
});

// Transformación-Ranger
$routes->group('transformationrangers', ['filter' => 'auth:transformation'], function ($routes) {
	$filter = ['filter' => 'transformationranger_filter'];
	$routes->get('(:alphanum)', 'TransformationRanger::index/$1', $filter);
	$routes->get('(:alphanum)/(:alphanum)', 'TransformationRanger::show/$1/$2', $filter);
	$routes->post('(:alphanum)', 'TransformationRanger::create/$1', $filter);
	$routes->put('(:alphanum)/(:alphanum)', 'TransformationRanger::update/$1/$2', $filter);
	$routes->patch('(:alphanum)/(:alphanum)', 'TransformationRanger::update/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)', 'TransformationRanger::delete/$1/$2', $filter);
});

// Morphers
$routes->group('morphers', ['filter' => 'auth:morpher'], function ($routes) {
	$filter = ['filter' => 'morpher_filter'];
	$routes->get('', 'Morpher::index', $filter);
	$routes->get('(:alphanum)', 'Morpher::show/$1', $filter);
	$routes->post('', 'Morpher::create', $filter);
	$routes->put('(:alphanum)', 'Morpher::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Morpher::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Morpher::delete/$1', $filter);
});

// Arsenal
$routes->group('arsenal', ['filter' => 'auth:arsenal'], function ($routes) {
	$filter = ['filter' => 'arsenal_filter'];
	$routes->get('', 'Arsenal::index', $filter);
	$routes->get('(:alphanum)', 'Arsenal::show/$1', $filter);
	$routes->post('', 'Arsenal::create', $filter);
	$routes->put('(:alphanum)', 'Arsenal::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Arsenal::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Arsenal::delete/$1', $filter);
});

// Temporada-Arsenal
$routes->group('seasonarsenal', ['filter' => 'auth:arsenal'], function ($routes) {
	$filter = ['filter' => 'seasonarsenal_filter'];
	$routes->get('(:alphanum)/(:alphanum)', 'SeasonArsenal::index/$1/$2', $filter);
	$routes->post('(:alphanum)/(:alphanum)', 'SeasonArsenal::create/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)', 'SeasonArsenal::delete/$1/$2/$3', $filter);
});

// Villanos
$routes->group('villains', ['filter' => 'auth:villain'], function ($routes) {
	$filter = ['filter' => 'villain_filter'];
	$routes->get('', 'Villain::index', $filter);
	$routes->get('(:alphanum)', 'Villain::show/$1', $filter);
	$routes->post('', 'Villain::create', $filter);
	$routes->put('(:alphanum)', 'Villain::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Villain::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Villain::delete/$1', $filter);
});

// Temporada-Villano
$routes->group('seasonvillain', ['filter' => 'auth:villain'], function ($routes) {
	$filter = ['filter' => 'seasonvillain_filter'];
	$routes->get('(:alphanum)/(:alphanum)', 'SeasonVillain::index/$1/$2', $filter);
	$routes->post('(:alphanum)/(:alphanum)', 'SeasonVillain::create/$1/$2', $filter);
	$routes->delete('(:alphanum)/(:alphanum)/(:alphanum)', 'SeasonVillain::delete/$1/$2/$3', $filter);
});

// Módulos
$routes->group('modules', ['filter' => 'auth:module'], function ($routes) {
	$filter = ['filter' => 'module_filter'];
	$routes->get('', 'Module::index', $filter);
	$routes->get('(:alphanum)', 'Module::show/$1', $filter);
	$routes->post('', 'Module::create', $filter);
	$routes->put('(:alphanum)', 'Module::update/$1', $filter);
	$routes->patch('(:alphanum)', 'Module::update/$1', $filter);
	$routes->delete('(:alphanum)', 'Module::delete/$1', $filter);
});

// Usuarios
$routes->group('users', ['filter' => 'auth:user'], function ($routes) {
	$filter = ['filter' => 'user_filter'];
	$routes->get('', 'User::index', $filter);
	$routes->get('(:alphanum)', 'User::show/$1', $filter);
	$routes->post('', 'User::create', $filter);
	$routes->put('(:alphanum)', 'User::update/$1', $filter);
	$routes->delete('(:alphanum)', 'User::delete/$1', $filter);
});

// Permisos
$routes->group('permissions', ['filter' => 'auth:permission'], function ($routes) {
	$filter = ['filter' => 'permission_filter'];
	$routes->get('(:alphanum)', 'Permission::index/$1', $filter);
	$routes->post('(:alphanum)', 'Permission::create/$1', $filter);
	$routes->delete('(:alphanum)/(:alphanum)', 'Permission::delete/$1/$2', $filter);
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
