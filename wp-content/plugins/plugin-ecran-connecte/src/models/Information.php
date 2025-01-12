<?php

namespace Models;

use JsonSerializable;
use PDO;

/**
 * Class Information
 *
 * Information entity
 *
 * @package Models
 */
class Information extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var User
     */
    private User $author;

    /**
     * @var string
     */
    private string $creationDate;

    /**
     * @var string
     */
    private string $expirationDate;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string (Text | Image | excel | PDF | Event)
     */
    private string $type;

    /**
     * @var int
     */
    private string $adminId;

	/**
	 * Inserts a new record into the ecran_information table with the specified properties.
	 *
	 * @return string The ID of the newly inserted record.
	 */
    public function insert(): string {
        $database = $this->getDatabase();
        $request = $database->prepare("INSERT INTO ecran_information (title, content, creation_date, expiration_date, type, author, administration_id) VALUES (:title, :content, :creationDate, :expirationDate, :type, :userId, :administration_id) ");

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':creationDate', $this->getCreationDate(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);
        $request->bindValue(':userId', $this->getAuthor(), PDO::PARAM_INT);
        $request->bindValue('administration_id', $this->getAdminId(), PDO::PARAM_INT);

        $request->execute();

        return $database->lastInsertId();
    }

	/**
	 * Updates the record in the database with the current object's data.
	 *
	 * @return int The number of rows affected by the update operation.
	 */
    public function update(): int {
        $request = $this->getDatabase()->prepare("UPDATE ecran_information SET title = :title, content = :content, expiration_date = :expirationDate WHERE id = :id");

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

	/**
	 * Deletes the record associated with the current object's ID from the database.
	 *
	 * @return int The number of rows affected by the delete operation.
	 */
    public function delete(): int {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_information WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

	/**
	 * Retrieves information by its ID.
	 *
	 * @param int $id The ID of the information to retrieve.
	 *
	 * @return Information|bool Returns an Information object if found, or false if no record is found.
	 */
    public function get($id): Information | bool {
        $request = $this->getDatabase()->prepare("SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE id = :id LIMIT 1");

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

	/**
	 * Retrieves a list of entities from the database within a specified range.
	 *
	 * @param int $begin The offset to start retrieving records from. Default is 0.
	 * @param int $numberElement The number of records to retrieve. Default is 25.
	 *
	 * @return array The list of entities retrieved from the database. Returns an empty array if no records are found.
	 */
    public function getList(int $begin = 0,int $numberElement = 25): array {
        $request = $this->getDatabase()->prepare("SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

	/**
	 * Retrieves a list of information entries for a specific author from the database.
	 *
	 * @param User $author The ID of the author whose information entries should be retrieved.
	 * @param int $begin The starting point for the query results (default is 0).
	 * @param int $numberElement The maximum number of entries to retrieve (default is 25).
	 *
	 * @return array The list of information entries associated with the given author.
	 */
    public function getAuthorListInformation(User $author, int $begin = 0, int $numberElement = 25): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE author = :author ORDER BY expiration_date LIMIT :begin, :numberElement');

        $request->bindParam(':author', $author, PDO::PARAM_INT);
        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    } //getAuthorListInformation()

	/**
	 * Retrieves the total count of records from the `ecran_information` table in the database.
	 *
	 * @return int The total count of records.
	 */
	public function countAll(): int {
        $request = $this->getDatabase()->prepare("SELECT COUNT(*) FROM ecran_information");

        $request->execute();

        return $request->fetch()[0];
    }

	/**
	 * Retrieves a list of information events from the database.
	 *
	 * @return array The list of information events, each containing id, title, content, creation_date, expiration_date, author, and type.
	 */
    public function getListInformationEvent(): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type FROM ecran_information WHERE type = "event" ORDER BY expiration_date ASC');

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }


	/**
	 * Retrieves a list of information from the admin website database.
	 *
	 * @return array The fetched list of information entities.
	 */
    public function getFromAdminWebsite(): array {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, title, content, type, author, expiration_date, creation_date FROM ecran_information LIMIT 200');

        $request->execute();

        return $this->setEntityList($request->fetchAll(), true);
    }

	/**
	 * Retrieves a list of admin website information from the database, including details such as id, title, content, creation date, expiration date, author, type, and administration ID, limited to 500 entries.
	 *
	 * @return array The list of admin website information as an array of entities.
	 */
    public function getAdminWebsiteInformation(): array {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE administration_id IS NOT NULL LIMIT 500');

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

	/**
	 * Retrieves information from the admin site based on a given ID.
	 *
	 * @param int $id The unique identifier for the information to retrieve.
	 *
	 * @return bool|Information|static Returns the entity set with the fetched data if found, or false if no data is found.
	 */
    public function getInformationFromAdminSite(int $id): static|bool|Information {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, title, content, type, author, expiration_date, creation_date FROM ecran_information WHERE id = :id LIMIT 1');

        $request->bindValue(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(), true);
        }
        return false;
    }

	/**
	 * Sets an entity list by processing the provided data list.
	 *
	 * @param array $dataList The list of data to process into entities.
	 * @param bool $adminSite A flag to determine if the operation is for the admin site.
	 *
	 * @return array|Information Returns an array of entities or an Information object after processing.
	 */
    public function setEntityList($dataList, bool $adminSite = false): array | Information {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }


	/**
	 * Sets the entity data based on the provided input and returns the configured entity.
	 *
	 * @param array $data An associative array containing the entity data, including
	 *                    keys such as 'id', 'title', 'content', 'creation_date',
	 *                    'expiration_date', 'type', 'author', and optionally 'administration_id'.
	 * @param bool $adminSite A boolean flag indicating whether the entity is being set for an admin site.
	 *
	 * @return Information Returns the configured entity object with all provided data set.
	 */
    public function setEntity($data, bool $adminSite = false): Information {
        $entity = new Information();
        $author = new User();

        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setContent($data['content']);
        $entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
        $entity->setExpirationDate(date('Y-m-d', strtotime($data['expiration_date'])));

        $entity->setType($data['type']);

        if ($data['administration_id'] != null) {
            $author->setLogin('Administration');
            $entity->setAuthor($author);
        } else {
            $entity->setAuthor($author->get($data['author']));
        }

        if ($adminSite) {
            $entity->setAdminId($data['id']);
        } else {
            $entity->setAdminId($data['administration_id']);
        }

        return $entity;
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
     * @return User
     */
    public function getAuthor(): User {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getCreationDate(): string {
        return $this->creationDate;
    }

    /**
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate): void {
        $this->creationDate = $creationDate;
    }

    /**
     * @return string
     */
    public function getExpirationDate(): string {
        return $this->expirationDate;
    }

    /**
     * @param string $expirationDate
     */
    public function setExpirationDate(string $expirationDate): void {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void {
        $this->content = $content;
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
     * @return int
     */
    public function getAdminId(): int {
        return $this->adminId;
    }

    /**
     * @param int $adminId
     */
    public function setAdminId(int $adminId): void {
        $this->adminId = $adminId;
    }

	/**
	 * Serializes the object to a value that can be serialized natively by json_encode().
	 *
	 * @return mixed An array or object that can be serialized to JSON format.
	 */
	public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }
}