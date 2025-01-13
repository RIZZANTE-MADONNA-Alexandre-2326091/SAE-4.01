<?php

namespace Views;


use Controllers\InformationController;
use Models\Information;

/**
 * Class InformationView
 *
 * All view for Information (Forms, tables, messages)
 *
 * @package Views
 */
class InformationView extends View
{

	/**
	 * Generates and returns a form for creating or managing text content with fields for title, content, and expiration date.
	 *
	 * @param string|null $title The pre-filled value for the title input field. Optional.
	 * @param string|null $content The pre-filled value for the content textarea field. Optional.
	 * @param string|null $endDate The pre-filled value for the expiration date input field. Optional.
	 * @param string $type The type of action for the form, e.g., "createText" or "submit". Defaults to "createText".
	 *
	 * @return string The generated HTML form as a string.
	 */
    public function displayFormText(string $title = null, string $content = null, string $endDate = null, string $type = "createText"): string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="info" class="form-control" type="text" name="title" minlength="4" maxlength="40" placeholder="Titre..." value="' . $title . '">
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="3" placeholder="280 caractères au maximum" maxlength="280" minlength="4" required>' . $content . '</textarea>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

	/**
	 * Generates and displays a form for uploading an image with optional title and expiration date.
	 *
	 * @param string|null $title The title of the form or image, optional.
	 * @param string|null $content The file name of the current image to display, optional.
	 * @param string|null $endDate The pre-filled expiration date for the form, optional.
	 * @param string $type The type of form submission (default is "createImg").
	 *
	 * @return string The generated HTML form as a string.
	 */
    public function displayFormImg(string $title = null, string $content = null, string $endDate = null, string $type = "createImg"): string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
		            </div>';
        if ($content != null) {
            $form .= '
		       	<figure class="text-center">
				  <img class="img-thumbnail" src="' . TV_UPLOAD_PATH . $content . '" alt="' . $title . '">
				  <figcaption>Image actuelle</figcaption>
				</figure>';
        }
        $form .= '
			<div class="form-group">
				<label for="contentFile">Ajouter une image</label>
		        <input class="form-control-file" id="contentFile" type="file" name="contentFile"/>
		        <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
	        </div>
	        <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

	/**
	 * Builds and returns an HTML string for a form to create or manage a PDF.
	 *
	 * @param string|null $title The pre-filled title for the PDF form, optional.
	 * @param string|null $content The file path or identifier for an existing PDF to include, optional.
	 * @param string|null $endDate The pre-filled expiration date for the PDF form, optional.
	 * @param string $type The form submission type, defaults to "createPDF". Use "submit" for management forms.
	 *
	 * @return string The assembled HTML string representing the PDF form.
	 */
    public function displayFormPDF(string $title = null, string $content = null, string $endDate = null, string $type = "createPDF"): string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
		            </div>';

        if ($content != null) {
            $form .= '
			<div class="embed-responsive embed-responsive-16by9">
			  <iframe class="embed-responsive-item" src="' . TV_UPLOAD_PATH . $content . '" allowfullscreen></iframe>
			</div>';
        }

        $form .= '
			<div class="form-group">
                <label>Ajout du fichier PDF</label>
                <input class="form-control-file" type="file" name="contentFile"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
            </div>
            <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

	/**
	 * Generates and displays an HTML form for event management.
	 *
	 * @param string|null $endDate The default expiration date to prefill in the date input field. If null, no value is prefilled.
	 * @param string $type The type of form action, either for creating or submitting the event.
	 *
	 * @return string The generated HTML form as a string.
	 */
    public function displayFormEvent(string $endDate = null, string $type = "createEvent"): string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $form = '
		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
                <label>Sélectionner les fichiers</label>
                <input class="form-control-file" multiple type="file" name="contentFile[]"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                <small id="fileHelp" class="form-text text-muted">Images ou PDF</small>
        	</div>
        	<div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit') {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }
        $form .= '</form>';

