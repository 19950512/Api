<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "14/08/2018",
		"CONTROLADOR": "Auth",
		"LAST EDIT": "06/05/2018",
		"VERSION":"0.0.4"
	}
*/
class Auth {

	public $_nucleo;

	public $_drive;

	function __construct($nucleo){

		$this->_nucleo = $nucleo;

		$this->_drive = new Drive;
	}

	function index(){
		exit;
	}

	function register(){
		/*
			Action responsavel por efetuar um registro na API
			Para ser gerado um token de acesso
		*/
		// Se existir NOME e SENHA, vamos criar o acesso
		if(isset($_POST['acc_name'], $_POST['acc_password']) and !empty($_POST['acc_name']) and !empty($_POST['acc_password'])){
			
			// passa os dados por um striptags, trim, etc..
			$acc_name 		= $this->_nucleo->basic($_POST['acc_name']);
			$acc_password 	= $_POST['acc_password']; // Excerto senha que não

			// tenta registrar o novo usuário
			$registra = $this->_drive->authRegister(array('acc_name' => $acc_name, 'acc_password' => $acc_password));

			// Resposta da model
			switch ($registra) {

				case 0:
					echo json('no', 'Não identificamos seu Nome nem seu Password, porfavor, informe um Name e seu Password para efetuar seu registro.');
					break;

				case 1:
					echo json('no', 'Já existe um registro com este nome, tente outro.');
					break;

				case 2:

					echo json('ok', 'Registrado com sucesso! Agora você já pode utilizar a API com as credenciais informadas');
					break;
				
				default:

					echo json('no', 'Algo de errado não está certo! hmm...');
					break;
			}

			exit;
		}

		echo json('no', 'Ops, para efetuar seu registro, você precisa informar um nome se senha.');
		exit;
	}


	function authorize(){
		/* 
			Valida e verifica o token de acesso à API
		*/
	}

	function getToken(){
		/*
			Action responsavel por devolver um Token de acesso
		*/
		// Se existir NOME e SENHA, vamos criar o acesso
		if(isset($_POST['acc_name'], $_POST['acc_password']) and !empty($_POST['acc_name']) and !empty($_POST['acc_password'])){
			
			// passa os dados por um striptags, trim, etc..
			$acc_name 		= $this->_nucleo->basic($_POST['acc_name']);
			$acc_password 	= $_POST['acc_password']; // Excerto senha que não

			// tenta gerar um token
			$token = $this->_drive->authAuthUser(array('acc_name' => $acc_name, 'acc_password' => $acc_password));

			// Resposta da model
			switch ($token){

				case 0:
					echo json('no', 'Mano, eu preciso das suas credenciais para autenticar!');
					exit;

				case 1:
					echo json('no', 'Ops, não existe nenhuma conta com as credenciais informadas!');
					exit;

				case 2:

					/* Gera o Token */
					$token = $this->_drive->authGenerateToken();
					
					/* Retorna o Token para o usuário */
					echo json('ok', array(
							"token" => $token,
						)
					);
					exit;
			}
		}

		echo json('no', 'Ops, para gerar um token, você precisa informar suas credenciais!');
		exit;
	}

    function __call($method, $args) {

		$trace = debug_backtrace();
		$class = $trace[1]['class'];
		if(method_exists($class, $method)){

			return true;
		}

        throw new Sempermissao(MSG_ERRO_ACTION);
    }
}