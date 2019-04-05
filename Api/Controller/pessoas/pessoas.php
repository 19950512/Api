<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "07/02/2019",
		"CONTROLADOR": "Pessoa",
		"LAST EDIT": "07/02/2019",
		"VERSION":"0.0.1"
	}
*/
class Pessoas {

	public $_nucleo;

	private $_pessoas;

	private $drive;

	function __construct($nucleo){

		$this->_nucleo = $nucleo;

		$this->_pessoas = new Pessoas_Pessoas;
	
		$this->drive = new Drive;
	}

	function index(){
		/*
			Retorna todas as pessoas registrada na API
		*/

		if(isset($_GET)){
			$pessoas = $this->drive->_pessoas;
			echo json('ok', $pessoas);
			exit;
		}

		// Se existir POST
		if(isset($_POST)){
			$this->add();
		}
	}

	function add(){
		/*
			Registra nova pessoa
		*/

	}

	function id(){
		/*
			Retorna uma pessoa
		*/
	}

	function del(){
		/*
			Remove uma pessoa
		*/
	}
}
