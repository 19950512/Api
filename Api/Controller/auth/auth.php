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

	private $JWT;

	private $privateKey;

	private $getPublicKey;

	private $algoritimo = 'HS256';

	function __construct($nucleo){
		$this->_nucleo = $nucleo;

		/* Key openssl */
		$this->privateKey = $this->getPrivateKey();

		$this->publicKey = $this->getPublicKey();

		$this->_auth = new Auth_Auth;

		/* Instancia o JWT */
		$this->JWT = new JWT;
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

	private function generateToken(){
		/*
			Gera/codifica Token
		*/


		/*
			sub (subject) = Entidade à quem o token pertence, normalmente o ID do usuário;
			iss (issuer) = Emissor do token;
			exp (expiration) = Timestamp de quando o token irá expirar;
			iat (issued at) = Timestamp de quando o token foi criado;
			aud (audience) = Destinatário do token, representa a aplicação que irá usá-lo.
		*/
		$token = array(
		    "iss" => "abigor.com.br",
		    "aud" => "api.abigor.com.br",
		    "iat" => time(), // Momento inicial - gerou o token
		    "nbf" => time() + 300, // momento que o token PODE SER UTILIZADO
            "exp" => time() + 6900 // 2horas e 5min para expirar
		);

		$jwt = $this->JWT->encode($token, $this->privateKey, $this->algoritimo);

		return $jwt;
	}

	private function decodeToken($string){
		/*
			Decodificar o Token
		*/
		$jwt = $this->JWT->decode($string, $this->privateKey, array($this->algoritimo));

		return $jwt;
	}

	private function getPublicKey(){

		return <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

	}

	private function getPrivateKey(){

		return <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

	}
}