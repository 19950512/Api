<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "08/02/2019",
		"CONTROLADOR": "Model DRIVE",
		"LAST EDIT": "06/04/2019",
		"VERSION":"0.0.3"
	}
*/

class Drive {

	public $_conexao;

	function __construct(){

		/* Pega as credenciais para a conexao */
		$config_conexao['host'] = '';
		$config_conexao['db'] = '';
		$config_conexao['user'] = '';
		$config_conexao['pass'] = '';
		$config_conexao['port'] = '';

		/* Check se existe os dados para conexão*/
		if(function_exists('postgreSQL')){
			$config_conexao = postgreSQL();
		}

		/* Manda as configurações para a conexão */
		$con = new Conexao_Conexao($config_conexao['host'], $config_conexao['db'], $config_conexao['user'], $config_conexao['pass'], $config_conexao['port']);

		/* Tenta conexão */
		$this->_conexao = $con->con();
	}

	function checkOrigin(){

		/* Função responsavel por válidar o HTTP_ORIGIN */

		// SE EXIR UMA ORIGIN DO REQUEST
		if(isset($_SERVER['HTTP_ORIGIN'])){

			// ARMAZENA O HTTP_ORIGIN
			$HTTP_ORIGIN = $_SERVER['HTTP_ORIGIN'];

			// SE NÃO EXISTIR ESSE HTTP_ORIGIN, O REQUEST DEVE FALHAR POIS NÃO ESTÁ AUTORIZADO !
			if(!isset($this->_origins[$HTTP_ORIGIN])){

				echo json('no', 'Você não está autorizado a acessar a API.');
				exit;
			}
		
			// HTTP_ORIGIN IDENFICADO, VALIDADO E APROVADO
			// ATUALIZA O ID DO CLIENTE
			$this->_clienteID = $this->_origins[$HTTP_ORIGIN];

		}else{

			// NÃO IDENTIFICADO O HTTP_ORIGIN
			echo json('no', 'Você não está acessando a API de onde deveria... O.0"');
			exit;
		}

	}
}