<?php

namespace Models;

use JsonSerializable;
use PDO;

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
	 * @var string
	 */
	private string $adress;

	/**
	 * @var int
	 */
	private int $id_user;


	/**
	 * Insert a location in the database.ZS
	 *
	 * @return string
	 */
	public function insert():string {
		$database = $this->getDatabase();
		$request = $database->prepare("INSERT INTO ecran_location (longitude, latitude, address, user_id) VALUES (:longitude, :latitude, :address, :id_user)");

		$request->bindValue(':longitude', $this->getLongitude(), PDO::PARAM_INT);
		$request->bindValue(':latitude', $this->getLatitude(), PDO::PARAM_INT);
		$request->bindValue(':address', $this->getAdress(), PDO::PARAM_STR);
		$request->bindValue(':user_id', $this->getIdUser(), PDO::PARAM_INT);

		$request->execute();

		return $database->lastInsertId();
	}

	public function update():int {
		$request = $this->getDatabase()->prepare("UPDATE ecran_location SET longitude = :longitude, latitude = :latitude,
                          address = :address, user_id = :user_id WHERE id = :id");

		$request->bindValue(':longitude', $this->getLongitude(), PDO::PARAM_INT);
		$request->bindValue(':latitude', $this->getLatitude(), PDO::PARAM_INT);
		$request->bindValue(':address', $this->getAdress(), PDO::PARAM_STR);
		$request->bindValue(':user_id', $this->getIdUser(), PDO::PARAM_INT);

		$request->execute();

		return $request->rowCount();
	}

	public function delete(): int {
		$request = $this->getDatabase()->prepare('DELETE FROM ecran_location WHERE id = :id');

		$request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

		$request->execute();

		return $request->rowCount();
	}

	public function get( $id ): array|false {
		$request = $this->getDatabase()->prepare("SELECT id, name FROM ecran_location WHERE id = :id LIMIT 1");

		$request->bindParam(':id', $id, PDO::PARAM_INT);

		$request->execute();

		if ($request->rowCount() > 0) {
			return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
		}
		return false;
	}

	public function getList(int $begin = 0, int $numberElement = 25): array {
		$request = $this->getDatabase()->prepare("SELECT id, longitude, latitude, address, id_user  FROM ecran_department ORDER BY id ASC LIMIT :begin, :numberElement");

		$request->bindValue(':begin', $begin, PDO::PARAM_INT);
		$request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);

		$request->execute();

		if ($request->rowCount() > 0) {
			return $this->setEntityList($request->fetchAll());
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

	private function getLongitude() {
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

	private function getLatitude() {
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
	 * @return string
	 */
	public function getAdress(): string {
		return $this->adress;
	}

	/**
	 * @param string $adresse
	 *
	 * @return void
	 */
	public function setAdresse( string $adress ): void {
		$this->adress = $adress;
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
	 */
	public function setEntity( $data ) {
		$entity = new Location();

		$entity->setId($data['id']);
		$entity->setLongitude( $data['longitude'] );
		$entity->setLatitude( $data['latitude'] );
		$entity->setAdresse( $data['address'] );
		$entity->setIdUser( $data['id_user'] );

		return $entity;
	}

	/**
	 * @param $dataList
	 *
	 */
	public function setEntityList( $dataList ) {
		$listEntity = array();
		foreach ($dataList as $data){
			$listEntity[] = $this->setEntity($data);
		}
		return $listEntity;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array {
		return get_object_vars($this);
	}
}