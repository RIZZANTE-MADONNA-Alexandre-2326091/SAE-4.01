<?php
namespace Views;

use Models\CodeAde;

class TabletView extends UserView
{
    public function displayFormTablet(array $departments, $isAdmin = null, $currentDept = null, array $rooms) {
        $disabled = $isAdmin ? '' : 'disabled';

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
        <div class="form-group">
            <label for="deptTablet">Département</label>
            <select name="deptTablet" class="form-control"' . $disabled . '>
                ' . $this->displayAllDept($departments, $currentDept) . '
            </select>
        </div>
        <div class="form-group">
            <label>Salle : </label>' .
            $this->buildSelectCode($rooms) . '
        </div>
        <button type="submit" class="btn button_ecran" id="validTablet" name="createTablet">Créer</button>
    </form>';
    }

    public function displayAllTablets($users, $userDeptList) {
        $output = '<h2>Liste des utilisateurs Tablet</h2><table class="table"><thead><tr><th>Login</th><th>Département</th></tr></thead><tbody>';
        foreach ($users as $index => $user) {
            $output .= '<tr><td>' . $user->getLogin() . '</td><td>' . $userDeptList[$index] . '</td></tr>';
        }
        $output .= '</tbody></table>';
        return $output;
    }

    public function buildSelectCode(array $rooms, CodeAde $code = null, int $count = 0): string {
        if (empty($rooms)) {
            return '<p class="text-danger">Aucune salle disponible.</p>';
        }

        $select = '<select name="selectTablet[]" class="form-control" required>';
        foreach ($rooms as $room) {
            if (method_exists($room, 'getCode') && method_exists($room, 'getTitle')) {
                $select .= '<option value="' . htmlspecialchars($room->getCode()) . '">' . htmlspecialchars($room->getTitle()) . '</option>';
            }
        }
        $select .= '</select>';

        return $select;
    }


    // Views/TabletView.php
    public function displayRoomSchedule(array $rooms): string
    {
        $output = '<h2>Emplois du temps des salles</h2>';

        foreach ($rooms as $room) {
            $icsUrl = "https://exemple.com/ade/export?code=" . $room->getCode();
            $output .= '
        <div class="room-schedule">
            <h3>' . htmlspecialchars($room->getTitle()) . '</h3>
            ' . do_shortcode('[ics_calendar url="' . $icsUrl . '"]') . '
        </div>';
        }

        return $output;
    }

}