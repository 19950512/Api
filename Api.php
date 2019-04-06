<?php
/* 
	{
		"AUTHOR": "Matheus Maydana",
		"CREATED": "07/02/2019",
		"NOME": "APIV2",
		"LAST_EDIT": "06/05/2019",
		"VERSION": "0.0.3"
	}
*/

if(is_file(DIR.'Senha.php')){

	require_once DIR.'Senha.php';
}

class Api{

	public $controler 	= 'index';
	public $action		= 'index';
	public $visao		= 'index';
	public $url			= array();
	public $url_str		= '';
	public $uri 		= '';

	public $_agora		= '--:--';
	public $_hoje		= '--/--/----';
	public $_ip			= '---.---.-.---';

	function __construct(){

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Type', 'application/json');
		header("Access-Control-Allow-Headers: *");

		$this->_agora		= date('H:i:s');
		$this->_hoje		= date('d/m/Y');
		$this->_ip			= $_SERVER['REMOTE_ADDR'];

		if(isset($_SERVER['REQUEST_URI']) and !empty($_SERVER['REQUEST_URI'])){

			$url 		= $this->parseURL($_SERVER['REQUEST_URI']);
			$this->url 	= $url;
			$this->uri 	= $_SERVER['REQUEST_URI'];
			$this->url_str = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}

		if(empty($this->url[1])){

			// SE NÃO HOUVER NADA NA URL, EXIBE O CONTROLADOR/VISÃO INDEX
			$this->controler 	= 'index';
			$this->action 		= 'index';
			$this->visao		= 'index';

			try {

				require_once (DIR.SUBDOMINIO.'/Controller/index/index.php');
			
			} catch (Exception $e) {

				
				new de('ERRO: '. $e);
				/**
				** Caso controlador não seja encontrado
				**/
			}

			/* Passa o $this para o controler para o controler ter acesso as coisas do núcleo */
			$controlador = new $this->controler($this);
			$controlador->index();

		}else{

			$controlador = str_replace('-', '', $this->url[1]);

			/* EXISTE ALGO NA URL, VERIFICAR SE DESTE ALGO, EXISTE UM CONTROLADOR */
			if(file_exists(DIR.SUBDOMINIO.'/Controller/'.$controlador.'/'.$controlador.'.php')){

				// MONTA O CONTROLADOR E ACTION (SE TIVER NA URL)
				$this->controler 	= $controlador;
				$this->visao 		= $controlador;

				try {

					if(file_exists(DIR.SUBDOMINIO.'/Controller/'.$controlador.'/'.$controlador.'.php')){

						require_once (DIR.SUBDOMINIO.'/Controller/'.$controlador.'/'.$controlador.'.php');
		
					}else{

						require_once (DIR.SUBDOMINIO.'/Controller/index/index'.'.php');
					}

				} catch (Exception $e) {

					new de('ERRO: '. $e);
					/**
					** Caso controlador não seja encontrado
					**/
				}

				try {
					
					$controlador = new $this->controler($this);
	
				} catch (Exception $e) {
					
					new de('ERRO: '. $e);

				}


				// VERIFICA SE EXISTE A ACTION NO CONTROLADOR,
				if(isset($this->url[2]) and !empty($this->url[2])){

					$action = str_replace('-', '', $this->url[2]);

					if(method_exists($controlador, $action)){

						$this->action 	  = $action;

						try {
							
							// AQUI EXECUTA A ACTION EXISTENTE NO CONTROLADOR E NA URL
							$controlador->{$this->action}();

						} catch (Exception $e) {

							new de('ERRO: '. $e);							
						}

					}else{
						// ACTION NÃO ENCONTRADA / 404!
						$this->error404();
					}

				}else{
					// AQUI EXECUTA A ACTION INDEX (TODO CONTROLADOR TEM)
					$controlador->index();
				}

			}else{
				// 404 CONTROLADOR NÃO EXISTE
				$this->error404();
			}
		}
	}

	private function error404(){

		try{

			require_once (DIR.SUBDOMINIO.'/Controller/erro404/erro404'.'.php');

		}catch(PDOException $e){

			/**
			** Caso controlador não seja encontrado
			**/
		}

		$erro404 = new erro404($this);

		$erro404->index();
	}

	// "QUEBRA" O URL PARA DEFINIR OQUE É CONTROLADOR, ACTION..
	function parseURL($url){

		$array = explode('/', $url);
		$temp = array();

		foreach ($array as $key => $value) {

			$temp[$key] = preg_replace('/\?.*$|\!.*$|#.*$|\'.*$|\@.*$|\$.*$|&.*$|\*.*$|\+.*$|\..*$/', '', $value);
		}

		return $temp;
	}

	// Função BASICA
	function basic($string){

		$novaString = trim(strip_tags($string), ' ');

		return $novaString;
	}
}

function _autoload($classe){

	$php = str_replace('_', '/', $classe);

	//try{

		if(is_file(DIR.'Model/'.$php.'.php')){

			require_once (DIR.'Model/'.$php.'.php');

		}else{

			echo $classe.': Classe não encontrada.';
			exit;
		}

	/*}catch(PDOException $e){

		*
		** @see Remover o ECHO antes de publicar
		*

		echo $classe.': Classe nao encontrada';
	}*/
}

spl_autoload_register('_autoload');

/**
** RESPONSAVEL PELO DEBUG, exemplo, new de($variavel); ou new de('allow');
** @see Créditos - Criador : Moises - https://github.com/themoiza
**/
class de{

	function __construct($a){

		if(is_array($a)){

			echo '<pre>';
			print_r($a);
			exit;

		}else{

			echo '<pre>';
			var_dump($a);
			exit;
		}
	}
}

/**
** Função padrão de response da API
**/
function json($res, $data){
	return json_encode(
		array(
			'res' => $res, 
			'data' => $data,
			/*'informacoes' => array(
				'hora' 	=> date('H:i:s'),
				'data' 	=> date('d/m/Y'),
				'ip'	=> $_SERVER['REMOTE_ADDR']
			)*/
		)
	);
}

/* Caso tentem acessar uma action private/protected */
class Sempermissao{

	function __construct($mensagem){
		echo json('no', $mensagem);
		exit;
	}
}