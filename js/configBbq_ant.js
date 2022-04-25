var id_login = '';
var sliderAbiertos = [0];
var paso_actual = 0;
var acordeon;
var id_activo;
var URLFrontera = "https://vmasideas.online/frontera/";
var debug = "";
var desde_app = 1;

// This is called with the results from from FB.getLoginStatus().
//function statusChangeCallback(response) {
//	console.log('statusChangeCallback');
//	console.log(response);
//	// The response object is returned with a status field that lets the
//	// app know the current login status of the person.
//	// Full docs on the response object can be found in the documentation
//	// for FB.getLoginStatus().
//	if (response.status === 'connected') {
//	  // Logged into your app and Facebook.
//	  testAPI();
//	} else {
//	  // The person is not logged into your app or we are unable to tell.
////	  	document.getElementById('status').innerHTML = 'Please log ' +
////		'into this app.';
//		console.log('Please log ' + 'into this app.');
//	}
//}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
//function checkLoginState() {
//	FB.getLoginStatus(function(response) {
//	  statusChangeCallback(response);
//	});
//}

//window.fbAsyncInit = function() {
//	FB.init({
//	  appId      : '457442978424858',
//	  cookie     : true,  // enable cookies to allow the server to access 
//						  // the session
//	  xfbml      : true,  // parse social plugins on this page
//	  version    : 'v3.3' // The Graph API version to use for the call
//	});
//
//	// Now that we've initialized the JavaScript SDK, we call 
//	// FB.getLoginStatus().  This function gets the state of the
//	// person visiting this page and can return one of three states to
//	// the callback you provide.  They can be:
//	//
//	// 1. Logged into your app ('connected')
//	// 2. Logged into Facebook, but not your app ('not_authorized')
//	// 3. Not logged into Facebook and can't tell if they are logged into
//	//    your app or not.
//	//
//	// These three cases are handled in the callback function.
//
//	FB.getLoginStatus(function(response) {
//	  statusChangeCallback(response);
//	});
//
//};

// Load the SDK asynchronously
//(function(d, s, id) {
//	var js, fjs = d.getElementsByTagName(s)[0];
//	if (d.getElementById(id)) return;
//	js = d.createElement(s); js.id = id;
//	js.src = "https://connect.facebook.net/en_US/sdk.js";
//	fjs.parentNode.insertBefore(js, fjs);
//}(document, 'script', 'facebook-jssdk'));

document.addEventListener("deviceready", function () {
	try {
		alert("dispositivo listo");
		
//		try {
			alert("URL: " + URLFrontera+'app/controller/configBbqController.php' + debug);
			$(".loader2").show();
			$.ajax({
				// tipo de llamado
				type: 'POST',
				// url del llamado a ajax
				url: URLFrontera+'app/controller/configBbqController.php' + debug,
				// obtener siempre los datos mas actualizados
				cache: false,
				// tipo de datos de regreso
				dataType: "json",
				// datos del llamado
				data: {
					action : 'getDatosIniciales',
				},
				// funcion en caso de exito
				success: function (data, textStatus, jqXHR) 
				{
	//				alert("Data: " + JSON.stringify(data));
					$(".loader2").hide();
					id_login = data[0];
					if (id_login === "") {
						$("#loginFace").attr("href", data[1]);	
					}

					$("#listaTodosProductos").html(data[2]);
					
					activarAcordeon();
					
//					initAutocomplete('map-canvas-acordeon');

		//			$("#formRegistro").hide();
					$('#datetimepicker').datetimepicker({
						format: 'yyyy-mm-dd',
						autoclose: true,
						minView: 2,
						maxView: 2,
						language: 'es',
						fontAwesome: 'fa'
					});
					var hoy = '';
					$('#datetimepicker').datetimepicker('setStartDate', hoy);

					moreFriends();
				},
				// funcion en caso de error
				error: function (xhr, status, error) {
					$(".loader2").hide();
					alert("No fue posible obtener los datos iniciale: " + error + " ~ " + JSON.stringify(xhr));
				}
			});
	//		google.maps.event.addDomListener(window, 'load', initAutocomplete);
			initAutocomplete('map-canvas');
			alert(window.location.href);
	//		}
	//		catch(err) {
	//			alert("Error try dom listo: " + err.toString());
	//		}
//		alert("Intenta login ready");
//		fblogin();
//		if (facebookConnectPlugin !== 'undefined') {
//			facebookConnectPlugin.login( 
//				["public_profile","email"],
//				function (response) {
//					if(response.authResponse.userID!=''){
//						facebookConnectPlugin.api(response.authResponse.userID+"/?fields=id,email,name", 
//							["public_profile"],
//							function (response) {
//								alert('SUCCESS:');
//								alert(JSON.stringify(response));
//								alert('name : '+response.name+',email:'+response.email+',id:'+response.id);
//							},
//							function (response) {
//								alert('ERROR desp de invoke:');
//								alert(JSON.stringify(response));
//							}
//						);
//					}    
//				}, 
//				function (response) {   
//					alert('ERROR al invoke:');
//					alert(JSON.stringify(response));
//				}
//			);
//		}
//		else {
//			alert("FB indefinido");
//		}
	}
	catch(err) {
		alert("Error: READY: " + err);
	}
});

