<?php
/***********************************************************************
 *           Funciones para manipulación de imágen                     *
 ***********************************************************************/
//ini_set('memory_limit', '-1');
$relativo = "../";

use PHPImageWorkshop\ImageWorkshop;
require_once(__DIR__."/PHPImageWorkshop/ImageWorkshop.php");

use GDText\Box;
use GDText\Color;
require_once (__DIR__."/ImageBox/Box.php");
require_once (__DIR__."/ImageBox/Color.php");
require_once (__dir__."/".$relativo."../model/Bbq.php");
require_once (__dir__."/".$relativo."../model/Bbq_product.php");
require_once (__dir__."/".$relativo."../model/User.php");
require_once (__dir__."/".$relativo."../model/Product.php");
require_once (__dir__."/../config.php");   

//Método que debe recibir un objeto de tipo Compañia, Unidad y Modelo, que contenga información del formato, modelo y zonas requeridas para poder generar 1 imagen.
//El parámetro de tipo permite indicar si va a pintar la plantilla de previsualización o el render final.
function construyeRender($Bbq, $meses, $relativo, $url_controller)
{
	try {
		global $log,$db,$IP;

		$plantilla = ImageWorkshop::initFromPath("../imgShare/plantillaShareParrillada.png");
		$plantillaAncho=$plantilla->getWidth();
		$plantillaAlto=$plantilla->getHeight();

		$group = ImageWorkshop::initVirginLayer($plantillaAncho, $plantillaAlto);
		//Plantilla
		$level = 2; 
		$sublayer = $plantilla;
		$positionX = 0; 
		$positionY = 0; 
		$position = "LT";
		// $group->addLayerOnTop($sublayer, $positionX, $positionY, $position);
		$datosPlantilla = $group->addLayer($level, $sublayer, $positionX, $positionY, $position);

		//$log->trace("[".$IP."] "."construyeRender imagen: ".$imagenFondo);
		$log->trace("[".$IP."] "."Inicio de construyeRender: ".microtime(true));

		$bbq_name = $Bbq->getName();
		DibujaMultitexto($group, $bbq_name, "../fonts/QuadonBold.otf", 60,'#ffffff',27,336,747,121,3,0,1,3);

		$bbq_user = $Bbq->getUser()->getName_complete();
		DibujaMultitexto($group, "De ".$bbq_user, "../fonts/QuadonBold.otf", 25,'#ffffff',0,478,800,30,3,0,1.2,3);

		$bbq_dia = substr($Bbq->getBbq_date(),8,2);
		$bbq_mes = $meses[substr($Bbq->getBbq_date(),5,2)];
		$bbq_date = $bbq_dia." de ".$bbq_mes;
		$bbq_time = substr($Bbq->getBbq_time(),0,5);
		$bbq_address = $Bbq->getAddress();
		DibujaMultitexto($group, "El día ".$bbq_date." a las ".$bbq_time." hrs.\nEn ".$bbq_address, "../fonts/Quadon.otf", 16,'#ffffff',0,529,800,60,3,0,1.2,3);

		$bbq_latitude = $Bbq->getLatitude();
		$bbq_longitud = $Bbq->getLongitud();
		$bbq_ubic = "";
		if ($bbq_latitude !== "0" && $bbq_longitud !== "0" && strlen($bbq_latitude) > 0 && strlen($bbq_longitud) > 0) {
			$liga = "https://maps.googleapis.com/maps/api/staticmap?center=".$bbq_latitude.",".$bbq_longitud."&zoom=16&size=640x318&markers=color:red%7Clabel:Aqui%7C".$bbq_latitude.",".$bbq_longitud."&style=feature:transit|element:labels.icon|visibility:off&style=feature:poi.business|element:all|visibility:off&key=AIzaSyAusPp1OHyEGOs3PVlvKoZQPYOfy5KiebI";
			$desFolder = '../imgUbic/';
			$imageName = 'parrillada_'.$Bbq->getId_bbq().'.png';
			$imagePath = $desFolder.$imageName;
			file_put_contents($imagePath,file_get_contents($liga));
			$bbq_ubic = '../imgUbic/parrillada_'.$Bbq->getId_bbq().'.png';
			$x = 80;
			$y = 600;
		}
		else {
			$bbq_ubic = '../images/titulo4Emailing.png';
			$x = 233;
			$y = 664;
		}
		$imagenUbic=ImageWorkshop::initFromPath($bbq_ubic);
		$datosCapa = $group->addLayer(3, $imagenUbic, $x, $y, $position);

		$bbq_message = $Bbq->getMessage();
		DibujaMultitexto($group, $bbq_message, "../fonts/Quadon.otf", 18,'#ffffff',45,946,341,431,2,0,1.2,3);

		$imagenVineta=ImageWorkshop::initFromPath("../images/vinetaEmailingBlack.jpg");
		$xProd = 439;
		$yProd = 991;
		for($i=0; $i<count($Bbq->getProducts()); $i++) {
			$Bbq_prod = $Bbq->getProducts()[$i];
			$Prod = $Bbq_prod->getProduct();
			$gr = ' ('.$Bbq_prod->getGrammage().'g c/u)';
			if ($Prod->getCategory()->getName() == "Complementos") {
				$gr = "";
			}
			$datosCapa = $group->addLayer(3, $imagenVineta, $xProd, $yProd, $position);
			$texto_producto = $Bbq_prod->getQuantity().' '.$Prod->getName().$gr;
			DibujaMultitexto($group, $texto_producto, "../fonts/Quadon.otf", 17,'#ffffff',$xProd + 18,$yProd - 8,310,55,2,0,1.5,3);

			if (strlen($texto_producto) >= 38) {
				$yProd = $yProd + 55;	
			}
			else {
				$yProd = $yProd + 26;
			}
		}

		imagepng($group->getResult(),"../imgShare/shareBbq_".$Bbq->getId_bbq().".png", 9);

		return true;
	}
	catch(Exception $ex) {
		return $ex->getMessage();
	}
 }
 
 function DibujaImagen($group,$img,$zonaImgX,$zonaImgY,$zonaImgAncho,$zonaImgAlto,$imgType=1, $level = 'x')
 {
 	global $log,$db,$IP;
	$log->trace("[".$IP."] "."--DibujaImagen-- "); 
	if($imgType==1)
	{
		//$log->trace("[".$IP."] "."if1: ".$img); 
		//AppLogInformacion("DibujaImagen","$img","TIPO RESOURCE",1);
		$imagen=ImageWorkshop::initFromPath($img);
	}
	else
	{
		//$log->trace("[".$IP."] "."if1else: ");
		$imagen=ImageWorkshop::initFromResourceVar($img);
		//AppLogInformacion("DibujaImagen","$img","TIPO IMG VARIABLE",1);
	}
	
	$imgAncho=$imagen->getWidth();
	$imgAlto=$imagen->getHeight();
	// Detectando orientación del espacio en blanco, estos valores se deben leer de la BD (zona de imagen de fondo).
	
	$ratioImg=$imgAncho/$imgAlto;
	$ratioZona=$zonaImgAncho/$zonaImgAlto;
	$log->trace("[".$IP."] "."ratioZona: ".$ratioZona);
	//se valida si la proporcion de la imágen es suficiente para el rezising
	if($ratioZona>=$ratioImg)
	{
		//redimensiona basándose en altura
		$log->trace("[".$IP."] "."IFratioZona: ".$ratioZona);
		$imagen->resizeInPixel(null, $zonaImgAlto, true); 
		//AppLogInformacion("DibujaImagen","RATIOZONA>=RATIOIMG $ratioZona - $ratioImg","TIPO RESOURCE",1);
	}
	else
	{
		//redimensiona basándose en anchura
		$log->trace("[".$IP."] "."elseratioZona: ".$ratioZona);
		$imagen->resizeInPixel($zonaImgAncho, null, true); 
		//AppLogInformacion("DibujaImagen","RATIOZONA<RATIOIMG $ratioZona - $ratioImg","TIPO RESOURCE",1);
	}	
	
	$sublayer = $imagen;
	// $sublayer->rotate(25);
	$positionX = $zonaImgX; 
	$positionY = $zonaImgY; 
	$position = "LT";
	// Linea para rotar el texto5
	// $sublayer->rotate(45);
	$log->trace("[".$IP."] "."DibujaImagen2Fin--"); 
	if ($level == 'x') {
		$group->addLayerOnTop($sublayer, $positionX, $positionY, $position);
	}
	else {
		$group->addLayer($level, $sublayer, $positionX, $positionY, $position);	
	}
	 
	//unión de capas
	$group->mergeAll();

	//AppLogInformacion("DibujaImagen","FINALIZADO","TIPO RESOURCE",1);
 }

 function SetPositionText($fontAlign,$box, $bandera)
 {
 	if ($bandera == 0) 
	{
 		$align = 'top';
 	}else
	{
 		$align = 'center';
 	}
    switch("".$fontAlign)
    {
        case "1" : $box->setTextAlign('right', $align); return;
        case "2" : $box->setTextAlign('left', $align); return;
        case "3" : $box->setTextAlign('center', $align); return;
    }
 }
 
