<?php

namespace Views;

use Models\CodeAde;
use Models\User;

/**
 * Class TechnicianView
 *
 * Contain all view for technician (Forms, tables)
 *
 * @package Views
 */
class TechnicianView extends UserView
{
    /**
     * Displays the form for creating technician accounts with schedule selection.
     *
     * @param array $years An array of years to populate the dropdown for schedule selection.
     * @param array $groups An array of groups used for schedule selection.
     * @param array $halfGroups An array of half-groups used for schedule selection.
     *
     * @return string The rendered output of the technician account creation form.
     */
    public function displayFormTechnician(array $years, array $groups, array $halfGroups): string {
        return '
        <h2>Compte technicien</h2>
        <p class="lead">Pour créer des techniciens, remplissez ce formulaire avec les valeurs demandées.</p>
        <p class="lead">Vous pouvez associer des emplois du temps au technicien, cliquez sur "Ajouter des emplois du temps".</p>
        <form method="post" id="registerTechForm">
            <div class="form-group">
                <label for="loginTech">Login</label>
                <input type="text" class="form-control" name="loginTech" placeholder="Nom de compte" required="">
                <small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractères.</small>
            </div>
            <div class="form-group">
                <label for="pwdTech">Mot de passe</label>
                <input type="password" class="form-control" id="pwdTech" name="pwdTech" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tech")>
                <input type="password" class="form-control" id="pwdConfTech" name="pwdConfirmTech" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("Tech")>
                <small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractères.</small>
            </div>
            <div class="form-group">
                <label>Premier emploi du temps</label>' .
            $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" class="btn button_ecran" id="addSchedule" onclick="addButtonTech()" value="Ajouter des emplois du temps">
            <button type="submit" class="btn button_ecran" id="validTech" name="createTech">Créer</button>
        </form>';
    }

    /**
     * Displays all technicians with their corresponding login information and number of schedules.
     *
     * @param array $users An array of user objects, where each object represents a technician and contains relevant data such as ID, login, and codes.
     *
     * @return string The rendered output of the technicians' data in a formatted display.
     */
    public function displayAllTechnicians(array $users): string {
        $page = get_page_by_title_V2('Modifier un utilisateur');
        $linkManageUser = get_permalink($page->ID);

        $title = 'Techniciens';
        $name = 'Tech';
        $header = ['Login', 'Nombre d\'emplois du temps', 'Modifier'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), sizeof($user->getCodes()), $this->buildLinkForModify($linkManageUser . '?id=' . $user->getId())];
        }

        return $this->displayAll($name, $title, $header, $row, $name);
    }

    /**
     * Displays the form for modifying a technician account, including schedule selection.
     *
     * @param User $user The technician to modify.
     * @param array $years An array of years to populate the dropdown for schedule selection.
     * @param array $groups An array of groups used for schedule selection.
     * @param array $halfGroups An array of half-groups used for schedule selection.
     *
     * @return string The rendered output of the technician modification form.
     */
    public function modifyForm(User $user, array $years, array $groups, array $halfGroups): string {
        $count = 0;
        $string = '
        <a href="' . esc_url(get_permalink(get_page_by_title_V2('Gestion des utilisateurs'))) . '">< Retour</a>
        <h2>' . $user->getLogin() . '</h2>
        <form method="post" id="registerTechForm">
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
            <input type="button" class="btn button_ecran" id="addSchedule" onclick="addButtonTech()" value="Ajouter des emplois du temps">
            <button name="modifValidate" class="btn button_ecran" type="submit" id="validTech">Valider</button>
            <a href="' . $linkManageUser . '" id="linkReturn">Annuler</a>
        </form>';
        return $string;
    }

    /**
     * Builds an HTML select element with options for years, groups, and half-groups.
     *
     * @param array $years An array of objects representing years.
     * @param array $groups An array of objects representing groups.
     * @param array $halfGroups An array of objects representing half-groups.
     * @param CodeAde|null $code An optional object representing a preselected code.
     * @param int $count A unique identifier used to differentiate the generated select element.
     *
     * @return string The generated HTML string for the select element.
     */
    public function buildSelectCode(array $years, array $groups, array $halfGroups, CodeAde $code = null, int $count = 0): string {
        $select = '<select class="form-control firstSelect" id="selectId' . $count . '" name="selectTech[]" required="">';

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