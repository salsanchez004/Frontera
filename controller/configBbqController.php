<?php
ini_set('display_errors', 0);
header('Access-Control-Allow-Origin: *');

$relativo = "../";

require_once (__dir__."/../config.php"); 
require_once (__dir__."/".$relativo."../model/Bbq.php");
require_once (__dir__."/".$relativo."../model/Bbq_host.php");
require_once (__dir__."/".$relativo."../model/Bbq_product.php");
require_once (__dir__."/".$relativo."../model/Bbq_host_image.php");
require_once (__dir__."/".$relativo."../model/Bbq_guest.php");
require_once (__dir__."/".$relativo."../model/User.php");
require_once (__dir__."/".$relativo."../model/Product.php");
require_once (__dir__."/".$relativo."../vendor/banbuilder-master/src/CensorWords.php");

// indicamos tipo de datos
header('Content-Type: application/json');

//$url_controller = "https://vmasideas.online/rycfrontera/app/";
$url_controller = "https://www.fronteracarneparrillera.com/app/";
$vi="5";

function throwJsonException($msg) {
	return json_encode(array('error'=> true, 'msg' => $msg));
}

function registrarUsuario($name, $email, $gender, $session_id){
	global $log,$db,$IP;
	$usuario = new User($email);
	if ($usuario->getId_user() === 0) {
		$usuario->setName_complete($name);
		$isFace = 0;
		$isGoogle = 0;
		$isApple = 0;
		if ($gender == "F") {
			$isFace = 1;
			$gender = "H";
		}
		else if ($gender == "G") {
			$isGoogle = 1;
			$gender = "H";
		}
		else if ($gender == "A") {
			$isApple = 1;
			$gender = "H";
		}
		$usuario->setGender($gender);
		$usuario->setIs_facebook($isFace);
		$usuario->setIs_google($isGoogle);
		$usuario->setIs_apple($isApple);
		$usuario->setStatus(1);
		$resp = $usuario->save();
		$log->trace("Respuesta guardar usuario: ".$resp);
	}
	$_SESSION[$session_id]["user_dentro"] = serialize($usuario);
	return true;
}

function calculateProd($numInvitados, $numInvitadas) {
	global $log,$db,$IP;
	$total_gramos = ($numInvitados * 300) + ($numInvitadas * 250);
	$categorias = $db->rawQuery("SELECT * FROM CAT_CATEGORY_PRODUCT WHERE status = 1");
	$cant_por_prod = array();
	for($i=0; $i<count($categorias);$i++) {
		$Categoria = new Product_category($categorias[$i]["id_category"],1);
		$cat_gr = round( $total_gramos * ($Categoria->getPercentage_dist() / 100) );
		$cat_sum_gr = 0;
		$num_prods = count($Categoria->getProducts());
		if ($num_prods <= 0) continue;
		$log->trace("Cantidad para categoria ".$Categoria->getName().": ".$cat_gr);
		if ($Categoria->getName() != "Complementos") {
			while($cat_sum_gr < $cat_gr) {
				$index = rand(0,$num_prods - 1);
				$Producto = $Categoria->getProducts()[$index];
				$log->trace("Index: ".$index.", prod elegido: ".$Producto->getName().", id: ".$Producto->getId_product());
				$cat_sum_gr += $Producto->getGrammage();
				$cant_por_prod[$Producto->getId_product()] += 1;
				$log->trace("Suma de gr: ".$cat_sum_gr);
			}
		}
		else {
			for($j=0; $j<$num_prods; $j++) {
				$Producto = $Categoria->getProducts()[$j];
				$cant = $total_gramos * $Producto->getGrammage() / 1000;
				$cant = round( $cant / $Producto->getGrammage() );
				$cant_por_prod[$Producto->getId_product()] = $cant;
			}
		}
	}
	$log->trace("Cantidad por productos: ".print_r($cant_por_prod,true));
	return $cant_por_prod;
}

function armarResumenProd($rel_prod_cant) {
	global $log, $db;
	$lista = array();
	$total_kg = 0;
	if (count($rel_prod_cant) > 0) {
		foreach($rel_prod_cant as $datos) {
			$id_prod = $datos["id"];
			$cant = $datos["cant"];
			$Producto = new Product($id_prod);
			if ($Producto->getCategory()->getName() == "Complementos") {
				continue;
			}
			$lista[$id_prod]["nombre"] = $Producto->getName();
			$log->trace("Gramaje prod: ".$Producto->getGrammage().", cant: ".$cant);
			$gr = $Producto->getGrammage() * $cant;
			$total_kg += $gr;
			if ($gr >= 1000) {
				$gr = round( $gr / 1000, 2 );
				$gr = $gr." kg";
			}
			else {
				$gr = $gr." g";
			}
			$lista[$id_prod]["gr"] = $gr;
		}
	}
	$total_kg = round( $total_kg / 1000, 2 );
	return array($lista, $total_kg);
}

function saveBbq($rel_prod_cant, $numInvitados, $numInvitadas, $session_id) {
	global $log, $db;
	$log->trace("App: SESSION saveBbq: ".print_r($_SESSION,true));
	
	if (count($_SESSION[$session_id]) <= 0) {
		return "false";
	}
	
	$usuario = unserialize($_SESSION[$session_id]["user_dentro"]);
	$name = $_SESSION[$session_id]["bbq_info"]["name"];
	$date = $_SESSION[$session_id]["bbq_info"]["date"];
	$time = $_SESSION[$session_id]["bbq_info"]["time"];
	$id_bbq = $_SESSION[$session_id]["bbq_info"]["id_bbq"];
	
	$log->trace("Datos de session de Bbq: ".print_r($_SESSION[$session_id],true));
	
	if (strlen($id_bbq) <= 0) {
		$id_bbq = 0;
	}
	$bbq_prods = array();
	for($i=0; $i < count($rel_prod_cant); $i++) {
		$id_product = $rel_prod_cant[$i]["id"];
		$quantity = $rel_prod_cant[$i]["cant"];
		$Producto = new Product($id_product);
		$grammage = $Producto->getGrammage();
		$bbq_prods[] = new Bbq_product($id_bbq,$id_product,$quantity,$grammage);
	}
	
	if ($id_bbq === 0 || $id_bbq === NULL) {
		$Bbq = new Bbq($name,NULL,NULL,NULL,$numInvitadas,$numInvitados,NULL,$usuario->getId_user(),1);
		$Bbq->setBbq_date($date);
		$Bbq->setBbq_time($time);
	}
	else {
		$Bbq = new Bbq($id_bbq);
		$Bbq->setUser($usuario);
		$Bbq->setName($name);
		$Bbq->setBbq_date($date);
		$Bbq->setBbq_time($time);
		$Bbq->setFemale_guests($numInvitadas);
		$Bbq->setMale_guests($numInvitados);
	}
	$Bbq->setProducts($bbq_prods);
	
	if ($id_bbq === 0) {
		$resp = $Bbq->save();
	}
	else {
		$resp = $Bbq->update();
	}
	$_SESSION[$session_id]["bbq_info"]["id_bbq"] = $Bbq->getId_bbq();
	return $resp;
}

function saveFriends($bbq_friends, $session_id) {
	global $log, $db;
	$id_bbq = $_SESSION[$session_id]["bbq_info"]["id_bbq"];
	if (strlen($id_bbq) <= 0 || $id_bbq === 0) {
		return "Es necesario intentarlo nuevamente";
	}
	$bbq_guests = array();
	for($i=0; $i<count($bbq_friends); $i++) {
		$nombre = $bbq_friends[$i]["nombre"];
		$email = $bbq_friends[$i]["email"];
		$bbq_guests[] = new Bbq_guest($id_bbq, $nombre, $email, 1);
	}
	$Bbq = new Bbq($id_bbq);
	$Bbq->setGuests($bbq_guests);
	$resp = $Bbq->updateGuests();
	return $resp;
}

function finishBbq($address, $message, $latitude, $longitud, $session_id) {
	global $log, $db;
	$id_bbq = $_SESSION[$session_id]["bbq_info"]["id_bbq"];
	$log->trace("Datos de session finish Bbq: ".print_r($_SESSION[$session_id],true));
	if (strlen($id_bbq) <= 0 || $id_bbq === 0) {
		return "Es necesario intentarlo nuevamente.";
	}
	
	$censor = new Snipe\BanBuilder\CensorWords;
	$badwords = $censor->setDictionary('es');
	$censor->setReplaceChar("~");
	$string = $censor->censorString($message);
	
	$log->trace("Cadenas diccionario: ".print_r($string, true));
	
	if (strpos($string['clean'],"~") !== false) {
//		return "El texto ingresado en el mensaje contiene palabras que no se permiten en el sitio";
		$message = $string['clean'];
	}
	
	$Bbq = new Bbq($id_bbq);
	$Bbq->setAddress($address);
	$Bbq->setMessage($message);
	$Bbq->setLatitude($latitude);
	$Bbq->setLongitud($longitud);
	$resp = $Bbq->updateExtras();
	if ($resp) {
//		$resp = enviarBbq($Bbq, $session_id);
		require_once("imgLibrary.php");
		global $meses, $relativo, $url_controller;
		$resp = construyeRender($Bbq, $meses, $relativo, $url_controller);
		if ($resp) {
			$msg=file_get_contents(__DIR__ ."/../imgShare/emailCompartirWhats.html");
			$msg=str_replace("@@url_imagen@@",$url_controller."/imgShare/shareBbq_".$Bbq->getId_bbq().".png", $msg);
			$msg=str_replace("@@description@@",str_replace("\n"," ",$Bbq->getMessage()),$msg);
			$msg=str_replace("@@short_title@@","Parrillada de ".$Bbq->getUser()->getName_complete(),$msg);

			file_put_contents(__DIR__ ."/../imgShare/emailCompartirWhats_".$Bbq->getId_bbq().".html",$msg);
			
			$email=$Bbq->getUser()->getEmail();

			$subject ="Arma tu parrillada: ".$Bbq->getName();
			require(__DIR__ ."/".$relativo."../SAW/includes/funcMail.php");  
		}
	}
	return $resp;
}

