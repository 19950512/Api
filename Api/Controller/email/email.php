<?php
/*
	{
		"AUTHOR":"Matheus Maydana",
		"CREATED_DATA": "16/09/2019",
		"CONTROLADOR": "Email",
		"LAST EDIT": "16/09/2019",
		"VERSION":"0.0.1"
	}
*/
class Email {

	public $_nucleo;

	private $_email;

	private $drive;

	function __construct($nucleo){

		$this->_nucleo = $nucleo;

		$this->_email = new Email_Email;

	}

	function index(){
		/*
		
		 */
	}
}