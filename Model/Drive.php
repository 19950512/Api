<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "08/02/2019",
		"CONTROLADOR": "Model DRIVE",
		"LAST EDIT": "08/02/2019",
		"VERSION":"0.0.1"
	}
*/

// Aqui fica todas as informações/models da API, TÁ TUDO AQUI!
class Drive {

	function __construct(){

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