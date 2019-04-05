<?php

/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "07/02/2019",
		"CONTROLADOR": "Erro404",
		"LAST EDIT": "07/02/2019",
		"VERSION":"0.0.1"
	}
*/

class Erro404 {

	public $_nucleo;

	function __construct($nucleo){

		$this->_nucleo = $nucleo;
	}

	function index(){

		echo json_encode(array('res' => 'no', 'data' => 'Controlador não encontrado.'));
		exit;
	}
}