function enviarBbq($Bbq, $session_id) {
	global $log, $db, $meses, $relativo, $url_controller;
	try {
		$bbq_name = $Bbq->getName();
		$bbq_user = $Bbq->getUser()->getName_complete();
		$bbq_dia = substr($Bbq->getBbq_date(),8,2);
		$bbq_mes = $meses[substr($Bbq->getBbq_date(),5,2)];
		$bbq_date = $bbq_dia." de ".$bbq_mes;
		$bbq_time = substr($Bbq->getBbq_time(),0,5);
		$bbq_address = $Bbq->getAddress();
		$bbq_latitude = $Bbq->getLatitude();
		$bbq_longitud = $Bbq->getLongitud();
		$bbq_ubic = "";
		if ($bbq_latitude !== "0" && $bbq_longitud !== "0") {
			$liga = "https://maps.googleapis.com/maps/api/staticmap?center=".$bbq_latitude.",".$bbq_longitud."&zoom=16&size=400x300&markers=color:red%7Clabel:Aqui%7C".$bbq_latitude.",".$bbq_longitud."&style=feature:transit|element:labels.icon|visibility:off&style=feature:poi.business|element:all|visibility:off&key=AIzaSyAusPp1OHyEGOs3PVlvKoZQPYOfy5KiebI";
			$desFolder = '../imgUbic/';
			$imageName = 'parrillada_'.$Bbq->getId_bbq().'.png';
			$imagePath = $desFolder.$imageName;
			file_put_contents($imagePath,file_get_contents($liga));
//			$bbq_ubic = '<p>
//						<img src="https://vmasideas.online/frontera/app/imgUbic/parrillada_'.$Bbq->getId_bbq().'.png" style="width:350px" alt="ubicacion" />
//					</p>';
			$bbq_ubic = '<p>
						<img src="'.$url_controller.'imgUbic/parrillada_'.$Bbq->getId_bbq().'.png" style="width:350px" alt="ubicacion" />
					</p>';
		}
		else {
//			$bbq_ubic = '<p>
//						<img src="https://vmasideas.online/frontera/images/titulo4Emailing.png" style="width:350px" alt="ubicacion" />
//					</p>';
			$bbq_ubic = '<p>
						<img src="'.$url_controller.'images/titulo4Emailing.png" style="width:350px" alt="ubicacion" />
					</p>';
		}

		$bbq_products = "<div style='width: 80%; text-align: left;'>";
		for($i=0; $i<count($Bbq->getProducts()); $i++) {
			$Bbq_prod = $Bbq->getProducts()[$i];
			$Prod = $Bbq_prod->getProduct();
			$gr = ' ('.$Bbq_prod->getGrammage().'g c/u)';
			if ($Prod->getCategory()->getName() == "Complementos") {
				$gr = "";
			}
//			$bbq_products .= '<img src="https://www.vmasideas.online/frontera/images/vinetaEmailingBlack.jpg" style="width: 12px; max-width:12px; min-width: 12px" alt="vineta" />&nbsp;'.$Bbq_prod->getQuantity().' '.$Prod->getName().$gr.'<br>';
			$bbq_products .= '<div style="line-height: 22px;"><span style="background-color: #da543b; border-radius: 25px;color: #da543b; height: 40%; max-height: 40%; min-height: 40%; font-size: 40%;vertical-align: middle;">99</span>&nbsp;'.$Bbq_prod->getQuantity().' '.$Prod->getName().$gr.'</div>';
		}
		$bbq_products .= "</div>";
		
		$bbq_message = nl2br($Bbq->getMessage());
		
		$bbq_guests = "<div style='width: 80%; text-align: left;'>";
		for($i=0; $i<count($Bbq->getGuests()); $i++) {
			$Bbq_guest = $Bbq->getGuests()[$i];
			$nombre_invitado = $Bbq_guest->getName();
//			$bbq_guests .= '<img src="https://www.vmasideas.online/frontera/images/vinetaEmailingBlack.jpg" style="width: 12px; max-width:12px; min-width: 12px" alt="vineta" />&nbsp;'.$nombre_invitado.'<br>';
			$bbq_guests .= '<div style="line-height: 22px;"><span style="background-color: #da543b; border-radius: 25px;color: #da543b; height: 40%; max-height: 40%; min-height: 40%; font-size: 40%;vertical-align: middle;">99</span>&nbsp;'.$nombre_invitado.'</div>';
		}
		$bbq_guests .= "</div>";
		
		for($i=0; $i<count($Bbq->getGuests()); $i++) {
			$msg=file_get_contents(__DIR__ ."/../emailInvitacion.html");
			$msg=str_replace("@@bbq_name@@",$bbq_name,$msg);
			$msg=str_replace("@@bbq_user@@",$bbq_user,$msg);
			$msg=str_replace("@@bbq_date@@",$bbq_date,$msg);
			$msg=str_replace("@@bbq_time@@",$bbq_time,$msg);
			$msg=str_replace("@@bbq_address@@",$bbq_address,$msg);
			$msg=str_replace("@@bbq_ubic@@",$bbq_ubic,$msg);
			$msg=str_replace("@@bbq_products@@",$bbq_products,$msg);
			$msg=str_replace("@@bbq_message@@",$bbq_message,$msg);
			$msg=str_replace("@@bbq_guests@@",$bbq_guests,$msg);
			
			file_put_contents(__DIR__ ."/../../invitaciones/app_emailInvitacion_".$Bbq->getId_bbq().".html",$msg);
			
//			$msg2=file_get_contents(__DIR__ ."/../emailInvitacion_".$Bbq->getId_bbq().".html");
			
			$email=$Bbq->getGuests()[$i]->getEmail();
			$emailAdmin = "";
			if ($i==0) {
				$emailAdmin = $Bbq->getUser()->getEmail();
			}
			
			$subject ="Arma tu parrillada: ".$bbq_name;
			require(__DIR__ ."/".$relativo."../SAW/includes/funcMail.php"); 
		}
		
		$_SESSION[$session_id]["bbq_info"]["id_bbq"] = NULL;
		
		return true;
	}
	catch(Exception $ex) {
		return $ex->getMessage();
	}
}

function shareBbq($session_id) {
	global $log, $db;
	$id_bbq = $_SESSION[$session_id]["bbq_info"]["id_bbq"];
	$log->trace("Datos de session share Bbq: ".print_r($_SESSION[$session_id],true));
	if (strlen($id_bbq) <= 0 || $id_bbq === 0) {
		return "Es necesario intentarlo nuevamente.";
	}
	$Bbq = new Bbq($id_bbq);
	return array(
//		$Bbq->getMessage(),
		"Te invito a mi parrillada",
		"Arma tu parrillada: ".$Bbq->getName(),
		"shareBbq_".$Bbq->getId_bbq().".png",
//		"emailing_tst_primero.jpg",
		"Ubicación: https://www.google.com.mx/maps/search/".$Bbq->getLatitude().",".$Bbq->getLongitud()."/"
	);
}

function mediaShareBbq($app, $session_id) {
	global $log, $db;
	$id_bbq = $_SESSION[$session_id]["bbq_info"]["id_bbq"];
	$log->trace("Datos de session media Share Bbq: ".print_r($_SESSION[$session_id],true));
	if (strlen($id_bbq) <= 0 || $id_bbq === 0) {
		return "Es necesario intentarlo nuevamente.";
	}
	$res = $db->rawQuery("UPDATE TBL_BBQ SET media_share = if(isnull(media_share), '".$app."', CONCAT(media_share, '|".$app."')) WHERE id_bbq = '".$id_bbq."'");
	return $res;
}

function getProductosPorCategoria() {
	global $log, $db, $url_controller;
	$categorias = $db->rawQuery("SELECT * FROM CAT_CATEGORY_PRODUCT WHERE status = 1");
	$col = 12 / count($categorias);
	$col = round($col);
	$html_prod_cat = array();
	$id_prod = 0;
//  $html_cat_responsive = "<div class='col-md-12'>";
	for($i=0; $i<count($categorias);$i++) {
//											echo '<div class="col-md-'.$col.'" style="cursor:pointer" id="categoria_'.$categorias[$i]["id_category"].'" onclick="selectCategoria('.$categorias[$i]["id_category"].')">';
//											echo '	<img src="./images/'.strtolower($categorias[$i]["name"]).'1.png" />';
////											echo '	<br>'.$categorias[$i]["name"];
//											echo '</div>';
//											$claseImg = 'class="catImgWidth"';
//											if ($categorias[$i]["name"] == "Complementos") {
//												$claseImg = ' width="142px"';
//											}
//											$html_cat_responsive .= '<img src="./images/'.strtolower($categorias[$i]["name"]).'1.png" style="cursor:pointer" id="categoria_'.$categorias[$i]["id_category"].'_small" onclick="selectCategoria('.$categorias[$i]["id_category"].')" '.$claseImg.' />';

		$objCat = new Product_category($categorias[$i]["id_category"],1);

		if (count($objCat->getProducts()) <= 0) continue;

		$html_prod_cat[$objCat->getId_category()] = '<div class="row" style="margin-top: 20px; padding: 15px 0px 0px 0px; border-top: 1px dashed; height: 100px"><div class="col-md-12">
			<img src="'.$url_controller.'images/'.strtolower($categorias[$i]["name"]).'4n.png" style="height: ##alturaProd##; display: inline-block" />
			<label style="font-size: 161.5%; padding-left: 15px;">'.$categorias[$i]["name"].'</label>
		</div></div>';

		for($j=0; $j<count($objCat->getProducts()); $j++) {
			$id_prod = $objCat->getProducts()[$j]->getId_product();
			$margen_sup = ' style="margin-top: 10px"';
//												if ($j==0) {
//													$margen_sup = ' style="margin-top: 25px"';
//												}
			$html_prod_cat[$objCat->getId_category()] .= '<div class="row">
				<div class="col-md-12"'.$margen_sup.'>
					<div style="width: 70%; display: inline-block; line-height: 1.1rem;">
						'.$objCat->getProducts()[$j]->getName().'
						<br>
						<span style="font-size: 85%; margin-top: -10px">'.$objCat->getProducts()[$j]->getDescription().'</span>
					</div>
					<div style="width: 30%; display: inline-block; text-align:center;" class="pull-right">
						<img src="images/menosInvitados.png" style="cursor:pointer; width: 14px; display: inline-block;" onclick="menosProductos('.$objCat->getProducts()[$j]->getId_product().')" />
						<label style="text-align: center; font-size: 25px; color: #d9543b; width: 35%; display: inline-block;" id="prod_quantity_'.$objCat->getProducts()[$j]->getId_product().'">0</label>
						<img src="images/masInvitados.png" style="cursor:pointer; width: 14px; display: inline-block;" onclick="masProductos('.$objCat->getProducts()[$j]->getId_product().')" />
					</div>
				</div>
			</div>';
		}
	}
//	$html_cat_responsive .= "</div>";
	$html = "";
	if (count($html_prod_cat) > 0) {
		foreach($html_prod_cat as $id_category=>$productos) {
			$html .= '<div id="productos_'.$id_category.'">'.$productos.'</div>';
		}	
	}
	$html .= "<input type='hidden' id='un_producto' name='un_producto' value='".$id_prod."' />";
	
	return $html;
}

