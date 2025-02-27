<?php

namespace Views;

class CommunicatorView extends UserView
{

    /**
     * Displays the form for creating or managing department administrator accounts.
     *
     * @return string The HTML content of the form.
     */
    public function displayFormCommunicator(array $departments = null, bool $isAdmin = false, int $currentDept = null): string {

        return '
        <h2>Compte communicant</h2>
        <p class="lead">Pour créer des communicants, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Communicator', $departments, $isAdmin, $currentDept);
    }

}