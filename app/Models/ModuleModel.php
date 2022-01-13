<?php

namespace App\Models;

use App\Traits\ModelTrait;
use CodeIgniter\Model;

class ModuleModel extends Model
{
	use ModelTrait;

	protected $table = 'modules';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['id', 'name'];
	protected $validationRules = [
		'id' => 'required|max_length[50]|is_unique[modules.id,id,{_id}]',
		'name' => 'required|max_length[50]'
	];

	protected function setRecordsCondition($query)
	{
		if (isset($query['q']) && !empty($query['q'])) {
			$this->groupStart();
			$this->orLike('name', $query['q'], 'both');
			$this->groupEnd();
		}
	}

	public function get($id)
	{
		$this->where('id', $id);
		$record = $this->findAll();
		return count($record) ? $record[0] : null;
	}
}
