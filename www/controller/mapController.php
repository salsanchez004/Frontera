<?php
header('Access-Control-Allow-Origin: *');

session_start();

$relativo = "../";

require_once (__dir__."/../config.php"); 
require_once (__dir__."/".$relativo."../model/Store.php");
require_once (__dir__."/".$relativo."../model/Product.php");

// indicamos tipo de datos
header('Content-Type: application/json');


function getUbicaciones()
{
	global $log,$db,$IP;
	$ubicaciones= array();
	
	$locations = $db->rawQuery("SELECT TS.id_store as id_store, TS.name as nameSuc, TS.address as address, TS.latitude, TS.longitude, CC.name as nameCity, CS.name as nameState, CS.id_state as id_state, CC.id_city as id_city FROM TBL_STORE TS, CAT_CITY CC, CAT_STATE CS WHERE TS.id_city=CC.id_city AND TS.id_state=CS.id_state");
	for($i=0; $i<count($locations);$i++) 
	{
			$ubicaciones[] = array("id_store" => $locations[$i]["id_store"], "nameSuc" => $locations[$i]["nameSuc"], "address" => $locations[$i]["address"], "latitude" => $locations[$i]["latitude"],"longitude" => $locations[$i]["longitude"],"nameCity" => $locations[$i]["nameCity"],"nameState" => $locations[$i]["nameState"],"id_state" => $locations[$i]["id_state"],"id_city" => $locations[$i]["id_city"]);

	}
	return $ubicaciones;
}

function getStoresyCity($city)
{
	global $log,$db,$IP;
	$stores=array();
	$db->where("status", "1");
	$db->where("id_city",$city);
	$store_query=$db->get("TBL_STORE");
	for($i=0; $i<count($store_query);$i++) 
	{
		$tienda=new Store($store_query[$i]["id_store"]);
		$tienda->asignaProductos();
		$stores[] = $tienda;
	}
	return $stores;
}



function getStoresbyLocation($lat,$lon)
{
	global $log,$db,$IP;
	$stores=array();
	$store_query=$db->rawQuery("SELECT *, ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$lon." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515 * 1.609344) as distance FROM TBL_STORE WHERE status = 1 HAVING distance <= 10 ORDER BY distance ASC");
	for($i=0; $i<count($store_query);$i++) 
	{
		$tienda=new Store($store_query[$i]["id_store"]);
		$tienda->asignaProductos();
		$stores[] = $tienda;
	}
	return $stores;
}



/*******************************************************************************/
/*                                Page Load                                    */
/*******************************************************************************/

// Comprobamos si esta es una llamada de ajax
if(isset($_POST["action"]))
{
	global $log,$db,$IP;
	$action = $_POST["action"];
	if($action == "getUbicaciones") 
	{
		$result = getUbicaciones();
		echo json_encode($result);
	}
	elseif ($action == "getStoresbyCity") 
	{
		$city=$_POST["id_ciudad"];
		$result = getStoresyCity($city);
		echo json_encode($result);
	}
	elseif ($action == "getStoresbyLocation") 
	{
		$lat=$_POST["lat"];
		$lon=$_POST["lon"];
		$result = getStoresbyLocation($lat,$lon);
		echo json_encode($result);
	}
}
  ?>