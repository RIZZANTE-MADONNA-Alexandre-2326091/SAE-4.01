<?php

namespace Models;

use JsonSerializable;
use PDO;
use WP_User;

/**
 * Class User
 *
 * User entity
 *
 * @package Models
 */
class User extends Model implements Entity, JsonSerializable
{

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string (Television | Secretary | Technician)
     */
    private $role;

    /**
     * @var CodeAde[]
     */
    private $codes;

    /**
     * @var string Type of videos slideshow for a television
     */
    private $typeDefilement;

    /**
     * @var int Time of timeout for each information, by default 10 seconds
     * */
    private $timeout;

	/**
	 * @var int
	 */
	private $deptId;

    /**
	 * Inserts a new user into the system with the specified details and assigns codes if the user's role is 'television'.
	 *
	 * This method creates a user with the provided login, password, email, and role. If the role is 'television',
	 * it associates certain codes with the user by inserting records into the `ecran_code_user` table.
	 *
	 * @return string Returns the ID of the created user.
	 */
    public function insert(): string
    {
        // Take 7 lines to create an user with a specific role
        $userData = array(
            'user_login' => $this->getLogin(),
            'user_pass' => $this->getPassword(),
            'user_email' => $this->getEmail(),
            'role' => $this->getRole()
        );

        $id = wp_insert_user($userData);

	    // Add to table ecran_dept_user
	    $database = $this->getDatabase();

	    $request = $database->prepare('INSERT INTO ecran_dept_user (dept_id, user_id) VALUES (:dept_id, :user_id)');
	    $request->bindValue(':dept_id', $this->getDeptId(), PDO::PARAM_INT);
	    $request->bindParam(':user_id', $id, PDO::PARAM_INT);

	    $request->execute();

        // To review
        if ($this->getRole() == 'television')
        {
            foreach ($this->getCodes() as $code)
            {

                $request = $this->getDatabase()->prepare('INSERT INTO ecran_code_user (user_id, code_ade_id) VALUES (:userId, :codeAdeId)');

                $request->bindParam(':userId', $id, PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }

            //Ajoute les informations de la télévision dans la table ecran_television, id de l'utilisateur en clé étrangère, typeDefilement et timeout
            $request = $this->getDatabase()->prepare('INSERT INTO ecran_television (id_user, type_defilement, timeout) VALUES (:idUser, :typeDefilement, :timeout)');
            $request->bindParam(':idUser', $id, PDO::PARAM_INT);
            $request->bindValue(':typeDefilement', $this->getTypeDefilement(), PDO::PARAM_STR);
            $request->bindValue(':timeout', $this->getTimeout(), PDO::PARAM_INT);
            $request->execute();
        }

        return $database->lastInsertId();
    }

	/**
	 * Updates user information in the database, including their password and associated codes.
	 *
	 * It clears all associated codes for the user and then inserts the appropriate codes
	 * based on the user's current code data.
     *
     * If the user's role is 'television', it updaates the type and the time of the slideshow of infomations
	 *
	 * @return int Returns the number of rows affected by the final executed database operation.
	 */
	public function update(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare('UPDATE wp_users SET user_pass = :password WHERE ID = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindValue(':password', $this->getPassword(), PDO::PARAM_STR);

        $request->execute();

        $request = $database->prepare('DELETE FROM ecran_code_user WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        foreach ($this->getCodes() as $code) {
            if ($code instanceof CodeAde && !is_null($code->getId())) {
                $request = $database->prepare('INSERT INTO ecran_code_user (user_id, code_ade_id) VALUES (:userId, :codeAdeId)');

                $request->bindValue(':userId', $this->getId(), PDO::PARAM_INT);
                $request->bindValue(':codeAdeId', $code->getId(), PDO::PARAM_INT);

                $request->execute();
            }
        }

        /*
          Si l'utilisateur qui est modifié est une télévision, on modifie les valeurs des attributs de la table ecran_television en fonction de l'id de l'utilisateur qui est une clé étrangère
        */
        if ($this->getRole() === 'television')
        {
            $request = $database->prepare('UPDATE ecran_television SET type_defilement = :typeDefilement, timeout = :timeout WHERE id_user = :idUser');
            $request->bindValue(':idUser', $this->getId(), PDO::PARAM_INT);
            $request->bindValue(':typeDefilement', $this->getTypeDefilement(), PDO::PARAM_STR);
            $request->bindValue(':timeout', $this->getTimeout(), PDO::PARAM_INT);
            $request->execute();
        }

        return $request->rowCount();
    }

	/**
	 * Deletes a user and their associated metadata from the database.
	 *
	 * @return int Returns the number of rows affected during the deletion of the user record.
	 */
    public function delete(): int
    {
        $database = $this->getDatabase();
        $request = $database->prepare('DELETE FROM wp_users WHERE ID = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();
        $count = $request->rowCount();

        $request = $database->prepare('DELETE FROM wp_usermeta WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        /*
          Si l'utilisateur qui est modifié est une télévision, on supprime les valeurs des attributs de la table ecran_television en fonction de l'id de l'utilisateur qui est une clé étrangère
        */
        if ($this->getRole() === 'television')
        {
            $request = $database->prepare('DELETE FROM ecran_television WHERE id_user = :idUser');
            $request->bindValue(':idUser', $this->getId(), PDO::PARAM_INT);
            $request->execute();
        }

        return $count;
    }

	/**
	 * Retrieves a user by their ID from the database.
	 *
	 * @param int $id The ID of the user to retrieve.
	 *
	 * @return User|false Returns a User object if the user is found, otherwise returns false.
	 */
    public function get($id): User | false {
        $request = $this->getDatabase()->prepare('SELECT wp.ID, user_login, user_pass, user_email, edu.dept_id FROM wp_users wp
                                             			LEFT JOIN ecran_dept_user edu ON edu.user_id = wp.ID
                                             			WHERE wp.ID = :id LIMIT 1');

        $request->bindParam(':id', $id, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntity($request->fetch());
        }
        return false;
    }

	/**
	 * Retrieves a list of users with their associated metadata from the database.
	 *
	 * @param int $begin The starting point for the query, used for pagination. Defaults to 0.
	 * @param int $numberElement The number of elements to retrieve. Defaults to 25.
	 *
	 * @return array Returns an array of user data with associated metadata if records are found, otherwise returns an empty
	 */
    public function getList(int $begin = 0, int $numberElement = 25): array {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email FROM wp_users user JOIN wp_usermeta meta ON user.ID = meta.user_id LIMIT :begin, :numberElement');

        $request->bindValue(':begin', (int)$begin, PDO::PARAM_INT);
        $request->bindValue(':numberElement', (int)$numberElement, PDO::PARAM_INT);

        $request->execute();

        if ($request->rowCount() > 0) {
            return $this->setEntityList($request->fetchAll());
        }
        return [];
    }

	/**
	 * Retrieves a list of users filtered by their role.
	 *
	 * @param string $role The role to filter users by.
	 *
	 * @return User[]|void The list of users matching the specified role, or void if no users are found.
	 */
    public function getUsersByRole($role)
    {
        $request = $this->getDatabase()->prepare('SELECT wpu.ID as ID, user_login, user_pass, user_email, edu.dept_id 
                                                        FROM wp_users wpu JOIN wp_usermeta meta ON wpu.ID = meta.user_id  
                                                        JOIN ecran_dept_user edu ON edu.user_id = wpu.ID AND meta.meta_value = :role 
                                                        ORDER BY wpu.user_login LIMIT 1000');

        $size = strlen($role);
        $role = 'a:1:{s:' . $size . ':"' . $role . '";b:1;}';

        $request->bindParam(':role', $role, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

	/**
	 * Retrieves a list of codes for each user and assigns them to the corresponding user object.
	 *
	 * @param array $users Array of user objects, each implementing a getId() method.
	 *
	 * @return array Modified array of user objects with their associated codes set.
	 */
    public function getMyCodes(array $users): array
    {
        foreach ($users as $user)
        {
            $request = $this->getDatabase()->prepare('SELECT code.id, type, title, code FROM ecran_code_ade code, ecran_code_user user
                                  							WHERE user.user_id = :id AND user.code_ade_id = code.id ORDER BY code.id LIMIT 100');

            $id = $user->getId();

            $request->bindParam(':id', $id, PDO::PARAM_INT);

            $request->execute();

            $code = new CodeAde();
            if ($request->rowCount() <= 0)
            {
                $codes = [];
            }
            else
            {
                $codes = $code->setEntityList($request->fetchAll());
            }

            $user->setCodes($codes);
        }

        return $users;
    }

	/**
	 * @param string $login The login of the user to check.
	 * @param string $email The email of the user to check.
	 *
	 * @return array An array of user information matching the given login or email.
	 */
    public function checkUser(string $login, string $email): array {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email, dept_id FROM wp_users
                                             	 JOIN ecran_dept_user deptUs ON wp_users.ID = deptUs.user_id
                                             	 WHERE user_login = :login OR user_email = :email LIMIT 2');

        $request->bindParam(':login', $login, PDO::PARAM_STR);
        $request->bindParam(':email', $email, PDO::PARAM_STR);

        $request->execute();

        return $this->setEntityList($request->fetchAll(PDO::FETCH_ASSOC));
    }

	/**
	 * Retrieves a list of user links to associated code entries from the database.
	 *
	 * @return array The list of user links to code as an associative array.
	 */
    public function getUserLinkToCode(): array {
        $request = $this->getDatabase()->prepare('SELECT ID, user_login, user_pass, user_email FROM ecran_code_user JOIN wp_users ON ecran_code_user.user_id = wp_users.ID WHERE user_id = :userId LIMIT 300');

        $request->bindValue(':id_user', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $this->setEntityList($request->fetchAll());
    }

	/**
	 * @param string $code The unique code to be inserted into the database.
	 *
	 * @return void
	 */
	public function createCode(string $code): void {
        $request = $this->getDatabase()->prepare('INSERT INTO ecran_code_delete_account (user_id, code) VALUES (:user_id, :code)');

        $request->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();
    }

	/**
	 * Updates the code in the database for the associated user.
	 *
	 * @param string $code The new code value to be updated in the database.
	 *
	 * @return void
	 */
	public function updateCode(string $code): void {
        $request = $this->getDatabase()->prepare('UPDATE ecran_code_delete_account SET code = :code WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $request->bindParam(':code', $code, PDO::PARAM_STR);

        $request->execute();
    }

	/**
	 * Deletes a code associated with the current user's account from the database.
	 *
	 * @return int The number of rows affected by the delete operation.
	 */
	public function deleteCode(): int {
        $request = $this->getDatabase()->prepare('DELETE FROM ecran_code_delete_account WHERE user_id = :id');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        return $request->rowCount();
    }

	/**
	 * Retrieves the deletion code associated with the current user's account.
	 *
	 * @return string|null The deletion code if found, or null if no code is associated.
	 */
	public function getCodeDeleteAccount(): string|null {
        $request = $this->getDatabase()->prepare('SELECT code FROM ecran_code_delete_account WHERE user_id = :id LIMIT 1');

        $request->bindValue(':id', $this->getId(), PDO::PARAM_INT);

        $request->execute();

        $result = $request->fetch();

        return $result['code'];
    }

    /**
     * Return data for a television user
     * @param int $idUser
     * @return array|null
     */
    public function getTypeOfTelevision(int $idUser): array|null
    {
        $user = new User();
        $user = $user->get($idUser);
        if ($user->getRole() === 'television')
        {
            $request = $this->getDatabase()->prepare('SELECT id_user, type_defilement, timeout FROM ecran_television
                                                            JOIN wp_users ON wp_users.ID = ecran_television.id_user
                                                            WHERE id_user = :id_user LIMIT 1');
            $request->bindParam(':id_user', $idUser, PDO::PARAM_INT);
            $request->execute();

            return $request->fetch();
        }
        return null;
    }

	public function getDeptAdmin( int $id){
		$request = $this->getDatabase()->prepare('SELECT dept_id FROM ecran_dept_user WHERE dept_id = :id LIMIT 1');

		$request->bindValue(':id', $id, PDO::PARAM_INT);

		$request->execute();

		$result = $request->fetch();

		return $result['dept_id'];
	}

    /**
     * Sets the attributes of a User entity based on provided data and fetches associated codes.
     *
     * @param array $data An associative array containing user data with keys such as 'ID', 'user_login', 'user_pass', 'user_email'.
     *
     * @return User The populated User entity object with all properties set, including associated codes.
     */
    public function setEntity($data): User
    {
        $entity = new User();

        $entity->setId($data['ID']);

        $entity->setLogin($data['user_login']);
        $entity->setPassword($data['user_pass']);
        $entity->setEmail($data['user_email']);
        $entity->setRole(get_user_by('ID', $data['ID'])->roles[0]);
	    $entity->setDeptId(($data['dept_id']) ?: 0);

        $request = $this->getDatabase()->prepare('SELECT id, title, code, type, dept_id FROM ecran_code_ade
    													JOIN ecran_code_user ON ecran_code_ade.id = ecran_code_user.code_ade_id
                             							WHERE ecran_code_user.user_id = :id');

        $request->bindValue(':id', $data['ID']);

        $request->execute();

        $codeAde = new CodeAde();

        $codes = $codeAde->setEntityList($request->fetchAll());

        $entity->setCodes($codes);

        return $entity;
    }

	/**
	 * @param array $dataList List of data entries to be converted into entity objects.
	 *
	 * @return array Processed list of entity objects.
	 */
    public function setEntityList($dataList): array
    {
        $listEntity = array();
        foreach ($dataList as $data) {
            $listEntity[] = $this->setEntity($data);
        }
        return $listEntity;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return CodeAde[]
     */
    public function getCodes(): array
    {
        return $this->codes;
    }

    /**
     * @param CodeAde[] $codes
     */
    public function setCodes(array $codes): void
    {
        $this->codes = $codes;
    }

	public function getDeptId() {
		return $this->deptId;
	}

	public function setDeptId( $deptId ) {
		$this->deptId = $deptId;
	}

    public function jsonSerialize(): mixed
    {
        return array(
            'id' => $this->id,
            'name' => $this->login
        );
    }

    /**
     * @return string
     */
    public function getTypeDefilement(): string
    {
        return $this->typeDefilement;
    }

    /**
     * @param string $typeDefilement
     */
    public function setTypeDefilement(string $typeDefilement): void
    {
        $this->typeDefilement = $typeDefilement;
    }

    /**
     * @return int
     * */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }
}
