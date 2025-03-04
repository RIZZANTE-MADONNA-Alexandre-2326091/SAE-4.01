<?php

namespace Models;

use JsonSerializable;
use PDO;
use PDOException;

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
    private $id;

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
     * @var string (Text | Image | PDF | Event | Video Youtube ou local (short ou classique) | RSS)
     */
    private string $type;

    /**
     * @var int|null
     */
    private int|null $adminId;

    /**
     * @var int
     */
    private int $authorId;

	/**
	 * @var CodeAde[]
	 */
	private array $codes;

	/**
	 * @var int
	 */
	private int $deptId;

    /**
     * Insert an information in the database
     *
     * @return string
     */
    public function insert(): string
    {
        $database = $this->getDatabase();
        $request = $database->prepare("INSERT INTO ecran_information (title, content, creation_date, expiration_date, type, author, administration_id) VALUES (:title, :content, :creationDate, :expirationDate, :type, :userId, :administration_id)");

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':creationDate', $this->getCreationDate(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':type', $this->getType(), PDO::PARAM_STR);
        $request->bindValue(':userId', $this->getAuthor()->getId(), PDO::PARAM_INT);
        $request->bindValue(':administration_id', $this->getAdminId(), PDO::PARAM_INT);

        try {
            $request->execute();
            $lastId = $database->lastInsertId();
        } catch (PDOException $e) {
            error_log('Insert Error: ' . $e->getMessage());
            return false;
        }

		if($this->getDeptId() !== 0){
			$request = $database->prepare("INSERT INTO ecran_information_department (info_id, dept_id) VALUES (:info_id, :dept_id)");

			$request->bindValue(':info_id', $lastId, PDO::PARAM_INT);
			$request->bindValue(':dept_id', $this->getDeptId(), PDO::PARAM_INT);
		} elseif ($this->getCodes() !== null){
			foreach ($this->getCodes() as $code){
				$request = $database->prepare("INSERT INTO ecran_code_information (info_id, code_ade_id) VALUES (:info_id, :code_ade_id)");

				$request->bindValue(':info_id', $lastId, PDO::PARAM_INT);
				$request->bindValue(':code_ade_id', $code->getId(), PDO::PARAM_INT);
			}
		}

		return $lastId;
    }

    /**
     * Modify an information
     *
     * @return int
     */
    public function update(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare("UPDATE ecran_information SET title = :title, content = :content, expiration_date = :expirationDate WHERE id = :id");

        $request->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $request->bindValue(':content', $this->getContent(), PDO::PARAM_STR);
        $request->bindValue(':expirationDate', $this->getExpirationDate(), PDO::PARAM_STR);
        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Delete an information in the database
     */
    public function delete(): int
    {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_information WHERE id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

    /**
     * Return an information corresponding to the ID
     *
     * @param int $id
     *
     * @return Information|false
     */
    public function get($id): Information|false
    {
        $request = $this->getDatabase()->prepare("SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE id = :id LIMIT 1");

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0)
        {
            return $this->setEntity($request->fetch(PDO::FETCH_ASSOC));
        }
        return false;
    }

    /**
     * @param int $begin
     * @param int $numberElement
     *
     * @return array
     */
    public function getList(int $begin = 0, int $numberElement = 25): array
    {
        $request = $this->getDatabase()->prepare("SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information ORDER BY id ASC LIMIT :begin, :numberElement");

        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0)
        {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

    /**
     * Return the list of information created by an user
     *
     * @param User $author
     * @param int $begin
     * @param int $numberElement
     *
     * @return array
     */
    public function getAuthorListInformation(User $author, int $begin = 0, int $numberElement = 25): array
    {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE author = :author ORDER BY expiration_date LIMIT :begin, :numberElement');

        $request->bindParam(':author', $author, PDO::PARAM_INT);
        $request->bindValue(':begin', $begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', $numberElement, PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    public function countAll()
    {
        $request = $this->getDatabase()->prepare("SELECT COUNT(*) FROM ecran_information");

        $request->execute();

        return $request->fetch()[0];
    }

    /**
     * Returns a list of information where the type are normal videos
     * @return array
     */
    public function getListClassicsVideos(array $types): array
    {
        $request = $this->getDatabase()->prepare('SELECT * FROM ecran_information WHERE type = :type1 OR type = :type2');
        $request->bindValue(':type1', $types[0], PDO::PARAM_STR);
        $request->bindValue(':type1', $types[1], PDO::PARAM_STR);
        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Return the list of event present in database
     * @return array
     */
    public function getListInformationEvent(): array
    {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type FROM ecran_information WHERE type = "event" ORDER BY expiration_date ASC');

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }


    /**
     * @return array
     */
    public function getFromAdminWebsite(): array
    {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, title, content, type, author, expiration_date, creation_date FROM ecran_information LIMIT 200');

        $request->execute();

        return $this->setEntityList($request->fetchAll(), true);
    }

    /**
     *
     * @return array
     */
    public function getAdminWebsiteInformation(): array
    {
        $request = $this->getDatabase()->prepare('SELECT id, title, content, creation_date, expiration_date, author, type, administration_id FROM ecran_information WHERE administration_id IS NOT NULL LIMIT 500');

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

    /**
     * @param $id
     * @return Information|false
     */
    public function getInformationFromAdminSite($id): Information|false
    {
        $request = $this->getDatabaseViewer()->prepare('SELECT id, title, content, type, author, expiration_date, creation_date FROM ecran_information WHERE id = :id LIMIT 1');

        $request->bindValue(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0)
        {
            return $this->setEntity($request->fetch(), true);
        }
        return false;
    }

    /**
     * Build a list of information
     *
     * @param $dataList
     *
     * @return array
     */
    public function setEntityList($dataList, $adminSite = false): array
    {
        $listEntity = array();
        foreach ($dataList as $data)
        {
            $listEntity[] = $this->setEntity($data, $adminSite);
        }
        return $listEntity;
    }


    /**
     * Create an information
     *
     * @param $data
     *
     * @return Information
     */
    public function setEntity($data, $adminSite = false): Information
    {
        $entity = new Information();
        $author = new User();

        $entity->setId($data['id']);
        $entity->setTitle($data['title']);
        $entity->setContent($data['content']);
        $entity->setCreationDate(date('Y-m-d', strtotime($data['creation_date'])));
        $entity->setExpirationDate(date('Y-m-d', strtotime($data['expiration_date'])));

        $entity->setType($data['type']);

        if ($data['administration_id'] != null)
        {
            $author->setLogin('Administration');
            $entity->setAuthor($author);
        }
        else
        {
            $entity->setAuthor($author->get($data['author']));
        }

        if ($adminSite)
        {
            $entity->setAdminId($data['id']);
        }
        else
        {
            $entity->setAdminId($data['administration_id']);
        }

        return $entity;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @param int $author
     */
    public function setAuthorId(int $authorId): void
    {
        $this->authorId = $authorId;
    }

    /**
     * @return string
     */
    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    /**
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return string
     */
    public function getExpirationDate(): string
    {
        return $this->expirationDate;
    }

    /**
     * @param string $expirationDate
     */
    public function setExpirationDate(string $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int|null
     */
    public function getAdminId(): int|null
    {
        return $this->adminId;
    }

    /**
     * @param int|null $adminId
     */
    public function setAdminId(int|null $adminId): void
    {
        $this->adminId = $adminId;
    }

	/**
	 * @return CodeAde[]
	 */
	public function getCodes(): array {
		return $this->codes;
	}

	/**
	 * @param CodeAde[] $codes
	 */
	public function setCodes( array $codes ): void {
		$this->codes = $codes;
	}

	public function getDeptId(): int {
		return $this->deptId;
	}

	public function setDeptId( int $deptId ): void {
		$this->deptId = $deptId;
	}

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}