<?php

namespace Controllers;

use Models\Department;
use Views\DepartmentView;

class DepartmentController extends Controller
{
	/**
	 * @var Department
	 */
	private $model;

	/**
	 * @var DepartmentView
	 */
	private $view;

	/**
	 * DepartmentController constructor.
	 */
	public function __construct() {
		$this->model = new Department();
		$this->view = new DepartmentView();
	}

	public function insertDept(){
		//TODO
	}

	public function deleteDept(){
		//TODO
	}

	public function updateDept(){
		//TODO
	}
}