function getRecetas() {
	global $url_controller;
	$html_recetas = '
	<div id="lpmoReceta1" class="receta wow slideInLeft" style="border: 3px #FFF solid;">
		 <div>
			  <img src="'.$url_controller.'images/recipes/lpmo1_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'nUidmhytfMY\')">

			 <!--
			 <img src="'.$url_controller.'images/recipes/lpmo1.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
			-->

			 <h4 class="tituloReceta">Cecina Enchilada y rajas poblanas</h4>
			  <h5 class="subtituloReceta">con Los Parrilleros Mamones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  </p><ul style="color: #000;">
							<li>1 paquete de CECINA ENCHILADA FRONTERA</li>
							  <li>4 cucharadas de aceite</li>
							  <li>1 cebolla, fileteada</li>
							   <li>3 chiles poblanos, pelados y en rajas</li>

						  </ul>

					 <p></p>
				 </div>


			  <div class="col-md-6">
					  <p style="color: #000;">
						  </p><ul style="color: #000;">
							 <li>½ taza de agua</li>
							  <li>1 taza de granos de elote, cocidos sal y pimienta</li>
							  <li>200 g de queso tipo manchego, rallado</li>
						  </ul>

					 <p></p>
				 </div>
		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación (4 porciones).</span> <br><br>
						  Calienta la mitad del aceite y acitrona la cebolla hasta que tome color dorado, agrega los chiles, el agua, sal y pimienta y deja hervir hasta que los chiles estén suaves, por último incorpora los elotes, cubre con el queso y tapa el recipiente hasta el momento de servir.
						  <br><br>

						  Calienta el resto del aceite y fríe la CECINA ENCHILADA FRONTERA por ambos lados.  Divide e cada plato una porción y sobre cada una distribuye las rajas con el queso gratinado. Acompaña con tortillas calientes.<br><br>


					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	 <div id="lpmoReceta2" class="receta wow slideInLeft" style="border: 3px #FFF solid;">
		 <div>
			  <img src="'.$url_controller.'images/recipes/lpmo2_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'L3_OF-aBhKg\')">

			 <!--
			 <img src="'.$url_controller.'images/recipes/lpmo2.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
			-->

			 <h4 class="tituloReceta">Torta de Chorizo y Arrachera</h4>
			  <h5 class="subtituloReceta">con Los Parrilleros Mamones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  </p><ul style="color: #000;">
							<li>1 paquete de Chorizo Frontera 370g</li>
							  <li>2 paquetes de Arrachera Frontera</li>
							  <li>3 Bolillos</li>

						  </ul>

					 <p></p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  </p><ul style="color: #000;">
							 <li>Chile serrano</li>
							  <li>Cebolla</li>
							  <li>Tomate</li>
						  </ul>

					 <p></p>
				 </div>
		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación (4 porciones).</span> <br><br>
						  1 - Corta los vegetales: Corta la cebolla, chile serrano y tomate en pequeños cubos proporcionales.<br>
						  2 - Una vez que esté listo el carbón, coloca la arrachera. Cocina 5 minutos por cada lado hasta llegar a tu término preferido.<br>
						  3 - Cocina los chorizos a fuego medio - lento, gira el chorizo en intervalos de 2 a 3 minutos según la fuerza del fuego hasta llegar a su cocción.<br>
						  4 - Corta la arrachera en cuadritos. <br>
						  5 - Abre un bolillo con la ayuda de un cuchillo, y rellena con la arrachera ya cortada.<br>
						  6 - Coloca 1 chorizo por bolillo en la parte central.<br>
						  7 - Complementa tu torta a tu gusto con los vegetales. <br>
	<br><br>
					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="resReceta1" class="receta wow slideInLeft" style="border: 3px #FFF solid;">
		 <div>
			  <img src="'.$url_controller.'images/recipes/res1_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'ywEdP91mGmA\')">

			 <!--
			 <img src="'.$url_controller.'images/recipes/res1.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
			-->

			 <h4 class="tituloReceta">Hamburguesa Carnívoro</h4>
			  <h5 class="subtituloReceta">4 piezas</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>8 piezas Hamburguesa Homestyle Frontera</li>
							  <li>300 gr. Chistorra para asar Frontera</li>
							  <li>120 gr. Tocino corte grueso a la parrilla </li>
							  <li>150 gr. Queso cheddar amarillo rebanadas</li>
							  <li>15 ml.  Aceite de canola</li>

						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							   <li>4 pzas. Pan para hamburguesa grande</li>
							   <li>60 gr.  Cebolla tatemada</li>
							   <li>80 gr.  Pimiento morrón verde tatemado</li>
							   <li>15 ml.  Aceite de canola</li>
							   <li>8 cdas. Mayonesa de chile asado</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span>
						  <br>
								Las brochetas de madera deben remojarse previamente por al menos 2 horas.<br><br>
								Prepara tu asador para calor mixto, es decir 50% fuego directo y 50% fuego indirecto.<br><br>
Para las cebollas tatemadas cortar las puntas  y posteriormente cortar rodajas de 1 centímetro de espesor aproximadamente. Atravesar la rodaja con una brocheta de madera previamente remojada en agua. Con ayuda de una brocha, aceitar ligeramente y salpimentar. <br><br>
Asar a la parrilla a fuego indirecto hasta suavizar y posteriormente cocinar a fuego directo para tatemar superficialmente. Retirar del asador y reservar.<br><br>
Pincha la Chistorra Frontera con las brochetas de madera para evitar que se separen. Así lograrás una cocción más pareja de todo el producto.<br><br>
Colocar a fuego indirecto el rollo de Chistorra Frontera para lograr una cocción interna por aproximadamente 8-10 minutos, girando cada 2 minutos. <br><br>
Pasar a fuego directo para marcar y dorar exterior, girando cada 20 segundos.<br><br>
Retirar del fuego, quitar las brochetas, cortar en trozos de aprox. 5 cms. De largo y posteriormente abrir por mitad. Reservar.<br>
Llevar el tocino a la parrilla a fuego directo (190°C). Asar por máximo un minuto de cada lado. Cortar en bastoncitos. Retirar y reserva.<br><br>
Para los pimientos cortar el tallo del chile y cortar la base. Hacer un corte transversal, desenrollar el pimiento y limpiarlo con ayuda del cuchillo retirando las semillas y fibras. Cortar en 4 cada pimiento y barnizar con aceite vegetal y colocar en la parrilla a fuego directo por ambos lados hasta que comience a quemarse ligeramente. Salpimentar y reservar.<br><br>
Cortar el pedúnculo de los jitomates. Recostar los jitomates sobre la tabla y cortar rodajas de grosor medio. Reservar.<br><br>
Con ayuda de una brocha, barnizar ligeramente la carne para Hamburguesa Homestyle Frontera (ambos lados). Llevar a la parrilla a fuego medio alto (160° C).<br><br>Asar aproximadamente 2 minutos y voltear. Colocar el queso cheddar encima del lado caliente que acabamos de voltear y colocar la tapa al asador.<br><br>
Dorar ligeramente el pan en la parrilla y posteriormente aderezar con la mayonesa de chile asado. Colocar sobre la base del pan 2 piezas de carne de Hamburguesa Homestyle Frontera seguidas por el pimiento y cebolla tatemados, la Chistorra Frontera, el tocino, 2 rodajas de jitomate y una hoja de lechuga.<br><br>
Colocar la tapa del pan y servir de inmediato.


					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="resReceta2" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			 <img src="'.$url_controller.'images/recipes/pepito.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">

			 <h4 class="tituloReceta">PEPITO EMPAPELADO DE ARRACHERA</h4>
			 <h5 class="subtituloReceta">Y 3 QUESOS</h5>

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">

							  <li>480 gr.     Arrachera Frontera</li>
							  <li>15 ml.       Aceite de canola</li>
							   <li>60 gr.       Queso gouda rebanado</li>
							   <li>60 gr.       Queso manchego rebanado</li>
							   <li>60 gr.       Queso oaxaca deshebrado</li>
							   <li>120 gr.       Aderezo chipotle</li>


						  </ul>

					 </p>
				 </div>


			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							 <li>120 gr.       Frijoles bayos refritos (lata / paquete)
</li>
							  <li>120 ml.      Jitomate rebanado</li>
							  <li>80 gr.         Aguacate rebanado</li>
							  <li>40 gr.         Cebolla morada fileteada</li>
							  <li>5 pzas.       Chapata natural grande</li>
							  <li>c/n             Papel aluminio</li>

						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span>
						  <br>

						  Con ayuda de una brocha, barnizar ligeramente con aceite vegetal la Arrachera Frontera. Llevar a la parrilla a fuego alto (190° C – 375° F). <br><br>
						Asar 1 minuto de cada lado y retirar. Cortar en cubos de 1x1 centímetros aproximadamente, dividir en 4 porciones y reservar caliente. <br><br>
						Cortar el pedúnculo del jitomate. Recostar el jitomate sobre la tabla y cortar rodajas de grosor medio. Reservar. <br><br>
						Abrir el aguacate por mitad, retirar el hueso. Cortar la pulpa en cuartos y posteriormente en octavos. Retirar la cáscara previa a su uso. <br><br>
						Cortar las puntas de la cebolla, cortar por mitad de manera transversal, filetear sin separar los cortes, retirar piezas deformes y reservar. <br><br>
						Abrir la chapata por mitad. Untar la base con los frijoles bayos y la tapa con el aderezo chipotle. <br><br>
						Agregar la Arrachera Frontera seguida de los quesos (15 gr. De cada queso por porción). Incorporar el jitomate, aguacate y cebolla morada. Envolver la chapata en papel aluminio, en forma de bolsa y calentar a fuego indirecto o en la orilla de la parrilla por alrededor de 10 minutos, girando constantemente. <br><br>
						Verificar que las chapatas se encuentren doradas, crujientes y con el queso derretido antes de retirar y servir.



					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="resReceta3" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			 <img src="'.$url_controller.'images/recipes/alambre_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'Qz-TeX2lxo0\')">
			 <h4 class="tituloReceta">ALAMBRE DE ARRACHERA</h4>
			  <h5 class="subtituloReceta">5 personas</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li> 480 gr.     Arrachera Frontera a la parrilla</li>
							  <li>150 gr.     Queso gouda rallado</li>
							  <li>150 gr.     Tocino corte grueso a la parrilla </li>
							   <li>1     Pimiento verde en cubo</li>
							   <li>1     Pimiento rojo en cubo</li>

						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">

							  <li>100 gr .     Cebolla blanca en cubo</li>
							   <li>15 ml.       Aceite de canola</li>
							  <li>15 pzas.   Tortillas de harina</li>
							   <li>c/n           Sal refinada</li>
							  <li>c/n           Pimienta</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span>
						  <br>
						  Con ayuda de una brocha, barnizar con aceite ligeramente la Arrachera Frontera y el tocino. <br>
Llevar a la parrilla a fuego alto (190° C – 375° F). Asar 1 minuto de cada lado y retirar.<br><br>
Cortar la arrachera en cubos de 1x1 centímetros aproximadamente, dividir en 4 porciones y reservar caliente. Cortar el tocino en bastoncitos y reservar caliente.<br><br>
Para el pimiento rojo y verde cortar el pedúnculo del pimiento y cortar la base, hacer un corte transversal, desenrollar el pimiento y limpiarlo con ayuda del cuchillo retirando las semillas y fibras, cortar tiras de aproximadamente 2 centímetros de ancho y posteriormente cortar los cubos de la misma medida y reservar. <br><br>
Para la cebolla, cortar las puntas de la cebolla, cortar en cuartos de manera transversal, cortar cada cuarto por la mitad, separar las láminas de cebolla y reservar. <br><br>
Calentar el aceite en un sartén o plancha de hierro colado, asar en el sartén, por un minuto cada pimiento morrón, en simultáneo. Agregar la cebolla y asar. <br><br>
Incorporar el tocino, cocinar un minuto más e incorporar la arrachera cortada en cubos.<br><br>
Agregar el queso y mezclar. Mover constantemente hasta fundir el queso homogéneamente. Servir con tortillas de harina calientes.

						  <br>

					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="resReceta4" class="receta" style="border: 3px #FFF solid; display: none;">
						 <div>
							 <img src="'.$url_controller.'images/recipes/tacos_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'sSlZ5efHsZw\')">

							 <h4 class="tituloReceta">TACOS VILLAMELÓN</h4>
							  <h5 class="subtituloReceta">10 tacos</h5>
							  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

							 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
								 <div class="col-md-6">
									  <p style="color: #000;">
										  <ul style="color: #000;">
											<li>540 gr.     Cecina Extrafina Frontera a la parrilla</li>
											  <li>450 gr.     Chorizo para Asar Frontera </li>
											  <li>2 manojos  Cebolla cambray a la parrilla</li>
											 <li>10 pzas.      Tortilla de maíz de su preferencia</li>
										  </ul>

									 </p>
								 </div>



							  <div class="col-md-6">
									  <p style="color: #000;">
										  <ul style="color: #000;">
											 <li>150 gr.        Chicharrón picado</li>
											  <li>c/n           Salsa 3 Chiles</li>
											  <li>c/n           Sal refinada</li>
											  <li>c/n           Pimienta</li>
										  </ul>

									 </p>
								 </div>

						  <div class="col-md-12">
									  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
										  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
										  Con ayuda de una brocha, barnizar ligeramente con aceite la Cecina Extrafina Frontera.<br>
Llevar a la parrilla a fuego directo alto (190° C – 375° F). Asar rápido (15-20 segundos) de cada lado y retirar. Picar la cecina y reservar caliente.<br><br>
Preparar el asador para calor mixto, es decir 50% fuego directo y 50% fuego indirecto.<br><br>
Colocar a fuego indirecto las piezas de chorizo para lograr una cocción interna más pareja sin quemar el exterior. (aproximadamente 8-10 minutos, girando cada 2 minutos). <br><br>
Pasar a fuego directo para marcar y dorar exterior, girando cada 20 segundos. Retirar, picar y reservar caliente.<br>
Sobre una tabla de cortar y con ayuda de un cuchillo, pica el chicharrón hasta lograr una textura similar a la del pan molido. Reservar para preparar los tacos.<br><br>
Combina la cecina y el chorizo en la salsa caliente. Servir la mezcla en tortillas a modo de taco.

										  <br>

									 </p>
								 </div>

							 </div>
						 </div>
					 </div>
	<div id="cerdoReceta1" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			  <img src="'.$url_controller.'images/recipes/cerdo1_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'v_jFAD3fXGc\')">
			 <h4 class="tituloReceta">AGUACHILE DE CECINA</h4>
			 <h5 class="subtituloReceta">10 porciones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li> 480 gr Cecina extrafina Frontera a la parrilla</li>
							  <li>15 ml. Aceite de canola</li>
							  <li>250 ml Jugo de limón</li>
							   <li>10 gr Cilantro</li>
							  <li>40 gr Pepino </li>

						  </ul>

					 </p>
				 </div>


			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							 <li>5 gr Chile serrano en rodajas</li>
							  <li>20 gr Cebolla morada fileteada</li>
							  <li>40 gr Pepino en medias lunas</li>
							  <li>c/n Tostadas de maíz</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br><br>
						  Cortar la cecina en tiras, marinar con la mitad del jugo de limón por 20 minutos. Licuar el resto de jugo de limón, pepino, chile y cilantro. Escurrir la cecina y agregar lo licuado. <br><br>
							Agregar la cebolla morada, el pepino en medias lunas a la carne. <br><br>
							Servir acompañado de tostadas de maíz. <br><br>
<br>


					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="cerdoReceta2" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			<img src="'.$url_controller.'images/recipes/tecolota.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
			 <h4 class="tituloReceta">TECOLOTA DE CECINA A LA PARRILLA</h4>
			 <h5 class="subtituloReceta">4 tortas</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>480 gr.     Cecina extrafina Frontera</li>
							  <li>600 ml.    Salsa verde para chilaquiles</li>
							  <li>360 grs.      Totopos de maíz caseros</li>
							  <li>40 gr.          Queso rallado</li>


						  </ul>

					 </p>
				 </div>


			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							 <li>120 ml.       Crema entera</li>
							  <li>40 gr.          Cebolla blanca fileteada</li>
							  <li>4 pzas.        Bolillo</li>
							  <li>c/n            Aceite de canola</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
						  Con ayuda de una brocha, barnizar ligeramente con aceite vegetal la Cecina Extrafina Frontera.<br><br>
Llevar a la parrilla a fuego alto (190° C – 375° F).<br><br>
Asar rápido (15-20 segundos) de cada lado y retirar. Cortar en fajitas, dividir en 4 porciones y reservar caliente.<br><br>
En una cacerola, poner a calentar la salsa para chilaquiles. Incorporar los totopos y cocinar un par de minutos.<br><br>
Abrir los bolillos por el costado y agregar los chilaquiles y sobre estos la crema entera, queso rallado y cebolla fileteada.<br><br>
Finalmente, agregar la porción de Cecina Extrafina Frontera y cubrir con la tapa del bolillo.<br><br>
Servir.

						  <br>

					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="cerdoReceta3" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			 <img src="'.$url_controller.'images/recipes/cerdo3_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">

			 <h4 class="tituloReceta">EMPALMES NORTEÑOS DE ADOBADA</h4>
			 <h5 class="subtituloReceta">6 porciones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>540 gr.     Cecina enchilada Frontera parrillada</li>
							  <li>12 pzas.   Tortilla de harina de su preferencia </li>
							  <li>40 grs.      Mantequilla sin sal </li>

						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							 <li>15 ml.       Aceite de canola</li>
							  <li>240 grs.    Queso manchego rebanado</li>
							  <li>c/n         Salsa martajada de chiles toreados</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
						  Preparar el asador para fuego alto (190° C – 375° F). Con ayuda de una brocha, barnizar ligeramente la Cecina Enchilada Frontera con aceite vegetal. Colocar las piezas de cecina a la parrilla, cocinar por medio minuto de cada lado. <br><br>
Retirar la Cecina Enchilada Frontera y picar en cubos pequeños. Reservar caliente para emplatar y servir.<br>
En una cacerola pequeña, derretir la mantequilla y reservar caliente.<br><br>
Colocar la Cecina Enchilada Frontera picada sobre una tortilla de harina, cubrir con 2 rebanadas de queso manchego y finalmente tapar con otra tortilla de harina.<br><br>
Con ayuda de una brocha, barnizar ligeramente las tortillas con mantequilla derretida y llevar a la parrilla sobre fuego indirecto. Dorar ligeramente ambas caras del empalme.<br><br> Servir cuando el queso se encuentre derretido y acompañar con la salsa martajada de chiles toreados.<br>

						  <br>

					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="cerdoReceta4" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			<img src="'.$url_controller.'images/recipes/cerdo4_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'ipRuB3oNu6A\')">
			 <h4 class="tituloReceta">HUARACHE DE CECINA ENCHILADA</h4>
			 <h5 class="subtituloReceta">4 porciones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>540 gr.     Cecina Enchilada Frontera</li>
							  <li>4 pzas.   Huarache de maíz con frijol</li>
							  <li>15 ml.    Aceite de canola</li>
							  <li>40 grs.   Manteca de cerdo</li>
							  <li>40 grs.   Queso rallado</li>
							  <li>120 ml.   Crema entera</li>
							  <li>c/n       Sal refinada</li>

						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							   <li>40 grs.     Rábano rojo en rodajas</li>
							   <li>40 grs.     Cebolla blanca fileteada</li>
							   <li>15 grs.     Cilantro picado fino</li>
							  <li>8 pzas.     Nopal baby asado</li>
							  <li>200 gr.    Frijol negro refrito lata</li>
							  <li>240 ml.   Salsa verde cruda</li>
							  <li>c/n           Pimienta</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
						  Prepara tu asador para fuego alto (190° C – 375° F).<br>
						  Con ayuda de una brocha, barnizar ligeramente los nopales y la Cecina Enchilada Frontera con aceite vegetal.<br><br>
						  Asar por ambos lados, salpimentar, cortar en fajitas y reservar.<br><br>
						  Cortar las puntas del rábano. Obtener rodajas lo más delgadas posible. Reservar en agua.<br><br>
						  Cortar las puntas de la cebolla. Cortar por mitad de manera transversal. Filetear sin separar los cortes.<br> <br>Retirar piezas deformes. Reservar.<br><br>
						  Retirar los tallos de cilantro, enrollar las hojas y cortar finamente. Repasar con cuchillo para lograr un corte fino. Reservar.<br><br>
						  Untar los huaraches ligeramente con manteca de cerdo y calentar en parrilla a fuego indirecto.<br><br>
						  Retirar los huaraches de la parrilla y untar con frijoles negros previamente calentados.<br><br>
						  Cubrir con una capa ligera de salsa verde cruda.<br><br>
						  Agregar la Cecina Enchilada Frontera parrillada ya cortada en fajitas, seguido del nopal, cebolla blanca fileteada, crema entera, queso rallado.<br><br>
						  Finalmente espolvorear el cilantro fresco picado y las rodajas de rábano.<br><br>

						  <br>

					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="cerdoReceta5" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			<img src="'.$url_controller.'images/recipes/cerdo5_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'KCftV8bhe4Y\')">
			 <h4 class="tituloReceta">MOLCAJETE DE LONGANIZA Y SALSA TATEMADA</h4>
			 <h5 class="subtituloReceta">4 porciones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>400 gr.     Longaniza Parrillera Frontera</li>
							  <li>4 	   Brochetas de madera</li>
							  <li>220 gr.     Salsa tatemada</li>
							  <li>30 gr.       Queso Cotija rallado</li>
							  <li>½ pza.      Aguacate en abanico</li>
							  <li>12 pzas.   Tortilla de maíz de su preferencia</li>
							  <li>2 pzas.      Limón sin semilla mitades</li>
							  <li>c/n           Sal refinada</li>
							  <li>c/n           Pimienta</li>

						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  INGREDIENTES SALSA TATEMADA:
						  <ul style="color: #000;">
							   <li>2 pzas.     Jitomate saladet</li>
							   <li>3 pzas.     Tomatillo pelado</li>
							   <li>2 dientes de ajo pelados</li>
							  <li>¼ pza.       Cebolla blanca</li>
							  <li>3 ramitas de cilantro fresco</li>
							  <li>3 chiles de árbol secos</li>
							  <li>1 pizca de comino molido</li>
							  <li>1 pizca de orégano seco</li>
							  <li>15 ml.       Aceite de canola</li>
							  <li>c/n           Sal de grano</li>
							  <li>c/n           Pimienta</li>
						  </ul>

					 </p>
				 </div>


		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify; ">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
<strong>PREPARACIÓN DE LA SALSA TATEMADA:</strong> <br>
Quitar el tallo y semillas a los chiles. Suavizarlos en agua caliente. Reservar. <br><br>
Con ayuda de una brocha barnizar los jitomates, tomatillos y dientes de ajo.  <br><br>
Cortar las puntas de la cebolla y posteriormente cortar rodajas de 1 centímetro de espesor aproximadamente. Atravesar la rodaja con una brocheta de madera previamente remojada en agua. Con ayuda de una brocha, aceitar ligeramente.  <br><br>
Llevar los vegetales a la parrilla a fuego directo (190°C). Asar hasta que se quemen ligera y homogéneamente por todos sus lados. <br><br>
Moler los jitomates, tomatillos, ajo y cebolla en el molcajete con ayuda de un poco de sal de grano. Agregar el cilantro fresco finamente picado. <br><br>
Sazonar con el comino, orégano y pimienta. Rectificar punto de sal.  <br> <br>
<br>
<strong>PREPARACIÓN DE MOLCAJETE:</strong> <br>
Preparar el asador para calor mixto, es decir 50% fuego directo y 50% fuego indirecto. <br><br>
Colocar a fuego indirecto las piezas de Longaniza Parrillera Frontera para lograr una cocción interna más pareja sin quemar el exterior. (aproximadamente 10-12 minutos, girando cada 2 minutos).  <br><br>
Pasar a fuego directo para marcar y dorar exterior, girando cada 20 segundos. <br><br>
Para el aguacate retirar la piel, cortar en rodajas gruesas y reservar. <br><br>
Cortar con un cuchillo filoso y delgado. Presionar ligeramente para separar las rebanadas y marcar el abanico. Reservar en agua con limón previo a su uso. <br><br>
Colocar las rodajas de Longaniza Parrillera Frontera caliente sobre la salsa dentro del molcajete. <br><br>
Espolvorear el queso cotija. Decorar con el abanico de aguacate. Servir acompañado de tortillas de maíz calientes y mitades de limón. <br>


						  <br>

					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="cerdoReceta6" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			<img src="'.$url_controller.'images/recipes/cerdo6.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
			 <h4 class="tituloReceta">TACOS DE CHORIZO POBLANO EN TORTILLA AZUL</h4>
			 <h5 class="subtituloReceta">4 porciones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>4 pzas.     Chorizo para Asar Frontera</li>
							  <li>15 ml.       Aceite de canola</li>
							  <li>8 cdas.     Chile poblano fileteado tatemado</li>
							  <li>4 pza.      Cebolla cambray tatemada</li>
							  <li>2 pzas.     Limón sin semilla cortado por mitad</li>
							  <li>4 pzas.     Tortilla de maíz azul hecha a mano</li>
							  <li>c/n           Sal refinada</li>
							  <li>c/n           Pimienta</li>												 
						  </ul>

					 </p>
				 </div>


			  <div class="col-md-6">
					  <p style="color: #000;">
						  INGREDIENTES SALSA TATEMADA:
						  <ul style="color: #000;">
							   <li>2 pzas.     Jitomate saladet</li>
							   <li>3 pzas.     Tomatillo pelado</li>
							   <li>2 dientes de ajo pelados</li>
							  <li>¼ pza.       Cebolla blanca</li>
							  <li>3 ramitas de cilantro fresco</li>
							  <li>3 chiles de árbol secos</li>
							  <li>1 pizca de comino molido</li>
							  <li>1 pizca de orégano seco</li>
							  <li>15 ml.       Aceite de canola</li>
							  <li>c/n           Sal de grano</li>
							  <li>c/n           Pimienta</li>
						  </ul>

					 </p>
				 </div>


		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify;">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
Preparar el asador para calor mixto, es decir 50% fuego directo y 50% fuego indirecto.<br><br>
Colocar a fuego indirecto las piezas de Chorizo para Asar Frontera para lograr una cocción interna más pareja sin quemar el exterior. (aproximadamente 8-10 minutos, girando cada 2 minutos). Pasar a fuego directo para marcar y dorar exterior, girando cada 20 segundos. Retirar y reservar.<br><br>
Para las cebollas cambray cortar el tallo de la cebolla. Con ayuda de una brocha, aceitar ligeramente. Salpimentar. Asar a la parrilla a fuego indirecto hasta suavizar y posteriormente cocinar a fuego directo para tatemar superficialmente. Retirar del asador, cortar por mitad y reservar.<br><br>
Calentar las tortillas de maíz azul, evitar dorarlas o quemarlas.<br><br>
Cortar por mitad el Chorizo para Asar Frontera y colocar sobre la tortilla.<br><br>
Agregar las julianas de chile poblano tatemado y la cebolla cambray.<br><br>
Acompañar cada taco con medio limón y la salsa de su preferencia.<br><br>
<br>


						  <br>

					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	 <div id="polloReceta1" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>		
			<img src="'.$url_controller.'images/recipes/pollo1_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'XiU2ixqSHdU\')">
			  <h4 class="tituloReceta">Burrito de pollo parrilero, queso Chihuahua y frijoles puercos</h4>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>500 gr. Pollo Parrillero Muslo estilo Zarandeado Frontera</li>
							  <li>180 grs. Queso Chihuahua rallado</li>
							  <li>160 grs. Cebolla blanca asada</li>
							  <li>250 grs. Frijoles puercos</li>
						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  INGREDIENTES FRIJOLES PUERCOS:
						  <ul style="color: #000;">
							  <li>250 grs.   Frijol bayo refrito de lata</li>
							  <li>25 grs.     Manteca de cerdo</li>
							  <li>¼ pza.      Cebolla blanca mediana</li>
							  <li>60 grs.     Chorizo para Asar Frontera</li>
							  <li>60 grs.     Jamón de pierna</li>
							  <li>60 grs.     Tocino ahumado rebanadoo</li>
							  <li>60 gr.       Queso manchego rebanado</li>
							  <li>1 cda.       Chiles jalapeños en vinagre</li>
							  <li>20 grs.      Aceituna verde rebanada</li>
							  <li>4 pzas.      Tortilla de harina “Burrera”</li>
							  <li>c/n           Sal refinada</li>
							  <li>c/n           Pimienta</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify; ">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
<strong>PREPARACIÓN FRIJOLES PUERCOS:</strong> <br>
Picar finamente la cebolla. Picar en cuadritos el tocino y el jamón, desmenuzar el chorizo.<br><br>
Poner a calentar la manteca en una cacerola pequeña y acitronar la cebolla, posteriormente bajar la lumbre y agregar el tocino y el chorizo para que suelten la grasa.<br><br>
Posteriormente agregar el jamón y los frijoles. Cocinar moviendo constantemente por 3 minutos.<br><br>
Picar finamente los jalapeños e incorporarlos a la mezcla junto con el queso y las aceitunas rebanadas.<br><br>
Cocinar 3 minutos más a fuego lento. Rectificar punto de sal y salpimentar al gusto. Reservar.<br><br>
<br>
<strong>PREPARACIÓN DEL BURRITO:</strong> <br>
Cortar las puntas de la cebolla, cortar por mitad de manera transversal, filetear sin separar los cortes.<br><br> 
En un sartén o plancha de hierro, calentar el aceite, agregar la cebolla fileteada y asar hasta que la cebolla se acitrone (cambie de color y se vuelva transparente). Retirar y reservar.<br><br>
Prepara tu asador para fuego mixto (directo e indirecto) con una temperatura en calor directo de aproximadamente 190° C (375° F).<br><br>
Con ayuda de una brocha, barniza ligeramente el Pollo Parrillero Frontera con aceite vegetal. Sellar las piezas de pollo a fuego directo por ambos lados, y posteriormente pasar a calor indirecto para terminar su cocción. Retirar y cortar en fajitas, reservar caliente para emplatar y servir.<br><br>
Calentar en la parrilla las tortillas burreras, evitando quemarlas. Retirar.<br><br>
Untar las tortillas con 4 cucharadas de frijoles puercos dejando las orillas de la tortilla limpias, agregar al centro de la tortilla el pollo cortado en fajitas y 2 cucharadas de cebolla asada.<br><br>
Colocar el queso rallado encima del resto de los ingredientes.<br><br>
Doblar el burrito y llevar nuevamente a la parrilla, a fuego indirecto, para dorar ligeramente la tortilla y para que se funda el queso. Servir.<br><br>


					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	<div id="polloReceta2" class="receta" style="border: 3px #FFF solid; display: none;">
		 <div>
			<img src="'.$url_controller.'images/recipes/pollo2_play.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid" onclick="playVideoRecipe(\'BEGMvSqb8I4\')">
			  <h4 class="tituloReceta">POLLO PARRILLERO CON PICO DE GALLO DE FRIJOL Y PIÑA</h4>
			  <h5 class="subtituloReceta">4 porciones</h5>
			  <img src="'.$url_controller.'images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
			 <div class="row contenidoReceta" style="margin-left: 0px; margin-right: 0px;">
				 <div class="col-md-6">
					  <p style="color: #000;">
						  <ul style="color: #000;">
							<li>500 gr.     Pollo Parrillero Muslo estilo Zarandeado Frontera</li>
							  <li>15 ml.       Aceite de canola</li>
							  <li>400 grs.   Pico de gallo de frijol y piña</li>
						  </ul>

					 </p>
				 </div>

			  <div class="col-md-6">
					  <p style="color: #000;">
						  INGREDIENTES PICO DE GALLO DE FRIJOL Y PIÑA:
						  <ul style="color: #000;">
							  <li>250 grs.   Piña miel</li>
							  <li>1 lata       Frijol negro entero</li>
							  <li>¼ pza.      Cebolla morada</li>
							  <li>½ pza.      Pimiento morrón rojo</li>
							  <li>15 hojas  Cilantro fresco</li>
							  <li>½ taza      Jugo de naranja</li>
							  <li>¼ taza      Jugo de limón</li>
							  <li>15 ml.      Aceite de olivo</li>
							  <li>12 pzas.   Tortilla de maíz de su preferencia</li>
							  <li>2 pzas.     Limón sin semilla</li>
							  <li>c/n           Sal refinada</li>
							  <li>c/n           Pimienta</li>
						  </ul>

					 </p>
				 </div>

		  <div class="col-md-12">
					  <p style="color: #000; margin-top: 15px; padding: 25px; text-align: justify; ">
						  <span style="font-family: quadonextraBold; font-size: 20px;">Preparación</span> <br>
<strong>PREPARACIÓN DE PICO DE GALLO DE FRIJOL Y PIÑA:</strong> <br>
Lavar y desinfectar todos los ingredientes.<br><br>
Cortar la piña en láminas gruesas. Asar las láminas de piña para posteriormente cortarlas en cubos de 1 cm x 1 cm aproximadamente, colocar en un contenedor.<br><br>
Escurrir los frijoles en una coladera y enjuagar a chorro de agua. Agregarlos a la piña.<br><br> 
Picar finamente la cebolla morada y el cilantro para posteriormente incorporarlos a la mezcla.<br><br>
Cortar el pimiento morrón en pequeños cubos y agregar a la mezcla.<br><br> 
Agregar los jugos y mezclar.<br><br> 
Salpimentar al gusto, agregar el resto del aceite de olivo, mezclar nuevamente y reservar.<br><br>
<br>
<strong>PREPARACIÓN DE POLLO PARRILLERO:</strong> <br>
Preparar el asador para fuego mixto (directo e indirecto) con una temperatura en calor directo de aproximadamente 190° C (375° F).<br><br>
Con ayuda de una brocha, barniza ligeramente el pollo con aceite vegetal.<br><br>
Sellar las piezas de pollo a fuego directo por ambos lados, y posteriormente pasar a calor indirecto para terminar su cocción. Retirar y reservar caliente para emplatar y servir.<br><br>
Después de parrillar el Pollo Parrillero Frontera y lograr el punto de cocción deseado, servir y acompañar con el pico de gallo de piña y frijol.<br><br>
Sugerimos llevar a la mesa limones y tortillas de maíz calientes.<br><br>


					 </p>
				 </div>

			 </div>
		 </div>
	 </div>
	';
	return $html_recetas;
}

