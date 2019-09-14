<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "08/02/2019",
		"CONTROLADOR": "Model DRIVE",
		"LAST EDIT": "13/09/2019",
		"VERSION":"0.0.5"
	}
*/

class Drive extends Auth_Auth {

	public $_conexao;

	public $_hash;

	public $privateKey;

	public $getPublicKey;

	public $algoritimo = 'HS256';

	function __construct(){

		/* Check header para conversar com a API, PRECISA DISSO!! */
		$this->checkHeaders();

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

		/* Criptografia da API */
		$this->_hash = new Hash;

		/* Key openssl */
		$this->privateKey = $this->authGetPrivateKey();

		$this->publicKey = $this->authGetPublicKey();

		/* Instancia o JWT */
		$this->JWT = new JWT;

	}

	/*
		Para conversar com a API, precisa-se do header Maydana => Lindo, gostoso e ticudo
	*/
	function checkHeaders(){

		if(PRODUCAO === false){
			return false;
		}


		/* Se não houver o header MAYDANA OU o valor do header for DIFERENTE DE */
		if(!isset($_SERVER[HEADER_PERMISSAO]) or $_SERVER[HEADER_PERMISSAO] !== HEADER_PERMISSAO_VALOR){

			echo json('no', MSG_HEADERLESS);
			exit;
		}
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