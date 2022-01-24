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
$routes->group('actors', ['filter' => 'actor_filter:actor'], function ($routes) {
	$routes->get('', 'Actor::index');
	$routes->get('(:segment)', 'Actor::show/$1');
	$routes->post('', 'Actor::create');
	$routes->put('(:segment)', 'Actor::update/$1');
	$routes->patch('(:segment)', 'Actor::update/$1');
	$routes->delete('(:segment)', 'Actor::delete/$1');
});

// Eras
$routes->group('ages', ['filter' => 'age_filter:age'], function ($routes) {
	$routes->get('', 'Age::index');
	$routes->get('(:segment)', 'Age::show/$1');
	$routes->post('', 'Age::create');
	$routes->put('(:segment)', 'Age::update/$1');
	$routes->patch('(:segment)', 'Age::update/$1');
	$routes->delete('(:segment)', 'Age::delete/$1');
});

// Personajes
$routes->group('characters', ['filter' => 'character_filter:character'], function ($routes) {
	$routes->get('', 'Character::index');
	$routes->get('(:segment)', 'Character::show/$1');
	$routes->post('', 'Character::create');
	$routes->put('(:segment)', 'Character::update/$1');
	$routes->patch('(:segment)', 'Character::update/$1');
	$routes->delete('(:segment)', 'Character::delete/$1');
});

// Rangers
$routes->group('rangers', ['filter' => 'ranger_filter:ranger'], function ($routes) {
	$routes->get('', 'Ranger::index');
	$routes->get('(:segment)', 'Ranger::show/$1');
	$routes->post('', 'Ranger::create');
	$routes->put('(:segment)', 'Ranger::update/$1');
	$routes->patch('(:segment)', 'Ranger::update/$1');
	$routes->delete('(:segment)', 'Ranger::delete/$1');
});

$routes->group('rangermorpher', ['filter' => 'ranger_morpher_filter:ranger'], function ($routes) {
	$routes->get('(:segment)', 'RangerMorpher::show/$1');
	$routes->post('(:segment)', 'RangerMorpher::create/$1');
	$routes->delete('(:segment)', 'RangerMorpher::delete/$1');
});

// Series
$routes->group('series', ['filter' => 'serie_filter:serie'], function ($routes) {
	$routes->get('', 'Serie::index');
	$routes->get('(:segment)', 'Serie::show/$1');
	$routes->post('', 'Serie::create');
	$routes->put('(:segment)', 'Serie::update/$1');
	$routes->patch('(:segment)', 'Serie::update/$1');
	$routes->delete('(:segment)', 'Serie::delete/$1');
});

// Temporadas
$routes->group('seasons', ['filter' => 'season_filter:season'], function ($routes) {
	$routes->get('(:segment)', 'Season::index/$1');
	$routes->get('(:segment)/(:segment)', 'Season::show/$1/$2');
	$routes->post('(:segment)', 'Season::create/$1');
	$routes->put('(:segment)/(:segment)', 'Season::update/$1/$2');
	$routes->patch('(:segment)/(:segment)', 'Season::update/$1/$2');
	$routes->delete('(:segment)/(:segment)', 'Season::delete/$1/$2');
});

// Capítulos
$routes->group('chapters', ['filter' => 'chapter_filter:chapter'], function ($routes) {
	$routes->get('(:segment)/(:segment)', 'Chapter::index/$1/$2');
	$routes->get('(:segment)/(:segment)/(:segment)', 'Chapter::show/$1/$2/$3');
	$routes->post('(:segment)/(:segment)', 'Chapter::create/$1/$2');
	$routes->put('(:segment)/(:segment)/(:segment)', 'Chapter::update/$1/$2/$3');
	$routes->patch('(:segment)/(:segment)/(:segment)', 'Chapter::update/$1/$2/$3');
	$routes->delete('(:segment)/(:segment)/(:segment)', 'Chapter::delete/$1/$2/$3');
});

// Casting
$routes->group('casting', ['filter' => 'casting_filter:casting'], function ($routes) {
	$routes->get('(:segment)/(:segment)', 'Casting::index/$1/$2');
	$routes->post('(:segment)/(:segment)', 'Casting::create/$1/$2');
	$routes->delete('(:segment)/(:segment)/(:segment)/(:segment)', 'Casting::delete/$1/$2/$3/$4');
	$routes->delete('(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Casting::delete/$1/$2/$3/$4/$5');
});

