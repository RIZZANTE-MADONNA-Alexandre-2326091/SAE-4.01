<?php

namespace Controllers;

use Exception;
use Models\Information;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Views\InformationView;

/**
 * Class InformationController
 *
 * Manage information (create, update, delete, display)
 *
 * @package Controllers
 */
class InformationController extends Controller
{

    /**
     * @var Information
     */
    private Information $model;

    /**
     * @var InformationView
     */
    private InformationView $view;

    /**
     * Constructor of InformationController
     */
    public function __construct() {
        $this->model = new Information();
        $this->view = new InformationView();
    }

	/**
	 * Handles the creation of information based on user input, including setting metadata,
	 * uploading files, and managing different content types (text, images, tables, PDFs, events).
	 * It also displays the corresponding forms and validation UI components.
	 *
	 * @return string A rendered HTML selector for information creation forms, including type and content options.
	 * @throws Exception If an error occurs during file registration or database insertion.
	 */
    public function create(): string {
        $current_user = wp_get_current_user();

        // All forms
        $actionText = filter_input(INPUT_POST, 'createText');
        $actionImg = filter_input(INPUT_POST, 'createImg');
        $actionTab = filter_input(INPUT_POST, 'createTab');
        $actionPDF = filter_input(INPUT_POST, 'createPDF');
        $actionEvent = filter_input(INPUT_POST, 'createEvent');

        // Variables
        $title = filter_input(INPUT_POST, 'title');
        $content = filter_input(INPUT_POST, 'content');
        $endDate = filter_input(INPUT_POST, 'expirationDate');
        $creationDate = date('Y-m-d');

        // If the title is empty
        if ($title == '') {
            $title = 'Sans titre';
        }

        $information = $this->model;

        // Set the base of all information
        $information->setTitle($title);
        $information->setAuthor($current_user->ID);
        $information->setCreationDate($creationDate);
        $information->setExpirationDate($endDate);
        $information->setAdminId(null);

        if (isset($actionText)) {   // If the information is a text
            $information->setContent($content);
            $information->setType("text");

            // Try to insert the information
            if ($information->insert()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->displayErrorInsertionInfo();
            }
        }
        if (isset($actionImg)) {  // If the information is an image
            $type = "img";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $fileTmpName = $_FILES['contentFile']['tmp_name'];
            $explodeName = explode('.', $filename);
            $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
            if (in_array(end($explodeName), $goodExtension)) {
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
            }
        }
        if (isset($actionPDF)) {
            $type = "pdf";
            $information->setType($type);
            $filename = $_FILES['contentFile']['name'];
            $explodeName = explode('.', $filename);
            if (end($explodeName) == 'pdf') {
                $fileTmpName = $_FILES['contentFile']['tmp_name'];
                $this->registerFile($filename, $fileTmpName, $information);
            } else {
                $this->view->buildModal('PDF non valide', '<p>Ce fichier est un tableau non PDF, veuillez choisir un autre PDF</p>');
            }
        }
        if (isset($actionEvent)) {
            $type = 'event';
            $information->setType($type);
            $countFiles = count($_FILES['contentFile']['name']);
            for ($i = 0; $i < $countFiles; $i++) {
                $this->model->setId(null);
                $filename = $_FILES['contentFile']['name'][$i];
                $fileTmpName = $_FILES['contentFile']['tmp_name'][$i];
                $explodeName = explode('.', $filename);
                $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg', 'pdf'];
                if (in_array(end($explodeName), $goodExtension)) {
                    $this->registerFile($filename, $fileTmpName, $information);
                }
            }
        }
        // Return a selector with all forms
        return
            $this->view->displayStartMultiSelect() .
            $this->view->displayTitleSelect('text', 'Texte', true) .
            $this->view->displayTitleSelect('image', 'Image') .
            $this->view->displayTitleSelect('pdf', 'PDF') .
            $this->view->displayTitleSelect('event', 'Événement') .
            $this->view->displayEndOfTitle() .
            $this->view->displayContentSelect('text', $this->view->displayFormText(), true) .
            $this->view->displayContentSelect('image', $this->view->displayFormImg()) .
            $this->view->displayContentSelect('pdf', $this->view->displayFormPDF()) .
            $this->view->displayContentSelect('event', $this->view->displayFormEvent()) .
            $this->view->displayEndDiv() .
            $this->view->contextCreateInformation();
    }

