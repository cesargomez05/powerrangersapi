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
		// Autenticación en la API
		'auth' => \App\Filters\AuthFilter::class,
		// Módulos de la aplicación
		'actor_filter' => \App\Filters\ActorFilter::class,
		'age_filter' => \App\Filters\AgeFilter::class,
		'character_filter' => \App\Filters\CharacterFilter::class,
		'ranger_filter' => \App\Filters\RangerFilter::class,
		'serie_filter' => \App\Filters\SerieFilter::class,
		'season_filter' => \App\Filters\SeasonFilter::class,
		'chapter_filter' => \App\Filters\ChapterFilter::class,
		'casting_filter' => \App\Filters\CastingFilter::class,

		
		'megazord_filter' => \App\Filters\MegazordFilter::class,
		'transformation_filter' => \App\Filters\TransformationFilter::class
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
