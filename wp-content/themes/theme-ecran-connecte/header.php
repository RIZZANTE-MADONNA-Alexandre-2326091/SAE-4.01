                                </div>
                            </li>
						<?php endif;
						if (in_array('administrator', $user_info->roles) || in_array('adminDept', $user_info->roles)  || in_array('secretaire', $user_info->roles)) : ?>
                            <li class="nav-item dropdown active">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Alertes</a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Créer une alerte'))); ?>">Créer une alerte</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Gestion des alertes'))); ?>">Voir mes alertes</a>
                                </div>
                            </li>
						<?php endif;
						if (in_array('administrator', $user_info->roles) || in_array('adminDept', $user_info->roles) || in_array('secretaire', $user_info->roles)) : ?>
                            <li class="nav-item dropdown active">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Utilisateurs</a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Créer un utilisateur'))); ?>">Créer un utilisateur</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Gestion des utilisateurs'))); ?>">Voir les utilisateurs</a>
                                </div>
                            </li>
						<?php endif;
						if (in_array('administrator', $user_info->roles)) : ?>
                            <li class="nav-item dropdown active">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Départements</a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Créer un département'))); ?>">Créer un département</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Gestion des départements'))); ?>">Voir les départements</a>
                                </div>
                            </li>
						<?php endif;
						if (in_array('administrator', $user_info->roles) || in_array('adminDept', $user_info->roles) ) : ?>
                            <li class="nav-item active">
                                <a class="nav-link" href="<?php echo esc_url(get_permalink(get_page_by_title_V2('Gestion des codes ADE'))); ?>">Code ADE</a>
                            </li>
						<?php endif; ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo esc_url(get_permalink(get_page_by_title_V2("Mon compte"))); ?>"><?php echo wp_get_current_user()->user_login; ?></a>
                        </li>
                        <li class="nav-item active my-2 my-lg-0">
                            <a class="nav-link" href="<?php echo wp_logout_url(get_home_url()); ?>">Déconnexion</a>
                        </li>
                    </ul>
				<?php else : ?>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo wp_login_url(get_home_url()); ?>">Connexion</a>
                        </li>
                    </ul>
				<?php endif; ?>
            </div>
        </nav>
	<?php endif; ?>
</header>
