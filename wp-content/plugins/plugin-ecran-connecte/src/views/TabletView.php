<?php
namespace Views;

use Models\CodeAde;

class TabletView extends UserView
{
    public function displayFormTablet(array $departments, $isAdmin = null, $currentDept = null, array $years, array $groups, array $halfGroups) {
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
                <label>Premier emploi du temps</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" class="btn button_ecran" id="addSchedule" onclick="addButtonTablet()" value="Ajouter des emplois du temps">
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

    public function buildSelectCode(array $years, array $groups, array $halfGroups, CodeAde $code = null, int $count = 0): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectTablet[]" required="">';

        if (!is_null($code)) {
            $select .= '<option value="' . $code->getCode() . '">' . $code->getTitle() . '</option>';
        }

        $select .= '<option value="0">Aucun</option>
                    <optgroup label="Année">';

        foreach ($years as $year) {
            $select .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option>';
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
}