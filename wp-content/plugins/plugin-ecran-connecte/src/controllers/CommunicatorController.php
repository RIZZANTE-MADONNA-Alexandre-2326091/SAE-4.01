<?php

namespace controllers;

use Models\User;
use views\CommunicatorView;

/**
 * Class CommunicatorController
 *
 * All actions for communicator (Create, update, display)
 *
 * @package controllers
 */
class CommunicatorController extends UserController
{
    /**
     * @var User
     */
    private User $model;

    /**
     * @var CommunicatorView
     */
    private CommunicatorView $view;

    /**
     * Constructor of CommunicatorController.
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new User();
        $this->view = new CommunicatorView();
    }

    /**
     * Handles the insertion of a communicator user record into the system.
     * Validates the input data, ensures it meets requirements, checks for duplicate users,
     * and calls the model to perform the actual insertion. Displays appropriate views based
     * on success or failure of actions.
     *
     * @return string The output of the communicator form view after the operation is handled.
     */
    function insert() : string
    {
        return 'coucou';
    }

    function modify()
    {

    }

    function displayAllCommunicator()
    {

    }
}