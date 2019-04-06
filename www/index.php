<?php

/**
** CONFIGURAÇÕES DO MVC
**/
define('DIR', '../');

define('SUBDOMINIO', 'Api');
define('DIR_CLASS', '');

/* Mensagens API Aplicação*/
define('MSG_ERRO_ACTION', 'Hmm, parece que você está forçando acesso a uma área sem acesso, oque você acha que vai acontecer ? #Kappa');




/* Mensagems API Authentic */
define('MSG_HEADERLESS', 'Meu querido, pra você conversar comigo, você vai precisar do header secreto. Você sabe !?');

require_once '../Api.php';
new Api();