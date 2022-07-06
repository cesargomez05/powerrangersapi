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

$routesTree = [
	'actors' => function ($routes) {
		$routes->get('', 'Actor::index');
		$routes->get('(:segment)', 'Actor::show/$1');
		$routes->post('', 'Actor::create');
		$routes->put('(:segment)', 'Actor::update/$1');
		$routes->patch('(:segment)', 'Actor::update/$1');
		$routes->delete('(:segment)', 'Actor::delete/$1');
	},
	'ages' => function ($routes) {
		$routes->get('', 'Age::index');
		$routes->get('(:segment)', 'Age::show/$1');
		$routes->post('', 'Age::create');
		$routes->put('(:segment)', 'Age::update/$1');
		$routes->patch('(:segment)', 'Age::update/$1');
		$routes->delete('(:segment)', 'Age::delete/$1');
	},
	'characters' => function ($routes) {
		$routes->get('', 'Character::index');
		$routes->get('(:segment)', 'Character::show/$1');
		$routes->post('', 'Character::create');
		$routes->put('(:segment)', 'Character::update/$1');
		$routes->patch('(:segment)', 'Character::update/$1');
		$routes->delete('(:segment)', 'Character::delete/$1');
	},
	'rangers' => function ($routes) {
		$routes->get('', 'Ranger::index');
		$routes->get('(:segment)', 'Ranger::show/$1');
		$routes->post('', 'Ranger::create');
		$routes->put('(:segment)', 'Ranger::update/$1');
		$routes->patch('(:segment)', 'Ranger::update/$1');
		$routes->delete('(:segment)', 'Ranger::delete/$1');
	},
	'rangermorpher' => function ($routes) {
		$routes->get('(:segment)', 'RangerMorpher::show/$1');
		$routes->post('(:segment)', 'RangerMorpher::create/$1');
		$routes->delete('(:segment)', 'RangerMorpher::delete/$1');
	},
	'series' => function ($routes) {
		$routes->get('', 'Serie::index');
		$routes->get('(:segment)', 'Serie::show/$1');
		$routes->post('', 'Serie::create');
		$routes->put('(:segment)', 'Serie::update/$1');
		$routes->patch('(:segment)', 'Serie::update/$1');
		$routes->delete('(:segment)', 'Serie::delete/$1');
	},
	'seasons' => function ($routes) {
		$routes->get('(:segment)', 'Season::index/$1');
		$routes->get('(:segment)/(:segment)', 'Season::show/$1/$2');
		$routes->post('(:segment)', 'Season::create/$1');
		$routes->put('(:segment)/(:segment)', 'Season::update/$1/$2');
		$routes->patch('(:segment)/(:segment)', 'Season::update/$1/$2');
		$routes->delete('(:segment)/(:segment)', 'Season::delete/$1/$2');
	},
	'chapters' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'Chapter::index/$1/$2');
		$routes->get('(:segment)/(:segment)/(:segment)', 'Chapter::show/$1/$2/$3');
		$routes->post('(:segment)/(:segment)', 'Chapter::create/$1/$2');
		$routes->put('(:segment)/(:segment)/(:segment)', 'Chapter::update/$1/$2/$3');
		$routes->patch('(:segment)/(:segment)/(:segment)', 'Chapter::update/$1/$2/$3');
		$routes->delete('(:segment)/(:segment)/(:segment)', 'Chapter::delete/$1/$2/$3');
	},
	'casting' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'Casting::index/$1/$2');
		$routes->post('(:segment)/(:segment)', 'Casting::create/$1/$2');
		$routes->delete('(:segment)/(:segment)/(:segment)/(:segment)', 'Casting::delete/$1/$2/$3/$4');
		$routes->delete('(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Casting::delete/$1/$2/$3/$4/$5');
	},
	'castingby' => function ($routes) {
		$routes->get('actor/(:segment)', 'Casting::listByActor/$1');
		$routes->get('character/(:segment)', 'Casting::listByCharacter/$1');
		$routes->get('ranger/(:segment)', 'Casting::listByRanger/$1');
	},
	'teamup' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'Casting::indexTeamUpPublic/$1/$2');
	},
	'zords' => function ($routes) {
		$routes->get('', 'Zord::index');
		$routes->get('(:segment)', 'Zord::show/$1');
		$routes->post('', 'Zord::create');
		$routes->put('(:segment)', 'Zord::update/$1');
		$routes->patch('(:segment)', 'Zord::update/$1');
		$routes->delete('(:segment)', 'Zord::delete/$1');
	},
	'seasonzord' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'SeasonZord::index/$1/$2');
		$routes->post('(:segment)/(:segment)', 'SeasonZord::create/$1/$2');
		$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonZord::delete/$1/$2/$3');
	},
	'megazords' => function ($routes) {
		$routes->get('', 'Megazord::index');
		$routes->get('(:segment)', 'Megazord::show/$1');
		$routes->post('', 'Megazord::create');
		$routes->put('(:segment)', 'Megazord::update/$1');
		$routes->patch('(:segment)', 'Megazord::update/$1');
		$routes->delete('(:segment)', 'Megazord::delete/$1');
	},
	'seasonmegazord' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'SeasonMegazord::index/$1/$2');
		$routes->post('(:segment)/(:segment)', 'SeasonMegazord::create/$1/$2');
		$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonMegazord::delete/$1/$2/$3');
	},
	'megazordzord' => function ($routes) {
		$routes->get('(:segment)', 'MegazordZord::index/$1');
		$routes->post('(:segment)', 'MegazordZord::create/$1');
		$routes->delete('(:segment)/(:segment)', 'MegazordZord::delete/$1/$2');
	},
	'transformations' => function ($routes) {
		$routes->get('', 'Transformation::index');
		$routes->get('(:segment)', 'Transformation::show/$1');
		$routes->post('', 'Transformation::create');
		$routes->put('(:segment)', 'Transformation::update/$1');
		$routes->patch('(:segment)', 'Transformation::update/$1');
		$routes->delete('(:segment)', 'Transformation::delete/$1');
	},
	'transformationrangers' => function ($routes) {
		$routes->get('(:segment)', 'TransformationRanger::index/$1');
		$routes->get('(:segment)/(:segment)', 'TransformationRanger::show/$1/$2');
		$routes->post('(:segment)', 'TransformationRanger::create/$1');
		$routes->put('(:segment)/(:segment)', 'TransformationRanger::update/$1/$2');
		$routes->patch('(:segment)/(:segment)', 'TransformationRanger::update/$1/$2');
		$routes->delete('(:segment)/(:segment)', 'TransformationRanger::delete/$1/$2');
	},
	'morphers' => function ($routes) {
		$routes->get('', 'Morpher::index');
		$routes->get('(:segment)', 'Morpher::show/$1');
		$routes->post('', 'Morpher::create');
		$routes->put('(:segment)', 'Morpher::update/$1');
		$routes->patch('(:segment)', 'Morpher::update/$1');
		$routes->delete('(:segment)', 'Morpher::delete/$1');
	},
	'arsenal' => function ($routes) {
		$routes->get('', 'Arsenal::index');
		$routes->get('(:segment)', 'Arsenal::show/$1');
		$routes->post('', 'Arsenal::create');
		$routes->put('(:segment)', 'Arsenal::update/$1');
		$routes->patch('(:segment)', 'Arsenal::update/$1');
		$routes->delete('(:segment)', 'Arsenal::delete/$1');
	},
	'seasonarsenal' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'SeasonArsenal::index/$1/$2');
		$routes->post('(:segment)/(:segment)', 'SeasonArsenal::create/$1/$2');
		$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonArsenal::delete/$1/$2/$3');
	},
	'villains' => function ($routes) {
		$routes->get('', 'Villain::index');
		$routes->get('(:segment)', 'Villain::show/$1');
		$routes->post('', 'Villain::create');
		$routes->put('(:segment)', 'Villain::update/$1');
		$routes->patch('(:segment)', 'Villain::update/$1');
		$routes->delete('(:segment)', 'Villain::delete/$1');
	},
	'seasonvillain' => function ($routes) {
		$routes->get('(:segment)/(:segment)', 'SeasonVillain::index/$1/$2');
		$routes->post('(:segment)/(:segment)', 'SeasonVillain::create/$1/$2');
		$routes->delete('(:segment)/(:segment)/(:segment)', 'SeasonVillain::delete/$1/$2/$3');
	},
	'modules' => function ($routes) {
		$routes->get('', 'Module::index');
		$routes->get('(:segment)', 'Module::show/$1');
		$routes->post('', 'Module::create');
		$routes->put('(:segment)', 'Module::update/$1');
		$routes->patch('(:segment)', 'Module::update/$1');
		$routes->delete('(:segment)', 'Module::delete/$1');
	},
	'users' => function ($routes) {
		$routes->get('', 'User::index');
		$routes->get('(:segment)', 'User::show/$1');
		$routes->post('', 'User::create');
		$routes->put('(:segment)', 'User::update/$1');
		$routes->delete('(:segment)', 'User::delete/$1');
	},
	'permissions' => function ($routes) {
		$routes->get('(:segment)', 'Permission::index/$1');
		$routes->post('(:segment)', 'Permission::create/$1');
		$routes->delete('(:segment)/(:segment)', 'Permission::delete/$1/$2');
	}
];

