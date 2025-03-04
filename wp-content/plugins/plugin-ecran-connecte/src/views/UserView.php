<?php

namespace Views;


use Models\CodeAde;
use Models\User;

class UserView extends View
{

	/**
	 * Displays a base form for user input, including fields for login, email, and password.
	 *
	 * @param string $name The identifier used to customize the form field names and attributes.
	 *
	 * @return string The HTML string of the generated form.
	 */
    protected function displayBaseForm(string $name, array $departments = null, bool $isAdmin = false, int $currentDept = null):string {
        $disabled = $isAdmin ? '' : 'disabled';

        $form = '
            <form method="post" class="cadre">
            	<div class="form-group">
                	<label for="login' . $name . '">Login</label>
                	<input class="form-control" minlength="4" type="text" name="login' . $name . '" placeholder="Login" required="">
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
                </div>
                <div class="form-group">
                	<label for="email' . $name . '">Email</label>
                	<input class="form-control" type="email" name="email' . $name . '" placeholder="Email" required="">
                </div>';
        if($departments != null) {
            $form .= '<div class="form-group">
                	<label for="dept' . $name . '">Département</label>
                	<select name="deptId' . $name . '" class="form-control" ' . $disabled . '>
                		'. $this->displayAllDept($departments, $currentDept) .'
                	</select>
				</div>';
        }
        $form .= '<div class="form-group">
                	<label for="pwd' . $name . '">Mot de passe</label>
                	<input class="form-control" minlength="8" maxlength="25" type="password" id="pwd' . $name . '" name="pwd' . $name . '" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                    <input class="form-control" minlength="8" maxlength="25" type="password" id="pwdConf' . $name . '" name="pwdConfirm' . $name . '" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
                </div>
                <button type="submit" class="btn button_ecran" id="valid' . $name . '" name="create' . $name . '">Créer</button>
            </form>';

        return $form;
    }

	/**
	 * Display a form to modify the user's password.
	 *
	 * @return string The HTML content for the password modification form.
	 */
    public function displayModifyPassword(): string {
        return '
            <form id="check" method="post">
                <h2>Modifier le mot de passe</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                <label for="newPwd">Votre nouveau mot de passe</label>
                <input type="password" class="form-control text-center" name="newPwd" placeholder="Mot de passe" required="">
                <button type="submit" class="btn button_ecran" name="modifyMyPwd">Modifier</button>
            </form>';
    }

	/**
	 * Display a form to confirm account deletion by verifying the current password
	 *
	 * @return string The HTML string for the delete account form
	 */
    public function displayDeleteAccount(): string {
        return '
            <form id="check" method="post">
                <h2>Supprimer le compte</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                <button type="submit" class="btn button_ecran" name="deleteMyAccount">Confirmer</button>
            </form>';
    }

	/**
	 * Generates and returns an HTML structure for the user creation context.
	 *
	 * @return string The HTML content describing the context of creating users, including user types and their functionalities.
	 */
	public function contextCreateUser(): string {
        return '
        <hr class="half-rule">
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-2">
            <img src="' . TV_PLUG_PATH . '/public/img/user.png" alt="Logo utilisateur" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
                <h2 class="mb-3 bd-text-purple-bright">Les utilisateurs</h2>
                <p class="lead">Vous pouvez créer ici les utilisateurs</p>
                <p class="lead">Il y a plusieurs types d\'utilisateur : Les scrétaires, techniciens, télévisions.</p>
                <p class="lead">Les secrétaires peuvent poster des alertes et des informations. Ils peuvent aussi créer des utilisateurs.</p>
                <p class="lead">Les techniciens ont accès aux emplois du temps des promotions.</p>
                <p class="lead">Les télévisions sont les utilisateurs utilisés pour afficher ce site sur les téléviseurs. Les comptes télévisions peuvent afficher autant d\'emploi du temps que souhaité.</p>
            </div>
        </div>
        <a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des utilisateurs'))) . '">Voir les utilisateurs</a>';
    }

	/**
	 * Display the form to enter the account deletion code.
	 *
	 * @return string The HTML content for the account deletion code form.
	 */
    public function displayEnterCode(): string {
        return '
        <form method="post">
            <label for="codeDelete"> Code de suppression de compte</label>
            <input type="text" class="form-control text-center" name="codeDelete" placeholder="Code à rentrer" required="">
            <button type="submit" name="deleteAccount" class="btn button_ecran">Supprimer</button>
        </form>';
    }

