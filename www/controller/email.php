<?php
session_start();

require_once (__dir__."/../config.php"); 
	try {
			$msg=file_get_contents(__DIR__ ."/../emailInvitaciontst.html");
			$email="daneo.net@gmail.com";
			$subject ="Frontera: Arma tu parrillada";
			require(__DIR__ ."/../SAW/includes/funcMail.php"); 
			return true;
}
	catch(Exception $ex) {
		return $ex->getMessage();
	}