function getBotonesRecetas() {
	global $url_controller;
	$html_botones_recetas = '
	<div id="recetaLPMO" class="recetasClass row" style="padding-left: 20px;padding-top: 20px;">
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'lpmoReceta1\');">
			<img src="'.$url_controller.'images/recipes/lpmo1.jpg" style="height: 100%;">							
			<span id="txtresLPMO1" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Cecina enchilada y rajas poblanas</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'lpmoReceta1\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'lpmoReceta2\');">
			<img src="'.$url_controller.'images/recipes/lpmo2.jpg" style="height: 100%;">							
			<span id="txtresLPMO1" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Torta de chorizo y arrachera</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'lpmoReceta2\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
	</div>
	<div id="recetaRes" class="recetasClass row" style="padding-left: 20px;padding-top: 20px;">
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'resReceta1\');">
			<img src="'.$url_controller.'images/recipes/res1.jpg" style="height: 100%;">							
			<span id="txtresReceta1" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Hamburguesa carnívoro</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'resReceta1\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'cerdoReceta2\');">
			<img src="'.$url_controller.'images/recipes/tecolota.jpg" style="height: 100%;">							
			<span id="txtcerdoReceta2" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Tecolota de cecina a la parrilla</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'cerdoReceta2\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'cerdoReceta1\');">
			<img src="'.$url_controller.'images/recipes/cerdo1.jpg" style="height: 100%;">							
			<span id="txtcerdoReceta1" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Aguachile de cecina</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'cerdoReceta1\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'resReceta2\');">
			<img src="'.$url_controller.'images/recipes/pepito.jpg" style="height: 100%;">							
			<span id="txtresReceta2" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Pepito "empapelado" de arrachera y 3 quesos</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'resReceta2\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'resReceta3\');">
			<img src="'.$url_controller.'images/recipes/alambre.jpg" style="height: 100%;">							
			<span id="txtresReceta3" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Alambre de arrachera</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'resReceta3\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'resReceta4\');">
			<img src="'.$url_controller.'images/recipes/tacos.jpg" style="height: 100%;">							
			<span id="txtresReceta4" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Tacos Villamelón</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'resReceta4\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
	</div>
	<div id="recetaCerdo" class="recetasClass row" style="padding-left: 20px;padding-top: 20px; display: none">
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'cerdoReceta3\');">
			<img src="'.$url_controller.'images/recipes/cerdo3.jpg" style="height: 100%;">							
			<span id="txtcerdoReceta3" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Empalmes norteños de adobada</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'cerdoReceta3\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'cerdoReceta4\');">
			<img src="'.$url_controller.'images/recipes/cerdo4.jpg" style="height: 100%;">							
			<span id="txtcerdoReceta4" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Huarache de cecina enchilada</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'cerdoReceta4\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'cerdoReceta5\');">
			<img src="'.$url_controller.'images/recipes/cerdo5.jpg" style="height: 100%;">							
			<span id="txtcerdoReceta5" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Molcajete de longaniza y salsa tatemada</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'cerdoReceta5\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'cerdoReceta6\');">
			<img src="'.$url_controller.'images/recipes/cerdo6.jpg" style="height: 100%;">							
			<span id="txtcerdoReceta6" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 11.5px; line-height: 15px">Tacos de chorizo poblano en tortilla azul</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'cerdoReceta6\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
	</div>
	<div id="recetaPollo" class="recetasClass row" style="padding-left: 20px;padding-top: 20px; display: none">
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'polloReceta1\');">
			<img src="'.$url_controller.'images/recipes/pollo1.jpg" style="height: 100%;">							
			<span id="txtpolloReceta1" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Burrito de pollo parrillero, queso chihuahua y frijoles puercos</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'polloReceta1\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
		<div style="background: linear-gradient(#424141,black); width: 92%;height: 50px;display: inline-flex;text-align: left; margin-bottom: 5px; align-items: center" onclick="selectRecipe(\'polloReceta2\');">
			<img src="'.$url_controller.'images/recipes/pollo2.jpg" style="height: 100%;">							
			<span id="txtpolloReceta2" class="subcontent recetaPointer fontSizeTablet" style="padding-left: 10px; font-size: 12px; line-height: 15px">Pollo parrillero con pico de gallo de frijol y piña</span>
		</div>
		<div style="border-radius: 0px 10px 10px 0px;margin-right: 10px;width: 4%; align-items: center;background-image: url(images/FondoFlechaMenu.png);height: 50px;padding: 2px;display: inline-flex;" onclick="selectRecipe(\'polloReceta2\');">
			<img src="images/FlechaMenu.png" class="flechaMenu pull-right">
		</div>
	</div>
	';
	return $html_botones_recetas;
}