//document.addEventListener("DOMContentLoaded", function() {
//	try {
//		alert("URL: " + URLFrontera+'app/controller/configBbqController.php' + debug);
//		$(".loader2").show();
//		$.ajax({
//			// tipo de llamado
//			type: 'POST',
//			// url del llamado a ajax
//			url: URLFrontera+'app/controller/configBbqController.php' + debug,
//			// obtener siempre los datos mas actualizados
//			cache: false,
//			// tipo de datos de regreso
//			dataType: "json",
//			// datos del llamado
//			data: {
//				action : 'getDatosIniciales',
//			},
//			// funcion en caso de exito
//			success: function (data, textStatus, jqXHR) 
//			{
////				alert("Data: " + JSON.stringify(data));
//				$(".loader2").hide();
//				id_login = data[0];
//				if (id_login === "") {
//					$("#loginFace").attr("href", data[1]);	
//				}
//
//				$("#listaTodosProductos").html(data[2]);
//
//				activarAcordeon();
//
//	//			$("#formRegistro").hide();
//				$('#datetimepicker').datetimepicker({
//					format: 'yyyy-mm-dd',
//					autoclose: true,
//					minView: 2,
//					maxView: 2,
//					language: 'es',
//					fontAwesome: 'fa'
//				});
//				var hoy = '';
//				$('#datetimepicker').datetimepicker('setStartDate', hoy);
//
//				moreFriends();
//			},
//			// funcion en caso de error
//			error: function (xhr, status, error) {
//				$(".loader2").hide();
//				alert("No fue posible obtener los datos iniciale: " + error + " ~ " + JSON.stringify(xhr));
//			}
//		});
////		google.maps.event.addDomListener(window, 'load', initAutocomplete);
//		alert(window.location.href);
//	}
//	catch(err) {
//		alert("Error try dom listo: " + err.toString());
//	}
//}, false);

//function testAPI() {
//	console.log('Welcome!  Fetching your information.... ');
//	FB.api('/me?fields=id,email,name', function(response) {
//	  console.log('Successful login for: ' + response.name);
//	  alert(JSON.stringify(response));
//	});
//}

//$( document ).ready(function() {
//	$(".loader2").show();
//	$.ajax({
//		// tipo de llamado
//		type: 'POST',
//		// url del llamado a ajax
//		url: URLFrontera+'app/controller/configBbqController.php' + debug,
//		// obtener siempre los datos mas actualizados
//		cache: false,
//		// tipo de datos de regreso
//		dataType: "json",
//		// datos del llamado
//		data: {
//			action : 'getDatosIniciales',
//		},
//		// funcion en caso de exito
//		success: function (data, textStatus, jqXHR) 
//		{
//			$(".loader2").hide();
//			id_login = data[0];
//			if (id_login === "") {
//				$("#loginFace").attr("href", data[1]);	
//			}
//			
//			$("#listaTodosProductos").html(data[2]);
//			
//			activarAcordeon();
//
////			$("#formRegistro").hide();
//			$('#datetimepicker').datetimepicker({
//				format: 'yyyy-mm-dd',
//				autoclose: true,
//				minView: 2,
//				maxView: 2,
//				language: 'es',
//				fontAwesome: 'fa'
//			});
//			var hoy = '';
//			$('#datetimepicker').datetimepicker('setStartDate', hoy);
//
//			moreFriends();
//		},
//		// funcion en caso de error
//		error: function (xhr, status, error) {
//			$(".loader2").hide();
//			console.log("No fue posible conseguir la liga de Facebook");
//		}
//	});
//	google.maps.event.addDomListener(window, 'load', initAutocomplete);
//	alert(window.location.href);
//});

