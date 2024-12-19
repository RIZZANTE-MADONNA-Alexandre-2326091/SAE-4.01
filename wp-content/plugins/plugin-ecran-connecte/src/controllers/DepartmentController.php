<?php

namespace Controllers;

use Models\Department;
use Views\DepartmentView;

/**
 * Class DepartmentController
 *
 *  Manage departments (create, update, delete, display)
 *
 * @package Controllers
 */
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

	/**
	 * Insert a department in the database.
	 *
	 * @return string
	 */
	public function insert(): string{
		$action = filter_input(INPUT_POST, 'submit');

		if(isset($action)){
			$name = filter_input(INPUT_POST, 'deptName');
			if(is_string($name) && strlen($name) >= 10){

				$this->model->setName($name);

				if(!$this->checkDuplicateDept($this->model)){
					$this->model->insert();
					$this->view->displayCreationSuccess();

				}else{
					$this->view->displayErrorDoubleName();
				}
			}else{
				$this->view->displayCreationError();
			}
		}

		return $this->view->displayFormDepartment();
	}

	/**
	 * Delete a department to the database.
	 */
	public function delete(): void{
		$action = filter_input(INPUT_GET, 'deleteDepartment');

		if(isset($action)) {
			if ( isset( $_REQUEST['checkboxStatusCode'] ) ) {
				$checked_values = $_REQUEST['checkboxStatusCode'];
				foreach ( $checked_values as $id ) {
					$this->model = $this->model->get($id);
					$this->model->delete();
					$this->view->refreshPage();
				}
			}
		}

	}

	/**
	 * Modify a department.
	 *
	 * @return string
	 */
	public function modify(): string{
		$id = $_GET['id'];

		if(empty($id)){
			return $this->view->noDepartment();
		}

		$action = filter_input(INPUT_POST, 'modifDept');

		if(isset($action)){
			$newName = filter_input(INPUT_POST, 'deptName');
			if(is_string($newName) && strlen($newName) > 10){

				$this->model->setId($id);
				$this->model->setName($newName);

				if(!$this->checkDuplicateDept($this->model) && $this->model->update()){

					$this->view->displayModificationValidate();
					$this->view->refreshPage();

				}else{
					$this->view->displayErrorDoubleName();
				}
			}else{
				$this->view->displayModificationError();
			}
		}

		return $this->view->modifyForm();
	}

	/**
	 * Display all departments in a table
	 *
	 * @return void
	 */
	public function displayAll(): void{
		$departments = $this->model->getAll();

		$this->view->displayAllDept($departments);
	}

	/**
	 * Check if a name already exists in the database.
	 *
	 * @param Department $department
	 *
	 * @return bool
	 */
	public function checkDuplicateDept(Department $department): bool{
		$departments = $this->model->getDepartmentName($department->getName());

		foreach($departments as $dept){
			if($dept->getName() === $department->getName()){
				return true;
			}
		}
		return false;
	}
}