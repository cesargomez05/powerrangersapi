<?php

namespace App\Models;

use CodeIgniter\Model;

class APIModel extends Model
{
	/**
	 * Campo para validar la fotografía.
	 * @var string
	 */
	protected $photoField;

	/**
	 * Columnas de llave primaria asociadas al modelo.
	 * @var array
	 */
	protected $primaryKeys = [];

	/**
	 * Lista de campos de búsqueda.
	 * @var array
	 */
	protected $filterColumns = ['name'];

	/**
	 * Columna de donde se obtendrá el valor del slug.
	 * @var string
	 */
	protected $columnValue = 'name';

	/**
	 * Columna donde se establecerá el valor del slug.
	 * @var string
	 */
	protected $uriColumn = 'slug';

	/**
	 * Nombre de la vista de donde se obtiene los datos.
	 * @var string
	 */
	protected $viewName;

	/**
	 * Columnas para filtrar registros por URI's.
	 * @var array
	 */
	protected $uriColumns;

	/**
	 * Columnas para retornar en la consulta de registros por URI's.
	 * @var array
	 */
	protected $viewColumns = ['slug', 'name'];

	/**
	 * Reglas de validación para la creación de registros.
	 * @var array|string
	 */
	protected $validationRulesCreate = [];

	/**
	 * Reglas de validación para la actualización de registros.
	 * @var array|string
	 */
	protected $validationRulesUpdate = [];

	/**
	 * Realiza la consulta de regitros en la base de datos.
	 * @param array $filter Parámetros de consulta.
	 */
	public function getRecordsByFilter($filter, $ids = null)
	{
		$response = [];

		// Se define en la condición el valor de los Ids (si aplica)
		$this->setConditionIds($ids);

		// Se ejecuta la consulta de los registros
		$this->getRecords($filter, $response);

		return $response;
	}

	/**
	 * Obtiene la lista de registros para el API.
	 */
	public function getList($filter, $uris = null)
	{
		$response = [];

		// Se define la sentencia para los registros de la vista
		$this->setViewConditions();

		// Se define en la sentencia where de la consulta las URIS del registro
		$this->setConditionURIs($uris);

		// Se ejecuta la consulta de los registros
		$this->getRecords($filter, $response);

		return $response;
	}

	/**
	 * Obtiene el registro asociado al Id.
	 * @param array $ids Ids asociados al modelo.
	 */
	public function getRecord($ids)
	{
		// Se define en la sentencia where de la consulta el o los Ids del registro
		$this->setConditionIds($ids);

		// Se ejecuta la consulta del registro
		$record = $this->findAll();

		// Se retorna el registro (si existe)
		return count($record) ? $record[0] : false;
	}

	/**
	 * Obtiene los datos del registro por los valores del slug definidos por parámetros
	 * en el llamado del método.
	 */
	public function getRecordByURI($uris)
	{
		// Se cambia la tabla a la vista (si existe)
		if (isset($this->viewName) && !empty($this->viewName)) {
			$this->setTable($this->viewName);
		} else {
			// Se indica las columnas a retornar en la consulta
			$this->select($this->allowedFields);
		}

		// Se define en la sentencia where de la consulta las URIS del registro
		$this->setConditionURIs($uris);

		// Se ejecuta la consulta
		$row = $this->get()->getRow();

		// Se retorna el registro (si existe)
		return $row !== null ? $row : false;
	}

	/**
	 * Ejecuta el proceso de insertar el registro en la base de datos.
	 * @param object $record Objeto de datos a insertar.
	 * @return integer|boolean|string Id del registro insertado.
	 */
	public function insertRecord(&$record)
	{
		$response = [];

		// Se ejecuta el método encargado de insertar los registros anidados (si aplica)
		if (method_exists($this, 'insertNestedRecords')) {
			$result = $this->{'insertNestedRecords'}($record);
			if ($result !== true) {
				$response['error'] = $result;
				return $response;
			}
		}

		// Se ejecuta la sentencia INSERT del registro
		$recordId = $this->insert($record);
		if ($recordId === false) {
			$response['error'] = $this->errors();
			return $response;
		}

		if ($recordId !== 0) {
			$record[$this->primaryKey] = $recordId;
			$response['primaryKey'] = $recordId;
		}

		return $response;
	}

