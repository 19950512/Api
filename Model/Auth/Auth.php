<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "07/02/2019",
		"CONTROLADOR": "Model Auth",
		"LAST EDIT": "07/02/2019",
		"VERSION":"0.0.1"
	}
*/
class Auth_Auth {

	function __construct(){

	}

	function __destruct(){

	}

	function register($data){

		/*
			Função responsável por registrar um novo cadastro na API	
		*/

		if(isset($data['id'], $data['nome']) and !empty($data['id']) and !empty($data['nome'])){
			
			$nome 	= $data['nome'];
			$id 	= $data['id'];

			return true;
		}

		return 0;
	}

}