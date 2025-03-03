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
        ' . $this->displayBaseForm('Comm', $departments, $isAdmin, $currentDept);
    }

    public function displayWelcomeCommunicator(): string
    {
        return '
        <div class="row">
            <div class="col-6 mx-auto col-md-6 order-md-1">
                <img src="' . TV_PLUG_PATH . '/public/img/background.png" alt="Logo Amu" class="img-fluid mb-3 mb-md-0">
            </div>
            <div class="col-md-6 order-md-2 text-center text-md-left pr-md-5">
                <h1 class="mb-3 bd-text-purple-bright">' . get_bloginfo("name") . '</h1>
                <p class="lead">
                    Créer des informations pour toutes les télévisions connectées, les informations seront affichées sur chaque télévision en plus des informations déjà publiées.
                    Les informations des télévisions peuvent contenir du texte, des images, des PDFs et même des vidéos de type short ou en grand format.
                </p>
                <p class="lead mb-4">Vous pouvez faire de même avec les alertes des télévisions connectées.</p>
                <p class="lead mb-4">Les informations seront affichés dans la partie de droite des télévisions et les alertes dans la partie rouge en bas des téléviseurs.</p>
                <div class="row mx-n2">
                    <div class="col-md px-2">
                        <a href="' . esc_url(get_permalink(get_page_by_title_V2("Créer une information"))) . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une information</a>
                    </div>
                    <div class="col-md px-2">
                        <a href="' . esc_url(get_permalink(get_page_by_title_V2("Créer une alerte"))) . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une alerte</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="masthead-followup row m-0 border border-white">
            <div class="col-md-6 p-3 p-md-5 bg-light border border-white">
                <h3><img src="' . TV_PLUG_PATH . '/public/img/+.png" alt="Ajouter une information/alerte" class="logo">Ajouter</h3>
                <p>Ajouter une information ou une alerte. Elles seront affichées le lendemain sur toutes les télévisions.</p>
                <a href="' . esc_url(get_permalink(get_page_by_title_V2("Créer une information"))) . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une information</a>
                <hr class="half-rule">
                <a href="' . esc_url(get_permalink(get_page_by_title_V2("Créer une alerte"))) . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une alerte</a>
            </div>
            <div class="col-md-6 p-3 p-md-5 bg-light border border-white">
                <h3><img src="' . TV_PLUG_PATH . '/public/img/gestion.png" alt="voir les informations/alertes" class="logo">Gérer</h3>
                <p>Voir toutes les informations et alertes déjà publiées. Vous pouvez les supprimers, les modifiers ou bien juste les regarder.</p>
                <a href="' . esc_url(get_permalink(get_page_by_title_V2("Gestion des informations"))) . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir mes informations</a>
                <hr class="half-rule">
                <a href="' . esc_url(get_permalink(get_page_by_title_V2("Gestion des alertes"))) . '" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir mes alertes</a>
            </div>
        </div>';
    }


	public function displayAllCommunicator(array $users): string {
		$title = 'Communicants';
		$name = 'Comm';
		$header = ['Login'];

		$row = array();
		$count = 0;
		foreach ($users as $user) {
			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
		}

		return $this->displayAll($name, $title, $header, $row, $name);
	}

}