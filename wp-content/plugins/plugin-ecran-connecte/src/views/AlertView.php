<?php

namespace Views;

use Models\Alert;
use Models\CodeAde;

/**
 * Class AlertView
 *
 * All view for Alert (Forms, tables, messages)
 *
 * @package Views
 */
class AlertView extends View
{

	/**
	 * Generates an HTML form for alert creation, including content input,
	 * expiration date, and select options for years, groups, and half-groups.
	 *
	 * @param array $years Array of available years.
	 * @param array $groups Array of available groups.
	 * @param array $halfGroups Array of available half-groups.
	 *
	 * @return string Array containing the HTML code for the form.
	 */
    public function creationForm(array $years, array $groups, array $halfGroups): string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));

        return '
        <form method="post" id="alert">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input class="form-control" type="text" id="content" name="content" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
			</div>
            <div class="form-group">
				<label>Date d\'expiration</label>
				<input type="date" class="form-control" id="expirationDate" name="expirationDate" min="' . $dateMin . '" required>
			</div>
            <div class="form-group">
                <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                ' . $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" id="plus" onclick="addButtonAlert()" class="btn button_ecran" value="+">
            <button type="submit" id="valider" class="btn button_ecran" name="submit">Valider</button>
        </form>
        <a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))) . '">Voir les alertes</a>' . $this->contextCreateAlert();
    }

	/**
	 * Generates and returns the HTML content for the alert context section,
	 * explaining how alerts will be displayed on the televisions that use the site.
	 *
	 * @return string The HTML string for the alert context section, including text descriptions and an illustrative image.
	 */
    public function contextCreateAlert(): string {
        return '
		<hr class="half-rule">
		<div>
			<h2>Les alertes</h2>
			<p class="lead">Lors de la création de votre alerte, celle-ci sera posté directement sur tous les téléviseurs qui utilisent ce site.</p>
			<p class="lead">Les alertes que vous créez seront affichées avec les alertes déjà présentes.</p>
			<p class="lead">Les alertes sont affichées les une après les autres défilant à la chaîne en bas des téléviseurs.</p>
			<div class="text-center">
				<figure class="figure">
					<img src="' . TV_PLUG_PATH . 'public/img/presentation.png" class="figure-img rounded" alt="Représentation d\'un téléviseur">
					<figcaption class="figure-caption">Représentation d\'un téléviseur</figcaption>
				</figure>
			</div>
		</div>';
    }

	/**
	 * Generates and returns the HTML form for modifying an alert, including fields for content, expiration date, and target audience.
	 *
	 * @param Alert $alert The alert object containing data for pre-populating the form fields.
	 * @param array $years An array of available years to select from in the form.
	 * @param array $groups An array of groups to populate the selection options.
	 * @param array $halfGroups An array of half-groups to populate additional selection options.
	 *
	 * @return string The HTML string representing the alert modification form.
	 */
    public function modifyForm(Alert $alert,array $years,array $groups,array $halfGroups): string {
        $dateMin = date('Y-m-d', strtotime("+1 day"));
        $codes = $alert->getCodes();

        $form = '
        <a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))) . '">< Retour</a>
        <form method="post" id="alert">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input type="text" class="form-control" id="content" name="content" value="' . $alert->getContent() . '" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input type="date" class="form-control" id="expirationDate" name="expirationDate" min="' . $dateMin . '" value = "' . $alert->getExpirationDate() . '" required>
            </div>
            <div class="form-group">
                <label for="selectId1">Année, groupe, demi-groupes concernés</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups, $codes[0], 1, $alert->getForEveryone()) . '
            </div>';

        if (!$alert->getForEveryone()) {
            $count = 2;
            foreach ($codes as $code) {
                $form .= '
				<div class="row" id="selctId' . $count . '">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $code, $count)
                    . '<input type="button" id="selectId' . $count . '" onclick="deleteRowAlert(this.id)" class="selectbtn" value="Supprimer">
                  </div>';
                $count = $count + 1;
            }
        }

	    $form .= '<input type="button" id="plus" onclick="addButtonAlert()" value="+">
                  <button type="submit" class="btn button_ecran" id="valider" name="submit">Valider</button>
                  <button type="submit" class="btn delete_button_ecran" id="supprimer" name="delete" onclick="return confirm(\' Voulez-vous supprimer cette alerte ?\');">Supprimer</button>
                </form>' . $this->contextModify();

        return $form;
    }

	/**
	 * Generates and returns the HTML content for the modify context section,
	 * describing how alert modifications are applied and their effects.
	 *
	 * @return string The HTML string for the modify context section, including details on expiration adjustments and content updates.
	 */
	public function contextModify(): string {
        return '
		<hr class="half-rule">
		<div>
			<p class="lead">La modification d\'une alerte prend effet comme pour la création, le lendemain.</p>
			<p class="lead">Vous pouvez donc prolonger le temps d\'expiration ou bien modifier le contenu de votre alerte.</p>
		</div>';
    }

    public function contextDisplayAll(): string {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="' . TV_PLUG_PATH . 'public/img/alert.png" alt="Logo alerte" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les alertes qui ont été créées sur ce site.</p>
				<p class="lead mb-4">Les alertes sont triées de la plus vieille à la plus récente.</p>
				<p class="lead mb-4">Vous pouvez modifier une alerte en cliquant sur "Modifier" à la ligne correspondante à l\'alerte.</p>
				<p class="lead mb-4">Vous souhaitez supprimer une / plusieurs alerte(s) ? Cochez les cases des alertes puis cliquez sur "Supprimer" le bouton se situant en bas du tableau.</p>
			</div>
		</div>
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une alerte'))) . '">Créer une alerte</a>
		<hr class="half-rule">';
    }

	/**
	 * Displays an HTML structure for a sliding alert system using the provided text content.
	 *
	 * @param array $texts An array of strings, where each string represents a piece of text to be displayed as an individual alert item.
	 *
	 * @return void
	 */
    public function displayAlertMain(array $texts): void {
        echo '
        <div class="alerts" id="alert">
             <div class="ti_wrapper">
                <div class="ti_slide">
                    <div class="ti_content">';
        for ($i = 0; $i < sizeof($texts); ++$i) {
            echo '<div class="ti_news"><span>' . $texts[$i] . '</span></div>';
        }
        echo '
                    </div>
                </div>
            </div>
        </div>
        ';
    }

	/**
	 * Builds and returns an HTML string for a select dropdown menu, populated with options for years, groups, and half groups.
	 *
	 * @param array $years An array of year objects, where each object is expected to have methods `getCode()` and `getTitle()` to fetch year code and title.
	 * @param array $groups An array of group objects, where each object is expected to have methods `getCode()` and `getTitle()` to fetch group
	 */
    public function buildSelectCode(array $years,array $groups,array $halfGroups,array $code = null, int $count = 0, int $forEveryone = 0): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectAlert[]" required="">';

        if ($forEveryone) {
            $select .= '<option value="all" selected>Tous</option>';
        } elseif (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '" selected>' . $code->getTitle() . '</option>';
        }

        $select .= '<option value="all">Tous</option>
                    <option value="0">Aucun</option>
            		<optgroup label="Année">';

        foreach ($years as $year) {
            $select .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        $select .= '</optgroup><optgroup label="Groupe">';

        foreach ($groups as $group) {
            $select .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $select .= '</optgroup><optgroup label="Demi groupe">';

        foreach ($halfGroups as $halfGroup) {
            $select .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        $select .= '</optgroup>
			</select>';

        return $select;
    }

	/**
	 * Generates and returns the HTML content for the "alert not found" section,
	 * providing information that the requested alert does not exist and offering
	 * options to navigate back or create a new alert.
	 *
	 * @return string The HTML string indicating the alert was not found and containing navigation links.
	 */
	public function noAlert(): string {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))) . '">< Retour</a>
		<div>
			<h3>Alerte non trouvée</h3>
			<p>Cette alerte n\'éxiste pas, veuillez bien vérifier d\'avoir bien cliqué sur une alerte.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une alerte'))) . '">Créer une alerte</a>
		</div>';
    }

	/**
	 * Generates and returns the HTML content for the alert notification,
	 * informing the user that they cannot modify the alert as it belongs to someone else.
	 * Includes navigation links to return to the alert management page or to create a new alert.
	 *
	 * @return string
	 */
	public function alertNotAllowed(): string {
        return '
		<a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))) . '">< Retour</a>
		<div>
			<h3>Vous ne pouvez pas modifier cette alerte</h3>
			<p>Cette alerte appartient à quelqu\'un d\'autre, vous ne pouvez donc pas modifier cette alerte.</p>
			<a href="' . esc_url(get_permalink(get_page_by_title_V2('Créer une alerte'))) . '">Créer une alerte</a>
		</div>';
    }

	/**
	 * Displays a validation modal indicating the successful addition of an alert.
	 * The modal contains a success message and a link to the alert management page.
	 *
	 * @return void
	 */
    public function displayAddValidate(): void {
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été envoyée !</div>', esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))));
    }

	/**
	 * Displays a modal confirming the successful modification of an alert and provides a link to the alert management page.
	 *
	 * @return void The generated content for the modal, including a success message and a link to manage alerts.
	 */
    public function displayModifyValidate(): void {
        $page = get_page_by_title_V2('Gestion des alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été modifiée ! </div>', $linkManageAlert);
    }
}
