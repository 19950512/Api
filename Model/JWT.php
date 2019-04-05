<?php

class JWT {

	public $leeway = 120;

	public $timestamp = null;

	public $supported_algs = array(
		'HS256' => array('hash_hmac', 'SHA256'),
		'HS512' => array('hash_hmac', 'SHA512'),
		'HS384' => array('hash_hmac', 'SHA384'),
		'RS256' => array('openssl', 'SHA256'),
		'RS384' => array('openssl', 'SHA384'),
		'RS512' => array('openssl', 'SHA512'),
	);

	public function decode($jwt, $key, array $allowed_algs = array()){

		$timestamp = is_null($this->timestamp) ? time() : $this->timestamp;

		$erro = 0;
		$mensagem = '';

		if (empty($key)){

			$erro = 1;
			$mensagem = 'A Chave não pode estar vazia';
		}

		$tks = explode('.', $jwt);
		if (count($tks) != 3){

			$erro = 1;
			$mensagem = 'Número de seguimentos errado!';
		}


		list($headb64, $bodyb64, $cryptob64) = $tks;

		if(null === ($header = $this->jsonDecode($this->urlsafeB64Decode($headb64)))){
			$erro = 1;
			$mensagem = 'Invalid header encoding';
		}

		if(null === $payload = $this->jsonDecode($this->urlsafeB64Decode($bodyb64))){
			$erro = 1;
			$mensagem = 'Invalid claims encoding';
		}

		if(false === ($sig = $this->urlsafeB64Decode($cryptob64))) {
			$erro = 1;
			$mensagem = 'Invalid signature encoding';
		}

		if(empty($header->alg)){
			$erro = 1;
			$mensagem = 'Algoritimo vazio';
		}

		if(empty($this->supported_algs[$header->alg])){
			$erro = 1;
			$mensagem = 'Algoritimo não suportado';
		}

		if(!in_array($header->alg, $allowed_algs)) {
			$erro = 1;
			$mensagem = 'Algoritimo não permitido';
		}

		if(is_array($key) || $key instanceof \ArrayAccess){
			if (isset($header->kid)) {
				if (!isset($key[$header->kid])){
					$erro = 1;
					$mensagem = '"kid" invalid, unable to lookup correct key';
				}
				$key = $key[$header->kid];

			} else {
				$erro = 1;
				$mensagem = '"kid" empty, unable to lookup correct key';
			}
		}

		// Check the signature
		if(!$this->verify("$headb64.$bodyb64", $sig, $key, $header->alg)){
			$erro = 1;
			$mensagem = 'Token inválido';
		}

		// Check if the nbf if it is defined. This is the time that the
		// token can actually be used. If it's not yet that time, abort.
		if(isset($payload->nbf) and $payload->nbf > ($timestamp + $this->leeway)){
			$erro = 1;
			$mensagem = 'Seu token só será liberado depois das ' . date(DateTime::ISO8601, $payload->nbf);
		}

		// Check that this token has been created before 'now'. This prevents
		// using tokens that have been created for later use (and haven't
		// correctly used the nbf claim).
		if(isset($payload->iat) and $payload->iat > ($timestamp + $this->leeway)){
			$erro = 1;
			$mensagem = 'Seu token só será liberado depois das ' . date(DateTime::ISO8601, $payload->iat);
		}

		// Check if this token has expired.
		if(isset($payload->exp) and ($timestamp - $this->leeway) >= $payload->exp){
			$erro = 1;
			$mensagem = 'Token expirado!';
		}

		if($erro === 1){

			return $mensagem;
		}

		return $payload;
	}

	public function encode($payload, $key, $alg = 'HS256', $keyId = null, $head = null){

		$header = array('typ' => 'JWT', 'alg' => $alg);

		if($keyId !== null){
			$header['kid'] = $keyId;
		}

		if(isset($head) and is_array($head)){
			$header = array_merge($head, $header);
		}

		$segments = array();
		$segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
		$segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
		$signing_input = implode('.', $segments);

		$signature = $this->sign($signing_input, $key, $alg);
		$segments[] = $this->urlsafeB64Encode($signature);

		return implode('.', $segments);
	}

