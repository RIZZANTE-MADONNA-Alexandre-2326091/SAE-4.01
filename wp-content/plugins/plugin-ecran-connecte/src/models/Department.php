<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class Department
 *
 * Department entity
 *
 * @package Models
 */
class Department extends Model implements Entity, JsonSerializable
{
	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var string
	 * */
	private string $nameDept;

	/**
	 * Inserts a new department into the database.
	 *
	 * @return string The ID of the newly inserted department.
	 */
	public function insert(): string {
		$database = $this->getDatabase();
		$request = $database ->prepare("INSERT INTO ecran_department (name) VALUES (:name)");

		$request->bindValue(':name', $this->getName(), PDO::PARAM_STR);

		$request->execute();

		return $database->lastInsertId();
	}

	/**
	 * Updates the department information in the database based on the current object properties.
	 *
	 * @return int Number of rows affected by the update query.
	 */
	public function update(): int {
		$request = $this->getDatabase()->prepare("UPDATE ecran_department SET name = :name WHERE dept_id = :id");

		$request->bindValue(':name', $this->getName(), PDO::PARAM_STR);
		$request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * Deletes the department record identified by its ID from the database.
	 *
	 * @return int The number of rows affected by the delete operation.
	 */
	public function delete(): int {
		$request = $this->getDatabase()->prepare('DELETE FROM ecran_department WHERE dept_id = :id');

		$request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * @param array $data The data to map to the Department entity, containing keys 'dept_id' and 'name'.
	 *
	 * @return Department The Department entity with the mapped data.
	 */
	public function setEntity( $data ):Department {
		$entity = new Department();

		$entity->setId($data['dept_id']);
		$entity->setName($data['name']);

		return $entity;
	}

	/**
	 * @param array $dataList The list of data to be converted into entities.
	 *
	 * @return array|Department Returns an array of Department entities.
	 */
	public function setEntityList( $dataList ): array | Department {
		$listEntity = array();
		foreach ($dataList as $data){
			$listEntity[] = $this->setEntity($data);
		}
		return $listEntity;
	}

	/**
	 * @param int $id
	 *
	 * @return Department|false
	 */
	public function get($id): false | Department {
		$request = $this->getDatabase()->prepare("SELECT dept_id, name FROM ecran_department WHERE dept_id = :id LIMIT 1");

		$request->bindParam(':id', $id, PDO::PARAM_INT);

		$request->execute();

		if ($request->rowCount() > 0) {
			return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
		}
		return false;
	}

	/**
	 * Retrieves all departments from the database.
	 *
	 * @return array An array of department entities.
	 */
	public function getAll(): array {
		$request = $this->getDatabase()->prepare("SELECT dept_id, name FROM ecran_department ORDER BY dept_id");

		$request->execute();

		return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Retrieves a list of departments from the database, starting at a specified position and retrieving a specified number of elements.
	 *
	 * @param int $begin The starting position for the query (default is 0).
	 * @param int $numberElement The number of elements to retrieve (default is 25).
	 *
	 * @return array Returns an array containing the list of departments, or an empty array if no departments are found.
	 */
	public function getList(int $begin = 0, int $numberElement = 25): array {
		$request = $this->getDatabase()->prepare("SELECT dept_id, name FROM ecran_department ORDER BY dept_id ASC LIMIT :begin, :numberElement");

		$request->bindValue(':begin', $begin, PDO::PARAM_INT);
		$request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);

		$request->execute();

		if ($request->rowCount() > 0) {
			return $this->setEntityList($request->fetchAll());
		}
		return [];
	}

	/**
	 * Retrieves department information based on the given department name.
	 *
	 * @param string $name The name of the department to search for in the database.
	 *
	 * @return array Returns an array containing department details that match the given name, or an empty array if no match is found.
	 */
	public function getDepartmentByName(string $name): array {
		$request = $this->getDatabase()->prepare("SELECT dept_id, name FROM ecran_department WHERE name = :name LIMIT 1");

		$request->bindValue(':name', $name, PDO::PARAM_STR);

		$request->execute();

		return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * Retrieves a list of departments associated with a given user.
	 *
	 * @param int $userId The ID of the user whose associated departments are to be retrieved.
	 *
	 * @return Department Returns an array of departments, where each department contains its ID and name.
	 */
    public function getUserInDept(int $userId): ?Department
    {
        $request = $this->getDatabase()->prepare("
        SELECT ed.dept_id, ed.name 
        FROM ecran_department ed
        LEFT JOIN ecran_dept_user edu ON edu.dept_id = ed.dept_id
        WHERE edu.user_id = :userId
    ");

        $request->bindValue(':userId', $userId, PDO::PARAM_INT);
        $request->execute();

        $data = $request->fetch(PDO::FETCH_ASSOC);

        // Gérer le cas où il n'y a pas de département
        if (empty($data['dept_id'])) {
            return null;
        }

        return $this->setEntity($data);
    }

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @param int
	 * */
	public function setId(int $id ): void {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
    public function getName(): string {
        return $this->nameDept;
    }

	/**
	* @param string
	 * */
	public function setName(string $nameDept ): void {
		$this->nameDept = $nameDept;
	}

	public function jsonSerialize(): array {
		return get_object_vars($this);
	}
}