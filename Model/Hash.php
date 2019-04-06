<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "06/04/2019",
		"MODEL": "Hash",
		"LAST EDIT": "06/04/2019",
		"VERSION":"0.0.1"
	}
*/
class Hash {

	private $options = array(
		'memory_cost' => 1024, // PADRÃO - PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
		'time_cost' => 2, // PADRÃO - PASSWORD_ARGON2_DEFAULT_TIME_COST,
		'threads' => 2, // PADRÃO - PASSWORD_ARGON2_DEFAULT_THREADS
	);

	function encrypt($password){
		
		$password_criptografado = password_hash($password, PASSWORD_ARGON2I, $this->options);

		return $password_criptografado;
	}

	function verify($senha_informada, $senha_real_hash){
		return password_verify($senha_informada, $senha_real_hash);
	}

	function getInfo($hash_argon){
		return password_get_info($hash_argon);
	}
}