	public function sign($msg, $key, $alg = 'HS256'){

		if(empty($this->supported_algs[$alg])){
			return 'Algoritimo não suportado';
		}
		
		list($function, $algorithm) = $this->supported_algs[$alg];
		
		switch($function){

			case 'hash_hmac':
				return hash_hmac($algorithm, $msg, $key, true);

			case 'openssl':

				$signature = '';
				$success = openssl_sign($msg, $signature, $key, $algorithm);
				if(!$success){
				
					return 'OpenSSL unable to sign data';
				
				}else{
					return $signature;
				}
		}
	}

	private function verify($msg, $signature, $key, $alg){

		if(empty($this->supported_algs[$alg])){
			return 'Algorithm not supported';
		}

		if(is_array($key)){
			return 'A Key não pode ser um array!';
		}

		list($function, $algorithm) = $this->supported_algs[$alg];

		switch($function){

			case 'openssl':

				$success = openssl_verify($msg, $signature, $key, $algorithm);

				if($success === 1){
					return true;
				}elseif($success === 0){
					return false;
				}

				// returns 1 on success, 0 on failure, -1 on error.
				throw new DomainException(
					'OpenSSL error: ' . openssl_error_string()
				);
			case 'hash_hmac':
			default:
				$hash = hash_hmac($algorithm, $msg, $key, true);
				if (function_exists('hash_equals')) {
					return hash_equals($signature, $hash);
				}
				$len = min($this->safeStrlen($signature), $this->safeStrlen($hash));

				$status = 0;
				for ($i = 0; $i < $len; $i++) {
					$status |= (ord($signature[$i]) ^ ord($hash[$i]));
				}
				$status |= ($this->safeStrlen($signature) ^ $this->safeStrlen($hash));

				return ($status === 0);
		}
	}

	public function jsonDecode($input){

		if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
			/** In PHP >=5.4.0, json_decode() accepts an options parameter, that allows you
			 * to specify that large ints (like Steam Transaction IDs) should be treated as
			 * strings, rather than the PHP default behaviour of converting them to floats.
			 */
			$obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
		} else {
			/** Not all servers will support that, however, so for older versions we must
			 * manually detect large ints in the JSON string and quote them (thus converting
			 *them to strings) before decoding, hence the preg_replace() call.
			 */
			$max_int_length = strlen((string) PHP_INT_MAX) - 1;
			$json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
			$obj = json_decode($json_without_bigints);
		}

		if (function_exists('json_last_error') && $errno = json_last_error()){

			$this->handleJsonError($errno);

		}elseif($obj === null && $input !== 'null'){
			return 'Null result with non-null input';
		}

		return $obj;
	}

	public function jsonEncode($input){

		$json = json_encode($input);
		if(function_exists('json_last_error') && $errno = json_last_error()){

			$this->handleJsonError($errno);

		}elseif($json === 'null' && $input !== null){
			return 'Null result with non-null input';
		}

		return $json;
	}

	public function urlsafeB64Decode($input){

		$remainder = strlen($input) % 4;

		if($remainder){
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}

		return base64_decode(strtr($input, '-_', '+/'));
	}

	public function urlsafeB64Encode($input){
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}

	private function handleJsonError($errno){

		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
			JSON_ERROR_UTF8 => 'Malformed UTF-8 characters' //PHP >= 5.3.3
		);

		if(isset($messages[$errno])){
			return $messages[$errno];
		}

		return 'ERRO Desconhecido no JSON';

	}

	private function safeStrlen($str){

		if (function_exists('mb_strlen')) {
			return mb_strlen($str, '8bit');
		}

		return strlen($str);
	}
}
