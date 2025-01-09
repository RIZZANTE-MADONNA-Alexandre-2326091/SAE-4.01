<?php

namespace Models;

use JsonSerializable;
use PDO;
use PDOException;

/**
 * Class Location
 *
 * Location entity
 *
 * @package Models
 */
class Location extends Model implements Entity, JsonSerializable {

	/**
	 * @var int
	 */
	private int $id;

	/**
	 * @var float
	 */
	private float $longitude;

	/**
	 * @var float
	 */
	private float $latitude;

	/**
	 * @var int
	 */
	private int $id_user;


	/**
	 * Insert a location in the database.
	 *
	 * @return string
	 */
	public function insert(): string {
		$database = $this->getDatabase();

		try {
			$database->beginTransaction();

			$request = $database->prepare(
				"INSERT INTO ecran_location (longitude, latitude, user_id)
                VALUES (:longitude, :latitude, :id_user)");

			$request->bindValue(':longitude', $this->getLongitude());
			$request->bindValue(':latitude', $this->getLatitude());
			$request->bindValue(':id_user', $this->getIdUser());

			$request->execute();

			$lastId = $database->lastInsertId();
			$database->commit();

			return $lastId;

		} catch (PDOException $e) {
			error_log('Error during INSERT: ' . $e->getMessage());
			throw $e;
		}
	}

	/**
	 * Update a location in the database.
	 *
	 * @return int
	 */
	public function update(): int {
		try {
			$request = $this->getDatabase()->prepare(
				"UPDATE ecran_location SET longitude = :longitude, latitude = :latitude
             WHERE user_id = :user_id AND (longitude != :longitude OR latitude != :latitude"
			);

			$request->bindValue(':longitude', $this->getLongitude());
			$request->bindValue(':latitude', $this->getLatitude());
			$request->bindValue(':user_id', $this->getIdUser(), PDO::PARAM_INT);

			$request->execute();

			$rowsAffected = $request->rowCount(); // Nombre de lignes affectées
			error_log("Lignes affectées par la mise à jour : $rowsAffected");

			return $rowsAffected;
		} catch (PDOException $e) {
			error_log('Erreur lors de la mise à jour : ' . $e->getMessage());
			return 0;
		}
	}

	/**
	 * Delete a location in the database.
	 *
	 * @return int
	 */
	public function delete(): int {
		$request = $this->getDatabase()->prepare( 'DELETE FROM ecran_location WHERE id = :id' );

		$request->bindValue( ':id', $this->getId(), PDO::PARAM_INT );

		$request->execute();

		return $request->rowCount();
	}

	/**
	 * Retrieves an entity from the database based on the provided ID.
	 *
	 * @param int $id The ID of the entity to retrieve.
	 *
	 * @return array|false The entity data as an associative array if found, or false if no matching entity was found.
	 */
	public function get( $id ): Location|false {
		$request = $this->getDatabase()->prepare( "SELECT id, longitude, latitude, user_id FROM ecran_location WHERE id = :id LIMIT 1" );

		$request->bindParam( ':id', $id, PDO::PARAM_INT );

		$request->execute();

		if ( $request->rowCount() > 0 ) {
			return $this->setEntity($request->fetch( PDO::FETCH_ASSOC ));
		}

		return false;
	}

	/**
	 * Retrieves a list of entities from the database based on the given parameters.
	 *
	 * @param int $begin The starting position for the query results.
	 * @param int $numberElement The number of elements to retrieve.
	 *
	 * @return array The list of entities fetched from the database.
	 */
	public function getList( int $begin = 0, int $numberElement = 25 ): array {
		$request = $this->getDatabase()->prepare( "SELECT id, longitude, latitude, user_id  FROM ecran_location ORDER BY id ASC LIMIT :begin, :numberElement" );

		$request->bindValue( ':begin', $begin, PDO::PARAM_INT );
		$request->bindValue( ':numberElement', $numberElement, PDO::PARAM_INT );

		$request->execute();

		if ( $request->rowCount() > 0 ) {
			return $this->setEntityList( $request->fetchAll() );
		}

		return [];
	}

	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}

	/**
	 * @param int $id
	 *
	 * @return void
	 */
	public function setId( int $id ): void {
		$this->id = $id;
	}

	/**
	 * @return float
	 */
	public function getLongitude(): float {
		return $this->longitude;
	}

	/**
	 * @param string $longitude
	 *
	 * @return void
	 */
	public function setLongitude( string $longitude ): void {
		$this->longitude = $longitude;
	}

	/**
	 * @return float
	 */
	public function getLatitude(): float {
		return $this->latitude;
	}

	/**
	 * @param string $latitude
	 *
	 * @return void
	 */
	public function setLatitude( string $latitude ): void {
		$this->latitude = $latitude;
	}

	/**
	 * @return int
	 */
	public function getIdUser(): int {
		return $this->id_user;
	}

	/**
	 * @param int $id_user
	 *
	 * @return void
	 */
	public function setIdUser( int $id_user ): void {
		$this->id_user = $id_user;
	}

	/**
	 * @param $data
	 *
	 * @return Location
	 */
	public function setEntity( $data ): Location {
		$entity = new Location();

		$entity->setId( $data['id'] );
		$entity->setLongitude( $data['longitude'] );
		$entity->setLatitude( $data['latitude'] );
		$entity->setIdUser( $data['user_id'] );

		return $entity;
	}

	/**
	 * @param $dataList
	 *
	 * @return Location[]
	 */
	public function setEntityList( $dataList ): array {
		$listEntity = array();
		foreach ( $dataList as $data ) {
			$listEntity[] = $this->setEntity( $data );
		}

		return $listEntity;
	}

	/**
	 * Checks if a location with the specified longitude, latitude, and user ID exists in the database.
	 *
	 * @param float $longitude The longitude of the location.
	 * @param float $latitude The latitude of the location.
	 * @param int $id_user The ID of the user associated with the location.
	 *
	 * @return Location|false Returns a Location entity if the location exists, or false otherwise.
	 */
	public function checkIfLocationExists(): Location|false{
		$request = $this->getDatabase()->prepare( "SELECT id, longitude, latitude, user_id FROM ecran_location
                                        WHERE longitude = :longitude AND latitude = :latitude AND user_id = :id_user" );

		$request->bindValue( ':longitude', $this->getLongitude());
		$request->bindValue( ':latitude', $this->getLatitude() );
		$request->bindValue( ':id_user', $this->getIdUser(), PDO::PARAM_INT );

		$request->execute();

		if ( $request->rowCount() > 0 ) {
			return $this->setEntity( $request->fetch( PDO::FETCH_ASSOC ) );
		}

		return false;
	}

	/**
	 * Checks if a user ID exists in the database and retrieves their location.
	 *
	 * @param int $userId The ID of the user to check.
	 *
	 * @return Location|false Returns a Location object if the user ID exists, or false otherwise.
	 */
	public function checkIfUserIdExists($userId):Location|false{
		$request = $this->getDatabase()->prepare( "SELECT id, longitude, latitude, user_id FROM ecran_location WHERE user_id = :id_user LIMIT 1" );

		$request->bindValue( ':id_user', $userId, PDO::PARAM_INT );

		$request->execute();

		if ( $request->rowCount() > 0 ) {
			return $this->setEntity( $request->fetch( PDO::FETCH_ASSOC ) );
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return get_object_vars( $this );

	}
}