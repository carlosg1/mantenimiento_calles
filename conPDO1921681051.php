<?php

// a partir del 18/02/2018 se utiliza PDO_PGSQL como driver para
//  conexiones a base de datos Postgresql
//
// Developer: Lic. Carlos Garcia - carlosgctes@gmail.com
// Ultima modif. 05/07/2018
//

try{

	$conPdoPg = new PDO('pgsql:host=190.7.30.142;port=5432;dbname=gis', 'pm_lectura', '_mUN.Ctes$gIS');

}

catch(PDOException $e){

	print 'Direccion General de Sistemas de Informacion Geografica<br /><br />';
	
	print 'Excepcion: <br />' . $e->getMessage() . '<br />';
	
	print 'Lo sentimos pero no se pudo conectar a la base de datos.';
	
	// print $e;
	
	exit;
	
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

