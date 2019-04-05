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


	// Lista dos HTTP_ORIGIN permitidos pela API
	public $_origins = array();

	// Objeto com todas as pessoas do cliente
	public $_pessoas;

	// Objeto com todos os produtos do cliente
	public $_produtos;

	// ID do cliente, é aquele que vai fazer a requisição na API - está armazenado em DB
	public $_clienteID = null;

	function __construct(){

		/* Objeto com todos os HTTP_ORIGIN permitidos pela API */
		$this->_origins = $this->origins();

		/* Função responsável por checar o HTTP_ORIGIN, - Segurança da API */
		$this->checkOrigin();


		/* Objeto com todas as pessoas do acesso a API */
		$this->_pessoas = $this->getPessoas();

		/* Objeto com todos os produtos do acesso a API */
		$this->_produtos = $this->getProdutos();
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

	function getProdutos(){

		$produtos = array(
			array(
				'nome' => 'Pendrive',
				'estado' => 'Novo',
				'descricao' => 'Pendrive com 8G de espaço',
				'valor' => 50.25
			),
			array(
				'nome' => 'Monitor',
				'estado' => 'Usado',
				'descricao' => 'Monitor 14polegadas usado',
				'valor' => 120.00
			),
			array(
				'nome' => 'Caderno',
				'estado' => 'Novo',
				'descricao' => 'Caderno 10 matérias',
				'valor' => 10.90
			),
		);

		return $produtos;
	}

	function getPessoas(){

		/*
			Função retorna todas as pessoas
		*/

		$pessoas = array(
			array(
				'nome' => 'Matheus Maydana',
				'idade' => 23,
				'sexo' => 'Masculino'
			),
			array(
				'nome' => 'Rita de Cássia',
				'idade' => 19,
				'sexo' => 'Feminino'
			),
			array(
				'nome' => 'Florinda',
				'idade' => 43,
				'sexo' => 'Feminino'
			),
		);

		return $pessoas;
	}


	private function origins(){

		/** 
		*	Função responsável por retornar os HTTP_ORIGIN permitidos pela API, 
		*	ou seja...
		*	Todos os request a API que forem DIFERENTE destes, serão Negados.
		*	Só iram receber os dados, aqueles requests que estiverem neste return
		*
		*	@return array
		**/

		// INDICE É = HTTP_ORIGIN
		// VALOR É = ID DO CLIENTE
		$origins = array(
			"http://teste.local" => 1,
			"http://dominio.com" => 2
		);

		return $origins;
	}
}