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


/*
 SÓ CONFIGURAR...
 */

class Email_Email {

	private $_mail;

	function __construct(){
		// Import PHPMailer classes into the global namespace
		// These must be at the top of your script, not inside a function
		$this->_mail = new Email_PHPMailer(true);                              // Passing `true` enables exceptions

		try {
			//Server settings
			$this->_mail->SMTPDebug = 1;                                 // Enable verbose debug output
			$this->_mail->isSMTP();                                      // Set mailer to use SMTP
			$this->_mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
			$this->_mail->SMTPAuth = true;                               // Enable SMTP authentication
			$this->_mail->Username = 'mattheuszcabal@gmail.com';                 // SMTP username
			$this->_mail->Password = '99865422A';                           // SMTP password
			$this->_mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$this->_mail->Port = 587;                                    // TCP port to connect to

			//Recipients
			$this->_mail->setFrom('mattheuszcabal@gmail.com', 'Mattheusz Maydana');
			$this->_mail->addAddress('matheus@objetivawebsites.com.br', 'Maydana');     // Add a recipient
			//$this->_mail->addAddress('ellen@example.com');               // Name is optional
			//$this->_mail->addReplyTo('info@example.com', 'Information');
			//$this->_mail->addCC('cc@example.com');
			//$this->_mail->addBCC('bcc@example.com');

			//Attachments
			//$this->_mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$this->_mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			$this->_mail->isHTML(true);                                  // Set email format to HTML
			$this->_mail->Subject = 'Here is the subject';
			$this->_mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			$this->_mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$this->_mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $this->_mail->ErrorInfo;
		}
	}
}