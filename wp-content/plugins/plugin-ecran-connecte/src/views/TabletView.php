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

    public function displayAllTablets(array $users, array $userDeptList): string {
        $title = 'Tablettes';
        $name = 'Tablet';
        $header = ['Login', 'Département', 'Modifier'];

        $rows = [];
        foreach ($users as $index => $user) {
            $rows[] = [
                $index + 1,
                $this->buildCheckbox($name, $user->getId()),
                htmlspecialchars($user->getLogin()),
                htmlspecialchars($userDeptList[$index] ?? 'Non assigné'),
                '<a href="' . esc_url(get_permalink(get_page_by_title_V2('Modifier un utilisateur')->ID) . '?id=' . $user->getId()) . '" class="btn button_ecran">Modifier</a>'
            ];
        }

        return $this->displayAll($name, $title, $header, $rows, $name);
    }


    public function displayRoomSchedule(array $rooms): string {
        $output = '<a class="welcome-text" href="/wp-login.php?action=logout">Emploi du temps</a>';

        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $startDate = strftime('%A %d %B %Y', strtotime("monday this week"));
        $endDate = strftime('%A %d %B %Y', strtotime("friday this week"));

        foreach ($rooms as $room) {
            $icsUrl = "https://ade-web-consult.univ-amu.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?projectId=8&resources=" . $room->getCode() . "&calType=ical&firstDate=" . date('Y-m-d', strtotime("monday this week")) . "&lastDate=" . date('Y-m-d', strtotime("friday this week"));

            $output .= '
        <div class="room-schedule">
            <h3>' . htmlspecialchars($room->getTitle()) . '</h3>
            <p>Du ' . $startDate . ' au ' . $endDate . '</p>
            ' . do_shortcode('[ics_calendar url="' . $icsUrl . '" view="week" time_min="08:00" time_max="20:00"]') . '
        </div>';
        }

        return $output;
    }

    public function modifyForm(User $user, array $rooms, array $departments, $isAdmin = null, $currentDept = null): string {
        $disabled = $isAdmin ? '' : 'disabled';

        // Récupérer la salle actuelle
        $currentRoom = $user->getCodes()[0] ?? null;

        return '
    <a class="returnbutton" href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des utilisateurs'))) . '">< Retour</a>
    <h2>Modifier la tablette ' . htmlspecialchars($user->getLogin()) . '</h2>
    <form method="post" class="cadre">
        <div class="form-group">
            <label for="loginTablet">Login</label>
            <input class="form-control" type="text" value="' . htmlspecialchars($user->getLogin()) . '" disabled>
        </div>
        <div class="form-group">
            <label for="deptTablet">Département</label>
            <select name="deptTablet" class="form-control" ' . $disabled . '>
                ' . $this->displayAllDept($departments, $currentDept) . '
            </select>
        </div>
        <div class="form-group">
            <label>Salle : </label>' .
            $this->buildSelectCode($rooms, $currentRoom) . '
        </div>
        <button type="submit" class="btn button_ecran" name="modifyTablet">Enregistrer</button>
    </form>';
    }

    private function buildSelectCode(array $rooms, ?CodeAde $currentRoom = null): string {
        $select = '<select class="form-control" name="selectTablet" required>';

        if ($currentRoom) {
            $select .= '<option value="' . $currentRoom->getCode() . '" selected>' . $currentRoom->getTitle() . '</option>';
        }

        $select .= '<option value="0">Aucune</option>';

        foreach ($rooms as $room) {
            $select .= '<option value="' . $room->getCode() . '">' . htmlspecialchars($room->getTitle()) . '</option>';
        }

        $select .= '</select>';
        return $select;
    }

}