//	selectCategoria(1);
	
var marker = null;
var image = 'images/iconoMarker.png'; 

function createMarker(mapa,latlng, name, html) {
	var contentString = html;
	var marker = new google.maps.Marker({
		position: latlng,
		map: mapa,
		draggable:true,
		icon : image,
		zIndex: Math.round(latlng.lat() * -100000) << 5
	});

	//			google.maps.event.addListener(marker, 'drag', function () {
		//infowindow.setContent(contentString);
		//infowindow.open(map, marker);
	//			});

	//			google.maps.event.trigger(marker, 'click');

	google.maps.event.addListener(marker, 'dragend', function() {
		$("#latitude").val(marker.getPosition().lat());
		$("#longitude").val(marker.getPosition().lng());
		mapa.panTo(marker.getPosition());
	});

	return marker;
}

function initAutocomplete(id_map) {
	try {
		alert("Inicializa mapa" + JSON.stringify(acordeon));
		var options = {
			'backgroundColor': 'black',
			'mapType': plugin.google.maps.MapTypeId.ROADMAP,
	//			'controls': {
	//				'compass': false,
	//				'myLocationButton': true,
	//				'indoorPicker': false,
	//				'zoom': true // Only for Android
	//			},
			'gestures': {
				'scroll': true,
				'tilt': true,
				'rotate': true,
				'zoom': true
			},
			'camera': {
				'latLng': {lat: 20.314223981308153, lng: -99.87218074500561},
	//				'tilt': 30,
				'zoom': 4,
	//				'bearing': 50
			},
			'preferences': {
				'zoom': {
					'minZoom': 4,
					'maxZoom': 18
				},
	//				'building': false
			}
		};
		var map = plugin.google.maps.Map.getMap(document.getElementById(id_map), options);
		map.on(plugin.google.maps.event.MAP_READY, onMapReady);
		if (id_map == 'map-canvas') {
			map.setVisible(false);
		}
//		map.on(plugin.google.maps.event.MAP_LOADED, onMapLoaded);
	}
	catch(err) {
		alert("Error iniciar mapa: " + err);
	}
	
//	var map = plugin.google.maps.Map(document.getElementById('map-canvas'), {
//	  center: {lat: 20.314223981308153, lng: -99.87218074500561},
//	  zoom: 4,
//	  streetViewControl: false,	
//	  mapTypeId: 'roadmap',
//	  mapTypeControl: false,
//	  key: 'AIzaSyC2wwq1DcOf6QND3WsX-bif4T07NegF0tU'
//	});

	/*
	// Create the search box and link it to the UI element.
	var input = document.getElementById('bbq_address');
	var searchBox = new google.maps.places.SearchBox(input);
	//		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	// Bias the SearchBox results towards current map's viewport.
	map.addListener('bounds_changed', function() {
	  searchBox.setBounds(map.getBounds());
	});

	google.maps.event.addListener(map, 'click', function (event) {
		// asignamos a nuextras variables
		$("#latitude").val(event.latLng.lat());
		$("#longitude").val(event.latLng.lng());
		//        console.log("Lat:" + newLat + ", Long: " + newLong);
		if (marker) {
		  marker.setMap(null);
		  marker = null;
		}
		marker = createMarker(map,event.latLng, "name", "<b>Location</b><br>" + event.latLng);
	});

	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener('places_changed', function() {
	  var places = searchBox.getPlaces();

	  if (places.length == 0) {
		return;
	  }

	  // For each place, get the icon, name and location.
	  var bounds = new google.maps.LatLngBounds();
	  places.forEach(function(place) {
		if (!place.geometry) {
		  alert("Returned place contains no geometry");
		  return;
		}
		var icon = image;

		if (marker) {
			marker.setMap(null);
			marker = null;
		}

		marker = createMarker(map,place.geometry.location, "name", "<b>Location</b><br>" + place.geometry.location);

		$("#latitude").val(place.geometry.location.lat());
		$("#longitude").val(place.geometry.location.lng());

		if (place.geometry.viewport) {
		  // Only geocodes have viewport.
		  bounds.union(place.geometry.viewport);
		} else {
		  bounds.extend(place.geometry.location);
		}
	  });
	  map.fitBounds(bounds);
	});
	*/
}