	/**
	 * Ejecuta el proceso de actualización de datos en la base de datos.
	 * @param string $id Id del registro.
	 * @param string $method Método HTTP del cual se invoca la función (PUT o PATCH).
	 * @param object $data Objeto de datos a actualizar.
	 */
	public function updateRecord($ids, array $record)
	{
		$response = [];

		// Se ejecuta el método encargado de insertar los registros anidados (si aplica)
		if (method_exists($this, 'insertNestedRecords')) {
			$result = $this->{'insertNestedRecords'}($record);
			if ($result !== true) {
				$response['error'] = $result;
				return $response;
			}
		}

		// Se define en la sentencia where de la consulta el o los Ids del registro
		$this->setConditionIds($ids);

		// Se ejecuta la sentencia UPDATE
		$result = $this->update(null, $record);
		if ($result === false) {
			$response['error'] = $this->errors();
			return $response;
		}
	}

	public function deleteRecord($ids)
	{
		// Se define en la sentencia where de la consulta el o los Ids del registro
		$this->setConditionIds($ids);

		// Se ejecuta la sentencia DELETE
		return $this->delete();
	}

	/**
	 * Valida los datos del registro con los valores de llave primaria compuesta.
	 * @param object $record Datos del registro.
	 * @param array $ids Ids principales del registro.
	 * @return bool Valor que indica si las reglas de validación se cumplen (true) o no (false).
	 */
	public function validateRecordFields(&$record, $ids, $method, $prevRecord = null): bool
	{
		$hasPrimaryKeys = false;

		// Se valida si el modelo tiene definido llaves primarias compuestas, para establecer los valores (según sea el caso)
		if (isset($this->primaryKeys) && count($this->primaryKeys) > 0) {
			$hasPrimaryKeys = true;

			if ($method == 'post') {
				foreach ($ids as $i => $id) {
					if (isset($this->primaryKeys[$i])) {
						$record[$this->primaryKeys[$i]] = $id;
					}
				}
			}
		}

		if (in_array($method, ['put', 'patch'])) {
			$columnsId = $hasPrimaryKeys ? $this->primaryKeys : [$this->primaryKey];
			foreach ($columnsId as $i => $value) {
				if (isset($ids[$i])) {
					$record['_' . $value] = $ids[$i];
				}
			}
		}

		// Se establece las reglas de valicación
		$validationRules = $this->validationRules;
		if ($method == 'post') {
			if (isset($this->validationRulesCreate) && count($this->validationRulesCreate) > 0) {
				$validationRules = array_merge($validationRules, $this->validationRulesCreate);
			}
		} elseif (in_array($method, ['put', 'patch'])) {
			if (isset($this->validationRulesUpdate) && count($this->validationRulesUpdate) > 0) {
				$validationRules = array_merge($validationRules, $this->validationRulesUpdate);
			}
		}

		// Se establece el valor del Slug (si aplica)
		$this->setSlugValue($record, $hasPrimaryKeys, $ids);

		// Se valida los campos asociados al registro
		foreach (array_keys($validationRules) as $i => $key) {
			if (isset($record[$key])) {
				if (strlen($record[$key]) == 0) {
					$record[$key] = null;
				}
			} else {
				// Se comprueba si la propiedad no está en la lista de campos que pueden omitirse en la validación
				switch ($method) {
					case 'post':
					case 'put':
						if ($hasPrimaryKeys) {
							if (isset($ids[$i])) {
								$record[$key] = $ids[$i];
							}
						} else {
							$record[$key] = null;
						}
						break;
					case 'patch':
						if ($hasPrimaryKeys) {
							if (isset($ids[$i])) {
								$record[$key] = $ids[$i];
							}
						} else {
							$record[$key] = null;
						}
						break;
				}
			}
		}

		// Se ejecuta las reglas de validación
		$this->setValidationRules($validationRules);
		$this->validation->reset();
		if (!$this->validate($record)) {
			return false;
		}

		return true;
	}