function getTipSlider() {
	global $url_controller, $vi;
	$html_tip_slider = '
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip1.jpg?v='.$vi.'" title="Arrachera marinada">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb1.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb1@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip2.jpg?v='.$vi.'" title="Arrachera marinada">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb2.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb2@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip3.jpg?v='.$vi.'" title="Arrachera marinada">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb3.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb3@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip4.jpg?v='.$vi.'" title="Arrachera marinada">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb4.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb4@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip5.jpg?v='.$vi.'" title="Arrachera marinada">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb5.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb5@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip6.jpg?v='.$vi.'" title="Prender el carbón">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb6.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb6@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip7.jpg?v='.$vi.'" title="Preservar jugo de la carne">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb7.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb7@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	<div class="item">
		<a class="modalProds" rel="fancybox-button" href="'.$url_controller.'images/tips/tip8.jpg?v='.$vi.'" title="Antojo de hamburguesas">
			<img class="owl-lazy" data-src="'.$url_controller.'images/tips/thumb8.png?v='.$vi.'" data-src-retina="'.$url_controller.'images/tips/thumb8@2x.png?v='.$vi.'" alt="image">
		</a>
	</div>
	';
	return $html_tip_slider;
}

function getBotonesCategories() {
	global $url_controller;
	$html_botones_cat = '
	<img src="'.$url_controller.'images/lmpoRecetas2.png" alt="image" id="logoLPMO" onclick="selecRecipeType(\'recetaLPMO\');" style="margin: auto; display: inline-block; width: 24%" class="img-fluid">
	<img src="'.$url_controller.'images/res1.png" alt="image" id="logoRes" onclick="selecRecipeType(\'recetaRes\');" style="margin: auto; display: inline-block; width: 24%" class="img-fluid">
	<img src="'.$url_controller.'images/cerdo1.png" alt="image" id="logoCerdo" onclick="selecRecipeType(\'recetaCerdo\');" style="margin: auto; display: inline-block; width: 24%" class="img-fluid">
	<img src="'.$url_controller.'images/pollo1.png" alt="image" id="logoPollo" onclick="selecRecipeType(\'recetaPollo\');" style="margin: auto; display: inline-block; width: 24%" class="img-fluid">
	';
	return $html_botones_cat;
}

