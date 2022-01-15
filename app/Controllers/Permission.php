<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\BaseResource;

class Permission extends BaseResource
{
	use ResponseTrait;

	protected $modelName = 'App\Models\PermissionModel';

	/**
	 * @var \App\Models\PermissionModel
	 */
	protected $model;

	protected $helpers = ['app'];

	public function index($userId)
	{
		$validateUser = $this->validateUser($userId);
		if ($validateUser !== true) {
			return $validateUser;
		}

		$filter = $this->request->getGet();
		set_pagination($filter);

		$userPermissions = $this->model->list($filter, $userId);
		return $this->respond($userPermissions);
	}

	public function create($userId)
	{
		$validateUser = $this->validateUser($userId);
		if ($validateUser !== true) {
			return $validateUser;
		}

		// Datos de entrada de la petición
		$postData = $this->request->getPost();
		$postFiles = $this->request->getFiles();

		// Se valida si no existen datos enviados por método POST
		if (empty($postData) && empty($postFiles)) {
			return $this->fail('Please define the data to be recorded');
		}

		$postData['username'] = $userId;

		// Se valida los datos de la petición
		$validateRecord = $this->model->validateRecord($postData, $postFiles, 'post');
		if ($validateRecord !== true) {
			return $this->respond(['errors' => $validateRecord], 400);
		}

		$result = $this->model->insertRecord($postData);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully created", 201);
	}

	public function delete($userId, $moduleId)
	{
		$validateUser = $this->validateUser($userId);
		if ($validateUser !== true) {
			return $validateUser;
		}

		$permission = $this->model->check($userId, $moduleId);
		if (!$permission) {
			return $this->failNotFound('Record not found');
		}

		$result = $this->model->deleteRecord($userId, $moduleId);
		if ($result !== true) {
			// Se retorna un mensaje de error si las validaciones no se cumplen
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}

	private function validateUser($userId)
	{
		$userModel = new \App\Models\UserModel();
		$validationId = $userModel->validateId($userId);
		if ($validationId !== true) {
			return $this->respond(['errors' => $validationId], 400);
		}

		$user = $userModel->get($userId);
		if (!isset($user)) {
			return $this->failNotFound('User not found');
		}
		return true;
	}
}