	/**
	 * Consulta si existe registros cuyos Id correspondan a los definidos en el objeto de datos.
	 * @param object $record Datos del registro.
	 * @return bool Valor que indica si las reglas de validación se cumplen (true) o no (false).
	 */
	public function existsRecord($record, $ids = []): bool
	{
		// Se valida si el modelo tiene definido llaves primarias compuestas, para establecer los valores (según sea el caso)
		if (isset($this->primaryKeys) && count($this->primaryKeys) > 0) {
			$hasChange = count($ids) == 0 ? true : false;

			// Se define la condición de la sentencia a ejecutar para validar la existencia de registros
			foreach ($this->primaryKeys as $i => $column) {
				if (!isset($record[$column])) {
					$record[$column] = null;
				}
				if (isset($ids[$i])) {
					if ($ids[$i] !== $record[$column]) {
						$hasChange = true;
					}
				} else {
					if (!is_null($record[$column])) {
						$hasChange = true;
					}
				}
				$this->where($column, $record[$column]);
			}

			// Se consulta si existe algún registro con los ids definidos
			$builder = $this->builder();
			$count = $builder->countAllResults();
			if ($count) {
				// Se consulta si hubo cambios en alguno de los datos
				if ($hasChange) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Consulta si existen registros cuya llave foránea corresponde al registro que se desea eliminar.
	 * @param string $foreignKeyColumn Columna de la llave foránea.
	 * @param mixed $recordId Id del registro a validar.
	 */
	public function checkRecordsByForeignKey($foreignKeyColumns)
	{
		// Se establece las condiciones a validar
		foreach ($foreignKeyColumns as $column => $value) {
			$this->where($column, $value);
		}
		// Se obtiene el número de registros y se retorna si este es mayor a cero (true) o no (false)
		$count = $this->countAllResults();
		return (bool) $count > 0;
	}

	public function setSlugValue(&$record, $hasPrimaryKeys, $ids = false)
	{
		// Se genera el valor del URI asociado al registro (si aplica)
		if (isset($this->columnValue) && !empty($this->columnValue)) {
			// Se inicializa la librería
			$Slug = new \App\Libraries\Slug([
				'table' => $this->table, // Nombre de la tabla
				'title' => $this->columnValue, // Campo de donde se obtiene el valor
				'field' => $this->uriColumn, // Campo sobre el cual se establece el valor
				'id' => $hasPrimaryKeys ? $this->primaryKeys : [$this->primaryKey], // Campo del Id único
				'replacement' => '_' // Caracter de reemplazo de espacios y caracteres inválidos
			]);

			// Se obtiene el valor del slug para el registro
			$slug = $Slug->create_uri($record, $ids);
			if ($slug !== false) {
				$record[$this->uriColumn] = $slug;
			}
		}
	}

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		// Se valida los datos de los archivos adjuntos subidos
		$filesData[$property] = $this->checkUploadFile($postData, $postFiles, $nodes);
		if (isset($filesData[$property]['errors'])) {
			return $filesData[$property]['errors'];
		}

		// Se valida los datos del registro, contemplando la generación del SLUG
		$validation = $this->validateRecordFields($postData, $ids, $method, $record);
		if ($validation === false) {
			return $this->errors();
		}
		return true;
	}

	public function checkUploadFile(&$postData, $postFiles, $nodes)
	{
		$response = [];

		// Valor que indica si se debe validar datos de la imagen
		$validatePhoto = isset($this->photoField) && !empty($this->photoField);

		// Se valida los datos de la foto (si aplica)
		if ($validatePhoto && isset($postFiles)) {
			// Se concatena en la ruta de acceso el campo de la columna
			array_push($nodes, $this->photoField);
			$rule = join('.', $nodes);

			// Se ejecuta la regla de validación del archivo
			$validation = \Config\Services::validation();
			$validation->setRules([$rule => $this->getPhotoRules($rule)]);
			$validation->reset();
			if ($validation->run($postFiles)) {
				$file = get_dot_array($postFiles, $rule);
				if (isset($file)) {
					// Se establece el nombre del archivo en el objeto de datos del registro (si aplica).
					$name = $file->getName();
					if (isset($name) && !empty($name)) {
						// Se obtiene la extensión del archivo seleccionado
						$ext = explode(".", $name);
						// Se establece el nuevo nombre al archivo subido
						$postData[$this->photoField] = random_int(100, 999) . round(microtime(true)) . '.' . end($ext);

						// Se establece la propiedad donde se define el nombre del archivo
						$response['file'] = $file;
						$response['property'] = $this->photoField;
					}
				}
			} else {
				$response['errors'] = $validation->getErrors();
			}
		}

		return $response;
	}

	/**
	 * Elimina una regla de validación de la lista de opciones.
	 * @param string $fields Propiedad a eliminar.
	 */
	public function removeValidationRule($fields)
	{
		// Se obtiene los campos separados por coma, y se procede a eliminar cada uno de ellos
		$fields = explode(',', $fields);
		foreach ($fields as $field) {
			unset($this->validationRules[$field]);
			unset($this->validationRulesCreate[$field]);
			unset($this->validationRulesUpdate[$field]);
		}
	}

	/**
	 * Ejecuta la consulta de los registros
	 */
	protected function getRecords($filter, &$response)
	{
		// Se define los criterios de consulta de los registros (si aplica)
		if (isset($filter['q']) && !empty($filter['q'])) {
			$this->groupStart();
			foreach ($this->filterColumns as $value) {
				$this->orLike($value, $filter['q'], 'both');
			}
			$this->groupEnd();
		}

		// Número de registros asociados a la consulta
		$response['count'] = $this->countAllResults(false);

		// Se valida si el número de registros es menor a la cantidad de registros a consultar
		if ($response['count'] < $filter['pageSize'] * $filter['page']) {
			// División entera entre el número total de registros y el tamaño de página de los registros
			$filter['page'] = intdiv($response['count'], $filter['pageSize']) + 1;
		}

		$response['page'] = intval($filter['page']);
		$response['records'] = $this->findAll($filter['pageSize'], ($filter['page'] - 1) * $filter['pageSize']);
	}

	/**
	 * Establece la tabla y columnas sobre las cuales se ejecutará la consulta de información.
	 * @param array $columns Lista de columnas a establecer en la consulta.
	 */
	protected function setViewConditions($columns = null)
	{
		// Se cambia la tabla a la vista (si existe)
		if (isset($this->viewName) && !empty($this->viewName)) {
			$this->setTable($this->viewName);
		}

		// Se indica las columnas a retornar en la consulta
		if (isset($columns) && count($columns) > 0) {
			$this->select($columns);
		} else {
			if (isset($this->viewColumns)) {
				$this->select($this->viewColumns);
			} else {
				$this->select($this->allowedFields);
			}
		}
	}

	/**
	 * Establece el valor de los Id en la sentencia a ejecutar.
	 * @param array $ids Ids asociados al modelo.
	 */
	protected function setConditionIds($ids)
	{
		if (isset($ids) && count($ids) > 0) {
			// Columnas del Id asociado al registro
			$columnsId = isset($this->primaryKeys) && count($this->primaryKeys) > 0 ? $this->primaryKeys : [$this->primaryKey];

			// Se establece la condición en la consulta a ejecutar
			foreach ($ids as $index => $value) {
				if (isset($columnsId[$index])) {
					$this->where($columnsId[$index], $value);
				} else {
					$this->where($columnsId[$index], null);
				}
			}
		}
	}

	/**
	 * Establece el valor de las URIs en la sentencia a ejecutar.
	 * @param array $uris URIs asociadas a los registros de la consulta.
	 */
	protected function setConditionURIs($uris)
	{
		if (isset($uris) && count($uris) > 0) {
			// Columnas del Id asociado al registro
			$columnsSlug = isset($this->uriColumns) && count($this->uriColumns) > 0 ? $this->uriColumns : [$this->uriColumn];

			// Se establece la condición en la consulta a ejecutar
			foreach ($uris as $index => $value) {
				if (isset($columnsSlug[$index])) {
					$this->where($columnsSlug[$index], $value);
				} else {
					$this->where($columnsSlug[$index], null);
				}
			}
		}
	}

	protected function checkNestedRecords(&$postData, $postFiles, $property)
	{
		// Se valida si existe archivos adjuntos selecionados en la propiedad
		if (isset($postFiles[$property])) {
			// Se define la data asociada (si esta no se definió previamente)
			if (!isset($postData[$property])) {
				$postData[$property] = [];
			}
		}
	}

	protected function validateNestedRecord(&$errors, &$filesData, &$postData, &$postFiles, $property, $modelName, $foreignKeyColumn, $nodes)
	{
		// Se valida los datos del registro anidado
		$this->checkNestedRecords($postData, $postFiles, $property);
		if (isset($postData[$property])) {
			// Se invoca el modelo asociado al registro que se desea validar
			$model = model('App\\Models\\' . $modelName);
			// Se realiza la validación del registro
			$validRecord = $model->validateRecord($filesData, $property, $postData[$property], $postFiles, [], 'post', null, $nodes);
			if ($validRecord !== true) {
				$errors[$property] = $validRecord;
				// Se retorna al llamado de la función
				return;
			}
			// Se elimina la propiedad correspondiente a la llave foránea; así como la regla de validación asociada a la misma
			if (isset($foreignKeyColumn)) {
				unset($postData[$foreignKeyColumn]);
				$this->removeValidationRule($foreignKeyColumn);
			}
		}
	}

	protected function insertNestedRecord(&$errors, &$record, $property, $modelName, $foreignKeyColumn)
	{
		// Se inserta el registro anidado (si aplica)
		if (isset($record[$property])) {
			$model = model('App\\Models\\' . $modelName);
			$result = $model->insertRecord($record[$property]);
			if (isset($result['error'])) {
				$errors[$property] = $result['error'];
				// Se retorna al llamado de la función
				return;
			}
			$record[$foreignKeyColumn] = $result['primaryKey'];
		}
	}

	protected function getPhotoRules($rule)
	{
		return "permit_empty|uploaded[{$rule}]|is_image[{$rule}]|ext_in[{$rule},png,jpg,gif]|max_size[{$rule},1024]";
	}
}
