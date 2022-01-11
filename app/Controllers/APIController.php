<?php

namespace App\Controllers;

use CodeIgniter\RESTful\BaseResource;
use CodeIgniter\API\ResponseTrait;

class APIController extends BaseResource
{
	use ResponseTrait;

	/**
	 * Nombre de la vista en base de datos.
	 * @var string
	 */
	protected $viewName;

	/**
	 * Mensaje de ids duplicados.
	 * @var string
	 */
	protected $existsRecordMessage = 'The ids value is used by other record';

	/**
	 * Mensaje de registro 'padre' no encontrado.
	 * @var string
	 */
	protected $parentRecordNotFoundMessage = 'Parent record not found';

	/**
	 * Mensaje de registro no encontrado.
	 * @var string
	 */
	protected $recordNotFoundMessage = 'Not record found';

	/**
	 * Mensaje de objeto de datos vació al crear un nuevo registro.
	 * @var string
	 */
	protected $emptyRecordToCreateMessage = 'Please define the data to be recorded';

	/**
	 * Mensaje de objeto de datos vació al crear un nuevo registro.
	 * @var string
	 */
	protected $emptyRecordToUpdateMessage = 'Please define the data to be updated';

	// Helpers
	protected $helpers = ['app'];

	public function index()
	{
		// Se obtiene los parámetros de consulta de registros
		$filter = $this->request->getGet();
		set_pagination($filter);

		// Parámetros definidos en la función
		$ids = func_get_args();

		// Se ejecuta el método que valida si existe el registro del elemento 'padre' (si aplica)
		if (method_exists($this, 'checkParentRecord')) {
			if (!$this->{'checkParentRecord'}($ids)) {
				return $this->failNotFound($this->parentRecordNotFoundMessage);
			}
		}

		// Se ejecuta la consulta de registros
		$records = $this->model->getRecordsByFilter($filter, $ids);
		return $this->respond($records);
	}

	public function show()
	{
		// Se obtiene y se valida los parámetros definidos en la función
		$ids = func_get_args();

		// Se ejecuta el método que valida si existe el registro del elemento 'padre' (si aplica)
		if (method_exists($this, 'checkParentRecord')) {
			if (!$this->{'checkParentRecord'}($ids, true)) {
				return $this->failNotFound($this->parentRecordNotFoundMessage);
			}
		}

		// Se obtiene el registro de la base de datos con base a los Ids
		$response = ['record' => $this->model->getRecord($ids)];
		if (!$response['record']) {
			return $this->failNotFound('Record not found');
		}
		return $this->respond($response);
	}

	public function create()
	{
		// Se obtiene y se valida los parámetros definidos en la función
		$ids = func_get_args();

		// Se ejecuta el método que valida si existe el registro del elemento 'padre' (si aplica)
		if (method_exists($this, 'checkParentRecord')) {
			if (!$this->{'checkParentRecord'}($ids)) {
				return $this->failNotFound($this->parentRecordNotFoundMessage);
			}
		}

		// Se obtiene y se valida los datos enviados en el cuerpo de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail($this->emptyRecordToCreateMessage);
		}

		// Datos correspondientes a los archivos subidos previamente
		$filesData = [];

		// Se invoca la función que valida los registros asociados
		$validRecord = $this->model->validateRecord($filesData, 'record', $postData, $postFiles, $ids, 'post');
		if ($validRecord !== true) {
			return $this->respond(['errors' => $validRecord], 400);
		}

		// Se valida si existe un registro cuyos Id corresponda al del elemento a registrar
		if ($this->model->existsRecord($postData)) {
			return $this->fail($this->existsRecordMessage);
		}