// Zords
$routes->group('zords', ['filter' => 'zord_filter:zord'], function ($routes) {
	$routes->get('', 'Zord::index');
	$routes->get('(:segment)', 'Zord::show/$1');
	$routes->post('', 'Zord::create');
	$routes->put('(:segment)', 'Zord::update/$1');
	$routes->patch('(:segment)', 'Zord::update/$1');
	$routes->delete('(:segment)', 'Zord::delete/$1');
});

// Temporada-Zord
$routes->group('seasonzord', ['filter' => 'seasonzord_filter:zord'], function ($routes) {
	$routes->get('(:segment)/(:segment)', 'SeasonZord::index/$1/$2');
	$routes->post('(:segment)/(:segment)', 'SeasonZord::create/$1/$2');
	$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonZord::delete/$1/$2/$3');
});

// Megazords
$routes->group('megazords', ['filter' => 'megazord_filter:megazord'], function ($routes) {
	$routes->get('', 'Megazord::index');
	$routes->get('(:segment)', 'Megazord::show/$1');
	$routes->post('', 'Megazord::create');
	$routes->put('(:segment)', 'Megazord::update/$1');
	$routes->patch('(:segment)', 'Megazord::update/$1');
	$routes->delete('(:segment)', 'Megazord::delete/$1');
});

// Temporada-Megazord
$routes->group('seasonmegazord', ['filter' => 'seasonmegazord_filter:megazord'], function ($routes) {
	$routes->get('(:segment)/(:segment)', 'SeasonMegazord::index/$1/$2');
	$routes->post('(:segment)/(:segment)', 'SeasonMegazord::create/$1/$2');
	$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonMegazord::delete/$1/$2/$3');
});

// Megazord-Zord
$routes->group('megazordzord', ['filter' => 'megazordzord_filter:megazord'], function ($routes) {
	$routes->get('(:segment)', 'MegazordZord::index/$1');
	$routes->post('(:segment)', 'MegazordZord::create/$1');
	$routes->delete('(:segment)/(:segment)', 'MegazordZord::delete/$1/$2');
});

// Transformaciones
$routes->group('transformations', ['filter' => 'transformation_filter:transformation'], function ($routes) {
	$routes->get('', 'Transformation::index');
	$routes->get('(:segment)', 'Transformation::show/$1');
	$routes->post('', 'Transformation::create');
	$routes->put('(:segment)', 'Transformation::update/$1');
	$routes->patch('(:segment)', 'Transformation::update/$1');
	$routes->delete('(:segment)', 'Transformation::delete/$1');
});

// Transformación-Ranger
$routes->group('transformationrangers', ['filter' => 'transformationranger_filter:transformation'], function ($routes) {
	$routes->get('(:segment)', 'TransformationRanger::index/$1');
	$routes->get('(:segment)/(:segment)', 'TransformationRanger::show/$1/$2');
	$routes->post('(:segment)', 'TransformationRanger::create/$1');
	$routes->put('(:segment)/(:segment)', 'TransformationRanger::update/$1/$2');
	$routes->patch('(:segment)/(:segment)', 'TransformationRanger::update/$1/$2');
	$routes->delete('(:segment)/(:segment)', 'TransformationRanger::delete/$1/$2');
});

// Morphers
$routes->group('morphers', ['filter' => 'morpher_filter:morpher'], function ($routes) {
	$routes->get('', 'Morpher::index');
	$routes->get('(:segment)', 'Morpher::show/$1');
	$routes->post('', 'Morpher::create');
	$routes->put('(:segment)', 'Morpher::update/$1');
	$routes->patch('(:segment)', 'Morpher::update/$1');
	$routes->delete('(:segment)', 'Morpher::delete/$1');
});

// Arsenal
$routes->group('arsenal', ['filter' => 'arsenal_filter:arsenal'], function ($routes) {
	$routes->get('', 'Arsenal::index');
	$routes->get('(:segment)', 'Arsenal::show/$1');
	$routes->post('', 'Arsenal::create');
	$routes->put('(:segment)', 'Arsenal::update/$1');
	$routes->patch('(:segment)', 'Arsenal::update/$1');
	$routes->delete('(:segment)', 'Arsenal::delete/$1');
});