function getProductF() {
	global $url_controller, $vi;
	$html_productF = '
	<img src="'.$url_controller.'images/prod/t1.png?v='.$vi.'" style="width: 80%; padding-top: 20px; margin: auto" />
	<div id="productF1" class="owl-carousel" style="margin-top: -40px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos1_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos1_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t2.png?v='.$vi.'" style="width: 66%; margin: auto" />
	<div id="productF2" class="owl-carousel" style="margin-top: -15px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos2_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos2_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t3.png?v='.$vi.'" style="width: 68%; margin: auto" />
	<div id="productF3" class="owl-carousel" style="margin-top: -40px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos3_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos3_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t4.png?v='.$vi.'" style="width: 76%; margin: auto" />
	<div id="productF4" class="owl-carousel" style="margin-top: -40px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos4_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos4_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t5.png?v='.$vi.'" style="width: 63%; margin: auto" />
	<div id="productF5" class="owl-carousel" style="margin-top: -40px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos5_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos5_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t6.png?v='.$vi.'" style="width: 55%; margin: auto" />	
	<div id="productF6" class="owl-carousel" style="margin-top: 15px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos6_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos6_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t7.png?v='.$vi.'" style="width: 76%; margin: auto" />
	<div id="productF7" class="owl-carousel" style="margin-top: -40px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos7_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos7_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	<img src="'.$url_controller.'images/prod/t8.png?v='.$vi.'" style="width: 72%; margin: auto" />
	<div id="productF8" class="owl-carousel" style="margin-top: -40px">
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos8_1.png?v='.$vi.'" alt="image">
		</div>
		<div class="item">
			<img src="'.$url_controller.'images/prod/Productos8_2.png?v='.$vi.'" onclick="irAPuntosDeVenta()" alt="image">
		</div>
	</div>
	';
	return $html_productF;
}

