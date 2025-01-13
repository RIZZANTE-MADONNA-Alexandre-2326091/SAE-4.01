<?php


namespace Models;

/**
 * Interface Entity
 *
 * Link the database tables to the PHP code
 *
 * @package Models
 */
interface Entity
{

	/**
	 * Insert a new entity into the repository
	 *
	 * @return string
	 */
    public function insert(): string;

	/**
	 * Update the entity with new data
	 *
	 * @return int Returns true on success or false on failure
	 */
    public function update(): int;

	/**
	 * Delete an entity
	 *
	 * @return int
	 */
    public function delete(): int;

	/**
	 * Retrieve an entity by its ID
	 *
	 * @param int $id The identifier of the entity to retrieve
	 *
	 * @return mixed
	 */
    public function get($id): mixed;

	/**
	 * Retrieves a list of items or data.
	 *
	 * This method is responsible for providing a collection of elements,
	 * typically used to access or manipulate grouped data. The specific
	 * implementation and return type depend on the use case in the given context.
	 *
	 * @return mixed A list or collection containing the requested data.
	 */
    public function getList(): mixed;

	/**
	 * Sets the entity data.
	 *
	 * This method is used to provide or update the data for a specific entity.
	 * The provided data will typically be processed and stored within the system.
	 *
	 * @param mixed $data The data to associate with the entity.
	 */
    public function setEntity($data): mixed;

	/**
	 * Sets a list of entities.
	 *
	 * This method is responsible for assigning a collection of entities to be used
	 * or processed within the system. The structure and content of the provided
	 * data list should align with the expected format for proper functionality.
	 *
	 * @param mixed $dataList A collection or list of entities to be set.
	 */
    public function setEntityList($dataList): mixed;
}