		// Se ejecuta el proceso de insertar el registro en la base de datos
		$result = $this->insertRecord($postData, $filesData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 400);
		}

		// Se retorna la información del registro creado
		return $this->respondCreated($postData);
	}

	public function update()
	{
		// Se obtiene y se valida los parámetros definidos en la función
		$ids = func_get_args();

		// Se ejecuta el método que valida si existe el registro del elemento 'padre' (si aplica)
		if (method_exists($this, 'checkParentRecord')) {
			if (!$this->{'checkParentRecord'}($ids, true)) {
				return $this->failNotFound($this->parentRecordNotFoundMessage);
			}
		}

		// Se obtiene el registro de la base de datos
		$record = $this->model->getRecord($ids);
		if (!$record) {
			return $this->failNotFound($this->recordNotFoundMessage);
		}

		// Se obtiene y se valida los datos enviados en el cuerpo de la petición
		$postData = $this->request->getPost();
		unset($postData['_method']);
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail($this->emptyRecordToCreateMessage);
		}

		// Se obtiene el tipo de petición que se realiza a la función (PUT o PATCH)
		$request = service('request');
		$method = $request->getMethod();

		// Datos correspondientes a los archivos subidos previamente
		$filesData = [];

		// Se invoca la función que valida los registros asociados
		$validRecord = $this->model->validateRecord($filesData, 'record', $postData, $postFiles, $ids, $method, $record);
		if ($validRecord !== true) {
			return $this->respond(['errors' => $validRecord], 400);
		}

		// Se valida si existe un registro cuyos Id corresponda al del elemento a registrar
		if ($this->model->existsRecord($postData, $ids)) {
			return $this->fail($this->existsRecordMessage);
		}

		// Se ejecuta el proceso de actualizar los datos del registro
		$result = $this->updateRecord($ids, $postData, $filesData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 400);
		}

		return $this->success("Record successfully updated");
	}

	public function delete()
	{
		// Se obtiene y se valida los parámetros definidos en la función
		$ids = func_get_args();

		// Se ejecuta el método que valida si existe el registro del elemento 'padre' (si aplica)
		if (method_exists($this, 'checkParentRecord')) {
			if (!$this->{'checkParentRecord'}($ids, true)) {
				return $this->failNotFound($this->parentRecordNotFoundMessage);
			}
		}

		// Se obtiene el registro de la base de datos
		$record = $this->model->getRecord($ids);
		if (!$record) {
			return $this->failNotFound($this->recordNotFoundMessage);
		}

		// Se ejecuta el método que permite validar registros anidados al que se desea eliminar (si aplica)
		if (is_callable([$this, 'validateDeleteRecord'])) {
			$validate = call_user_func_array([$this, 'validateDeleteRecord'], $ids);
			if ($validate !== true) {
				return $this->respond(['errors' => $validate], 400);
			}
		}

		// Se ejecuta la sentencia para eliminar el registro de la base de datos
		$result = $this->model->deleteRecord($ids);
		if ($result === false) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $this->model->errors()], 400);
		}

		return $this->success("Record successfully deleted");
	}

	public function getList()
	{
		$uris = func_get_args();

		// Se obtiene los parámetros de consulta de registros
		$filter = $this->request->getGet();
		set_pagination($filter);

		// Se ejecuta la consulta de registros
		$records = $this->model->getList($filter, $uris);
		return $this->respond($records);
	}

	public function getRecordByURI()
	{
		$uris = func_get_args();

		// Se obtiene los datos del registro
		$response = ['record' => $this->model->getRecordByURI($uris)];
		if (!$response['record']) {
			return $this->failNotFound('Record not found');
		}

		// Se ejecuta el método que permite adicionar información adicional al registro (si aplica)
		if (is_callable([$this, 'addRecordInformation'])) {
			// Se inserta en la primer posición del array el objeto de datos de retorno
			array_splice($uris, 0, 0, [&$response]);
			// Se ejecuta el método con los parámetros contemplados en el array $uris
			call_user_func_array([$this, 'addRecordInformation'], $uris);
		}

		return $this->respond($response);
	}

	protected function insertRecord(&$postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se procede a insertar el registro en la base de datos
		$record = $this->model->insertRecord($postData);
		if (isset($record['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $record['error'];
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se procede a mover los archivos asociados al registro
		$this->moveRecordFiles($filesData, $postData);

		// Se retorna true para indicar que la función se ejecutó correctamente
		return true;
	}

	protected function updateRecord($ids, $postData, $filesData)
	{
		// Se inicializa una transacción sobre la base de datos
		$this->model->db->transBegin();

		// Se invoca la función que actualiza los datos
		$result = $this->model->updateRecord($ids, $postData);
		if (isset($result['error'])) {
			// Se retorna los mensajes de error de la validación
			$this->model->db->transRollback();
			return $result['error'];
		}

		// Se finaliza la transacción
		$this->model->db->transCommit();

		// Se procede a mover los archivos asociados al registro
		$this->moveRecordFiles($filesData, $postData);

		// Se retorna true para indicar que la función se ejecutó correctamente
		return true;
	}

	protected function moveRecordFiles($filesData, $postData)
	{
		// Se procede a mover el archivo subido a la carpeta destinada para ello (si aplica)
		if (isset($filesData['record'])) {
			$this->moveFiles($filesData['record'], $postData);
		}
	}

	protected function moveFiles($fileData, $postData)
	{
		// Se procede a mover el archivo subido a la carpeta destinada para ello (si aplica)
		if (isset($fileData['property']) && isset($fileData['file'])) {
			if (isset($postData[$fileData['property']])) {
				$fileData['file']->move("images", $postData[$fileData['property']]);
			}
		}
	}
}
