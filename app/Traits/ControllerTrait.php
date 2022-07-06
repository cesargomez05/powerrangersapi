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
			return $this->respond(['errors' => $result], 500);
		}

		return $this->success("Record successfully deleted");
	}
}
