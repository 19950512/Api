<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "14/08/2018",
		"CONTROLADOR": "Index",
		"LAST EDIT": "18/08/2018",
		"VERSION":"0.0.2"
	}
*/
class Index {

	public $_nucleo;

	function __construct($nucleo){
		$this->_nucleo = $nucleo;
		header('Content-Type: text/html; charset=utf-8');
	}

	function index(){

		$html = <<<html
<!DOCTYPE html>
<html>
	<head>
		<title>Abigor - API</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<style>
			body {
				background-color: #f1f1f1;
				color: #696969;
				margin: auto;
				display: block;
				text-align: center;
			}

			h1 {
				position: fixed;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
			}
		</style>
	</head>
	<body>
		<h1>Abigor - API</h1>
	</body>
</html>
html;

		echo $html;
	}
}