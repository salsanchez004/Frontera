var id_login = '';
var sliderAbiertos = [0];
var paso_actual = 0;
var acordeon;
var id_activo;
var URLFrontera = "https://vmasideas.online/frontera/";
var debug = "";
var desde_app = 1;
var map;
var ubicActual = {
	lat: 0,
	lng: 0
}
var markerActual = null;
var session_id = null;

document.addEventListener("deviceready", function () {
	try {
//		alert("dispositivo listo");
//		alert("URL: " + URLFrontera+'app/controller/configBbqController.php' + debug);
		$(".loader").fadeIn("slow");
//		$(".loader2").show();
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
//				$(".loader2").hide();
				$(".loader").fadeOut("slow");
//				id_login = data[0];
//				if (id_login === "") {
					$("#loginFace").attr("href", data[1]);	
//				}
				
				session_id = data[0];

				$("#listaTodosProductos").html(data[2]);
				
				activarAcordeon(true);
				
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
				
				$("#bbq_time").datetimepicker({
					startView: 0,
					minView: 0,
					maxView: 0,
					autoclose: true,
					format: 'hh:ii',
					language: 'es',
					fontAwesome: 'fa'
				});
				
				var hoy = data[3];
				var tomorrow = data[4];
				$('#datetimepicker').datetimepicker('setStartDate', hoy);
				$('#bbq_time').datetimepicker('setStartDate', hoy);
				$('#bbq_time').datetimepicker('setEndDate', tomorrow);

				moreFriends();
				
				$('#contenedorGral').slick({
					adaptiveHeight : true,
					arrows: false,
					draggable: false,
					swipe: false,
					touchMove: false
				});
			},
			// funcion en caso de error
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("No fue posible obtener los datos iniciales: " + error + " ~ " + JSON.stringify(xhr));
			}
		});
		
		getLatLngGPS(false);
		
//		google.maps.event.addDomListener(window, 'load', initAutocomplete);
//		alert(window.location.href);
	}
	catch(err) {
		alert("Error: READY: " + err);
	}
});

function getLatLngGPS(conMapa) {
	if (navigator != null && navigator.geolocation != null) {
		navigator.geolocation.getCurrentPosition(function(position) {
//				alert('Latitude: '          + position.coords.latitude          + '\n' +
//		              'Longitude: '         + position.coords.longitude         + '\n' +
//		              'Altitude: '          + position.coords.altitude          + '\n' +
//		              'Accuracy: '          + position.coords.accuracy          + '\n' +
//		              'Altitude Accuracy: ' + position.coords.altitudeAccuracy  + '\n' +
//		              'Heading: '           + position.coords.heading           + '\n' +
//		              'Speed: '             + position.coords.speed             + '\n' +
//		              'Timestamp: '         + position.timestamp                + '\n');
			ubicActual.lat = position.coords.latitude;
			ubicActual.lng = position.coords.longitude;
			if (conMapa) {
				$('html,body').animate({ scrollTop: $("#mapaArmaParrillada").offset().top }, 500);
				getUbicacionActual();
			}
		}, function (error) {
			alert('ERROR GPS code: '    + error.code    + '\n' + 'message: ' + error.message + '\n');
		});
	}
	else {
		alert("No hay GPS disponible");
	}
}

function armarParrillada(){
	id_login = '';
	sliderAbiertos = [0];
	paso_actual = 0;
	markerActual = null;
//	session_id = null;
	
	acordeon.closeAll();
//	acordeon.open("0");
//	id_activo = acordeon.headers[0].id;
//	$("#" + id_activo).css("background-color","#B32524");
	
	activarAcordeon(false);
	acordeon.open("0");
	$('#botonesRegistro').show(); 
	
	goToSlide(1);
	$('#mapaArmaParrillada').hide();
	$('#msjFelicidades').hide();
}

