<?php

namespace Models;

use JsonSerializable;
use PDO;
use SimplePie\Author;

/**
 * Class Alert
 *
 * Alert entity
 *
 * @package Models
 */
class Alert extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private int $id;

    /**
     * @var User
     */
    private User $author;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string
     */
    private string $creation_date;

    /**
     * @var string
     */
    private string $expirationDate;

    /**
     * @var CodeAde[]
     */
    private array $codes;

    /**
     * @var int
     */
    private int $forEveryone;

    /**
     * @var int
     */
    private int $adminId;

    /**
     * @var int
     */
    private int $authorId;


	/**
	 * Inserts a new alert record into the database and associates related codes.
	 *
	 * @return string The ID of the newly inserted alert record.
	 */
    public function insert() : string
    {
        $database = $this->getDatabase();
        $request = $database->prepare('INSERT INTO ecran_alert (author, content, creation_date, expiration_date) VALUES (:author, :content, :creation_date, :expirationDate)');

        $request->bindValue(':author', $this->getAuthorId(), PDO::PARAM_INT);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':creation_date', $this->getCreationDate(),PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(),PDO::PARAM_STR);

        $request->execute();

        $id = $database->lastInsertId();

        foreach ( $this->getCodes() as $code ) {
            if ($code->getCode() != 'all' && $code->getCode() != 0 ) {
                $request = $database->prepare('INSERT INTO ecran_code_alert (alert_id, code_ade_id) VALUES (:idAlert, :idCodeAde)');
                $request->bindValue(':idAlert', $id, PDO::PARAM_INT);
                $request->bindValue(':idCodeAde', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        }

        return $id;
    }

	/**
	 * Updates the alert in the database by modifying its content, expiration date, and audience.
	 * Additionally, existing codes associated with the alert are removed and replaced with new ones.
	 *
	 * @return int The number of rows affected by the update.
	 */
    public function update(): int {
        $database = $this->getDatabase();
        $request = $database->prepare('UPDATE ecran_alert SET content = :content, expiration_date = :expirationDate, for_everyone = :for_everyone WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':for_everyone', $this->isForEveryone(), PDO::PARAM_INT);

        $request->execute();

        $count = $request->rowCount();

        $request = $database->prepare('DELETE FROM ecran_code_alert WHERE alert_id = :alertId');

        $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        foreach ($this->getCodes() as $code) {

            if ($code->getCode() !== 'all' || $code->getCode() !== 0) {
                $request = $database->prepare('INSERT INTO ecran_code_alert (alert_id, code_ade_id) VALUES (:alertId, :codeAdeId)');

                $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        }

        return $count;
    }

	/**
	 * Deletes a record from the database based on the current object's ID.
	 *
	 * @return int The number of rows affected by the delete operation.
	 */
    public function delete(): int {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_alert WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

	/**
	 * Retrieves an alert from the database based on the provided ID.
	 *
	 * @param int $id The unique identifier of the alert to retrieve.
	 *
	 * @return Alert|null Returns an Alert object if found, or null if no matching record exists.
	 */
    public function get($id): Alert | null {
        $request = $this->getDatabase()->prepare('SELECT id, content, creation_date, expiration_date, author, for_everyone, administration_id FROM ecran_alert WHERE id = :id LIMIT 1');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
    }

	/**
	 * Retrieves a list of alerts from the database.
	 *
	 * @param int $begin The starting position for the query, defaults to 0.
	 * @param int $numberElement The number of elements to retrieve, defaults to 25.
	 *
	 * @return array A list of alerts as an array of entities or an empty array if no results are found.
	 */
    public function getList(int $begin = 0,int $numberElement = 25): array {
        $request = $this->getDatabase()->prepare("SELECT id, content, creation_date, expiration_date, author, for_everyone, administration_id FROM ecran_alert ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);

        $request->execute();


        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

	/**
	 * Retrieves a list of author alerts from the database based on the given parameters.
	 *
	 * @param int $author The ID of the author whose alerts are to be retrieved.
	 * @param int $begin The starting point for the query results. Defaults to 0.
	 * @param int $numberElement The maximum number of elements to retrieve. Defaults to 25.
	 *
	 * @return array An array of alerts associated with the specified author, or an empty array if none are found.
	 */
    public function getAuthorListAlert(int $author, int $begin = 0, int $numberElement = 25): array {
        $request = $this->getDatabase()->prepare("SELECT id, content, creation_date, expiration_date, author, for_everyone, administration_id FROM ecran_alert WHERE author = :author ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);
        $request->bindParam(':author', $author, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

	/**
	 * Retrieves and processes a list of entries from the admin website's database.
	 *
	 * @return array Processed list of entities fetched from the database.
	 */
    public function getFromAdminWebsite(): array {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, content, author, expiration_date, creation_date FROM ecran_alert LIMIT 200');

        $request->execute();

        return $this->setEntityList($request->fetchAll(), true);
    }

	/**
	 * Fetches alerts for a specific user by their ID.
	 *
	 * @param int $id The ID of the user for whom to fetch alerts.
	 *
	 * @return Alert A list of alerts associated with the specified user.
	 */
    public function getForUser(int $id): Alert {
        $request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author, administration_id
															FROM ecran_alert
															JOIN ecran_code_alert ON ecran_alert.id = ecran_code_alert.alert_id
															JOIN ecran_code_ade ON ecran_code_alert.code_ade_id = ecran_code_ade.id
															JOIN ecran_code_user ON ecran_code_ade.id = ecran_code_user.code_ade_id
															WHERE ecran_code_user.user_id = :id ORDER BY expiration_date ASC');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC), false);
    }

	/**
	 * Retrieves a list of alerts intended for everyone, sorted by expiration date in ascending order, limited to 50 entries.
	 *
	 * @return array An array of associative arrays representing the retrieved alerts.
	 */
    public function getForEveryone(): array {
        $request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author, administration_id FROM ecran_alert WHERE for_everyone = 1 ORDER BY expiration_date ASC LIMIT 50');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Retrieves alert links associated with a specific code based on the alert ID.
	 *
	 * @return Alert|array The result of setting the entity list with fetched data from the database.
	 */
    public function getAlertLinkToCode(): Alert|array {
        $request = $this->getDatabase()->prepare('SELECT ecran_alert.id, content, creation_date, expiration_date, author FROM ecran_code_alert JOIN ecran_alert ON ecran_code_alert.alert_id = ecran_alert.id WHERE alert_id = :alertId LIMIT 50');

        $request->bindValue(':alertId', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(), false);
    }

	/**
	 * Retrieves a list of admin website alerts from the database.
	 *
	 * @return array An array of alerts containing id, content, author, expiration_date, creation_date, and for_everyone fields.
	 */
	public function getAdminWebsiteAlert(): array {
        $request = $this->getDatabase()->prepare('SELECT id, content, author, expiration_date, creation_date, for_everyone FROM ecran_alert WHERE administration_id IS NOT NULL LIMIT 500');

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

	/**
	 * Retrieves the total count of rows from the 'ecran_alert' table in the database.
	 *
	 * @return int The total count of rows.
	 */
    public function countAll(): int {
        $request = $this->getDatabase()->prepare("SELECT COUNT(*) FROM ecran_alert");

        $request->execute();

        return $request->fetch()[0];
    }

	/**
	 * Retrieves an alert from the admin site based on the given ID.
	 *
	 * @param int $id The ID of the alert to retrieve.
	 *
	 * @return Alert|bool An alert entity if found, or false if no alert is found.
	 */
    public function getAlertFromAdminSite($id): Alert|bool {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, content, author, expiration_date, creation_date FROM ecran_alert WHERE id = :id LIMIT 1');

        $request->bindValue(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch(), true);
        }
        return false;
    }

	/**
	 * Sets and processes a list of entities based on the provided data list.
	 *
	 * @param array $dataList The list of data to be processed into entities.
	 * @param bool $adminSite Indicates whether the processing is for an admin site context.
	 *
	 * @return array The resulting list of processed entities.
	 */
    public function setEntityList($dataList,bool $adminSite = false): array {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }

	/**
	 * Sets the entity based on provided data and administration site flag.
	 *
	 * @param array $data The data to create or update the entity. Expected keys include 'id', 'content', 'creation_date', 'expiration_date', 'administration_id', and 'author'.
	 * @param bool $adminSite Indicates whether the entity is created in the context of the administration site.
	 *
	 * @return Alert The configured Alert entity.
	 */
    public function setEntity($data, bool $adminSite = false): Alert {
        $entity = new Alert();
        $codeAde = new CodeAde();
        $author = new User();
        $author->get($data['author']);

        $entity->setId($data['id']);
        $entity->setContent($data['content']);
        $entity->setAuthor($author);
        $entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
        $entity->setExpirationDate(
            date('Y-m-d', strtotime($data['expiration_date']))
        );

        $codes = array();
        foreach ( $codeAde->getByAlert($data['id']) as $code ) {
            $codes[] = $code;
        }
        $entity->setCodes($codes);

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
     * @return int
     */
    public function getAuthorId(): int {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     */
    public function setAuthorId(int $authorId): void {
        $this->authorId = $authorId;
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
    public function getCreationDate():string {
        return $this->creation_date;
    }

    /**
     * @param string $creation_date
     */
    public function setCreationDate(string $creation_date): void {
        $this->creation_date = $creation_date;
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
     * @return array
     */
    public function getCodes(): array {
        return $this->codes;
    }

	/**
	 * @param CodeAde[] $codes
	 */
    public function setCodes(array $codes): void {
        $this->codes = $codes;
    }

    /**
     * @return int
     */
    public function isForEveryone(): int {
        return $this->forEveryone;
    }

    /**
     * @param int $forEveryone
     */
    public function setForEveryone(int $forEveryone): void {
        $this->forEveryone = $forEveryone;
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
    public function setAdminId($adminId): void {
        $this->adminId = $adminId;
    }

	/**
	 * Prepares the object data for JSON serialization.
	 *
	 * Converts the object's properties into an associative array.
	 *
	 * @return array The serialized data of the object.
	 */
	public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
