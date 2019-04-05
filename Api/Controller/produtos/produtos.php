<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "08/02/2019",
		"CONTROLADOR": "Produtos",
		"LAST EDIT": "08/02/2019",
		"VERSION":"0.0.1"
	}
*/
class Produtos {

	public $_nucleo;

	private $_produtos;

	private $drive;

	function __construct($nucleo){

		$this->_nucleo = $nucleo;

		$this->_produtos = new Produtos_Produtos;
	
		$this->drive = new Drive;
	}

	function index(){
		/*
			Retorna todas as pessoas registrada na API
		*/

		if(isset($_GET)){
			$produtos = $this->drive->_produtos;
			echo json('ok', $produtos);
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