//si no se recibe lineheight, se inicializa con 1
 function DibujaMultitexto($group, $text, $fontPath, $fontSize,$fontColor,$positionX,$positionY,$ancho,$alto,$fontAlign,$inclinado,$lineHeight=1, $level = 'x')
 {	
 	global $log,$db,$IP;
	 
	 
	 //se añade como parámetro el espaciado de línea, el cual puede ser: 1, 1.15 , 1.5 , 2.0 , 2.5, 3.0
	 //$text="Prueba 2:┴Línea 1.┴Línea 2.┴Línea 3.┴Línea 4"; 
	 
	 //procesamiento de texto para reemplazo de saltos de línea por |n    se reemplaza el caracter ALT+193 : ┴
	 $text=str_replace("┴","\n", $text);
	 
	
	$log->trace("[".$IP."] "."DibujaMultitexto, texto: ".$text); 
	//ancho y alto de la zona de texto 
	$im = imagecreatetruecolor($ancho, $alto);
	
	if ($inclinado!=0) {
		$bandera = 1;
	}else{
		$bandera = 0;
	}
	
	imagesavealpha($im, true);
	// poner el color trasparente cuando se gira el texto
	$color = imagecolorallocatealpha($im, 255, 255, 255, 127);

	imagefill($im, 0, 0, $color);
	//imagepng($im, 'test.png');
	//se create con fondo verde (a eliminar como transparencia).
	/*$backgroundColor = imagecolorallocate($im, 92, 255, 38);
	imagefill($im, 0, 0, $backgroundColor);
	imagecolortransparent($im,$backgroundColor);*/
	//AppLogInformacion("TXT Multilinea","$text- Color: $fontColor Tamaño: $fontSize","$backgroundColor",1);
	$rgb=hex2rgb($fontColor);
	//AppLogInformacion("TXT Multilinea","Colores RGB: $rgb[0] - $rgb[1] - $rgb[2]","Color Texto",1);
	// $log->trace("[".$IP."] "."fontAlign: ".json_encode($fontAlign)); 
	// $log->trace("[".$IP."] "."fontSize: ".json_encode($fontSize)); 
	// $log->trace("[".$IP."] "."inclinado: ".json_encode($inclinado)); 
	// $log->trace("[".$IP."] "."positionX: ".json_encode($positionX)); 
	// $log->trace("[".$IP."] "."positionY: ".json_encode($positionY)); 
	// $log->trace("[".$IP."] "."fontColor: ".json_encode($fontColor)); 
	// $log->trace("[".$IP."] "."fontPath: ".json_encode($fontPath)); 
	// $log->trace("[".$IP."] "."text: ".json_encode($text));
	// $log->trace("[".$IP."] "."ancho: ".json_encode($ancho));
	// $log->trace("[".$IP."] "."minspacing: ".json_encode($minspacing));
	// $log->trace("[".$IP."] "."linespacing: ".json_encode($linespacing));
	if ($fontAlign == 4) {	
		$log->trace("[".$IP."] "."entrando a alineación justificada");
		$a = imagettftextjustified($im, $fontSize, $inclinado, 0, 0, $fontColor, $fontPath, $text, $ancho, $minspacing=3,$linespacing=1);
			
		
	}else
	{
		$box = new Box($im);
		$box->setFontFace($fontPath);
		$box->setFontColor(new Color($rgb[0], $rgb[1], $rgb[2]));
		//$box->setTextShadow(new Color(0, 0, 0, 0), 0, 0);
		$box->setFontSize($fontSize);
		$box->setLineHeight($lineHeight);
		$box->setBox(0, 0, $ancho, $alto);
		//$box->enableDebug();
		//$box->setTextAlign('left', 'top');
		//$box->setBaseline(0.5);
	    SetPositionText($fontAlign,$box, $bandera);
		$box->draw($text);	 
		$log->trace("[".$IP."] "."Pintado de texto sin inclinado: ".$inclinado); 
		// funcion para girar texto
		
		if ($inclinado!=0) 
		{
		$log->trace("[".$IP."] "."INCLINADO: ".$inclinado); 
		$im = imagerotate($im, $inclinado, $color);
		$rotated_width = imagesx($im);
	    $rotated_height = imagesy($im);
		$dx = $rotated_width - $ancho;
	    $dy = $rotated_height - $alto;
			
		$log->trace("Valores: ancho: $ancho, alto: $alto, ancho_rotado: $rotated_width, alto_rotado: $rotated_height, dx: $dx, dy: $dy");
		
//	    $cropped_rotated_image = imagecreatetruecolor($ancho, $alto);
//	    imagesavealpha($cropped_rotated_image, true);
//	    imagefill($cropped_rotated_image, 0, 0, $color);
//	    imagecopyresampled($cropped_rotated_image, $im, 0, 0, $dx / 2, $dy / 2, $ancho, $alto, $ancho, $alto);
//	    $im = $cropped_rotated_image;
			
		$positionX = $positionX - ($dx/2);
		$positionY = $positionY - ($dy/2);
		$ancho = $rotated_width;
		$alto = $rotated_height;
		}
	}
	    //$nameTMP="test_text_".uniqid().".png";
	 	//imagepng($im, $nameTMP, 9, PNG_ALL_FILTERS);
		DibujaImagen($group,$im,$positionX,$positionY,$ancho,$alto,2,$level);
	
	
 }
 
 
 function hex2rgb($hex) 
 {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   //return implode(",", $rgb); // returns the rgb values separated by commas
   return $rgb; // returns an array with the rgb values
}