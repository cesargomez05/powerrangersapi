<?php

namespace App\Traits;

trait ModelTrait
{
	/**
	 * Obtiene la lista de registros.
	 * @param array $query Parámetros de consulta de los registros.
	 * @return array Resultado de la consulta.
	 */
	public function list($query)
	{
		if (method_exists($this, 'setRecordsCondition')) {
			$this->setRecordsCondition($query);
		}

		$response = [];
		$response['count'] = $this->countAllResults(false);
		if ($response['count'] < $query['pageSize'] * $query['page']) {
			$query['page'] = intdiv($response['count'], $query['pageSize']) + 1;
		}
		$response['page'] = intval($query['page']);
		$response['records'] = $this->findAll($query['pageSize'], ($query['page'] - 1) * $query['pageSize']);

		return $response;
	}

	public function validateId($id, $property = 'id', $message = 'Id is not valid')
	{
		$validation = \Config\Services::validation();
		$validation->setRule($property, $message, 'required|is_natural_no_zero');
		if ($validation->run([$property => $id])) {
			return true;
		} else {
			return $validation->getErrors();
		}
	}

	private function validateRecordProperties(&$postData, $method, $prevRecord = null)
	{
		$rules = array_keys($this->validationRules);
		foreach ($rules as $field) {
			if ($method == 'patch') {
				if (!isset($postData[$field])) {
					if (isset($prevRecord[$field])) {
						$postData[$field] = $prevRecord[$field];
					} else {
						$postData[$field] = null;
					}
				}
			} else {
				if (!isset($postData[$field])) {
					$postData[$field] = null;
				}
			}
		}
	}

	/**
	 * Realiza la validación del archivo adjunto subido.
	 * @param array $postData Información del registro a insertar en base de datos.
	 * @param array $postFiles Información de archivos adjuntos.
	 * @param string $photoField Nombre del campo donde se define el archivo adjunto.
	 * @param array|bool $ids Lista de errores; o TRUE si se cumple la validación.
	 */
	private function validateUploadFiles(&$postData, $postFiles, $photoField = 'photo')
	{
		$validation = \Config\Services::validation();
		$validation->setRules([$photoField => "permit_empty|uploaded[{$photoField}]|is_image[{$photoField}]|max_size[{$photoField},1024]"]);
		if ($validation->run($postFiles)) {
			$file = get_dot_array($postFiles, $photoField);
			if (isset($file)) {
				// Se establece el nombre del archivo en el objeto de datos del registro (si aplica).
				$name = $file->getName();
				if (isset($name) && !empty($name)) {
					// Se obtiene la extensión del archivo seleccionado
					$ext = explode(".", $name);
					// Se establece el nuevo nombre al archivo subido
					$postData[$photoField] = random_int(100, 999) . round(microtime(true)) . '.' . end($ext);

					$postData['file'] = $file;
				}
			}
			return true;
		} else {
			return $validation->getErrors();
		}
	}

	/**
	 * Establece el valor del slug a un determinado registro.
	 * @param array $record Información del registro a insertar en base de datos.
	 * @param array $slugSettings Propiedades de la librería Slug.
	 * @param array|bool $ids Lista de Ids del registro; o FALSE si no existe.
	 */
	private function setSlugValue(&$record, $slugSettings, $ids = false)
	{
		unset($record[$slugSettings['field']]);

		// Se inicializa la librería
		$slugSettings = array_merge(['table' => $this->table, 'replacement' => '_'], $slugSettings);
		$Slug = new \App\Libraries\Slug($slugSettings);

		// Se obtiene el valor del slug para el registro
		$slug = $Slug->create_uri($record, $ids);
		if ($slug !== false) {
			$record[$slugSettings['field']] = $slug;
		}
	}
}
