<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "07/02/2019",
		"CONTROLADOR": "Model Auth",
		"LAST EDIT": "07/02/2019",
		"VERSION":"0.0.2"
	}
*/
class Auth_Auth {

	/* Registra uma nova conta na API / account */
	function authRegister($data){

		/*
			Função responsável por registrar um novo cadastro na API	
		*/
		if(isset($data['acc_name'], $data['acc_password']) and !empty($data['acc_name']) and !empty($data['acc_password'])){

			$acc_name 		= Static_Basic::basic($data['acc_name']);
			$acc_password 	= $data['acc_password']; // Esse não pode passar strip_tags nem trim nem nada

			/* Senha criptografada */
			$acc_password	= $this->_hash->encrypt($acc_password);

			$sql = $this->_conexao->prepare('
				INSERT INTO account (
					acc_name,
					acc_password
				) VALUES (
					:acc_name,
					:acc_password
				)
			');
			$sql->bindParam(':acc_name', $acc_name);
			$sql->bindParam(':acc_password', $acc_password);
			$sql->execute();
			$temp = $sql->fetch(PDO::FETCH_ASSOC);

			/* Se houver errorInfo[1] === 7 é acc_name duplicado */
			if(isset($sql->errorInfo()[1]) and $sql->errorInfo()[1] === 7){
				return 1;
			}

			$sql = null;

			/* Se registrar com sucesso */
			if(is_array($temp) and count($temp) >= 0 and $temp !== false){
				return 2;
			}

			return false;
		}

		return 0;
	}

	/* Authentica usuário, se de fato ele está registrado */
	function authAuthUser($data){

		/*
			Função responsável por validar um registro na API	
		*/
		if(isset($data['acc_name'], $data['acc_password']) and !empty($data['acc_name']) and !empty($data['acc_password'])){

			$acc_name 		= Static_Basic::basic($data['acc_name']);
			$acc_password 	= $data['acc_password']; // Esse não pode passar strip_tags nem trim nem nada

			/*
				LÓGICA...
				pegar o password do nome, e verificar se o mesmo bate com a senha informada
			*/
			$sql = $this->_conexao->prepare('
				SELECT 
					acc_password
				FROM account
				WHERE acc_name = :acc_name
			');
			$sql->bindParam(':acc_name', $acc_name);
			$sql->execute();
			$temp = $sql->fetch(PDO::FETCH_ASSOC);
			$sql = null;


			if(isset($temp['acc_password']) and !empty($temp['acc_password'])){
				
				/* Senha que está no banco do user_name / account acc_name*/
				$senha_real = $temp['acc_password'];

				/* Retorna True senha correta, false incorreta */
				$acc_password = $this->_hash->verify($acc_password, $senha_real);

				/* Se a senha for correta */
				if($acc_password){

					return 2;
				}
			}

			return 1;
		}
		
		return 0;
	}

	/* Retorna um Token válido */
	function authGenerateToken(){
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

		/* $jwt armazem o token */
		$jwt = $this->JWT->encode($token, $this->privateKey, $this->algoritimo);

		return $jwt;
	}

	private function authDecodeToken($string){
		/*
			Decodificar o Token
		*/
		$jwt = $this->JWT->decode($string, $this->privateKey, array($this->algoritimo));

		return $jwt;
	}

	protected function authGetPublicKey(){

		return <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

	}

	protected function authGetPrivateKey(){

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