function goToSlide(slide) {
	try {
//		alert("Slide: " + slide);
		$("#contenedorGral").slick('slickGoTo', slide, false);
	}
	catch(err) {
		alert("goToSlide catch: " + err);
	}
}

var marker = null;
var image = 'images/iconoMarker.png'; 

function initAutocomplete(id_map) {
	try {
//		alert("Inicializa mapa" + JSON.stringify(acordeon));
		var options = {
			'backgroundColor': 'black',
			'mapType': plugin.google.maps.MapTypeId.ROADMAP,
			'controls': {
	//				'compass': false,
	//				'myLocationButton': true,
	//				'indoorPicker': false,
				'zoom': true // Only for Android
			},
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
					'maxZoom': 22
				},
	//				'building': false
			}
		};
		map = plugin.google.maps.Map.getMap(document.getElementById(id_map), options);
		map.on(plugin.google.maps.event.MAP_READY, onMapReady);
//		map.on(plugin.google.maps.event.MAP_LOADED, onMapLoaded);
	}
	catch(err) {
		alert("Error iniciar mapa: " + err);
	}
}

function onMapReady(map) {
	cambiarColorMapa('black');
	$('html,body').animate({ scrollTop: $("#vinetaUbic").offset().top }, 500);
//	alert("MAPA LISTO!" + JSON.stringify(map));
}

function cambiarColorMapa(color) {
	plugin.google.maps.environment.setBackgroundColor(color);
}

function busquedaDireccion(address) {
	$(".loader").fadeIn("slow");
	var request = {
		'address': address
	};
	plugin.google.maps.Geocoder.geocode(request, function(results) {
		if (results.length) {
			var result = results[0];
			var position = result.position;
			map.addMarker({
				'position': position,
//				'title':  JSON.stringify(result.position),
				'icon': {
			    	'url': image
				},
				'draggable': true
			}, function(marker) {
				if (markerActual !== null) {
					markerActual.remove();
				}
				markerActual = marker;
				map.animateCamera({
					'target': position,
					'zoom': 17
				}, function() {
					$("#latitude").val(position.lat);
					$("#longitude").val(position.lng);
//					marker.showInfoWindow();
				});
				markerActual.addEventListener(plugin.google.maps.event.MARKER_DRAG_END, function(positionDE) {
					$("#latitude").val(positionDE.lat);
					$("#longitude").val(positionDE.lng);
//					alert("Posicion drag end: " + JSON.stringify(position));
//					marker.setTitle(position.toUrlValue());
//					marker.showInfoWindow();
				});
				$(".loader").fadeOut("slow");
			});
		} else {
			alert("No se localizó la dirección ingresada");
		}
	});
}

function getUbicacionActual() {
	var position = ubicActual;
	map.addMarker({
		'position': position,
//		'title':  JSON.stringify(position),
		'icon': {
	    	'url': image
		},
		'draggable': true
	}, function(marker) {
		if (markerActual !== null) {
			markerActual.remove();
		}
		markerActual = marker;
		map.animateCamera({
			'target': position,
			'zoom': 17
		}, function() {
			$("#latitude").val(position.lat);
			$("#longitude").val(position.lng);
//					marker.showInfoWindow();
		});
		markerActual.addEventListener(plugin.google.maps.event.MARKER_DRAG_END, function(positionDE) {
			$("#latitude").val(positionDE.lat);
			$("#longitude").val(positionDE.lng);
//					alert("Posicion drag end: " + JSON.stringify(position));
//					marker.setTitle(position.toUrlValue());
//					marker.showInfoWindow();
		});
		var request = {
		  'position': position
		};
		plugin.google.maps.Geocoder.geocode(request, function(results) {
			$(".loader").fadeIn("slow");
			if (results.length) {
				var result = results[0];
				var position = result.position;
				var address = [
					result.subThoroughfare || "",
					result.thoroughfare || "",
					result.locality || "",
					result.adminArea || "",
					result.postalCode || "",
					result.country || ""].join(", ");
				$("#bbq_address").val(address);
				$("#bbq_message").focus();
			} else {
				alert("No se pudo localizar la dirección");
			}
			$(".loader").fadeOut("slow");
		});
	});
}