// Temporada-Arsenal
$routes->group('seasonarsenal', ['filter' => 'seasonarsenal_filter:arsenal'], function ($routes) {
	$routes->get('(:segment)/(:segment)', 'SeasonArsenal::index/$1/$2');
	$routes->post('(:segment)/(:segment)', 'SeasonArsenal::create/$1/$2');
	$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonArsenal::delete/$1/$2/$3');
});

// Villanos
$routes->group('villains', ['filter' => 'villain_filter:villain'], function ($routes) {
	$routes->get('', 'Villain::index');
	$routes->get('(:segment)', 'Villain::show/$1');
	$routes->post('', 'Villain::create');
	$routes->put('(:segment)', 'Villain::update/$1');
	$routes->patch('(:segment)', 'Villain::update/$1');
	$routes->delete('(:segment)', 'Villain::delete/$1');
});

// Temporada-Villano
$routes->group('seasonvillain', ['filter' => 'seasonvillain_filter:villain'], function ($routes) {
	$routes->get('(:segment)/(:segment)', 'SeasonVillain::index/$1/$2');
	$routes->post('(:segment)/(:segment)', 'SeasonVillain::create/$1/$2');
	$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonVillain::delete/$1/$2/$3');
});

// Módulos
$routes->group('modules', ['filter' => 'module_filter:module'], function ($routes) {
	$routes->get('', 'Module::index');
	$routes->get('(:segment)', 'Module::show/$1');
	$routes->post('', 'Module::create');
	$routes->put('(:segment)', 'Module::update/$1');
	$routes->patch('(:segment)', 'Module::update/$1');
	$routes->delete('(:segment)', 'Module::delete/$1');
});

// Usuarios
$routes->group('users', ['filter' => 'user_filter:user'], function ($routes) {
	$routes->get('', 'User::index');
	$routes->get('(:segment)', 'User::show/$1');
	$routes->post('', 'User::create');
	$routes->put('(:segment)', 'User::update/$1');
	$routes->delete('(:segment)', 'User::delete/$1');
});

// Permisos
$routes->group('permissions', ['filter' => 'permission_filter:permission'], function ($routes) {
	$routes->get('(:segment)', 'Permission::index/$1');
	$routes->post('(:segment)', 'Permission::create/$1');
	$routes->delete('(:segment)/(:segment)', 'Permission::delete/$1/$2');
});

// API
$routes->group('api', function ($routes) {
	$routes->get('actors', 'Actor::indexPublic');
	$routes->get('actors/(:segment)', 'Actor::showPublic/$1');
	$routes->get('ages', 'Age::indexPublic');
	$routes->get('ages/(:segment)', 'Age::showPublic/$1');
	$routes->get('characters', 'Character::indexPublic');
	$routes->get('characters/(:segment)', 'Character::showPublic/$1');
	$routes->get('rangers', 'Ranger::indexPublic');
	$routes->get('rangers/(:segment)', 'Ranger::showPublic/$1');
	$routes->get('series', 'Serie::indexPublic');
	$routes->get('series/(:segment)', 'Serie::showPublic/$1');
	$routes->get('seasons/(:segment)', 'Season::indexPublic/$1');
	$routes->get('seasons/(:segment)/(:segment)', 'Season::showPublic/$1/$2');
	$routes->get('chapters/(:segment)/(:segment)', 'Chapter::indexPublic/$1/$2');
	$routes->get('chapters/(:segment)/(:segment)/(:segment)', 'Chapter::showPublic/$1/$2/$3');
	$routes->get('casting/(:segment)/(:segment)', 'Casting::indexPublic/$1/$2');
	$routes->get('teamup/(:segment)/(:segment)', 'Casting::indexTeamUpPublic/$1/$2');
	$routes->get('zords', 'Zord::indexPublic');
	$routes->get('zords/(:segment)', 'Zord::showPublic/$1');
	$routes->get('megazords', 'Megazord::indexPublic');
	$routes->get('megazords/(:segment)', 'Megazord::showPublic/$1');
	$routes->get('transformations', 'Transformation::indexPublic');
	$routes->get('transformations/(:segment)', 'Transformation::showPublic/$1');
	$routes->get('morphers', 'Morpher::indexPublic');
	$routes->get('morphers/(:segment)', 'Morpher::showPublic/$1');
	$routes->get('arsenal', 'Arsenal::indexPublic');
	$routes->get('arsenal/(:segment)', 'Arsenal::showPublic/$1');
	$routes->get('villains', 'Villain::indexPublic');
	$routes->get('villains/(:segment)', 'Villain::showPublic/$1');
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