        return $form;
    }

	/**
	 * Generates and returns the HTML content for creating and presenting information about the system's functionality.
	 *
	 * @return string The HTML content that describes the process and presentation of information, including images and descriptive text.
	 */
    public function contextCreateInformation(): string {
        return '
		<hr class="half-rule">
		<div>
			<h2>Les informations</h2>
			<p class="lead">Lors de la création de votre information, celle-ci est postée directement sur tous les téléviseurs qui utilisent ce site.</p>
			<p class="lead">Les informations que vous créez seront affichées avec les informations déjà présentes.</p>
			<p class="lead">Les informations sont affichées dans un diaporama défilant les informations une par une sur la partie droite des téléviseurs.</p>
			<div class="text-center">
				<figure class="figure">
					<img src="' . TV_PLUG_PATH . 'public/img/presentation.png" class="figure-img rounded" alt="Représentation d\'un téléviseur">
					<figcaption class="figure-caption">Représentation d\'un téléviseur</figcaption>
				</figure>
			</div>
		</div>';
    }

	/**
	 * Display the form to modify information based on the specified type.
	 *
	 * @param string $title The title of the information to be modified.
	 * @param string $content The content of the information to be modified.
	 * @param string $endDate The expiration date of the information.
	 * @param string $type The type of information to be modified (e.g., text, img, tab, pdf, event).
	 *
	 * @return string The HTML content of the form to be displayed for modification or a fallback message if no information is available.
	 */
    public function displayModifyInformationForm(string $title, string $content, string $endDate, string $type): string {
        if ($type == "text") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormText($title, $content, $endDate, 'submit');
        } elseif ($type == "img") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormImg($title, $content, $endDate, 'submit');
        } elseif ($type == "tab") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormTab($title, $content, $endDate, 'submit');
        } elseif ($type == "pdf") {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormPDF($title, $content, $endDate, 'submit');
        } elseif ($type == "event") {
            $extension = explode('.', $content);
            $extension = $extension[1];
            if ($extension == "pdf") {
                return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormPDF($title, $content, $endDate, 'submit');
            } else {
                return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormImg($title, $content, $endDate, 'submit');
            }
        } else {
            return $this->noInformation();
        }
    }

	/**
	 * Displays the start of a slideshow container.
	 *
	 * @return void
	 */
    public function displayStartSlideshow(): void {
        echo '<div class="slideshow-container">';
    }

	/**
	 * Displays a slide with content depending on the specified type and other parameters.
	 *
	 * @param string $title The title of the slide; if titled "Sans titre", no title will be displayed.
	 * @param string $content The content of the slide, could be text, image path, or special format data.
	 * @param string $type The type of content; allowed types are 'pdf', 'event', 'img', 'text', or 'special'.
	 * @param bool $adminSite Indicates whether the slide is displayed in an admin context, altering the content path accordingly. Default is false.
	 *
	 * @return void
	 */
    public function displaySlide(string $title, string $content, string $type, bool $adminSite = false): void {
        echo '<div class="myInfoSlides text-center">';

        // If the title is empty
        if ($title != "Sans titre") {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = TV_UPLOAD_PATH;
        if ($adminSite) {
            $url = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
        }

        if ($type == 'pdf' || $type == "event" || $type == "img") {
            $extension = explode('.', $content);
            $extension = $extension[1];
        }

        if ($type == 'pdf' || $type == "event" && $extension == "pdf") {
            echo '
			<div class="canvas_pdf" id="' . $content . '">
			</div>';
        } elseif ($type == "img" || $type == "event") {
            echo '<img class="img-thumbnail" src="' . $url . $content . '" alt="' . $title . '">';
        } else if ($type == 'text') {
            echo '<p class="lead">' . $content . '</p>';
        } else if ($type == 'special') {
            $func = explode('(Do this(function:', $content);
            $text = explode('.', $func[0]);
            foreach ($text as $value) {
                echo '<p class="lead">' . $value . '</p>';
            }
            $func = explode(')end)', $func[1]);
            echo $func[0]();
        } else {
            echo $content;
        }
        echo '</div>';
    }

	/**
	 * Generates and returns the HTML content for displaying all context information.
	 *
	 * @return string The generated HTML content for presenting all context-related information.
	 */
	public function contextDisplayAll(): string {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/info.png" alt="Logo information" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les informations qui ont été créées sur ce site.</p>
				<p class="lead">Les informations sont triées de la plus vieille à la plus récente.</p>
				<p class="lead">Vous pouvez modifier une information en cliquant sur "Modifier" à la ligne correspondante à l\'information.</p>
				<p class="lead">Vous souhaitez supprimer une / plusieurs information(s) ? Cochez les cases des informations puis cliquez sur "Supprimer" le bouton se situant en bas du tableau.</p>
			</div>
		</div>
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		<hr class="half-rule">';
    }

	/**
	 * Generate HTML content to indicate that no information was found and provide navigation links.
	 *
	 * @return string The HTML content displaying an error message and navigation options.
	 */
	public function noInformation(): string {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Information non trouvée</h3>
			<p>Cette information n\'éxiste pas, veuillez bien vérifier d\'avoir bien cliqué sur une information.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		</div>';
    }

	/**
	 * Displays the start of a slideshow event by outputting the necessary container HTML.
	 *
	 * @return void
	 */
    public function displayStartSlideEvent(): void {
        echo '
            <div id="slideshow-container" class="slideshow-container">';
    }

	/**
	 * Displays the beginning of a slide element for an event.
	 *
	 * @return void
	 */
    public function displaySlideBegin(): void {
        echo '
			<div class="mySlides event-slide">';
    }

	/**
	 * Displays a confirmation modal following the successful addition of information.
	 *
	 * @return void
	 */
    public function displayCreateValidate(): void {
        $page = get_page_by_title_V2('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal('Ajout d\'information validé', '<p class="alert alert-success"> L\'information a été ajoutée </p>', $linkManageInfo);
    }

	/**
	 * Displays a confirmation modal upon the successful modification of information.
	 *
	 * @return void
	 */
    public function displayModifyValidate(): void {
        $page = get_page_by_title_V2('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal('Modification d\'information validée', '<p class="alert alert-success"> L\'information a été modifiée </p>', $linkManageInfo);
    }

	/**
	 * Displays an error message indicating a failure during the insertion of information.
	 *
	 * @return void
	 */
    public function displayErrorInsertionInfo(): void {
        echo '<p>Il y a eu une erreur durant l\'insertion de l\'information</p>';
    }

	/**
	 * Returns an HTML string indicating that the modification of a specific alert is not allowed.
	 * Includes navigation links to manage and create information pages.
	 *
	 * @return string
	 */
	public function informationNotAllowed(): string {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette alerte</h3>
			<p>Cette information appartient à quelqu\'un d\'autre, vous ne pouvez donc pas modifier cette information.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		</div>';
    }
}