function saveBbqHost($message, $images, $session_id) {
	global $log, $db, $relativo;
	$log->trace("App: SESSION saveBbqHost: ".print_r($_SESSION,true));
	
	if (count($_SESSION[$session_id]) <= 0) {
		return "false";
	}
	
	$usuario = unserialize($_SESSION[$session_id]["user_dentro"]);
	
	$imagesBbqHost = array();
	
	for ($i=0; $i<count($images); $i++) {
		define('UPLOAD_DIR', '../images/parrillero/');
		$img = $images[$i]["data"];
		$log->trace("IMG: ".$img);
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file = UPLOAD_DIR . uniqid() . '.png';
		$success = file_put_contents($file, $data);
		if ($success) {
			$imagesBbqHost[] = new Bbq_host_image(str_replace("../", "", $file),0,$images[$i]["isCover"],$db->now());
		}
	}
	
	$bbq_host = new Bbq_host($usuario->getId_user(),$message,0,1);
	$bbq_host->setImages($imagesBbqHost);
	
	$resp = $bbq_host->save();
	if ($resp) {
		$email = "soporte@vmasideas.com";
		$subject ="Parrillero invitado: ".$bbq_host->getId_bbq_host();
		$msg = "Buen día<br>
			Se ha registrado un parrillero invitado con ID BB_HOST: <strong>".$bbq_host->getId_bbq_host()."</strong><br>
			El correo registrado es: <strong>".$bbq_host->getUser()->getEmail()."</strong>.<br>
			Favor de verificar más información en base de datos.<br>
			<br>
			Gracias.<br>
			<strong>Soporte VMAS: Frontera Carne Parrillera.</strong>
		";
		$log->trace("Llego hasta aqui");
		require_once(__DIR__ ."/".$relativo."../SAW/includes/funcMail.php"); 
	}
	return $resp;
}

function getWinner() {
	global $log, $db;
	$id_winner = $db->rawQueryValue("SELECT id_bbq_host FROM TBL_BBQ_HOST WHERE status = 1 AND is_winner = 1 AND creation_time BETWEEN (DATE_ADD(LAST_DAY(CURRENT_DATE), INTERVAL 1 DAY) - INTERVAL 2 MONTH) AND (LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH)) LIMIT 1");
	$log->trace("ID Winner: ".$id_winner);
	if ($db->count > 0) {
		return new Bbq_host($id_winner);
	}
	return null;
}

function getEstados() {
	global $log, $db;
	$estados = $db->rawQuery("SELECT CS.id_state, CS.name FROM CAT_STATE CS, TBL_STORE TS WHERE CS.id_state=TS.id_state GROUP BY CS.id_state ORDER BY CS.name");
	
	$html_estados = '<option value="0" id="txtOpcSelec">Selecciona tu estado</option>';			
	for($i=0; $i < count($estados); $i++) 
	{
		$html_estados .= '<option value='.$estados[$i]["id_state"].'>'.$estados[$i]["name"].'</option>';
	}
	return $html_estados;
}

/*******************************************************************************/
/*                                Page Load                                    */
/*******************************************************************************/

