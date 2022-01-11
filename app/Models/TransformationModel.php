<?php

namespace App\Models;

class TransformationModel extends APIModel
{
	// Atributos de la clase Model
	protected $table = 'transformations';

	// Atributos de la clase BaseModel
	protected $allowedFields = ['slug', 'name', 'description'];
	protected $validationRules = [
		'slug' => 'required|max_length[100]',
		'name' => 'required|max_length[100]',
		'description' => 'permit_empty'
	];

	public function validateRecord(&$filesData, $property, &$postData, $postFiles, $ids, $method, $record = null, $nodes = [])
	{
		// Se valida si el proceso corresponde a un nuevo registro de serie
		if ($method == 'post') {
			// Se verifica el índice de archivos subidos de cada uno de los rangers
			if (isset($postFiles['rangers'])) {
				// Se establece
				if (!isset($postData['rangers'])) {
					$postData['rangers'] = [];
				}

				// Se recorre el índice de archivos para valida el índice de datos de cada uno de los rangers
				foreach ($postFiles['rangers'] as $key => $value) {
					if (!isset($postData['rangers'][$key])) {
						$postData['rangers'][$key] = [];
					}
				}
			}

			// Se valida si existe datos de al menos 1 ranger
			if (!isset($postData['rangers']) || !is_array($postData['rangers']) || count($postData['rangers']) == 0) {
				return ['rangers' => 'Please set at least one ranger'];
			}

			// Se valida los datos de cada uno de los rangers a asociar en la transformación, a traves de la respectiva clase Model
			$transformationRangerModel = new TransformationRangerModel();
			// Se omite la validación del Id de la transformación que se va a crear
			$transformationRangerModel->removeValidationRule('transformationId');

			// Se recorre la lista de rangers para validar los datos de cada uno de ellos
			$errors = [];
			foreach ($postData['rangers'] as $key => &$value) {
				// Se ejecuta la validación de los datos de cada uno de los rangers
				$validRecord = $transformationRangerModel->validateRecord($filesData['rangers'], $key, $value, $postFiles, [], 'post', $record, array_merge($nodes, ['rangers', $key]));
				if ($validRecord !== true) {
					$errors[$key] = $validRecord;
				}
			}
			if (count($errors)) {
				return ['rangers' => $errors];
			}
		}
		return parent::validateRecord($filesData, $property, $postData, $postFiles, $ids, $method, $record, $nodes);
	}
}
