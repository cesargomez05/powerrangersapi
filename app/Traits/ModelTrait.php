<?php

namespace App\Traits;

trait ModelTrait
{
	/**
	 * Obtiene la lista de registros.
	 * @param array $query Parámetros de consulta de los registros.
	 * @return array Resultado de la consulta.
	 */
	public function list($query, ...$ids)
	{
		if (method_exists($this, 'setRecordsCondition')) {
			array_unshift($ids, $query);
			call_user_func_array(array($this, "setRecordsCondition"), $ids);
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

	public function check(...$ids)
	{
		call_user_func_array(array($this, "setRecordCondition"), $ids);
		return $this->countAllResults() > 0;
	}

	public function get(...$ids)
	{
		call_user_func_array(array($this, "setRecordCondition"), $ids);
		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}

	public function insertRecord(&$record)
	{
		// Se procede a insertar el registro en la base de datos
		$recordId = $this->insert($record);
		if ($recordId === false) {
			return $this->errors();
		}

		if ($recordId !== 0) {
			$record[$this->primaryKey] = $recordId;
		}

		return true;
	}

	public function updateRecord($record, ...$ids)
	{
		call_user_func_array(array($this, "setRecordCondition"), $ids);
		$result = $this->update(null, $record);
		return $result === false ? $this->errors() : true;
	}

	public function validateNestedRecords(...$ids): array
	{
		$errors = [];
		if (method_exists($this, 'checkNestedRecords')) {
			$ids[] = &$errors;
			call_user_func_array(array($this, 'checkNestedRecords'), $ids);
		}
		return $errors;
	}

	public function deleteRecord(...$ids)
	{
		call_user_func_array(array($this, "setRecordCondition"), $ids);
		if (!$this->delete()) {
			return $this->errors();
		}
		return true;
	}

	public function validateId($id, $property = 'id', $label = 'Id')
	{
		$validation = \Config\Services::validation(null, false);
		$validation->setRule($property, $label, $this->getRulesId());
		if ($validation->run([$property => $id])) {
			return true;
		} else {
			return $validation->getErrors();
		}
	}

	public function getRulesId()
	{
		return $this->rulesId ?? 'required|is_natural_no_zero';
	}

	public function countByActorId($actorId): bool
	{
		$this->where('actorId', $actorId);
		return $this->countAllResults() > 0;
	}

	public function countByAgeId($ageId): bool
	{
		$this->where('ageId', $ageId);
		return $this->countAllResults() > 0;
	}

	public function countByArsenalId($arsenalId): bool
	{
		$this->where('arsenalId', $arsenalId);
		return $this->countAllResults() > 0;
	}

	public function countByCharacterId($characterId): bool
	{
		$this->where('characterId', $characterId);
		return $this->countAllResults() > 0;
	}

	public function countByMegazordId($megazordId): bool
	{
		$this->where('megazordId', $megazordId);
		return $this->countAllResults() > 0;
	}

	public function countByModuleId($moduleId): bool
	{
		$this->where('moduleId', $moduleId);
		return $this->countAllResults() > 0;
	}

	public function countByMorpherId($morpherId): bool
	{
		$this->where('morpherId', $morpherId);
		return $this->countAllResults() > 0;
	}

	public function countByRangerId($rangerId): bool
	{
		$this->where('rangerId', $rangerId);
		return $this->countAllResults() > 0;
	}

	public function countBySeasonId($serieId, $seasonNumber): bool
	{
		$this->where('serieId', $serieId)->where('seasonNumber', $seasonNumber);
		return $this->countAllResults() > 0;
	}

	public function countBySerieId($serieId): bool
	{
		$this->where('serieId', $serieId);
		return $this->countAllResults() > 0;
	}

	public function countByTransformationId($transformationId): bool
	{
		$this->where('transformationId', $transformationId);
		return $this->countAllResults() > 0;
	}

	public function countByUserId($userId): bool
	{
		$this->where('userId', $userId);
		return $this->countAllResults() > 0;
	}

	public function countByVillainId($villainId): bool
	{
		$this->where('villainId', $villainId);
		return $this->countAllResults() > 0;
	}

	public function countByZordId($zordId): bool
	{
		$this->where('zordId', $zordId);
		return $this->countAllResults() > 0;
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
				} else if (empty($postData[$field])) {
					$postData[$field] = null;
				}
			} else {
				if (!isset($postData[$field]) || empty($postData[$field])) {
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
		$validation = \Config\Services::validation(null, false);
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
