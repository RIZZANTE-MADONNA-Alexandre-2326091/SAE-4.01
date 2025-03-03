<?php

namespace Views;


use Controllers\InformationController;
use Models\Information;
use Models\RssModel;

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
     * Display a form to create an information with text
     *
     * @param string|null $title
     * @param string|null $content
     * @param string|null $endDate
     * @param string $type
     *
     * @return string
     */
    public function displayFormText(string $title = null, string $content = null,
                                    string $endDate = null, string $type = "createText"): string
    {
        $dateMin = date ('Y-m-d', strtotime("+1 day"));

        $form = '
        <form method="post">
            <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="info" class="form-control" type="text" name="title" minlength="4" maxlength="40"
                placeholder="Titre..." value="' . $title . '">
            </div>
            <div class="form-group">
                <label for="content">Contenu</label>
                <textarea class="form-control" id="content" name="content" rows="3" maxlength="280" minlength="4"
                placeholder="280 caractères au maximum" required="required">' . $content . '</textarea>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '"
                value="' . $endDate . '" required="required">
            </div>
            <button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a form to create an information with an image
     *
     * @param string|null $title
     * @param string|null $content
     * @param string|null $endDate
     * @param string $type
     *
     * @return string
     */
    public function displayFormImg(string $title = null, string $content = null,
                                   string $endDate = null, string $type = "createImg"): string
    {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
		            </div>';
        if ($content != null)
        {
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

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a form to create an information with a PDF
     *
     * @param string|null $title
     * @param string|null $content
     * @param string|null $endDate
     * @param string $type
     *
     * @return string
     */
    public function displayFormPDF(string $title = null, string $content = null,
                                   string $endDate = null, string $type = "createPDF"): string
    {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" maxlength="60" value="' . $title . '">
		            </div>';

        if ($content != null)
        {
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

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        return $form . '</form>';
    }

    /**
     * Display a form to create an event information with media or PDFs
     *
     * @param string|null $endDate
     * @param string $type
     *
     * @return string
     */
    public function displayFormEvent(string $endDate = null, string $type = "createEvent"): string
    {
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

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }
        $form .= '</form>';

        return $form;
    }

    /**
     * Display a form to create a video information
     *
     * @param string|null $title
     * @param string|null $content
     * @param string|null $endDate
     * @param string $type
     *
     * @return string form
     * */
    public function displayFormVideoYT(string $title = null, string $content = null,
                                       string $endDate = null, string $type = 'createVideoYT'): string
    {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $form = '
		<form method="post" enctype="multipart/form-data">
		    <div class="form-group">
                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="info" class="form-control" type="text" name="title" minlength="4" maxlength="40"
                placeholder="Titre..." value="' . $title . '">
            </div>
            <div class="form-group">
                <label for="contentVideo">Lien Youtube</label>
                <input id="linkVideo" class="form-control" type="url" name="content" minlength="25" maxlength="60"
                placeholder="Ajouter votre lien Youtube de forme: \'https://www.youtube.com/watch?v=...\' OU \'https://www.youtube.com/shorts/...\'" required="required"
                value="' . $content . '" pattern="^https:\/\/www\.youtube\.com\/((watch\?v=)|(shorts\/)).+$">
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' .
            $dateMin . '" value="' . $endDate . '" required="required">
            </div>

            <button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete"
                      onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        $form .= '</form>';
        return $form;
    }


    public function displayFormVideoCLocal(string $title = null, string $content = null,
                                           string $endDate = null, string $type = "createVideoCLocal"): string
    {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" minlength="4" maxlength="60" value="' . $title . '">
		            </div>';

        if ($content != null)
        {
            $form .= '
			<div class="embed-responsive embed-responsive-16by9">
			  <iframe class="embed-responsive-item" src="' . TV_UPLOAD_PATH . $content . '" allowfullscreen></iframe>
			</div>';
        }

        $form .= '
			<div class="form-group">
                <label>Ajouter une vidéo hébergée localement de format "classique". Le fichier doit être au format "mp4" et ne pas dépasser 1Go!</label>
                <input class="form-control-file" type="file" accept=".mp4" name="contentFile"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="1073741824"/>
            </div>

            <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        $form .= '</form>';
        return $form;
    }

    public function displayFormVideoSLocal(string $title = null, string $content = null,
                                           string $endDate = null, string $type = "createVideoSLocal"): string
    {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        $form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="title" class="form-control" type="text" name="title" placeholder="Inserer un titre" minlength="4" maxlength="60" value="' . $title . '">
		            </div>';

        if ($content != null)
        {
            $form .= '
			<div class="embed-responsive embed-responsive-16by9">
			  <iframe class="embed-responsive-item" src="' . TV_UPLOAD_PATH . $content . '" allowfullscreen></iframe>
			</div>';
        }

        $form .= '
			<div class="form-group">
                <label>Ajouter une vidéo hébergée localement de format "short". Le fichier doit être au format "mp4" et ne pas dépasser 1Go!</label>
                <input class="form-control-file" type="file" accept=".mp4" name="contentFile"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="1073741824"/>
            </div>

            <div class="form-group">
				<label for="expirationDate">Date d\'expiration</label>
				<input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required >
			</div>
			<button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

        if ($type == 'submit')
        {
            $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
        }

        $form .= '</form>';
        return $form;
    }
    

        public function displayFormRSS(string $title = null, string $content = null, string $endDate = null, string $type = "createRSS"): string
        {
            $dateMin = date('Y-m-d', strtotime("+1 day"));

            $form = '
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre <span class="text-muted">(Optionnel)</span></label>
                    <input id="title" class="form-control" type="text" name="title" placeholder="Titre..." value="' . $title . '">
                </div>
                <div class="form-group">
                    <label for="content">Lien RSS</label>
                    <input id="content" class="form-control" type="text" name="content" placeholder="Lien RSS..." value="' . $content . '" required>
                </div>
                <div class="form-group">
                    <label for="expirationDate">Date d\'expiration</label>
                    <input id="expirationDate" class="form-control" type="date" name="expirationDate" min="' . $dateMin . '" value="' . $endDate . '" required>
                </div>
                <button class="btn button_ecran" type="submit" name="' . $type . '">Valider</button>';

            if ($type == 'submit')
            {
                $form .= '<button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette information ?\');">Supprimer</button>';
            }

            return $form . '</form>';
        }

    /**
     * Explain how the information's display
     *
     * @return string
     */
    public function contextCreateInformation(): string
    {
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
     * Display a form to modify an information
     *
     * @param $title
     * @param $content
     * @param $endDate
     * @param $type
     *
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function displayModifyInformationForm($title, $content, $endDate, $type): string
    {
        if ($type == "text")
        {
            return '<a href="' . esc_url( get_permalink(get_page_by_title_V2('Gestion des informations' ))) . '">< Retour</a>' . $this->displayFormText( $title, $content, $endDate, 'submit' );
        }
        else if ($type == "YTvideosh" || $type == "YTvideow")
        {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormVideoYT($title, $content, $endDate, 'submit');
        }
        else if ($type == "LocCvideo")
        {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormVideoCLocal($title, $content, $endDate, 'submit');
        }
        else if ($type == "LocSvideo")
        {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormVideoSLocal($title, $content, $endDate, 'submit');
        }
        elseif ($type == "img")
        {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormImg($title, $content, $endDate, 'submit');
        }
        elseif ($type == "pdf")
        {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormPDF($title, $content, $endDate, 'submit');
        }
        else if ($type == "rss")
        {
            return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormRSS($title, $content, $endDate, 'submit');
        }
        elseif ($type == "event")
        {
            $extension = explode('.', $content);
            $extension = $extension[1];
            if ($extension == "pdf")
            {
                return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormPDF($title, $content, $endDate, 'submit');
            }
            else
            {
                return '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>' . $this->displayFormImg($title, $content, $endDate, 'submit');
            }
        }
        else
        {
            return $this->noInformation();
        }
    }

    /**
     * Display the begin of the slideshow
     */
    public function displayStartSlideshow(): void
    {
        echo '<div class="slideshow-container">';
    }

    /**
     * Display a slide for the slideshow
     *
     * @param string $title
     * @param string $content
     * @param string $type
     * @param int $timeout
     * @param bool $adminSite
     */
    public function displaySlide(string $title, string $content, string $type, string $typeDefilement, int $timeout, bool $adminSite = false): void
    {
        echo '<div class="myInfoSlides text-center">';
        echo '<p id="timeout" style="display: none">' . $timeout . '</p>';
        echo '<p id="typeDefilement" style="display: none">' . $typeDefilement . '</p>';

        // If the title is empty
        if ($title != "Sans titre")
        {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = TV_UPLOAD_PATH;
        $extension = '';
        if ($adminSite)
        {
            $url = URL_WEBSITE_VIEWER . TV_UPLOAD_PATH;
        }

        if ($type == 'pdf' || $type == "event" || $type == "img")
        {
            $extension = explode('.', $content);
            $extension = $extension[1];
        }

        if ($type == 'pdf' || $type == "event" && $extension == "pdf")
        {
            echo '
			<div class="canvas_pdf" id="' . $content . '">
			</div>';
        }
        elseif ($type == "img" || $type == "event")
        {
            echo '<img class="img-thumbnail" src="' . $url . $content . '" alt="' . $title . '">';
        }
        else if ($type == 'text')
        {
            echo '<div class="text-info">' . $content . '</div>';
        }

		else if ($type == 'YTvideosh')
		{
			$link = substr_replace($content,'embed',24,6);
            $link = substr_replace($link, '-nocookie', 19, 0);

            echo '<span class="lien"><p id="">' . $content . '</p></span>';
            echo '<div class="videosh"></div>';
			/*echo '<iframe id="" class="videosh" src="' . $link . '?autoplay=1&loop=1&playlist=' . substr($link,30) . '&mute=1&controls=0&disablekb=1&enablejsapi=1"
				  title="YouTube short player" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
				  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture;"></iframe>';*/
		}
		else if ($type == 'YTvideow' && $typeDefilement == 'suret')
		{
			$link = substr_replace($content,'embed/',24,8);
            $link = substr_replace($link, '-nocookie', 19, 0);

            echo '<span class="lien"><p>' . $content . '</p></span>';
            echo '<div class="videow"></div>';
			/*echo '<iframe id="" class="videow" src="' . $link . '?autoplay=1&loop=1playlist=' . substr($link,30) . '&mute=1&controls=0&disablekb=1&enablejsapi=1"
				  title="YouTube video player" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
				  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture;"></iframe>';*/
        }
        else if ($type == 'LocCvideo' && $typeDefilement == 'suret')
        {
            echo '<video class="localCvideo" muted>
				      <source src="' . TV_UPLOAD_PATH . $content . '" type="video/mp4">
				      <p>Impossible de lire la vidéo.</p>
				  </video>';
        }
        else if ($type == 'LocSvideo')
        {
            echo '<video class="localSvideo" muted>
				      <source src="' . TV_UPLOAD_PATH . $content . '" type="video/mp4">
				      <p>Impossible de lire la vidéo.</p>
				  </video>';
        }

        else if ($type == 'rss') {
            $rssModel = new RssModel($content);
            $rssFeed = $rssModel->getRssFeed();
            $rssView = new RssView();
            echo $rssView->render($rssFeed);
        }

        else if ($type == 'special')
        {
            $func = explode('(Do this(function:', $content);
            $text = explode('.', $func[0]);
            foreach ($text as $value)
            {
                echo '<p class="lead">' . $value . '</p>';
            }
            $func = explode(')end)', $func[1]);
            echo $func[0]();
        }

        else
        {
            echo $content;
        }
        echo '</div>';
    }

    /**
     * Display the start of the videos slideshow
     * */
    public function displayStartSlideVideo(): void
    {
        echo '<div class="video-slideshow-container">';
    }

    /**
     * Display the slideshow of video on the schedule
     *
     * @param string $title
     * @param string $content
     * @param string $type
     * @param string $typeDefilement
     * @param bool $adminSite
     */
    public function displaySlideVideo(string $title, string $content, string $type, string $typeDefilement, bool $adminSite = false): void
    {
        echo '<div class="myVideoSlides text-center" style="display: block;">';

        // If the title is empty
        if ($title != "Sans titre")
        {
            echo '<h2 class="titleInfo">' . $title . '</h2>';
        }

        $url = $adminSite ? URL_WEBSITE_VIEWER . TV_UPLOAD_PATH : TV_UPLOAD_PATH;

        if ($type === 'LocCvideo' && $typeDefilement === 'defil')
        {
            echo '<video class="video_container" src="' . $url . $content . '
              " autoplay loop muted type="video/mp4"></video>';
        }
        else if ($type === 'YTvideow' && $typeDefilement === 'defil')
        {
            $link = substr_replace($content,'embed/',24,8);
            $link = substr_replace($link, '-nocookie', 19, 0);
            echo '<iframe class="video_container" src="' . $link . '?autoplay=1&loop=1&playlist=' . substr($link,30) . '&mute=1&controls=0&disablekb=1&enablejsapi=0"
				  title="YouTube slide player" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
				  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture;"></iframe>';
        }

        echo '</div>';
    }

    public function contextDisplayAll(): string
    {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/info.png" alt="Logo information" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les informations qui ont été créées sur ce site.</p>
				<p class="lead">Les informations sont triées de la plus vieille à la plus récente.</p>
				<p class="lead">Vous pouvez modifier une information en cliquant sur "Modifier" à la ligne correspondante à l\'information.</p>
				<p class="lead">Vous souhaitez supprimer une / plusieurs information(s) ? Cochez les cases des informations puis cliquez sur "Supprimer" le bouton ce situe en bas du tableau.</p>
			</div>
		</div>
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		<hr class="half-rule">';
    }

    public function noInformation(): string
    {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Information non trouvée</h3>
			<p>Cette information n\'éxiste pas, veuillez bien vérifier d\'avoir bien cliqué sur une information.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		</div>';
    }

    /**
     * Start the slideshow
     */
    public function displayStartSlideEvent(): void
    {
        echo '
            <div id="slideshow-container" class="slideshow-container">';
    }

    /**
     * Start a slide
     */
    public function displaySlideBegin(): void
    {
        echo '
			<div class="mySlides event-slide">';
    }

    /**
     * Display a modal to validate the creation of an information
     */
    public function displayCreateValidate(): void
    {
        $page = get_page_by_title_V2('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal('Ajout d\'information validé', '<p class="alert alert-success"> L\'information a été ajoutée </p>', $linkManageInfo);
    }

    /**
     * Display a modal to validate the modification of an information
     * Redirect to manage page
     */
    public function displayModifyValidate(): void
    {
        $page = get_page_by_title_V2('Gestion des informations');
        $linkManageInfo = get_permalink($page->ID);
        $this->buildModal('Modification d\'information validée', '<p class="alert alert-success"> L\'information a été modifiée </p>', $linkManageInfo);
    }

    /**
     * Display a message if the insertion of the information doesn't work
     */
    public function displayErrorInsertionInfo(): void
    {
        $this->buildModal('Erreur lors de l\'insertion', '<p class="alert alert-danger">Il y a eu une erreur durant l\'insertion de l\'information</p>');
    }

    /**
     * Display if the modification of a video doesn't have the good format;
     */
    public function displayErrorVideoFormat(): void
    {
        $this->buildModal('Erreur de format vidéo', '<p>La vidéo que vous voulez modifier n\'est pas au bon format. Veuillez vérifier le bon format ou ajoutez une nouvelle information.</p>');
    }

    /**
     * Display if the video exceeds the maximum size;
     */
    public function displayVideoExceedsMaxSize(): void
    {
        $this->buildModal('Taille excessive de vidéo', '<p>La vidéo que vous voulez modifier dépasse la taille maximum possible. Veuillez réduire la vidéo ou baisser sa résolution avant de réessayer.</p>');
    }

    /**
     * Display if the video is not conform
     */
    public function displayNotConformVideo(): void
    {
        $this->buildModal('Vidéo non valide', '<p>Ce fichier est une vidéo non valide, veuillez choisir une autre vidéo</p>');
    }

    /**
     * Display if the information is null
     * @return string The error box to display
     * */
    public function displayNullInformation(): string
    {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Votre information est nulle</h3>
			<p>Le contenu ou la date d\'expiration de votre information est nul. Veuillez modifier les informations correctement.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		</div>';
    }

    public function informationNotAllowed(): string
    {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des informations'))) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette information</h3>
			<p>Cette information appartient à quelqu\'un d\'autre, vous ne pouvez donc pas modifier cette information.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une information'))) . '">Créer une information</a>
		</div>';
    }
}