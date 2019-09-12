<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "05/04/2019",
		"CONTROLADOR": "Model Conexão",
		"LAST EDIT": "12/09/2019",
		"VERSION":"0.0.2"
	}
*/
class Conexao_Conexao {

	private $host;

	private $db;

	private $user;

	private $pass;

	private $port;

	function __construct($host, $db, $user, $pass, $port){
		$this->host = $host;
		$this->db 	= $db;
		$this->user = $user;
		$this->pass = $pass;
		$this->port = $port;
	}

	function __destruct(){

	}

	function con(){

		try{

			// POSTGRES
			$PDO = new PDO('pgsql:host='.$this->host.' dbname='.$this->db.' user='.$this->user.' password='.$this->pass.' port='.$this->port.'');
    		$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return $PDO;

		}catch(PDOException $e){

			return 'erro conexao';
			exit;
		}
	}
}