<?php

if (!function_exists('set_pagination')) {
	function set_pagination(&$filter)
	{
		// Se asigna los valores por defecto a cada uno de los atributos (si aplica)
		if (!isset($filter['page']) || !is_numeric($filter['page'])) {
			$filter['page'] = 1;
		}
		if (!isset($filter['pageSize']) || !is_numeric($filter['pageSize'])) {
			$filter['pageSize'] = 10;
		}

		$filter['page'] = intval($filter['page']);
		$filter['pageSize'] = intval($filter['pageSize']);

		// Se establece los valores mínimos y máximo de cada uno de los atributos
		if ($filter['page'] <= 0) {
			$filter['page'] = 1;
		}
		if ($filter['pageSize'] <= 0) {
			$filter['pageSize'] = 10;
		}
		if ($filter['pageSize'] > 50) {
			$filter['pageSize'] = 50;
		}
	}
}

if (!function_exists('get_dot_array')) {
	function get_dot_array(&$arr, $path)
	{
		//$list = ['rangers' => ['form' => 7123]];
		//echo json_encode(get_dot_array($list, 'rangers.form'));
		$keys = explode('.', $path);
		foreach ($keys as $key) {
			$arr = &$arr[$key];
		}
		return $arr;
	}
}
