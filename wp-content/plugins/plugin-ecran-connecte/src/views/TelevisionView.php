<?php

namespace Views;


use Models\CodeAde;
use Models\User;

/**
 * Class TelevisionView
 *
 * Contain all view for television (Forms, tables)
 *
 * @package Views
 */
class TelevisionView extends UserView
{
	/**
	 * Generates and displays the television account creation form.
	 *
	 * @param array $years An array of years to populate the dropdown for schedule selection.
	 * @param array $groups An array of groups used for schedule selection.
	 * @param array $halfGroups An array of half-groups used for schedule selection.
	 *
	 * @return string The HTML string of the television account creation form.
	 */
    public function displayFormTelevision(array $years,array $groups, array $halfGroups): string {
        $form = '
        <h2> Compte télévision</h2>
        <p class="lead">Pour créer des télévisions, remplissez ce formulaire avec les valeurs demandées.</p>
        <p class="lead">Vous pouvez mettre autant d\'emploi du temps que vous souhaitez, cliquez sur "Ajouter des emplois du temps</p>
        <form method="post" id="registerTvForm">
            <div class="form-group">
            	<label for="loginTv">Login</label>
            	<input type="text" class="form-control" name="loginTv" placeholder="Nom de compte" required="">
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
            	<label for="pwdTv">Mot de passe</label>
            	<input type="password" class="form-control" id="pwdTv" name="pwdTv" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tv")>
            	<input type="password" class="form-control" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tv")>
            	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <!--Formulaire type de défilement des vidéos-->
            <div class="form-group">
                <p class="lead">Choisissez le mode d\'affichage des vidéos classiques</p>
                <label for="defilement">Défilement entre les emplois du temps</label>
                <input type="radio" name="defilement" value="defil"/>
                <br>
                <label for="defilement">Sur-impréssion par-dessus les emplois du temps</label>
                <input type="radio" name="defilement" value="suret"/>
            </div>
            <!--Formulaire temps de défilement des vidéos-->
            <div class="form-group">
                <label for="temps">Temps de défilement des informations</label>
                <input type="number" name="temps" placeholder="Temps en secondes (par défaut 10s)">
            </div>
            <div class="form-group">
            	<label>Premier emploi du temps</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" class="btn button_ecran" id="addSchedule" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button type="submit" class="btn button_ecran" id="validTv" name="createTv">Créer</button>
        </form>';

        return $form;
    }

	/**
	 * Displays all televisions with related user information.
	 *
	 * This method generates a structured representation of televisions, including details
	 * about the user login, number of schedules, and modification options.
	 *
	 * @param array $users An array of user objects, where each object contains user details such as ID, login, and codes.
	 *
	 * @return string A formatted string representation of televisions and their associated user information.
	 */
    public function displayAllTv(array $users): string {
        $page = get_page_by_title_V2('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Televisions';
        $name = 'Tele';
        $header = ['Login', 'Nombre d\'emplois du temps', 'Temps de défilement', 'Type de défilement', 'Modifier'];

        $row = array();
        $count = 0;
        //On affiche les valeurs de certains attributs pour les télévisions
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), sizeof($user->getCodes()), $user->getTimeout() . ' s', $user->getTypeDefilement(), $this->buildLinkForModify($linkManageUser . '?id=' . $user->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, 'tele');
    }