//$(window).on("load", function(){
//	activarAcordeon();
//	initAutocomplete();
//});

function onMapReady(map) {
	alert("MAPA LISTO!" + JSON.stringify(map));
//	$("#cuerpoArma").css("background-color","black");
}

//function onMapLoaded(map) {
//	alert("MAPA CARGADO!");
//}

function activarAcordeon() {
	$("#formRegistro").show();
	$("#registroUsuario").show();
	$("#facebookLogin").show();
	if (id_login !== '') {
		sliderAbiertos = [1];
		paso_actual = 1;
	}
	acordeon = new BadgerAccordion(".js-badger-accordion", {
		openHeadersOnLoad: sliderAbiertos
	});

	$(".badger-accordion__trigger").css("background-color","#353535");

	id_activo = acordeon.headers[0].id;
	if (id_login !== '') {
		$("#registroUsuario").hide();
		$("#facebookLogin").show();
		id_activo = acordeon.headers[1].id;
		$('html,body').animate({ scrollTop: $("#vinetaRegistro").offset().top }, 500);
	}
	else {
		$("#facebookLogin").hide();
	}
	$("#" + id_activo).css("background-color","#B32524");
	$("#formRegistro").hide();
//		google.maps.event.addDomListener(window, 'load', initAutocomplete);		
}

function fblogin() {
	try {
		alert("Plataforma ID de cordova");
		alert(window.cordova.platformId);
		alert("Intenta login");
		facebookConnectPlugin.login( 
			["public_profile","email"],
			function (response) {
				if(response.authResponse.userID!=''){
					facebookConnectPlugin.api(response.authResponse.userID+"/?fields=id,email,name", 
						["public_profile"],
						function (response) {
							alert('SUCCESS:'+JSON.stringify(response));
							alert('name : '+response.name+',email:'+response.email+',id:'+response.id);
							$.ajax({
								type: 'POST',
								url: URLFrontera+'app/controller/configBbqController.php' + debug,
								cache: false,
								dataType: "json",
								data: {
									action : 'registrarUsuario',
									gender: 'H',
									email: response.email,
									name: response.name
					//				,passwd: pass1
								},
								success: function (data, textStatus, jqXHR) {
									if (data) {
										id_login = response.id;
										$("#facebookName").html(response.name);
										activarAcordeon();
									}
									else {
										$.notify("Ocurrió un error al guardar información del cliente", "error");
									}
								},
								error: function (xhr, status, error) {
									alert("Error al registrar usuario FACE: " + error.toString());
								}

							});
						},
						function (response) {   
							alert('ERROR al invoke face dentro:');
							alert(JSON.stringify(response));
						}
					);
				}    
			}, 
			function (response) {   
				alert('ERROR al invoke face fuera:'+JSON.stringify(response));
			}
		);
//		FB.login(function(response) {
//			alert("Al loguear: " + JSON.stringify(response));
//		  // handle the response
//			if (response.status === 'connected') {
//				testAPI();
//				// Logged into your app and Facebook.
//			  } else {
//				// The person is not logged into this app or we are unable to tell. 
//				  alert("No pudo identificarse en facbook");
//			  }
//		}, {scope: 'public_profile,email'});
	}
	catch(err) {
		alert("ERROR face login: " + err);
	}
}

function fblogout() {
	try {
		facebookConnectPlugin.logout(function() {
			id_login = '';
			sliderAbiertos = [0];
			paso_actual = 0;
			activarAcordeon();
		}, function() {
			alert("No fue posible cerrar la sesión de Facebook");
		});
	}
	catch(err) {
		alert("ERROR logout face: " + err);
	}
}

