var ubicaciones = [];
var loading = '<div class="overlay"><div class="loading-gif"><img src="img/loader.gif" alt="Cargando..." /></div></div>';

var map_ubicaciones = null;
var markersUbi=[];

var ubiGPS=0;

var infowindows=[];

google.maps.event.addDomListener(window, 'load', initializeUbi);
var image = 'images/iconoMarker.png';

var storesByCity;

function getUbicacionesTiendas()
{
	try {
		$.ajax({
			type: 'POST',
			url: 'controller/mapController.php',
			cache: false,
			dataType: "json",
			data: {
				action : 'getUbicaciones'
			},
			success: function (data, textStatus, jqXHR) 
			{
				
				if (data) 
				{
					ubicaciones=data;
					setUbicaciones();

				}
				else {
//					alert("No fue posible invitar amigos: " + data);
					$.notify("No fue posible invitar amigos: " + data, "error");
				}
			},
			error: function (xhr, status, error) 
			{
				console.log(error.toString());
			}
		});
	}
	catch(ex) 
	{
		
		console.log(ex.message);
	}
}

	
function initializeUbi()
{
	var mapOptions = {
				zoom: 12
			};
	map_ubicaciones = new google.maps.Map(document.getElementById('map-ubicaciones'),mapOptions);
	//var pos = new google.maps.LatLng(19.043656037863794,-98.19815257564187);
	//map.setCenter(pos);
	getUbicacionesTiendas();
}
		

/**
 * funcion para colocar las ubicaciones
 */
function setUbicaciones()
{
	var suc = '';
	var latlng;
	if(ubiGPS)
	{
		//poner el centro en el GPS del usuario.
		 latlng= new google.maps.LatLng(19.043656037863794,-98.19815257564187);
	}
	else
	{
		latlng= new google.maps.LatLng(19.432779,-99.133152);
		//latlng= new google.maps.LatLng(ubicaciones[0].latitude,ubicaciones[0].longitude);
	}

	map_ubicaciones.setCenter(latlng);
	for(var i = 0; i  < ubicaciones.length; i++)
	{
		suc=suc+'<div class="proveedor"><h2><a class="link" onclick="setUbiTiendas('+ i +');" style="cursor:pointer"> '+ ubicaciones[i].nameSuc +'</a></h2><p>'+ ubicaciones[i].address +'<br>'+ ubicaciones[i].nameCity +'<br>'+ ubicaciones[i].nameState +'</p></div>';

		var markerlatlng = new google.maps.LatLng(ubicaciones[i].latitude,ubicaciones[i].longitude);
		var ubi = ubicaciones[i];
		//console.log(markerlatlng);
		createMarkerUbi(map_ubicaciones,markerlatlng, "name", "<div style='color:#000000;'><b>Tienda: </b> "+ubi.nameSuc+" <br> <b>Dirección: </b> "+ubi.address+" <br> <b>Ciudad:</b> "+ubi.nameCity+"<br> <b>Estado: </b> "+ubi.nameState+"<br></div>");
	}
	showMarkersUbi();
}

function setUbiTiendas(index)
{
	var ubi = ubicaciones[index];
	var latlng = new google.maps.LatLng(ubi.latitude,ubi.longitude);
	deleteMarkersUbi();			
	map_ubicaciones.setZoom(10);
	map_ubicaciones.setCenter(latlng);
	showMarkersUbi();
	document.body.scrollTop = document.documentElement.scrollTop = 0;
	google.maps.event.trigger(mark,'click');


}

/*******************************************************************************************************************/

// A function to create the marker and set up the event window function 
function createMarkerUbi(mapa,latlng, name, html) 
{
	var contentString = html;
	var markerUbi = new google.maps.Marker({
		position: latlng,
		map: mapa,
		icon : image,
		zIndex: Math.round(latlng.lat() * -100000) << 5
	});
	var infowindow = new google.maps.InfoWindow({
			content: html
			 });
	infowindows.push(infowindow);
	markerUbi.addListener('click', function() { 
		hideAllInfoWindows();
		infowindow.open(map_ubicaciones, markerUbi); 
	});
	
	markersUbi.push(markerUbi);
	return markerUbi;
}


