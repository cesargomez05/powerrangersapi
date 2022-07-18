<?php

namespace App\Traits;

trait ControllerTrait
{
	public function index(...$ids)
	{
		$filter = $this->request->getGet();
		set_pagination($filter);

		array_unshift($ids, $filter);
		$records = call_user_func_array(array($this->model, "list"), $ids);
		return $this->respond($records);
	}

	public function show(...$ids)
	{
		$record = call_user_func_array(array($this->model, "get"), $ids);
		return $this->respond(['record' => $record]);
	}

	public function delete(...$ids)
	{
		// Se ejecuta el método que valida los registros dependientes del registro a eliminar (si aplica)
		if (method_exists($this->model, 'validateNestedRecords')) {
			$validations = call_user_func_array(array($this->model, "validateNestedRecords"), $ids);
			if (count($validations) > 0) {
				return $this->respond(['errors' => $validations], 409);
			}
		}

		$result = call_user_func_array(array($this->model, "deleteRecord"), $ids);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->getResponse(500, $result);
		}

		return $this->success("Record successfully deleted");
	}

	/**
	 * Establece los valores de la serie y temporada en los datos a registrar.
	 * @param $postData Variable donde se consolida los datos a registrar.
	 * @param $serieId Id de la serie.
	 * @param $seasonNumber Número de temporada.
	 */
	private function setSeasonProperties(&$postData, $serieId, $seasonNumber = null)
	{
		$properties = ['serieId' => $serieId];
		if (isset($seasonNumber)) {
			$properties['seasonNumber'] = $seasonNumber;
		}

		$this->addSegmentProperties($postData, $properties);
	}

	/**
	 * Adiciona a los datos de la petición los valores definidos en segmentos del Endpoint.
	 * @param $postData Variable donde se consolida los datos a registrar.
	 * @param $properties Lista con las propiedades a establecer en los datos a registrar.
	 */
	private function addSegmentProperties(&$postData, $properties)
	{
		foreach ($properties as $key => $value) {
			$postData[$key] = $value;
		}
	}

	private function checkRequestData(&$postData, &$postFiles, &$method = 'post')
	{
		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		unset($postData['_method']);
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->getResponse(400, 'Please define the data to be recorded');
		}

		// Se obtiene el método HTTP de la petición
		$request = service('request');
		$method = $request->getMethod();
	}

	private function getResponse($code, $error)
	{
		return $this->respond(['status' => $code, 'error' => $error], $code);
	}
}
