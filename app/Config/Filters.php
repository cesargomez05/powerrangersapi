<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf' => CSRF::class,
		'toolbar' => DebugToolbar::class,
		'honeypot' => Honeypot::class,
		// Módulos de la aplicación
		'actor_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\ActorFilter::class
		],
		'age_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\AgeFilter::class
		],
		'character_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\CharacterFilter::class
		],
		'ranger_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\RangerFilter::class
		],
		'ranger_morpher_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\RangerMorpherFilter::class
		],
		'serie_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\SerieFilter::class
		],
		'season_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\SeasonFilter::class
		],
		'chapter_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\ChapterFilter::class
		],
		'casting_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\CastingFilter::class
		],
		'casting_filter_by' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\CastingByFilter::class
		],
		'zord_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\ZordFilter::class
		],
		'seasonzord_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\SeasonZordFilter::class
		],
		'megazord_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\MegazordFilter::class
		],
		'seasonmegazord_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\SeasonMegazordFilter::class
		],
		'megazordzord_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\MegazordZordFilter::class
		],
		'transformation_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\TransformationFilter::class
		],
		'transformationranger_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\TransformationRangerFilter::class
		],
		'morpher_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\MorpherFilter::class
		],
		'arsenal_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\ArsenalFilter::class
		],
		'seasonarsenal_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\SeasonArsenalFilter::class
		],
		'villain_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\VillainFilter::class
		],
		'seasonvillain_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\SeasonVillainFilter::class
		],
		'module_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\ModuleFilter::class
		],
		'user_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\UserFilter::class
		],
		'permission_filter' => [
			\App\Filters\AuthFilter::class,
			\App\Filters\PermissionFilter::class
		]
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
			// 'honeypot',
			// 'csrf',
		],
		'after' => [
			'toolbar',
			// 'honeypot',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */
	public $filters = [];
}