// API
$routes->group('api', function ($routes) use ($routesTree) {
	$routes->post('oauth2', 'Home::getOAuth2');
	$routes->post('jwt', 'Home::getJwt');
	$routes->post('jwtuser', 'Home::getJwtUser');

	// Modules
	$routes->group('actors', ['filter' => 'actor_filter:actor'], $routesTree['actors']);
	$routes->group('ages', ['filter' => 'age_filter:age'], $routesTree['ages']);
	$routes->group('characters', ['filter' => 'character_filter:character'], $routesTree['characters']);
	$routes->group('rangers', ['filter' => 'ranger_filter:ranger'], $routesTree['rangers']);
	$routes->group('rangermorpher', ['filter' => 'ranger_morpher_filter:ranger'], $routesTree['rangermorpher']);
	$routes->group('series', ['filter' => 'serie_filter:serie'], $routesTree['series']);
	$routes->group('seasons', ['filter' => 'season_filter:season'], $routesTree['seasons']);
	$routes->group('chapters', ['filter' => 'chapter_filter:chapter'], $routesTree['chapters']);
	$routes->group('casting', ['filter' => 'casting_filter:casting'], $routesTree['casting']);
	$routes->group('castingby', ['filter' => 'casting_filter_by:casting'], $routesTree['castingby']);
	$routes->group('teamup', ['filter' => 'casting_filter:casting'], $routesTree['teamup']);
	$routes->group('zords', ['filter' => 'zord_filter:zord'], $routesTree['zords']);
	$routes->group('seasonzord', ['filter' => 'seasonzord_filter:zord'], $routesTree['seasonzord']);
	$routes->group('megazords', ['filter' => 'megazord_filter:megazord'], $routesTree['megazords']);
	$routes->group('seasonmegazord', ['filter' => 'seasonmegazord_filter:megazord'], $routesTree['seasonmegazord']);
	$routes->group('megazordzord', ['filter' => 'megazordzord_filter:megazord'], $routesTree['megazordzord']);
	$routes->group('transformations', ['filter' => 'transformation_filter:transformation'], $routesTree['transformations']);
	$routes->group('transformationrangers', ['filter' => 'transformationranger_filter:transformation'], $routesTree['transformationrangers']);
	$routes->group('morphers', ['filter' => 'morpher_filter:morpher'], $routesTree['morphers']);
	$routes->group('arsenal', ['filter' => 'arsenal_filter:arsenal'], $routesTree['arsenal']);
	$routes->group('seasonarsenal', ['filter' => 'seasonarsenal_filter:arsenal'], $routesTree['seasonarsenal']);
	$routes->group('villains', ['filter' => 'villain_filter:villain'], $routesTree['villains']);
	$routes->group('seasonvillain', ['filter' => 'seasonvillain_filter:villain'], $routesTree['seasonvillain']);

	// Usuarios y permisos
	$routes->group('modules', ['filter' => 'module_filter:module'], $routesTree['modules']);
	$routes->group('users', ['filter' => 'user_filter:user'], $routesTree['users']);
	$routes->group('permissions', ['filter' => 'permission_filter:permission'], $routesTree['permissions']);
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