	/**
	 * Generates and returns the HTML form for modifying a user's schedule data.
	 *
	 * This method creates a dynamic form based on the user's existing schedule codes, with options
	 * to update, add, or remove schedules. The form includes functionality for interacting with the
	 * provided years, groups, and half-groups data.
	 *
	 * @param User $user The user object containing details such as login and existing schedule codes.
	 * @param array $years An array of available years to populate selection fields.
	 * @param array $groups An array of available
	 */
    public function modifyForm(User $user, array $years, array $groups, array $halfGroups): string {
        $count = 0;
        $string = '
        <a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
         <form method="post" id="registerTvForm">
            <!--Formulaire type de défilement des vidéos-->
            <div class="form-group">
                <p class="lead">Choisissez le mode d\'affichage des vidéos classiques</p>
                <label for="defilement">Défilement entre les emplois du temps</label>
                <input type="radio" name="defilement" value="defil"/>
                <br>
                <label for="defilement">Sur-impréssion par-dessus les emplois du temps</label>
                <input type="radio" name="defilement" value="suret"/>
            </div>
            <!--Formulaire temps de défilement des vidéos-->
            <div class="form-group">
                <label for="temps">Temps de défilement des informations</label>
                <input type="number" name="temps" placeholder="Temps en secondes (par défaut 10s)">
            </div>
            <label id="selectId1"> Emploi du temps</label>';

        foreach ($user->getCodes() as $code) {
            $count = $count + 1;
            if ($count == 1) {
                $string .= $this->buildSelectCode($years, $groups, $halfGroups, $code, $count);
            } else {
                $string .= '
					<div class="row">' .
                    $this->buildSelectCode($years, $groups, $halfGroups, $code, $count) .
                    '<input type="button" id="selectId' . $count . '" onclick="deleteRow(this.id)" class="btn button_ecran" value="Supprimer">
					</div>';
            }
        }

        if ($count == 0) {
            $string .= $this->buildSelectCode($years, $groups, $halfGroups, null, $count);
        }

        $page = get_page_by_title_V2('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $string .= '
            <input type="button" class="btn button_ecran" id="addSchedule" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <button name="modifValidate" class="btn button_ecran" type="submit" id="validTv">Valider</button>
            <a href="' . $linkManageUser . '" id="linkReturn">Annuler</a>
        </form>';
        return $string;
    }

	/**
	 * Builds an HTML select element with options for years, groups, and half-groups.
	 *
	 * This method dynamically generates a dropdown menu containing categorized groups of options,
	 * including options for years, groups, and half-groups. An initial option tied to an existing
	 * CodeAde object can be included if provided.
	 *
	 * @param array $years An array of objects representing years. Each object must have methods getCode() and getTitle() to retrieve the year code and title respectively.
	 * @param array $groups An array of objects representing groups. Each object must have methods getCode() and getTitle() to retrieve the group code and title respectively.
	 * @param array $halfGroups An array of objects representing half-groups. Each object must have methods getCode() and getTitle() to retrieve the half-group code and title respectively.
	 * @param CodeAde|null $code An optional object representing a preselected code. The object must provide getCode() and getTitle() methods. If null, no predefined option is selected.
	 * @param int $count A unique identifier used to differentiate the generated select element, typically in its ID attribute. Default is 0.
	 *
	 * @return string The generated HTML string for the select element, including all options and optgroup elements.
	 */
    public function buildSelectCode(array $years, array $groups, array $halfGroups, CodeAde $code = null, int $count = 0): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectTv[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">' . $code->getTitle() . '</option>';
        }

        $select .= '<option value="0">Aucun</option>
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
	 * Generates the HTML form for modifying a password.
	 *
	 * This method creates and returns an HTML string representation of a form,
	 * allowing users to input and confirm a new password. It includes validation
	 * attributes such as a minimum length and utilizes JavaScript for real-time
	 * password matching checks.
	 *
	 * @return string The HTML form as a string for modifying a password.
	 */
    public function modifyPassword(): string {
        return '
		<form method="post">
		<label>Nouveau mot de passe </label>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Nouveau mot de passe" onkeyup=checkPwd("Tv")>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le nouveau mot de passe" onkeyup=checkPwd("Tv")>
		</form>';

    }

	/**
	 * Generates and returns the HTML structure for the start of a slideshow container.
	 *
	 * This method provides the opening HTML tag for a slideshow container with predefined
	 * attributes and classes for styling and functionality.
	 *
	 * @return string The HTML string representing the opening of a slideshow container.
	 */
    public function displayStartSlide(): string {
        return '<div id="slideshow-container" class="slideshow-container">';
    }

	/**
	 * Displays a middle slide structure for a slideshow.
	 *
	 * This method generates the HTML structure for a single middle slide
	 * in the slideshow presentation.
	 *
	 * @return string A string containing the HTML structure for the middle slide.
	 */
    public function displayMidSlide(): string {
        return '<div class="mySlides">';
    }
}