function setMapOnAllUbi(map) {
  for (var i = 0; i < markersUbi.length; i++) {
	markersUbi[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkersUbi() {
  setMapOnAllUbi(null);
}

// Shows any markers currently in the array.
function showMarkersUbi() {
  setMapOnAllUbi(map_ubicaciones);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkersUbi() {
  clearMarkersUbi();
  markersUbi = [];
}

function hideAllInfoWindows() 
{
	for(var i=0; i<infowindows.length;i++)
		{
			infowindows[i].close();
		}
     /*markersUbi.forEach(function(marker) {
     	marker.infowindow.close(map_ubicaciones, marker);
  }); */
}



//obtiene posiciónGPS del usuario
function getLocation() 
{
  $("#divSearch").hide();
  if (navigator.geolocation) 
  {
    navigator.geolocation.getCurrentPosition(showPosition);
  }
  else 
  {
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}
function showPosition(position) 
{
	latlng= new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	/*var markerlatlng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);*/
	
	
	var imageHome = 'images/iconoMarkerHome.png';
	var markerUbi = new google.maps.Marker({
		position: latlng,
		map: map_ubicaciones,
		icon : imageHome,
		zIndex: Math.round(latlng.lat() * -100000) << 5
	});
	markersUbi.push(markerUbi);
	showMarkersUbi();
	map_ubicaciones.setCenter(latlng);
	
	//muestra de productos
	getStoresbyLocation(latlng.lat(), latlng.lng());
}

function getStoresbyLocation(latitude, longitude)
{
	try {
		$.ajax({
			type: 'POST',
			url: 'controller/mapController.php',
			cache: false,
			dataType: "json",
			data: {
				action : 'getStoresbyLocation',
				lat : latitude,
				lon: longitude
			},
			success: function (data, textStatus, jqXHR) 
			{
				
				var html="";
				if (data) 
				{
					$("#divStores").hide();
					storesByCity=data;
					$("#divBrands").hide();
					for(var i=0; i<storesByCity.length; i++)
						{
							html+='<h1 class="sucursalTitulo"><i class="fa fa-map-marker" aria-hidden="true" style="font-size: 30px; margin-right: 10px;"></i>'+storesByCity[i]["name"]+'</h1>';
							html+='<h2 class="sucursalDireccion">'+storesByCity[i]["address"]+'</h2>';
							html+='<hr style="border-color: white;"/>';
							html+='<div class="row">';
							//ciclo de productos
							for(var j=0; j<storesByCity[i].Products.length; j++)
							{
								html+='<div class="col-sm-4" style="text-align: center;line-height: 1.2; margin-bottom: 20px;"><img src="' +storesByCity[i].Products[j].thumb_path+ '" alt="Producto" style="display:block; margin:auto" /><span class="sucursalProducto">' +storesByCity[i].Products[j].name+ '</span></div>';
							}
							html+='</div>';
						}
					$("#divStores").html(html);
					$("#divStores").fadeIn( "slow", function() { });
				}
				else 
				{
					$.notify("Error de consulta: " + data, "error");
				}
			},
			error: function (xhr, status, error) 
			{
				console.log(error.toString());
			}
		});
	}
	catch(ex) 
	{
		
		console.log(ex.message);
	}
}


function eligeEstado()
{
	
	$('#selectCiudades')
    .find('option')
    .remove()
    .end()
    .append('<option value="0">Seleccione su ciudad</option>')
    .val('0');
	
	
	//se obtiene una tienda del estado, se centra el mapa en las coordenadas de la tienda
	var id_estado=$("#selectEstado").find('option:selected').val();
	var busqueda=true;
	//búsqueda de estado en arreglo.
	for(var i = 0; (i  < ubicaciones.length)&&(busqueda); i++)
	{
		if(ubicaciones[i].id_state==id_estado)
			{
				//centra mapa en coordenadas
				latlng= new google.maps.LatLng(ubicaciones[i].latitude,ubicaciones[i].longitude);
				map_ubicaciones.setZoom(8);
				map_ubicaciones.setCenter(latlng);
				busqueda=false;
			}
	}
	
	//relleno de ciudades que contengan productos en ese estado
	//extracción de ciudades del arreglo
	var ciudades = [];
	for(var i = 0; i <ubicaciones.length; i++)
	{
		if(ubicaciones[i].id_state==id_estado)
			{
				//valida si ciudad no está ya agregada
				var ciudadNueva=true;
				for(var j = 0; j <ciudades.length; j++)
				{
					if(ubicaciones[i].id_city==ciudades[j].id_city)
						{
							ciudadNueva=false;
						}
				}
				if(ciudadNueva)
					{
						//almacena item en ciudades
						ciudades.push({id_city:ubicaciones[i].id_city, city:ubicaciones[i].nameCity});
						//creación de combo
						$('#selectCiudades').append($('<option>', {
							value: ubicaciones[i].id_city,
							text: ubicaciones[i].nameCity
						}));
					}
			}
	}
	
}


function eligeCiudad()
{
	//map_ubicaciones.setZoom(10);
	//se obtiene una tienda del estado, se centra el mapa en las coordenadas de la tienda
	var id_ciudad=$("#selectCiudades").find('option:selected').val();
	var busqueda=true;
	//búsqueda de estado en arreglo.
	for(var i = 0; (i  < ubicaciones.length)&&(busqueda); i++)
	{
		if(ubicaciones[i].id_city==id_ciudad)
			{
				//centra mapa en coordenadas
				latlng= new google.maps.LatLng(ubicaciones[i].latitude,ubicaciones[i].longitude);
				map_ubicaciones.setZoom(12);
				map_ubicaciones.setCenter(latlng);
				busqueda=false;
			}
	}
	
	getStoresyCity(id_ciudad);
}


function getStoresyCity(id_ciudad)
{
	try {
		$.ajax({
			type: 'POST',
			url: 'controller/mapController.php',
			cache: false,
			dataType: "json",
			data: {
				action : 'getStoresyCity',
				id_ciudad : id_ciudad
			},
			success: function (data, textStatus, jqXHR) 
			{
				
				var html="";
				if (data) 
				{
					$("#divStores").hide();
					storesByCity=data;
					$("#divBrands").hide();
					for(var i=0; i<storesByCity.length; i++)
						{
							html+='<h1 class="sucursalTitulo"><i class="fa fa-map-marker" aria-hidden="true" style="font-size: 30px; margin-right: 10px;"></i>'+storesByCity[i]["name"]+'</h1>';
							html+='<h2 class="sucursalDireccion">'+storesByCity[i]["address"]+'</h2>';
							html+='<hr style="border-color: white;"/>';
							html+='<div class="row">';
							//ciclo de productos
							for(var j=0; j<storesByCity[i].Products.length; j++)
							{
								html+='<div class="col-sm-4" style="text-align: center;line-height: 1.2; margin-bottom: 20px;"><img src="' +storesByCity[i].Products[j].thumb_path+ '" alt="Producto" style="display:block; margin:auto" /><span class="sucursalProducto">' +storesByCity[i].Products[j].name+ '</span></div>';
							}
							html+='</div>';
						}
					$("#divStores").html(html);
					$("#divStores").fadeIn( "slow", function() { });
				}
				else 
				{
					$.notify("Error de consulta: " + data, "error");
				}
			},
			error: function (xhr, status, error) 
			{
				console.log(error.toString());
			}
		});
	}
	catch(ex) 
	{
		
		console.log(ex.message);
	}
}

function uniq(a) {
    var seen = {};
    return a.filter(function(item) {
        return seen.hasOwnProperty(item) ? false : (seen[item] = true);
    });
}



function showSearch()
{
	$("#divSearch").show();
}