	/**
	 * Modify an information entry.
	 *
	 * This method retrieves, validates, and updates an information entry based on user input. It ensures
	 * the user has the appropriate permissions and verifies file uploads (if applicable) for specific
	 * content types such as images, PDFs, or spreadsheets.
	 *
	 * @return string Returns various views including the modification form, validation messages,
	 *               or an error message based on the action and its outcome.
	 * @throws Exception Throws exceptions for invalid file operations or unexpected errors.
	 */
    public function modify(): string {
        $id = $_GET['id'];

        if (empty($id) || is_numeric($id) && !$this->model->get($id)) {
            return $this->view->noInformation();
        }

        $current_user = wp_get_current_user();
        $information = $this->model->get($id);

        if (!(in_array('administrator', $current_user->roles) || in_array('secretaire', $current_user->roles) || $information->getAuthor()->getId() == $current_user->ID)) {
            return $this->view->noInformation();
        }

        if (!is_null($information->getAdminId())) {
            return $this->view->informationNotAllowed();
        }

        $submit = filter_input(INPUT_POST, 'submit');
        if (isset($submit)) {
            $title = filter_input(INPUT_POST, 'title');
            $content = filter_input(INPUT_POST, 'content');
            $endDate = filter_input(INPUT_POST, 'expirationDate');

            $information->setTitle($title);
            $information->setExpirationDate($endDate);

            if ($information->getType() == 'text') {
                // Set new information
                $information->setContent($content);
            } else {
                // Change the content
                if ($_FILES["contentFile"]['size'] != 0) {
                    echo $_FILES["contentFile"]['size'];
                    $filename = $_FILES["contentFile"]['name'];
                    if ($information->getType() == 'img') {
                        $explodeName = explode('.', $filename);
                        $goodExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
                        if (in_array(end($explodeName), $goodExtension)) {
                            $this->deleteFile($information->getId());   //$_SERVER['DOCUMENT_ROOT'].$this->model->getContent()
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('Image non valide', '<p>Ce fichier est une image non valide, veuillez choisir une autre image</p>');
                        }
                    } else if ($information->getType() == 'pdf') {
                        $explodeName = explode('.', $filename);
                        if (end($explodeName) == 'pdf') {
                            $this->deleteFile($information->getId());
                            $this->registerFile($filename, $_FILES["contentFile"]['tmp_name'], $information);
                        } else {
                            $this->view->buildModal('PDF non valide', '<p>Ce fichier est un PDF non valide, veuillez choisir un autre PDF</p>');
                        }
                    }
                }
            }

            if ($information->update()) {
                $this->view->displayModifyValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }

        $delete = filter_input(INPUT_POST, 'delete');
        if (isset($delete)) {
            $information->delete();
            $this->view->displayModifyValidate();
        }
        return $this->view->displayModifyInformationForm($information->getTitle(), $information->getContent(), $information->getExpirationDate(), $information->getType());
    }


	/**
	 * Handles the registration and upload of a file, associates it with an entity,
	 * and updates the entity with the file content information.
	 *
	 * @param string $filename The original name of the file being uploaded.
	 * @param string $tmpName The temporary file path where the uploaded file is stored.
	 * @param object $entity The entity object to which the file content is associated.
	 *
	 * @return void
	 */
    public function registerFile( string $filename, string $tmpName, object $entity): void {
        $id = 'temporary';
        $extension_upload = strtolower(substr(strrchr($filename, '.'), 1));
        $name = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $id . '.' . $extension_upload;

        // Upload the file
        if ($result = move_uploaded_file($tmpName, $name)) {
            $entity->setContent('temporary content');
            if ($entity->getId() == null) {
                $id = $entity->insert();
            } else {
                $entity->update();
                $id = $entity->getId();
            }
        } else {
            $this->view->errorMessageCantAdd();
        }
        // If the file upload and the upload of the information in the database works
        if ($id != 0) {
            $entity->setId($id);

            $md5Name = $id . md5_file($name);
            rename($name, $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $md5Name . '.' . $extension_upload);

            $content = $md5Name . '.' . $extension_upload;

            $entity->setContent($content);
            if ($entity->update()) {
                $this->view->displayCreateValidate();
            } else {
                $this->view->errorMessageCantAdd();
            }
        }
    }

	/**
	 * Deletes a file from the server based on the provided ID.
	 *
	 * @param int $id The unique identifier of the file to be deleted.
	 *
	 * @return void
	 */
    public function deleteFile(int $id): void {
        $this->model = $this->model->get($id);
        $source = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $this->model->getContent();
        wp_delete_file($source);
    }

	/**
	 * Displays all information in a paginated format.
	 * Filters and formats the information based on user roles, types, and other criteria.
	 * Allows deleting of selected items if the user has the appropriate permissions.
	 *
	 * @return string The rendered HTML content for the information display and pagination.
	 */
	public function displayAll(): string {
		$numberAllEntity = $this->model->countAll();
		$url             = $this->getPartOfUrl();
		$number          = filter_input( INPUT_GET, 'number' );
		$pageNumber      = 1;
		if ( sizeof( $url ) >= 2 && is_numeric( $url[1] ) ) {
			$pageNumber = $url[1];
		}
		if ( isset( $number ) && ! is_numeric( $number ) || empty( $number ) ) {
			$number = 25;
		}
		$begin   = ( $pageNumber - 1 ) * $number;
		$maxPage = ceil( $numberAllEntity / $number );
		if ( $maxPage <= $pageNumber && $maxPage >= 1 ) {
			$pageNumber = $maxPage;
		}
		$current_user = wp_get_current_user();
		if ( in_array( 'administrator', $current_user->roles ) || in_array( 'secretaire', $current_user->roles ) ) {
			$informationList = $this->model->getList( $begin, $number );
		} else {
			$informationList = $this->model->getAuthorListInformation( $current_user->ID, $begin, $number );
		}

		$name         = 'Info';
		$header       = [ 'Titre', 'Contenu', 'Date de création', 'Date d\'expiration', 'Auteur', 'Type', 'Modifier' ];
		$dataList     = [];
		$row          = $begin;
		$imgExtension = [ 'jpg', 'jpeg', 'gif', 'png', 'svg' ];
		foreach ( $informationList as $information ) {
			++ $row;

			$contentExplode = explode( '.', $information->getContent() );

			$content = TV_UPLOAD_PATH;
			if ( ! is_null( $information->getAdminId() ) ) {
				$content = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
			}

			if ( in_array( $information->getType(), [ 'img', 'pdf', 'event' ] ) ) {
				if ( in_array( $contentExplode[1], $imgExtension ) ) {
					$content = '<img class="img-thumbnail img_table_ecran" src="' . $content . $information->getContent() . '" alt="' . $information->getTitle() . '">';
				} else if ( $contentExplode[1] === 'pdf' ) {
					$content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $information->getContent() . '"]';
				} else {
					$content = $information->getContent();
				}

				$type = $information->getType();
				if ( $information->getType() === 'img' ) {
					$type = 'Image';
				} else if ( $information->getType() === 'pdf' ) {
					$type = 'PDF';
				} else if ( $information->getType() === 'event' ) {
					$type = 'Événement';
				} else if ( $information->getType() === 'text' ) {
					$type = 'Texte';
				}
				$dataList[] = [
					$row,
					$this->view->buildCheckbox( $name, $information->getId() ),
					$information->getTitle(),
					$content,
					$information->getCreationDate(),
					$information->getExpirationDate(),
					$information->getAuthor()->getLogin(),
					$type,
					$this->view->buildLinkForModify( esc_url( get_permalink( get_page_by_title_V2( 'Modifier une information' ) ) ) . '?id=' . $information->getId() )
				];
			}

			$submit = filter_input( INPUT_POST, 'delete' );
			if ( isset( $submit ) ) {
				if ( isset( $_REQUEST['checkboxStatusInfo'] ) ) {
					$checked_values = $_REQUEST['checkboxStatusInfo'];
					foreach ( $checked_values as $id ) {
						$entity = $this->model->get( $id );
						if ( in_array( 'administrator', $current_user->roles ) || in_array( 'adminDept', $current_user->roles ) || in_array( 'secretaire', $current_user->roles ) || $entity->getAuthor()->getId() == $current_user->ID ) {
							$type  = $entity->getType();
							$types = [ "img", "pdf", "event" ];
							if ( in_array( $type, $types ) ) {
								$this->deleteFile( $id );
							}
							$entity->delete();
						}
					}
					$this->view->refreshPage();
				}
			}
			$returnString = "";
			if ( $pageNumber == 1 ) {
				$returnString = $this->view->contextDisplayAll();
			}
		}

		return $returnString . $this->view->displayAll( $name, 'Informations', $header, $dataList ) . $this->view->pageNumber( $maxPage, $pageNumber, esc_url( get_permalink( get_page_by_title_V2( 'Gestion des informations' ) ) ), $number );
	}


	/**
	 * Check if the end date of an information has passed and performs deletion if necessary.
	 *
	 * @param int|string $id The unique identifier of the information.
	 * @param string $endDate The expiration date of the information in 'Y-m-d' format.
	 *
	 * @return void
	 */
    public function endDateCheckInfo(int $id, int|string $endDate): void {
        if ($endDate <= date("Y-m-d")) {
            $information = $this->model->get($id);
            $this->deleteFile($id);
            $information->delete();
        }
    }

	/**
	 * Handles the display of information slides by retrieving a list of information,
	 * processing each item based on its type and expiration date, and displaying
	 * the components in a slideshow format.
	 *
	 * @return void
	 */
    public function informationMain(): void {
        $informations = $this->model->getList();
        $this->view->displayStartSlideshow();
        foreach ($informations as $information) {
            $endDate = date('Y-m-d', strtotime($information->getExpirationDate()));
            if (!$this->endDateCheckInfo($information->getId(), $endDate)) {
                if ($information->getType() == 'tab') {
                    $list = $this->readSpreadSheet(TV_UPLOAD_PATH . $information->getContent());
                    $content = "";
                    foreach ($list as $table) {
                        $content .= $table;
                    }
                    $information->setContent($content);
                }

                $adminSite = true;
                if (is_null($information->getAdminId())) {
                    $adminSite = false;
                }
                $this->view->displaySlide($information->getTitle(), $information->getContent(), $information->getType(), $adminSite);
            }
        }
        $this->view->displayEndDiv();
    }

	/**
	 * Synchronizes and updates the local information database with the information
	 * retrieved from the admin website. Existing information is compared and updated
	 * based on title, content, and expiration date. If an information entry no longer
	 * exists on the admin site, it is deleted from the local database. New information
	 * from the admin website is added to the local database if it doesn't already exist.
	 *
	 * @return void
	 */
	public function registerNewInformation(): void {
        $informationList = $this->model->getFromAdminWebsite();
        $myInformationList = $this->model->getAdminWebsiteInformation();
        foreach ($myInformationList as $information) {
            if ($adminInfo = $this->model->getInformationFromAdminSite($information->getId())) {
                if ($information->getTitle() != $adminInfo->getTitle()) {
                    $information->setTitle($adminInfo->getTitle());
                }
                if ($information->getContent() != $adminInfo->getContent()) {
                    $information->setContent($adminInfo->getContent());
                }
                if ($information->getExpirationDate() != $adminInfo->getExpirationDate()) {
                    $information->setExpirationDate($adminInfo->getExpirationDate());
                }
                $information->update();
            } else {
                $information->delete();
            }
        }
        foreach ($informationList as $information) {
            $exist = 0;
            foreach ($myInformationList as $myInformation) {
                if ($information->getId() == $myInformation->getAdminId()) {
                    ++$exist;
                }
            }
            if ($exist == 0) {
                $information->setAdminId($information->getId());
                $information->insert();
            }
        }
    }

	/**
	 * Handles the display of event slides by retrieving a list of event information,
	 * iterating through each event, and rendering content based on its type.
	 * Supports displaying images or embedding PDFs dynamically in the slideshow.
	 *
	 * @return string
	 */
    public function displayEvent(): string {
        $events = $this->model->getListInformationEvent();
        $this->view->displayStartSlideEvent();
        foreach ($events as $event) {
            $this->view->displaySlideBegin();
            $extension = explode('.', $event->getContent());
            $extension = $extension[1];
            if ($extension == "pdf") {
                echo '
				<div class="canvas_pdf" id="' . $event->getContent() . '"></div>';
                //echo do_shortcode('[pdf-embedder url="'.$event->getContent().'"]');
            } else {
                echo '<img src="' . TV_UPLOAD_PATH . $event->getContent() . '" alt="' . $event->getTitle() . '">';
            }
        }
        return $this->view->displayEndDiv();
    }

	/**
	 * Reads the content of a spreadsheet file and processes it into a formatted HTML table structure.
	 * The content is divided into multiple tables, each containing a maximum of 10 rows.
	 *
	 * @param string $content The relative path to the spreadsheet file.
	 *
	 * @return array An array of HTML strings, where each string represents a table with 10 rows (or fewer).
	 */
    public function readSpreadSheet(string $content): array {
        $file = $_SERVER['DOCUMENT_ROOT'] . $content;

	    $array     = explode( ".", $file );
	    $extension = ucfirst(strtolower(end( $array )));
        $reader    = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();

        $contentList = array();
        $content = "";
        $mod = 0;
        for ($i = 0; $i < $highestRow; ++$i) {
            $mod = $i % 10;
            if ($mod == 0) {
                $content .= '<table class ="table table-bordered tablesize">';
            }
            foreach ($worksheet->getRowIterator($i + 1, 1) as $row) {
                $content .= '<tr scope="row">';
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                foreach ($cellIterator as $cell) {
                    $content .= '<td class="text-center">' .
                        $cell->getValue() .
                        '</td>';
                }
                $content .= '</tr>';
            }
            if ($mod == 9) {
                $content .= '</table>';
                array_push($contentList, $content);
                $content = "";
            }
        }
        if ($mod != 9 && $i > 0) {
            $content .= '</table>';
            array_push($contentList, $content);
        }
        return $contentList;
    }
}