// Comprobamos si esta es una llamada de ajax
if(isset($_POST["action"]))
{
	global $log,$db,$IP;
	$action = $_POST["action"];
	
	if($action == "getDatosIniciales") {
		$session_id = $_POST["session_id"];
		$log->trace("Session id antes: ---->".$session_id."<-----");
		if (strlen($session_id) <= 0) {
			session_id(uniqid());
			session_start();
			$session_id = session_id();
			$_SESSION[$session_id] = array("id" => $session_id, "desde_app" => 1);
	//		require_once (__dir__."/".$relativo."../FB/conectarFacebook.php"); 
			$log->trace("App: SESSION datos iniciales session_id vacio: ".print_r($_SESSION,true));
			session_write_close();
		}
		else {
			session_id($session_id);
			session_start();
			if (!is_array($_SESSION[$session_id])) {
				session_id(uniqid());
				session_start();
				$session_id = session_id();
				$_SESSION[$session_id] = array("id" => $session_id, "desde_app" => 1);
		//		require_once (__dir__."/".$relativo."../FB/conectarFacebook.php"); 
				$log->trace("App: SESSION datos iniciales _SESSION vacio: ".print_r($_SESSION,true));
				session_write_close();
			}
		}
		$resultado = array($session_id, "javascript:;");
//		if (isset($_SESSION["fb_access_token"])) {
//			$user_info = $_SESSION["fb_user"];
//			$id_login = $user_info["id"];
//			$img_profile = "https://graph.facebook.com/".$id_login."/picture?type=square&height=300&width=300";
//			$_SESSION["img_profile"] = $img_profile;
//			unset($_SESSION["fb_access_token"]);
//			$resultado = array($id_login, "");
//		}
//		else {
//			$resultado = array("", $loginUrl);
//		}
		
		$html_prod_cat = getProductosPorCategoria();		
		$resultado[] = $html_prod_cat;
		$hoy = date('Y-m-d');
		$tomorrow = date('Y-m-d', strtotime($day . " +1 days"));
		$resultado[] = $hoy;
		$resultado[] = $tomorrow;
		
		$html_recetas = getRecetas();
		$resultado[] = $html_recetas;
		
		$html_botones_recetas = getBotonesRecetas();
		$resultado[] = $html_botones_recetas;
		
		$html_tip_slider = getTipSlider();
		$resultado[] = $html_tip_slider;
		
		$html_botones_cat = getBotonesCategories();
		$resultado[] = $html_botones_cat;
		
		$html_productF = getProductF();
		$resultado[] = $html_productF;
		
		$winner = getWinner();
		if ($winner instanceof Bbq_host) {
			$resultado[] = 1;
			$resultado[] = '<div class="title d-inline-block" style="margin-bottom: 0px;">
									<p style="font-size: 20px">
										<br>
										Nuestro <span style="font-weight: bold">parrillero invitado</span> es:<br>
										'.$winner->getUser()->getName_complete().'.<br>
									</p>
								</div>';
//			$resultado[] = '<img style="width: 100%; margin: auto; padding: 20px; border-radius: 35px" alt="parrilleroinvitado" src="'.$url_controller.$winner->getImageCover()->getPath().'" />';
			$resultado[] = $url_controller.$winner->getImageCover()->getPath();
			$html_winner = '
				<div class="title d-inline-block" style="margin-bottom: 0px;">
					<p style="font-size: 20px">
						<br>
						<span style="font-weight: bold">Nombre:</span><br>
						'.$winner->getUser()->getName_complete().'.<br>
					</p>
				</div>
			<div class="col-md-12">
				<img style="width: 100%; margin: auto; padding: 20px; border-radius: 35px" alt="parrilleroinvitadoW" src="'.$url_controller.$winner->getImageCover()->getPath().'" />
			</div>
			<div class="col-md-12">
				<div class="title d-inline-block" style="margin-bottom: 0px;">
					<p style="font-size: 20px">
						<br>
						'.nl2br($winner->getMessage()).'.<br><br>
					</p>
				</div>
			</div>
			<div class="col-md-12"s style="margin-bottom: 40px">
				<img src="'.$url_controller.'images/titulo_galeria.png?v='.$vi.'" style="width: 55%; margin: auto" />';	

			$html_winner .=	'<div id="carruselWinner" class="owl-carousel" style="">';
			for ($i=0; $i<count($winner->getImages()); $i++) {
				$html_winner .= '<div class="item">
							<img class="owl-lazy" src="'.$url_controller.$winner->getImages()[$i]->getPath().'?v='.$vi.'" alt="image">
						</div>';
			}				
			$html_winner .=	'</div>';
			
//			$html_winner .=	'<div id="carruselWinner" style="">';
//			for ($i=0; $i<count($winner->getImages()); $i++) {
//				$html_winner .= '<div class="item">
//							<img src="'.$url_controller.$winner->getImages()[$i]->getPath().'?v='.$vi.'" alt="image">
//						</div>';
//			}			
//			$html_winner .=	'</div>';
			
			$resultado[] = $html_winner;
		}
		else {
			$resultado[] = 0;
			$resultado[] = '<div class="title d-inline-block" style="margin-bottom: 0px;">
									<p style="font-size: 20px">
										<br>
										<span style="font-weight: bold">Frontera</span> te invita a ser nuestro<br>
										Parrillero Invitado<br>
									</p>
								</div>';
//			$resultado[] = '<img style="width: 100%; margin: auto; padding: 20px; border-radius: 35px" alt="parrilleroinvitado" src="'.$url_controller.'images/parrillero/dummy.jpg?v='.$vi.'" />';
			$resultado[] = $url_controller.'images/parrillero/dummy.jpg?v='.$vi;
			$resultado[] = "";
		}
		
		$html_bases = '<div class="title d-inline-block">
							<p style="font-size: 12px;padding: 20px;text-align: justify; margin: 0;line-height: 1.3;">
								<br>
								<span style="font-weight: bold">Bases y restricciones</span><br><br>
								<span>
								Parrillero Invitado es un apartado de la aplicación de Frontera: Carne Parrillera, donde se invita a los usuarios a compartir imágenes de sus platillos o parrillas en las cuales se empleen productos de la marca Frontera.<br>
								<span style="font-weight: bold">Política de privacidad y cesión de derechos.</span><br>
								Al participar y enviar su material, los usuarios están aceptado los términos presentes en este apartado y la política de privacidad disponible en:<br>
								<a style="color: red">https://www.granjasryc.com/APrivacidad.pdf</a><br>
								El usuario autoriza a RYC Alimentos S.A de C.V., así como a todas aquellas terceras personas físicas o jurídicas a las que RYC Alimentos pueda ceder los derechos de explotación sobre las fotografías, o parte de las mismas, a que indistintamente puedan utilizar todas las fotografías, o partes de las mismas en las que el usuario interviene.<br>
								La autorización del usuario no tiene ámbito geográfico determinado por lo que RYC Alimentos y otras personas físicas o jurídicas a las que RYC Alimentos pueda ceder los derechos de explotación sobre las fotografías, o partes de las mismas, en las que el usuario interviene como modelo, podrán utilizar esas fotografías, o partes de las mismas, en todos los países del mundo sin limitación geográfica de ninguna clase.<br>
								De igual manera, la autorización del usuario no fija ningún límite de tiempo para su concesión ni para la explotación de las fotografías, o parte de las mismas, en las que aparece como modelo, por lo que su autorización se considera concedida por un plazo de tiempo ilimitado.<br>
								Los contenidos (fotografías, videos y textos) que los usuarios envíen a través de esta aplicación,  serán recibidos por RYC Alimentos S.A. de C.V. quien a su vez revisará y seleccionará aquellos que cumplan con las restricciones aquí presentes para publicar el contenido en medios digitales como: app, redes sociales, sitio de internet e impresiones en tienda.<br>
								RYC Alimentos no empleará las fotografías recibidas por los usuarios para su uso en algún medio distinto a los explícitamente listados en este documento.<br>
								<span style="font-weight: bold">Selección de Ganadores</span><br>
								Los ganadores se seleccionarán de forma mensual por parte de RYC Alimentos de acuerdo con los siguientes criterios:<br>
								- Calidad fotográfica de la(s) fotografías.<br>
								- Originalidad.<br>
								- Calidad del mensaje ingresado.<br>
								- Visibilidad o descripción de los productos<br>Frontera usados.<br>
								Aquellos que cumplan de mejor manera con los requisitos serán seleccionados para publicación en app, redes sociales y página web.<br>
								En caso de existir un premio para los ganadores, será notificado en un documento de bases de promoción, mismo que estará disponible en la App y en medios digitales. Los ganadores serán contactados por RYC Alimentos vía correo electrónico para coordinación de la entrega de su producto.
								</span>
							</p>
						</div>';
		$resultado[] = $html_bases;
		
		$html_estados = getEstados();
		$resultado[] = $html_estados;

		echo json_encode($resultado);
	}
	elseif($action == "registrarUsuario") {
		$name = $_POST["name"];
		$email = $_POST["email"];
		$gender = $_POST["gender"];
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();		
		$result = registrarUsuario($name, $email, $gender, $session_id);
		$log->trace("App: SESSION registrar usuario: ".print_r($_SESSION,true));
		session_write_close();
		echo json_encode($result);
	}
	elseif ($action == "startBbq") {
		$bbq_name = $_POST["bbq_name"];
		$bbq_date = $_POST["bbq_date"];
		$bbq_time = $_POST["bbq_time"];
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$_SESSION[$session_id]["bbq_info"]["name"] = $bbq_name;
		$_SESSION[$session_id]["bbq_info"]["date"] = $bbq_date;
		$_SESSION[$session_id]["bbq_info"]["time"] = $bbq_time;
		session_write_close();
		echo json_encode(true);
	}
	elseif ($action == "calculateProd") {
		$numInvitados = $_POST["numInvitados"];
		$numInvitadas = $_POST["numInvitadas"];
		$result = calculateProd($numInvitados, $numInvitadas);
		echo json_encode($result);
	}
	elseif ($action == "armarResumenProd") {
		$rel_prod_cant = $_POST["rel_prod_cant"];
		$result = armarResumenProd($rel_prod_cant);
		echo json_encode($result);
	}
	elseif ($action == "saveBbq") {
		$rel_prod_cant = $_POST["rel_prod_cant"];
		$numInvitados = $_POST["numInvitados"];
		$numInvitadas = $_POST["numInvitadas"];
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$result = saveBbq($rel_prod_cant, $numInvitados, $numInvitadas, $session_id);
		session_write_close();
		echo json_encode($result);
	}
	elseif ($action == "saveFriends") {
		$bbq_friends = $_POST["bbq_friends"];
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$result = saveFriends($bbq_friends, $session_id);
		session_write_close();
		echo json_encode($result);
	}
	elseif ($action == "finishBbq") {
		$address = $_POST["address"];
		$message = $_POST["message"];
		$latitude = $_POST["latitude"];
		$longitud = $_POST["longitud"];	
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$result = finishBbq($address, $message, $latitude, $longitud, $session_id);
		session_write_close();
		echo json_encode($result);
	}
	elseif ($action == "crearShareBbq") {
		require_once("imgLibrary.php");
//		global $meses, $relativo, $url_controller;
		$Bbq = new Bbq(169);
		construyeRender($Bbq, $meses, $relativo, $url_controller);
		echo json_encode(true);
	}
	elseif ($action == "shareBbq") {
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$result = shareBbq($session_id);
		session_write_close();
		echo json_encode($result);
	}
	elseif ($action == "mediaShareBbq") {
		$session_id = $_POST["session_id"];
		$app = $_POST["app"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$result = mediaShareBbq($app, $session_id);
		session_write_close();
		echo json_encode($result);
	}
	elseif ($action == "closeBbq") {
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$_SESSION[$session_id]["bbq_info"]["id_bbq"] = NULL;
		session_write_close();
		echo json_encode(true);
	}
	elseif ($action == "saveBbqHost") {
		$message = $_POST["message"];
		$images = $_POST["images"];
		$session_id = $_POST["session_id"];
		session_id($session_id);
		session_start();
		if (count($_SESSION[$session_id]) <= 0) {
			echo json_encode(false);
			exit();
		}
		$result = saveBbqHost($message, $images, $session_id);
		session_write_close();
		echo json_encode($result);
	}
}
  ?>