//function onMapLoaded(map) {
//	alert("MAPA CARGADO!");
//}

function activarAcordeon(instanciar) {
	$("#formRegistro").show();
	$("#registroUsuario").show();
	$("#facebookLogin").show();
	if (id_login !== '') {
		sliderAbiertos = [1];
		paso_actual = 1;
	}
	if (instanciar) {
		acordeon = new BadgerAccordion(".js-badger-accordion", {
			openHeadersOnLoad: sliderAbiertos
		});
	}

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
//		alert("Plataforma ID de cordova");
//		alert(window.cordova.platformId);
//		alert("Intenta login");
		facebookConnectPlugin.login( 
			["public_profile","email"],
			function (response) {
				if(response.authResponse.userID!=''){
					facebookConnectPlugin.api(response.authResponse.userID+"/?fields=id,email,name", 
						["public_profile"],
						function (response) {
//							alert('SUCCESS:'+JSON.stringify(response));
//							alert('name : '+response.name+',email:'+response.email+',id:'+response.id);
							$.ajax({
								type: 'POST',
								url: URLFrontera+'app/controller/configBbqController.php' + debug,
								cache: false,
								dataType: "json",
								data: {
									action : 'registrarUsuario',
									gender: 'F',
									email: response.email,
									name: response.name,
									session_id: session_id
					//				,passwd: pass1
								},
								success: function (data, textStatus, jqXHR) {
									if (data) {
										id_login = response.id;
										$("#facebookName").html(response.name);
										activarAcordeon(false);
										acordeon.calculateAllPanelsHeight();
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
			activarAcordeon(false);
			acordeon.calculateAllPanelsHeight();
		}, function() {
			alert("No fue posible cerrar la sesión de Facebook");
		});
	}
	catch(err) {
		alert("ERROR logout face: " + err);
	}
}

function abrirRegistro() {
	$("#user_name").val("");
	$("#user_email").val("");
	$("[name^=user_gender]").removeAttr("checked");
	$('#formRegistro').show(); 
	$('#botonesRegistro').hide(); 
	acordeon.close(0); 
	acordeon.open(0);
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
	$(".loader").fadeIn("slow");
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
				name: name,
				session_id: session_id
//				,passwd: pass1
			},
			success: function (data, textStatus, jqXHR) {
				if (data) {
					acordeon.close("0");
					acordeon.open("1");
					var id_activo = acordeon.headers[1].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 1;
					acordeon.calculateAllPanelsHeight();
					$('html,body').animate({ scrollTop: $("#vinetaRegistro").offset().top }, 500);
				}
				else {
					$.notify("Ocurrió un error al guardar información del cliente", "error");
					$(".loader").fadeOut("slow");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("Error al registrar usuario: " + error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
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
	$(".loader").fadeIn("slow");
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
				bbq_time: bbq_time,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
				if (data) {
					acordeon.close("1");
					acordeon.open("2");
					var id_activo = acordeon.headers[2].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 2;
					acordeon.calculateAllPanelsHeight();
				}
				else {
//					alert("Ocurrió un error al almacenar el nombre de la parrillada");
					$.notify("Ocurrió un error al almacenar los datos de la parrillada", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("startBbq ajax error: " + error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("startBbq catch error: " + ex.message);
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
	$(".loader").fadeIn("slow");
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'calculateProd',
				numInvitados: numInvitados,
				numInvitadas: numInvitadas,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
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
					acordeon.calculateAllPanelsHeight();
					$('html,body').animate({ scrollTop: $("#vinetaProd").offset().top }, 500);
				}
				else {
					$(".loader").fadeOut("slow");
//					alert("Ocurrió un error al mostrar los productos con cantidades");
					$.notify("Ocurrió un error al mostrar los productos con cantidades", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("calculateProd ajax error: " + error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("calculateProd catch error: " + ex.message);
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
		$(".loader").fadeIn("slow");
	}
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'armarResumenProd',
				rel_prod_cant: rel_prod_cant,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
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
					acordeon.calculateAllPanelsHeight();
//					if (bandLoader) {
//						masProductos($("#un_producto").val());
//						menosProductos($("#un_producto").val());
//					}
				}
				else {
					$(".loader").fadeOut("slow");
//					alert("No fue posible armar el resumen de productos para la parrillada");
					$.notify("No fue posible armar el resumen de productos para la parrillada", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("armarResumenProd ajax error: " + error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("armarResumenProd catch error: " + ex.message);
	}
}

function saveBbq() {
	var rel_prod_cant = getRel_prod_cant();
	var numInvitados = $("#numInvitados").html();
	var numInvitadas = $("#numInvitadas").html();
	$(".loader").fadeIn("slow");
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
				numInvitadas: numInvitadas,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
				if (data) {
					acordeon.close("3");
					acordeon.open("4");
					var id_activo = acordeon.headers[4].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					$('html,body').animate({ scrollTop: $("#vinetaProd").offset().top }, 500);
					paso_actual = 4;
					acordeon.calculateAllPanelsHeight();
				}
				else {
					$(".loader").fadeOut("slow");
//					alert("No fue posible guardar la parrillada");
					$.notify("No fue posible guardar la parrillada", "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("saveBbq ajax error: " + error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("saveBbq catch error: " + ex.message);
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

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
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
		if (!validateEmail(email_amigo)) {
			continue;
		}
		bbq_friends.push({
			nombre: nombre_amigo,
			email: email_amigo
		});
	}
	if (bbq_friends.length <= 0) {
//		alert("Se requiere ingresar la información de al menos un amigo");
		$("#continuar5").notify("Se requiere ingresar la información de al menos un amigo con correo electrónico válido", {
			position: "top center"
		});
		return;
	}
	$(".loader").fadeIn("slow");
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'saveFriends',
				bbq_friends: bbq_friends,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
				if (data === true) {
					acordeon.close("4");
					acordeon.open("5");
					var id_activo = acordeon.headers[5].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					$("#" + id_activo).css("background-color","#B32524");
					paso_actual = 5;
					$('#mapaArmaParrillada').show();
					initAutocomplete('map-canvas-acordeon');
					acordeon.calculateAllPanelsHeight();
					$("#modal-confirm-gps").modal("show");
				}
				else {
					$(".loader").fadeOut("slow");
//					alert("No fue posible invitar amigos: " + data);
					$.notify("No fue posible invitar amigos: " + data, "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("saveFriends ajax error: " + error.toString());
			}
		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("saveFriends catch error: " + ex.message);
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
	$(".loader").fadeIn("slow");
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
				longitud: longitud,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
				$(".loader").fadeOut("slow");
				if (data === true) {
					acordeon.close("5");
					var id_activo = acordeon.headers[2].id;
					$(".badger-accordion__trigger").css("background-color","#353535");
					paso_actual = -9999;
					$('#mapaArmaParrillada').hide();
//					alert("¡Felicidades! Tu parrillada está por hacerse realidad");
					$("#msjFelicidades").show();
					$("#contenedorGral").find(".slick-slide").height("auto");
					$("#contenedorGral").slick("setOption", '', '', true);
					$('html,body').animate({ scrollTop: $("#vinetaUbic").offset().top }, 500);
				}
				else {
//					alert("No fue posible concluir con la parrillada: " + data);
					$.notify("No fue posible concluir con la parrillada: " + data, "error");
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("finishBbq ajax error: " + error.toString());
			}
		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("finishBbq catch error: " + ex.message);
	}
}