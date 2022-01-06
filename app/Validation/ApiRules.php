<?php

namespace App\Validation;

use Config\Database;

class ApiRules
{
	/**
	 * Year
	 *
	 * @param string|null $year
	 *
	 * @return boolean
	 */
	public function is_year(string $year): bool
	{
		if (isset($year) && !empty($year)) {
			$bool = preg_match('/^(?:19|[2-9][0-9])\d{2}$/', $year);
			return $bool == 0 ? false : true;
		}
		return false;
	}

	/**
	 * Exists Id
	 *
	 * @param string|null $year
	 *
	 * @return boolean
	 */
	public function exists_id(string $str = null, string $fields): bool
	{
		// Se obtiene el valor de la tabla y del campo
		sscanf($fields, '%[^.].%[^.]', $table, $field);

		$db = Database::connect($data['DBGroup'] ?? null);
		$row = $db->table($table)
			->select('1')
			->where($field, $str)
			->limit(1);

		return (bool) ($row->get()->getRow() !== null);
	}

	/**
	 * Valida y establece el valor del Id compuesto de un registro.
	 */
	public function check_id($str, string $fields, array &$data): bool
	{
		// Se obtiene los valores definidos en la función
		$fields = explode(',', $fields);

		// Propiedad donde se establece el valor concatenado del Id compuesto
		$propertyKey = $fields[0];
		// Columnas de donde se obtiene el valor del Id compuesto
		$columns = array_slice($fields, 1);

		$ids = [];
		$isValid = TRUE;
		foreach ($columns as $column) {
			preg_match('/([a-zA-Z0-9]+)(\?)*/', $column, $matches);

			if (isset($matches[1]) && strlen($matches[1])) {
				if (isset($data[$matches[1]])) {
					array_push($ids, $data[$matches[1]]);
				} else {
					$isValid = FALSE;
				}
			} else {
				$isValid = FALSE;
			}
			if (!$isValid && isset($matches[2])) {
				array_push($ids, '');
				$isValid = TRUE;
			}
		}
		if ($isValid) {
			$data[$propertyKey] = $ids;
		}
		return $isValid;
	}

	public function exists_record($str, string $fields, array &$data): bool
	{
		$fields = explode(',', $fields);

		// Propiedad donde se establece el valor concatenado del Id compuesto
		$propertyKey = $fields[0];
		$table = $fields[1];
		$columns = array_slice($fields, 2);

		// Id de los registros
		$ids = $data[$propertyKey];

		$db = Database::connect($data['DBGroup'] ?? null);
		$query = $db->table($table)->select('1');
		foreach ($columns as $i => $column) {
			if (isset($ids[$i])) {
				$query->where($column, $ids[$i]);
			}
		}
		$query->limit(1);

		unset($data[$propertyKey]);
		return (bool) ($query->get()->getRow() !== null);
	}

	/**
	 * Valida valores de Id separados por coma
	 */
	public function check_comma_separated($str = null): bool
	{
		// Se valida si hay valor definido en donde se invoca la regla de validación
		if (isset($str) && strlen($str)) {
			return preg_match('/^\d+(?:\,\d+)*$/', $str);
		}
		return TRUE;
	}

	/**
	 * Valida si existen los registros cuyo id se define separado por coma
	 */
	public function validate_children_ids($str = null, string $fields = null, $data = null): bool
	{
		// Se valida si hay valor definido en donde se invoca la regla de validación
		if (isset($str) && strlen($str)) {
			// Se obtiene el valor de la tabla y del campo
			sscanf($fields, '%[^.].%[^.]', $table, $columnId);

			// Se obtiene cada uno de los Ids, junto con la respectiva cantidad
			$ids = explode(',', $str);
			$count = count($ids);

			// Se consulta en la base de datos si el número de registros corresponde al número de Ids definidos
			$db = Database::connect($data['DBGroup'] ?? null);
			$query = $db->table($table)->select("COUNT(id) = $count AS total")->whereIn($columnId, $ids);
			$row = $query->get()->getRow();

			return $row->total;
		}
		return TRUE;
	}

	public function validate_password($password = null, string $fields = null, array $data = null, &$error = null)
	{
		if (isset($password) && strlen($password)) {
			$pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
			if (preg_match($pattern, $password)) {
				return TRUE;
			} else {
				$error = "Cesar";
			}
			return FALSE;
		}
		return TRUE;
	}
}
