<?php

namespace Views;

class TabletView extends UserView
{
    public function displayFormTablet() {
        return '
        <h2>Compte tablette</h2>
        <p class="lead">Pour créer des utilisateurs tablette, remplissez ce formulaire avec les valeurs demandées.</p>
        <form method="post" class="cadre">
            <div class="form-group">
                <label for="loginTablet">Login</label>
                <input class="form-control" minlength="4" type="text" name="loginTablet" placeholder="Login" required="">
                <small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractères</small>
            </div>
            <div class="form-group">
                <label for="pwdTablet">Mot de passe</label>
                <input class="form-control" minlength="8" maxlength="25" type="password" id="pwdTablet" name="pwdTablet" placeholder="Mot de passe" required="" onkeyup=checkPwd("Tablet")>
                <input class="form-control" minlength="8" maxlength="25" type="password" id="pwdConfTablet" name="pwdConfirmTablet" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Tablet")>
                <small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractères</small>
            </div>
            <button type="submit" class="btn button_ecran" id="validTablet" name="createTablet">Créer</button>
        </form>';
    }

    public function displayAllTablets($users) {
        $output = '<h2>Liste des utilisateurs Tablet</h2><table class="table"><thead><tr><th>Login</th></tr></thead><tbody>';
        foreach ($users as $user) {
            $output .= '<tr><td>' . $user->getLogin() . '</td></tr>';
        }
        $output .= '</tbody></table>';
        return $output;
    }

    public function displayTabletTitleAndLogout() {
        return '
        <h2>Tablette</h2>
        <form method="post" action="' . wp_logout_url() . '">
            <button type="submit" class="btn button_ecran">Se déconnecter</button>
        </form>';
    }
}