// JavaScript Document
function modifInvitados(id, inc) {
	var valor_actual = Number($("#num" + id).html());
	var valor_nuevo = valor_actual + inc;
	if (valor_nuevo < 0 || valor_nuevo > 30) {
		return;
	}
	var porcentaje = (valor_nuevo/10) * 100;
	var imagen = "images/Invitados0.png";
	if (porcentaje >= 20 && porcentaje < 40) {
		imagen = "images/Invitados20.png";
	}
	else if (porcentaje >= 40 && porcentaje < 60) {
		imagen = "images/Invitados40.png";
	}
	else if (porcentaje >= 60 && porcentaje < 80) {
		imagen = "images/Invitados60.png";
	}
	else if (porcentaje >= 80 && porcentaje < 100) {
		imagen = "images/Invitados80.png";
	}
	else if (porcentaje >= 100) {
		imagen = "images/Invitados100.png";
	}
	$("#img" + id).attr("src", imagen);
	$("#img" + id + "Small").attr("src", imagen);
	$("#num" + id).html(valor_nuevo);
	if (valor_nuevo >= 20) {
		$("#num" + id).css("padding-left", "0px");
	}
	else {
		$("#num" + id).css("padding-left", "5px");
	}
}

function registrarUsuario() {
	var name = $("#user_name").val();
	var email = $("#user_email").val();
//	var pass1 = $("#user_pass_1").val().trim();
//	var pass2 = $("#user_pass_2").val().trim();
//	if (pass1 !== pass2 || pass1 === "" || pass2 === "") {
//		alert("Favor de verificar las contraseñas ingresadas");
//		return;
//	}
	var gender = $("[name^=user_gender]:checked").val();
	if (gender === undefined) {
//		alert("No se ha seleccionado un género");
		$("#continuar1").notify("No se ha seleccionado un género", {
			position: "top right"
		});
		return;
	}
	$(".loader2").show();
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'registrarUsuario',
				gender: gender,
				email: email,
				name: name
//				,passwd: pass1
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				if (data) {
					acordeon.close("0");
					acordeon.open("1");
					var id_activo = acordeon.headers[1].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 1;
				}
				else {
					$.notify("Ocurrió un error al guardar información del cliente", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}

function startBbq() {
	var bbq_name = $("#bbq_name").val().trim();
	var bbq_date = $("#datetimepicker").val();
	var bbq_time = $("#bbq_time").val();
	if (bbq_name === "" || bbq_date === "" || bbq_time === "") {
//		alert("Favor de completar la información de nombre y fecha para la parrillada");
		$("#continuar2").notify("Completar la información solicitada de la parrilada", {
			position: "top right"
		});
		return;
	}
	$(".loader2").show();
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'startBbq',
				bbq_name: bbq_name,
				bbq_date: bbq_date,
				bbq_time: bbq_time
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				if (data) {
					acordeon.close("1");
					acordeon.open("2");
					var id_activo = acordeon.headers[2].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 2;
				}
				else {
//					alert("Ocurrió un error al almacenar el nombre de la parrillada");
					$.notify("Ocurrió un error al almacenar los datos de la parrillada", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}

function selectCategoria(id_categoria) {
	$("[id^=productos_]").hide();
	var categorias = $("[id^=productos_]");
	var src, src2, nuevoSrc, nuevoSrc2;
	for(var j=0; j<categorias.length; j++) {
		var categoria = categorias[j];
		var id_cat = categoria.id.split("_")[1];
		src = $("#categoria_" + id_cat + " > img").attr("src");
		src2 = $("#categoria_" + id_cat + "_small").attr("src");
		nuevoSrc = src.replace("2.png", "1.png");
		nuevoSrc2 = src2.replace("2.png", "1.png");
		$("#categoria_" + id_cat + " > img").attr("src", nuevoSrc);
		$("#categoria_" + id_cat + "_small").attr("src", nuevoSrc2);
	}
	$("#productos_" + id_categoria).show();
	src = $("#categoria_" + id_categoria + " > img").attr("src");
	src2 = $("#categoria_" + id_categoria + "_small").attr("src");
	nuevoSrc = src.replace("1.png", "2.png");
	nuevoSrc2 = src2.replace("1.png", "2.png");
	$("#categoria_" + id_categoria + " > img").attr("src", nuevoSrc);
	$("#categoria_" + id_categoria + "_small").attr("src", nuevoSrc2);
}

function calculateProd() {
	var numInvitados = $("#numInvitados").html();
	var numInvitadas = $("#numInvitadas").html();
	if (numInvitadas == "0" && numInvitados == "0") {
		$("#continuar3").notify("Se necesita ingresar al menos 1 parrillero o parrillera", {
			position: "top right"
		});
		return;
	}
	$(".loader2").show();
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'calculateProd',
				numInvitados: numInvitados,
				numInvitadas: numInvitadas
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				$("[id^=prod_quantity_]").html("0");
				if (data) {
					$.each(data, function(id_cat, cant) {
						$("#prod_quantity_" + id_cat).html(cant);
					});
					armarResumenProd(true);
//					selectCategoria(1);
					acordeon.close("2");
					acordeon.open("3");
					var id_activo = acordeon.headers[3].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 3;
				}
				else {
//					alert("Ocurrió un error al mostrar los productos con cantidades");
					$.notify("Ocurrió un error al mostrar los productos con cantidades", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}

function masProductos(id_prod) {
	var valor_act = $("#prod_quantity_" + id_prod).html();
	valor_act = Number(valor_act);
	valor_act += 1;
	if (valor_act >= 100) {
		return;
	}
	$("#prod_quantity_" + id_prod).html(valor_act);
	armarResumenProd(false);
}

function menosProductos(id_prod) {
	var valor_act = $("#prod_quantity_" + id_prod).html();
	valor_act = Number(valor_act);
	valor_act -= 1;
	if (valor_act < 0) {
		return;
	}
	$("#prod_quantity_" + id_prod).html(valor_act);
	armarResumenProd(false);
}

function getRel_prod_cant() {
	var productos = $("[id^=prod_quantity_]");
	var rel_prod_cant = [];
	for(var j=0; j<productos.length; j++) {
		var id_prod = productos[j].id.split("_")[2];
		var cant = productos[j].innerHTML;
		if (cant === "0") continue;
		rel_prod_cant.push({
			id: id_prod,
			cant: cant
		});
	}
	return rel_prod_cant;
}

function armarResumenProd(bandLoader) {
	var rel_prod_cant = getRel_prod_cant();
	if (bandLoader) {
		$(".loader2").show();
	}
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'armarResumenProd',
				rel_prod_cant: rel_prod_cant
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				if (data.length > 0) {
					var lista = data[0];
					var total_kg = data[1];
					var html_lista = "";
					$.each(lista, function(id_prod, texto){
						html_lista += '<div class="row" style="padding-left: 12px;">';
						html_lista += '	<div class="col-md-2 widthNumProdSmall" style="padding-left: 0px; padding-right: 0px; text-align: center;"><label style="background-image: url(\'images/ResumenKg.png\'); border-radius: 15px; width:100%; background-size: cover;">';
						html_lista += $("#prod_quantity_" + id_prod).html();
						html_lista += '	</label></div>';
						html_lista += '	<div class="col-md-10" style="width: 80%">';
						html_lista += texto.nombre + "<br>" + texto.gr;
						html_lista += '	</div>';
						html_lista += '</div>';
					});
					$("#listaProd").html(html_lista);
					$("#kgProd").html(total_kg + " kg");
					if (bandLoader) {
						$("#kgProdSugerido").html(total_kg + " kg");
					}
				}
				else {
//					alert("No fue posible armar el resumen de productos para la parrillada");
					$.notify("No fue posible armar el resumen de productos para la parrillada", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}

function saveBbq() {
	var rel_prod_cant = getRel_prod_cant();
	var numInvitados = $("#numInvitados").html();
	var numInvitadas = $("#numInvitadas").html();
	$(".loader2").show();
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'saveBbq',
				rel_prod_cant: rel_prod_cant,
				numInvitados: numInvitados,
				numInvitadas: numInvitadas
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				if (data) {
					acordeon.close("3");
					acordeon.open("4");
					var id_activo = acordeon.headers[4].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					$('html,body').animate({ scrollTop: $("#vinetaProd").offset().top }, 500);
					paso_actual = 4;
				}
				else {
//					alert("No fue posible guardar la parrillada");
					$.notify("No fue posible guardar la parrillada", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}

var cantAmigos = -1;

function lessFriends(amigo) {
	$("#amigo_" + amigo).remove();
	var id_primer_amigo, cantElem;
	if (versionSmall === true) {
		//Se resta uno por el genérico con terminacion ##num##
		cantElem = $("[id*=_small][id^=elim_amigo_]").length - 1;
		id_primer_amigo = $("[id*=_small][id^=elim_amigo_]")[0].id;
	}
	else {
		//Se resta uno por el genérico con terminacion ##num##
		cantElem = $("[id^=elim_amigo_]:not([id$=_small])").length - 1;
		id_primer_amigo = $("[id^=elim_amigo_]:not([id$=_small])")[0].id;
	}
	
	if (cantElem <= 1) {
		$("#" + id_primer_amigo).css("display","none");
	}
}

var versionSmall = false;
function moreFriends() {
	cantAmigos++;
	$("#cant_amigos").val(cantAmigos);
	var unAmigo = $("#unAmigo").html();
	unAmigo = unAmigo.replace(/##num##/g, cantAmigos);
	$("#listaAmigos").append(unAmigo);
	
	if (cantAmigos === 0) {
		var discrim = $("#elim_amigo_0_small").css("display");
		if (discrim == "block") {
			versionSmall = true;
		}	
	}
	
	var id_primer_amigo;
	if (versionSmall === true) {
		$("[id*=_small][id^=elim_amigo_]").css("display","block");	
		id_primer_amigo = $("[id*=_small][id^=elim_amigo_]")[0].id;
	}
	else {
//		var elements = $("[id^=elim_amigo_]:not([id$=_small])");
		$("[id^=elim_amigo_]:not([id$=_small])").css("display","block");
		id_primer_amigo = $("[id^=elim_amigo_]:not([id$=_small])")[0].id;
	}
	
	$("#" + id_primer_amigo).css("display","none");
	if (cantAmigos > 0) {
		$("#" + id_primer_amigo).css("display","block");
		$("#bbq_guest_name_" + cantAmigos).focus();
	}
}

function saveFriends() {
	var bbq_friends = [];
	for (var i=0; i<=cantAmigos; i++) {
		var nombre_amigo = $("#bbq_guest_name_" + i).val();
		var email_amigo = $("#bbq_guest_email_" + i).val();
		if (nombre_amigo === undefined || nombre_amigo === "" || email_amigo === undefined || email_amigo === "") {
			continue;
		}
		nombre_amigo = nombre_amigo.trim();
		email_amigo = email_amigo.trim();
		bbq_friends.push({
			nombre: nombre_amigo,
			email: email_amigo
		});
	}
	if (bbq_friends.length <= 0) {
//		alert("Se requiere ingresar la información de al menos un amigo");
		$("#continuar5").notify("Se requiere ingresar la información de al menos un amigo", {
			position: "top center"
		});
		return;
	}
	$(".loader2").show();
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'saveFriends',
				bbq_friends: bbq_friends
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				if (data === true) {
					acordeon.close("4");
					acordeon.open("5");
					var id_activo = acordeon.headers[5].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 5;
				}
				else {
//					alert("No fue posible invitar amigos: " + data);
					$.notify("No fue posible invitar amigos: " + data, "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}
		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}

function finishBbq() {
	var address = $("#bbq_address").val().trim();
	var message = $("#bbq_message").val();
	var latitude = $("#latitude").val();
	var longitud = $("#longitude").val();
	if (address === "" && latitude === "0" && longitud === "0") {
//		alert("Favor de indicar la dirección o bien la ubicación de la parrillada");
		var boton6 = "continuar6";
		var display = $("#" + boton6).css("display");
		if (display == "none") {
			boton6 = "continuar6_2";
		}
		$("#" + boton6).notify("Favor de indicar la dirección o la ubicación de la parrillada", {
			position: "top right"
		});
		return;
	}
	$(".loader2").show();
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'finishBbq',
				address: address,
				message: message,
				latitude: latitude,
				longitud: longitud
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader2").hide();
				if (data === true) {
					acordeon.close("5");
					var id_activo = acordeon.headers[2].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					paso_actual = -9999;
//					alert("¡Felicidades! Tu parrillada está por hacerse realidad");
					$("#msjFelicidades").show();
					$('html,body').animate({ scrollTop: $("#vinetaUbic").offset().top }, 500);
				}
				else {
//					alert("No fue posible concluir con la parrillada: " + data);
					$.notify("No fue posible concluir con la parrillada: " + data, "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader2").hide();
				alert(error.toString());
			}
		});
	}
	catch(ex) {
		$(".loader2").hide();
		alert(ex.message);
	}
}