    public function displayChangeTheme(): string {
        return '
        <div id="themePreview" class="theme-preview">Sélectionnez un thème</div>
        <div class="theme-selector-grid">
            <button class="theme-button" onclick="changeTheme(\'light\')" style="background-color: #1f63de; color: white;">Bleu AMU</button>
            <button class="theme-button" onclick="changeTheme(\'purple\')" style="background-color: #742899; color: white;">Violet AMU</button>
            <button class="theme-button" onclick="changeTheme(\'green\')" style="background-color: #009B75; color: white;">Vert AMU</button>
            <button class="theme-button" onclick="changeTheme(\'orange\')" style="background-color: #BC6C20; color: white;">Orange AMU</button>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", (event) => {
                const selectedTheme = localStorage.getItem("selectedTheme") || "light";
                document.body.classList.add(selectedTheme);
                document.getElementById("themePreview").className = "theme-preview " + selectedTheme;
            });

            function changeTheme(theme) {
                document.body.className = "";
                document.body.classList.add(theme);
                localStorage.setItem("selectedTheme", theme);
                document.getElementById("themePreview").className = "theme-preview " + theme;
            }
        </script>';
    }


    /**
     * Generates an HTML string for the unsubscribe page.
     *
     * @return string The HTML content of the unsubscribe page containing a message and the unsubscribe code.
     */
    public function displayUnsubscribe(int $code) {
        return ' 
 		<!DOCTYPE html>
             <html lang="fr">
                <head>
                    <title>Désnscription à la télé-connecté</title>
                </head>
                <body>
                    <p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté</p>
                    <p> Votre code de désinscription est : ' . $code . '.</p>
                    <p> Pour vous désinscrire, rendez-vous sur le site : <a href="' . home_url() . '/mon-compte/"> Tv Connectée.</p>
                </body>
             </html>';
    }


	/**
     * Display a form to change our own codes
     *
     * @param $codes        CodeAde[]
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     *
     * @return string
     */
    public function displayModifyMyCodes($codes, $years, $groups, $halfGroups) {
        $form = '
        <form method="post">
            <h2> Modifier mes emplois du temps</h2>
            <label>Année</label>
            <select class="form-control" name="modifYear">';
        if (!empty($codes[0])) {
            $form .= '<option value="' . $codes[0]->getCode() . '">' . $codes[0]->getTitle() . '</option>';
        }

        $form .= '<option value="0">Aucun</option>
				  <optgroup label="Année">';

        foreach ($years as $year) {
            $form .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
        $form .= '
                </optgroup>
            </select>
            <label>Groupe</label>
            <select class="form-control" name="modifGroup">';

        if (!empty($codes[1])) {
            $form .= '<option value="' . $codes[1]->getCode() . '">' . $codes[1]->getTitle() . '</option>';
        }
        $form .= '<option value="0">Aucun</option>
                  <optgroup label="Groupe">';

        foreach ($groups as $group) {
            $form .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $form .= '
                </optgroup>
            </select>
            <label>Demi-groupe</label>
            <select class="form-control" name="modifHalfgroup">';

        if (!empty($codes[2])) {
            $form .= '<option value="' . $codes[2]->getCode() . '">' . $codes[2]->getTitle() . '</option>';
        }
        $form .= '<option value="0"> Aucun</option>
                  <optgroup label="Demi-Groupe">';

        foreach ($halfGroups as $halfGroup) {
            $form .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
        $form .= '
                </optgroup>
            </select>
            <button name="modifvalider" type="submit" class="btn button_ecran">Valider</button>
         </form>';

        return $form;
    }

    /**
     * Display a message to select a schedule
     */
    public function displaySelectSchedule() {
        return '<p>Veuillez choisir un emploi du temps.</p>';
    }

	/**
	 * Display the welcome page
	 *
	 * @return string
	 */
	public function displayHome() {
		return '
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-1">
                <img src="' . TV_PLUG_PATH . '/public/img/background.png" alt="Logo Amu" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-2 text-center text-md-left pr-md-5">
                <h1 class="mb-3 bd-text-purple-bright">' . get_bloginfo("name") . '</h1>
                <p class="lead">Bienvenue sur le site de l\'écran connecté !</p>
                <p class="lead mb-4">Accédez à votre emploi du temps tant en recevant diverses informations de la part de votre département.</p>
            </div>
        </div>';
	}

    /**
     * Display a message for the modification of the password
     */
    public function displayModificationPassValidate() {
        $this->buildModal('Modification du mot de passe', '<div class="alert alert-success" role="alert">La modification à été réussie !</div>', home_url());
    }

    /**
     * Display a message if the password is wrong
     */
    public function displayWrongPassword() {
        $this->buildModal('Mot de passe incorrect', '<div class="alert alert-danger">Mauvais mot de passe</div>');
    }

    /**
     * Display a message if the
     */
    public function displayMailSend() {
        $this->buildModal('Mail envoyé', '<div class="alert alert-success"> Un mail a été envoyé à votre adresse mail, merci de bien vouloir entrer le code reçu</div>');
    }

    /**
     * Message to prevent a login already exist
     */
    public function displayErrorCreation() {
        $this->buildModal('Inscription échouée', '<div class="alert alert-danger">Il y a eu une erreur dans le formulaire, veuillez vérifier vos information et réessayer</div>');
    }

    /**
     * Message to prevent a login already exist
     */
    public function displayErrorLogin() {
        $this->buildModal('Inscription échouée', '<div class="alert alert-danger"> Le login est déjà utilisé ! </div>');
    }

    /**
     * Display to user, no lesson today
     *
     * @return string
     */
    public function displayNoStudy() {
        return '<p>Vous n\'avez pas cours!</p>';
    }

    public function errorMessageNoCodeRegister() {
        $current_user = wp_get_current_user();
        return '
        <h2>' . $current_user->user_login . '</h2>
        <p>Vous êtes enregistré sans aucun emploi du temps, rendez-vous sur votre compte pour pouvoir vous attribuez un code afin d\'accèder à votre emploi du temps</p>';
    }

    public function successMesageChangeCode() {
        $this->buildModal('Modification validée', '<div class="alert alert-success"> Le changement de groupe a été pris en compte</div>');
    }

    public function errorMesageChangeCode() {
        $this->buildModal('Modification échouée', '<div class="alert alert-danger"> Le changement de groupe n\'a pas été pris en compte</div>');
    }
}
