<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class CodeAde
 *
 * Code ADE entity
 *
 * @package Models
 */
class CodeAde extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string (year | group | halfGroup)
     */
    private string $type;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string | int
     */
    private int | string $code;

    /**
     * @var int
     */
    private int $dept_id;

	/**
	 * Inserts a new record into the ecran_code_ade table with the specified type, title, and code.
	 *
	 * @return string The ID of the newly inserted record.
	 */
    public function insert() : string
    {
        $database = $this->getDatabase();
        $request = $database->prepare('INSERT INTO ecran_code_ade (type, title, code, dept_id) VALUES (:type, :title, :code, :dept_id)');

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);
        $request->bindValue(':dept_id', $this->getDeptId(), PDO::PARAM_INT);

        $request->execute();

        return $database->lastInsertId();
    }

	/**
	 * Update an existing record in the ecran_code_ade table with the current object's data
	 *
	 * @return int Number of rows affected by the update operation
	 */
    public function update(): int {
        $request = $this->getDatabase()->prepare('UPDATE ecran_code_ade SET title = :title, code = :code, type = :type WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':code', $this->getCode(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);

        $request->execute();

        return $request->rowCount();
    }

	/**
	 * Deletes a record from the ecran_code_ade table based on the current object's ID
	 *
	 * @return int The number of rows affected by the delete operation
	 */
    public function delete(): int {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_code_ade WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

	/**
	 * Retrieve an ADE code entity by its ID.
	 *
	 * @param $id
	 *
	 * @return array|false
	 */
    public function get($id): array|false {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade WHERE id = :id LIMIT 1');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

	/**
	 * Retrieves a list of ADE codes, ordered by ID in descending order, with a limit of 1000 records.
	 *
	 * @return array
	 */
    public function getList(): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type FROM ecran_code_ade ORDER BY id DESC LIMIT 1000');

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Checks the database for entries matching the given title or code.
	 *
	 * @param string $title The title to search for in the database.
	 * @param string $code The code to search for in the database.
	 *
	 * @return array The list of matching entities retrieved from the database.
	 */
    public function checkCode(string $title,string $code): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type, dept_id FROM ecran_code_ade WHERE title = :title OR code = :code LIMIT 2');

        $request->bindParam(':title', $title, PDO::PARAM_STR);
        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Retrieves all entries from the database matching the given type.
	 *
	 * @param mixed $type The type to filter the database records by.
	 *
	 * @return array The list of matching entities retrieved from the database.
	 */
    public function getAllFromType(mixed $type): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type, dept_id FROM ecran_code_ade WHERE type = :type ORDER BY id DESC LIMIT 500');

        $request->bindParam(':type', $type, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Retrieves a single entity from the database based on the provided code.
	 *
	 * @param string $code The code to search for in the database.
	 *
	 * @return CodeAde The entity retrieved from the database, or null if no match is found.
	 */
    public function getByCode(string $code): CodeAde {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type, dept_id FROM ecran_code_ade WHERE code = :code LIMIT 1');

        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

	/**
	 * Retrieves a list of entries associated with the given alert ID.
	 *
	 * @param int $id The alert ID used to filter the results.
	 *
	 * @return array The list of entities linked to the specified alert ID.
	 */
    public function getByAlert(int $id): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, code, type, dept_id FROM ecran_code_ade JOIN ecran_code_alert ON ecran_code_ade.id = ecran_code_alert.code_ade_id WHERE alert_id = :id LIMIT 100');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Creates and sets an entity using the provided data.
	 *
	 * @param array $data The data used to populate the entity, including 'id', 'title', 'code', and 'type'.
	 *
	 * @return CodeAde The entity populated with the provided data.
	 */
    public function setEntity($data): CodeAde {
        $entity = new CodeAde();

        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setCode($data['code']);
        $entity->setType($data['type']);
        $entity->setDeptId($data['dept_id']);

        return $entity;
    }

	/**
	 * Converts an array of data entries into a list of entities.
	 *
	 * @param array $dataList The list of data entries to be transformed into entities.
	 *
	 * @return array The list of entities created from the provided data entries.
	 */
    public function setEntityList($dataList): array {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param int|string $code
     */
    public function setCode(int|string $code): void {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getDeptId(): int
    {
        return $this->dept_id;
    }


    /**
     * @param int $dept_id
     */
    public function setDeptId(int $dept_id): void
    {
        $this->dept_id = $dept_id;
    }

	/**
	 * Prepares the object data for JSON serialization.
	 *
	 * @return mixed The data to be serialized, represented as an associative array of object properties.
	 */
	public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }
}
