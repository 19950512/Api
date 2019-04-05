<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "14/08/2018",
		"CONTROLADOR": "Auth",
		"LAST EDIT": "18/08/2018",
		"VERSION":"0.0.2"
	}
*/
class Auth {

	public $_nucleo;

	private $_auth;

	function __construct($nucleo){
		$this->_nucleo = $nucleo;

		$this->_auth = new Auth_Auth;
	}

	function index(){
		exit;
	}

	function register(){
		/*
			Action responsavel por efetuar um registro na API
			Para ser gerado um token de acesso
		*/

		// Se existir ID e NOME, vamos criar o acesso
		if(isset($_POST['id'], $_POST['nome']) and !empty($_POST['id']) and !empty($_POST['nome'])){
			
			// passa os dados por um striptags, trim, etc..
			$id 	= $this->_nucleo->basic($_POST['id']);
			$nome 	= $this->_nucleo->basic($_POST['nome']);

			// tenta registrar o novo usuário
			$registra = $this->_auth->register(array('id' => $id, 'nome' => $nome));

			// Resposta da model
			switch ($registra) {

				case 0:
					echo json('no', 'Não identificamos seu ID nem seu NOME, porfavor, informe um ID e seu NOME para efetuar seu registro.');
					break;

				case 1:
					echo json('no', 'Já existe um registro com este ID, tente outro.');
					break;
				
				default:
					// Se retornar TRUE
					echo json('ok', 'Registrado com sucesso! Agora você já pode utilizar a API.');
					break;
			}

			exit;
		}

		echo json('no', 'Ops, para efetuar seu registro, você precisa informar um ID e seu NOME.');
		exit;
	}

	function authorize(){
		/* 
			Valida e verifica o token de acesso à API
		*/
	}

	private function generateToken($string){
		/*
			Gera/codifica Token
		*/
		return base64_encode($string);
	}

	private function decodeToken($string){
		/*
			Decodificar o Token
		*/
		return base64_decode($string);
	}
}