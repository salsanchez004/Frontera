//var id_login = '';
var sliderAbiertos = [0];
var paso_actual = 0;
var acordeon;
var id_activo;
var URLFrontera = "https://vmasideas.online/frontera/";
//var URLFrontera = "https://www.fronteracarneparrillera.com/";
var debug = "";
var desde_app = 1;
var map;
var ubicActual = {
	lat: 0,
	lng: 0
}
var markerActual = null;
var session_id = null;
var gps_activo = 0;
var intentos_gps = 0;
var watchID = null;
var conMapa = false;
var plataforma = null;
var slideActual = 0;
var fotosSubir = [];
var indexFoto = -1;
var terminaCargaImgParrillero = false;
var alturaDisp = -1;
var anchoDisp = -1;
var tipAbierto = false;
var recetaAbierta = false;
var videoRecetaAbierta = false;

function onBackKey() {
	if (slideActual == 0) {
		navigator.app.exitApp();
	}
	else if (slideActual == 2) {
		if (tipAbierto || recetaAbierta || videoRecetaAbierta) {
			var instance = $.fancybox.getInstance();
			instance.close();
		}
		else {
			goToSlide(0);
		}
	}
	else if (slideActual == 5 || slideActual == 6 || slideActual == 7) {
		if ($("#modal-term-cond").css("display") == "block") {
			$("#modal-term-cond").modal("hide");
		}
		else {
			goToSlide(4);
		}
	}
	else {
		goToSlide(0);
	}
}

function onLoad() {
	alturaDisp = $(this).height();
	anchoDisp = $(this).width();
	var restar = alturaDisp * 0.15;
	$("#map-canvas-stores").css("width", anchoDisp);
	$("#map-canvas-stores").css("height", Math.round(alturaDisp - restar));
	document.addEventListener("deviceready", onDeviceReady, false);
}

function onDeviceReady() {
	try {
		window.screen.orientation.lock("portrait");
		
		plataforma = window.cordova.platformId;
		console.log("Plataforma: " + plataforma);
		
		if (navigator.camera == undefined) {
			console.log("Sin camara");
		}

		document.addEventListener("backbutton", onBackKey, false);
		
		if (localStorage.getItem("session_id") !== null) {
			session_id = localStorage.getItem("session_id");
		}
		console.log("Session id: "+session_id);
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
				session_id: session_id
			},
			// funcion en caso de exito
			success: function (data, textStatus, jqXHR) 
			{
//				alert("Data: " + JSON.stringify(data));
//				$(".loader2").hide();
//				id_login = data[0];
//				if (id_login === "") {
					$("#loginFace").attr("href", data[1]);	
//				}
				
				session_id = data[0];
				var listaProd = data[2];
				var altura = "8%";
				if (plataforma == "ios") {
					altura = "65%";
				}
				listaProd = listaProd.replace(/##alturaProd##/g, altura);
				$("#listaTodosProductos").html(listaProd);
				
				if ($(document).width() >= 768 && $(document).width() <= 1024) {
					$("[id^=menu][id$=F]").removeClass();
					$("[id^=menu][id$=F]").addClass("col-md-1");
				}
				
				if (plataforma == "ios") {
					$("[name=user_gender]").removeClass();
				}
				
				$("#recetasHTML").html(data[5]);
				$("#botonesRecetas").html(data[6]);
				$("#tip_slider").html(data[7]);
				$("#botonesCategorias").html(data[8]);
				$("#prodFronteraList").html(data[9]);

				if (localStorage.getItem("session_id") === null) {
					localStorage.setItem("session_id", session_id);
				}
				if (localStorage.getItem("user_name") === null) {
					localStorage.setItem("user_name", '');	
				}
				if (localStorage.getItem("user_email") === null) {
					localStorage.setItem("user_email", '');	
				}
				if (localStorage.getItem("user_gender") === null) {
					localStorage.setItem("user_gender", '');	
				}
				
				if (session_id !== localStorage.getItem("session_id")) {
					verificarSesion(session_id);
				}
				
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
					minuteStep: 30,
					language: 'es',
					fontAwesome: 'fa'
				});
				
				var hoy = data[3];
				var tomorrow = data[4];
				$('#datetimepicker').datetimepicker('setStartDate', hoy);
				$('#bbq_time').datetimepicker('setStartDate', hoy);
				$('#bbq_time').datetimepicker('setEndDate', tomorrow);

				moreFriends();
				
				var prodHtml = $("[id^=productF]");
				for(var p=0; p<prodHtml.length; p++) {
					var id_prod = prodHtml[p].id;
					$("#" + id_prod).owlCarousel({
						dots: false,
						margin:0,
						nav: true,
						navContainerClass: "owl-carouselPromos",
						navClass: ["owl-prevPromos","owl-nextPromos"],
						navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
						responsive:{
						0:{
							items:1
						},
						480: {
							items:1
						},
						992: {
							items:1
							}
						},
						onInitialized: refreshArrows,
						onTranslated: refreshArrows
					});
				}
				
				$("#tip_slider").owlCarousel({
					dots: false,
					margin:50,
					nav: true,
					loop:true,
					center:true,
					navContainerClass: "owl-carouselPromos",
					navClass: ["owl-prevPromos","owl-nextPromos"],
					navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
					lazyLoad:true,
					responsive:{
					0:{
						items:1
					},
					480: {
						items:1
					},
					992: {
						items:3
						}
					}

				});
				
				$(".modalProds").fancybox(
				{

					helpers		: {
						title	: { type : 'inside' },
						buttons	: ["close", "download"]
					},
					onActivate : function() {
		                tipAbierto = true;
		            },
		            afterClose: function() {
		            	tipAbierto = false;
		            }
				});
				
				var optionsLinkRecipe = {
					maxWidth    : 800,
            		maxHeight   : 600,
            		fitToView   : false,
            		width       : '80%',
            		height      : '50%',
            		autoSize    : false,
					closeClick  : true,
					onActivate : function() {
		                videoRecetaAbierta = true;
		            },
		            afterClose: function() {
		            	videoRecetaAbierta = false;
						window.screen.orientation.lock("portrait");
		            },
					beforeShow: function(instance, current) {
						if (current.src.indexOf("https") < 0) {
							current.src = current.src.replace("//www.youtube.com/","https://www.youtube.com/");
						}
					},
		            afterShow: function() {
						window.screen.orientation.unlock();
		            }
				};
				if (plataforma == "ios") {
					optionsLinkRecipe.type = "iframe";
					optionsLinkRecipe.iframe = {
						preload: false
					};
				}
				console.log(JSON.stringify(optionsLinkRecipe));
							
				$("#linkRecipe").fancybox(optionsLinkRecipe);
				
//				initAutocomplete('map-canvas-acordeon');
				
				$('#contenedorGral').slick({
					adaptiveHeight : true,
					arrows: false,
					draggable: false,
					swipe: false,
					touchMove: false
				});
				
				$('#contenedorGral').on('afterChange', function(event, slick, currentSlide, nextSlide){
					var actualHeight = $("#" + slick.$slides[currentSlide].id + " > section").height();
					console.log(actualHeight);
					if (actualHeight < alturaDisp && currentSlide != 8) {
						$("#" + slick.$slides[currentSlide].id + " > section").height(alturaDisp);
					}
					refreshSlideHeight();
					if (currentSlide == 8) {
						if (iniciarMapaTiendas) {
							iniciarMapaTiendas = false;
							$("#mapaTiendas").show();
							initAutocompleteStores('map-canvas-stores');
						}
						else {
							$("#selectEstado").val("0");
							$('#selectCiudades').find('option').remove().end()
								.append('<option value="0">Seleccione su ciudad</option>').val('0');
							$("#divSearch").hide();
							$("#divStores").html("");
							hideAllInfoWindows();
							getStoresbyLocation(latlngTiendas.lat, latlngTiendas.lng);
							$("#mapaTiendas").show();
							$('.loader').fadeOut("slow");
						}
					}
				});
				
				$('#contenedorGral').on('reInit', function(event, slick, currentSlide, nextSlide){
					if (slideActual == 0) {
						$(".loader").fadeOut("slow");
					}
				});

				refreshSlideHeight();
				
				$("#txtParrilleroGanador").html(data[11]);
				$("#conoceWinner").html(data[13]);
				$("#txtParrilleroBases").html(data[14]);
				$("#modal-bases").html(data[14]);
				if (plataforma == "ios") {
					$("#modal-bases").css("margin-left", "-25px");
				}
				if (alturaDisp > 800) {
					$("#txtParrilleroBases").append("<br><br><br><br><br><br><br><br><br><br>");
				}
				
				if (data[10] == 1) {
					$("#btnConoceInv").css("opacity", "1");
					$("#btnConoceInv").attr("href", "javascript:goToSlide(6)");
					$("#carruselWinner").owlCarousel({
						dots: true,
						margin:50,
						nav: false,
						loop:true,
						center:true,
						lazyLoad:true,
						responsive:{
						0:{
							items:1
						},
						480: {
							items:1
						},
						992: {
							items:3
							}
						},
						onTranslated: function() {
							refreshSlideHeight();
						}
					});
//					$('#carruselWinner').slick({
//						adaptiveHeight : true,
//						arrows: true,
//						draggable: true,
//						swipe: true,
//						touchMove: true
//					});
				}
				else {
					$("#btnConoceInv").css("opacity", "0.5");
					$("#btnConoceInv").attr("href", "javascript:;");
				}
				
				var imgParrillero = new Image();
				imgParrillero.onload = function() {
//					console.log("termina de cargar imagen parrillero");
					
//					console.log($("#contenedorGral").css("height"));
//					if (Number($("#contenedorGral").css("height").replace("px","")) < alturaDisp) {
//						$("#contenedorGral").css("height", alturaDisp);
//					}
					var actualHeight = $("#parrilleroInvC").height();
					console.log(actualHeight);
					if (actualHeight < alturaDisp) {
						$("#parrilleroInvC").height(alturaDisp);
					}
					refreshSlideHeight();
//					console.log("termina de refrescar imagen parrillero");
					terminaCargaImgParrillero = true;
					$(".loader").fadeOut("slow");
				};
				imgParrillero.src = data[12];
				$("#imgParrilleroGanador").html(imgParrillero);
				$("#imgParrilleroGanador > img").css("width","100%");
				$("#imgParrilleroGanador > img").css("margin","auto"); 
				$("#imgParrilleroGanador > img").css("padding","20px");
				$("#imgParrilleroGanador > img").css("border-radius","35px");

				$("#selectEstado").html(data[15]);
				
				$('#selectEstado').on('change', function() 
				{
					  eligeEstado();
				});

				$('#selectCiudades').on('change', function() 
				{
					eligeCiudad();
				});
				
				$('#btnMoverFijarMapa').on('click', function() 
				{
					var displayPantalla = $("#pantallaMapa").css("display");
					if (displayPantalla == "block") {
						map_ubicaciones.setOptions({
							'gestures': {
								'scroll': true
							}
						});
						$(this).html('<span class="fa fa-thumb-tack" style="transform: rotate(45deg);padding-right: 10px;font-size: 200%;padding-left: 5px;"></span>Fijar mapa');
						$("#pantallaMapa").css("display","none");
					}
					else {
						map_ubicaciones.setOptions({
							'gestures': {
								'scroll': false
							}
						});
						$(this).html('<span class="fa fa-arrows-alt" style="padding-right: 10px;font-size: 200%;transform: rotate(45deg);"></span>Mover mapa');
						$("#pantallaMapa").css("display","block");
					}
				});
				
				$('#btnMoverFijarMapaP').on('click', function() 
				{
					var displayPantalla = $("#pantallaMapaP").css("display");
					if (displayPantalla == "block") {
						map.setOptions({
							'gestures': {
								'scroll': true
							}
						});
						$(this).html('<span class="fa fa-thumb-tack" style="transform: rotate(45deg);padding-right: 10px;font-size: 200%;padding-left: 5px;"></span>Fijar mapa');
						$("#pantallaMapaP").css("display","none");
					}
					else {
						map.setOptions({
							'gestures': {
								'scroll': false
							}
						});
						$(this).html('<span class="fa fa-arrows-alt" style="padding-right: 10px;font-size: 200%;transform: rotate(45deg);"></span>Mover mapa');
						$("#pantallaMapaP").css("display","block");
					}
				});

				$('html,body').animate({ scrollTop: $("#menuPrincipal").offset().top }, 500);
				
//				$(".loader").fadeOut("slow");

			},
			// funcion en caso de error
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
//				alert("Para poder continuar, debes estar conectado a Internet. [" + error + " ~ " + JSON.stringify(xhr) + "]");
				alert("Para poder continuar, debes estar conectado a Internet.");
				navigator.app.exitApp();
			}
		});
		
		monitoreoGPS();
//		google.maps.event.addDomListener(window, 'load', initAutocomplete);
//		alert(window.location.href);
	}
	catch(err) {
		alert("Error: READY: " + err);
	}
}

function refreshSlideHeight() {
	$("#contenedorGral").find(".slick-slide").height("auto");
	$("#contenedorGral").slick("setOption", '', '', true);
}

function refreshArrows(event) {
//	alert(event.item.index);
	var id_padre = event.currentTarget.id;
	if (event.item.index === 0) {
		$("#" + id_padre + " > div:nth-child(2) > .owl-nextPromos").show();
		$("#" + id_padre + " > div:nth-child(2) > .owl-prevPromos").hide();
	}
	if (event.item.index === event.item.count - 1) {
		$("#" + id_padre + " > div:nth-child(2) > .owl-nextPromos").hide();
		$("#" + id_padre + " > div:nth-child(2) > .owl-prevPromos").show();
	}
}

function verificarSesion(sid) {
	localStorage.setItem("session_id", sid);
	var user_email_local = localStorage.getItem("user_email");
	if (user_email_local != '') {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/configBbqController.php' + debug,
			cache: false,
			dataType: "json",
			data: {
				action : 'registrarUsuario',
				gender: localStorage.getItem("user_gender"),
				email: user_email_local,
				name: localStorage.getItem("user_name"),
				session_id: sid
//				,passwd: pass1
			},
			success: function (data, textStatus, jqXHR) {
				if (data) {
//										id_login = response.id;
					console.log("registro OK deviceready");
				}
				else {
					$.notify("Ocurrió un error al guardar información del cliente deviceready", "error");
				}
			},
			error: function (xhr, status, error) {
				alert("Error al registrar usuario DEVICE READY: " + error.toString());
			}

		});
	}
	else {
		localStorage.setItem("user_name", '');
		localStorage.setItem("user_email", '');
		localStorage.setItem("user_gender", '');		
	}
}

function openSettings() {
	intentos_gps++;
	if (window.cordova && window.cordova.plugins.settings) {
		var ventana = "location";
		if (plataforma != "android") {
			ventana = "locations";
		}
		window.cordova.plugins.settings.open(ventana, function() {
				$("#modal-invite-gps").modal("hide");
			},
			function () {
				gps_activo = 0;
			}
		);
	} else {
		gps_activo = 0;
	}	
}

function quitarGPS() {
	gps_activo = -1;
	watchID = null;
	intentos_gps++;
}

function getLatLngGPS() {
	try {
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
				gps_activo = 1;
			}, function (error) {
				console.log("Sin posicion actual desde inicio");
				if (intentos_gps < 1) {
					$("#modal-invite-gps").modal("show");
				}
			}, {
				timeout: 1500, enableHighAccuracy: true
			});
		}
		else {
			alert("No hay GPS disponible");
		}
	}
	catch(err) {
		alert("No fue posible obtener tu ubicación. Por favor ingresa la dirección.");
		console.log(err);
	}
}


function monitoreoGPS() {
	try {
		var tiempoEspera = 5000;
		if (plataforma == "ios") {
			tiempoEspera = 20000;
		}
		if (navigator != null && navigator.geolocation != null) {
			if (watchID !== null && gps_activo == 0) {
				navigator.geolocation.clearWatch(watchID);
				watchID = null;
			}
			if (gps_activo != 1 && (ubicActual.lat == 0 || ubicActual.lng == 0)) {
				console.log(ubicActual);
				console.log(tiempoEspera);
				watchID = navigator.geolocation.watchPosition(function(position) {
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
					gps_activo = 1;
					if (conMapa) {
						conMapa = false;
						$('html,body').animate({ scrollTop: $("#mapaArmaParrillada").offset().top }, 500);
						getUbicacionActual();
					}
					console.log("ubic por watch: " + JSON.stringify(ubicActual) + ", watchID: " + watchID);
				}, function (error) {
					if (ubicActual.lat == 0 || ubicActual.lng == 0) {
						console.log("sin posicion localizada watchPosition: " + watchID);
						gps_activo = 0;
						if (intentos_gps < 1) {
							$("#modal-invite-gps").modal("show");
						}
					}
				}, {
					timeout: tiempoEspera, enableHighAccuracy: true
				});
			}
			else {
				gps_activo = 1;
				if (conMapa) {
					conMapa = false;
					$('html,body').animate({ scrollTop: $("#mapaArmaParrillada").offset().top }, 500);
					getUbicacionActual();
				}
			}
		}
		else {
			gps_activo = 0;
			console.log("No hay GPS disponible");
		}
	}
	catch(err) {
		console.log("catch watchPosition: " + err);
	}
}


function usarMapa() {
	intentos_gps = 0;
	conMapa = true;
	monitoreoGPS();
//	setTimeout(function () {
//        if (gps_activo = 0 || ubicActual.lat == 0 || ubicActual.lng == 0) {
//			$.notify("No se logró encontrar una ubicación actual.", "error");
//		}
//	}, 2000);
}

function armarParrillada(){
//	id_login = localStorage.getItem("id_login");
	sliderAbiertos = [0];
	paso_actual = 0;
	if (markerActual !== null) {
		markerActual.remove();
	}
	markerActual = null;
	conMapa = false;
//	session_id = null;
	
//	acordeon.closeAll();
//	acordeon.open("0");
//	id_activo = acordeon.headers[0].id;
//	$("#" + id_activo).css("background-color","#B32524");
	
	verificarSesion(localStorage.getItem("session_id"));
	
	activarAcordeon(false);
//	acordeon.open("0");

//	$('#botonesRegistro').show(); 
	
	goToSlide(1);
	$('#mapaArmaParrillada').hide();
	$('#msjFelicidades').hide();
	limpiarFormularios();
	monitoreoGPS();
}

function limpiarFormularios() {
	$("#bbq_name").val("");
	$("#datetimepicker").val("");
	$("#bbq_time").val("");
	var numInvitados = $("#numInvitados").html();
	var numInvitadas = $("#numInvitadas").html();
	modifInvitados('Invitados', Number(numInvitados) * -1);
	modifInvitados('Invitadas', Number(numInvitadas) * -1);
	cant_amigos = -1;
	$("#listaAmigos").html("");
	moreFriends();
	$("#bbq_address").val("");
	$("#bbq_message").val("");
	$("#bbq_message_ios").val("");
}

function goToSlide(slide) {
	try {
		switch(slide) {
			case 1:
				$(".containerArma").css("opacity","1");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","0");
				break;
			case 2:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","1");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","0");
				break;
			case 3:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","1");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","0");
				break;
			case 4:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","1");
				$(".containerParticipa").css("opacity","0");
				if (terminaCargaImgParrillero == false) {
					$(".loader").fadeIn("slow");
				}
				break;
			case 5:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","1");
				fotosSubir = [];
				indexFoto = -1;
				$("#bbq_host_message").val("");
				$("#listFotos").html('<p style="text-align: left; display: none" id="txtPregPrincipal">¿Principal?</p>');
				verificarLoginFace();
				break;
			case 6:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","0");
				$(".containerConoce").css("opacity","1");
				$(".containerInvBases").css("opacity","0");
				$(".containerTiendas").css("opacity","0");
				break;
			case 7:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","0");
				$(".containerConoce").css("opacity","0");
				$(".containerInvBases").css("opacity","1");
				$(".containerTiendas").css("opacity","0");
				break;
//			case 6:
//				$("#carruselWinner").slick('setOption', 'adaptiveHeight', 'true', false);
//				break;
			case 8:
				$(".containerArma").css("opacity","0");
				$(".containerTips").css("opacity","0");
				$(".containerProd").css("opacity","0");
				$(".containerInv").css("opacity","0");
				$(".containerParticipa").css("opacity","0");
				$(".containerConoce").css("opacity","0");
				$(".containerInvBases").css("opacity","0");
				$('.loader').fadeIn("slow");
				$(".containerTiendas").css("opacity","1");
				paso_actual = 9999;
				break;
		}
//		alert("Slide: " + slide);
		$("#contenedorGral").slick('slickGoTo', slide, false);
		$('#mapaArmaParrillada').hide();
		$('#mapaTiendas').hide();
		$('#msjFelicidades').hide();
		if (watchID !== null) {
			navigator.geolocation.clearWatch(watchID);
			watchID = null;
		}
		if (slide == 0) {
			$("#contenedorGral").css("margin-top", "0px");
		}
		
//		if (terminaCargaImgParrillero == true) {
//			console.log($("#contenedorGral").css("height"));
//			if (Number($("#contenedorGral").css("height").replace("px","")) < alturaDisp) {
//				$("#contenedorGral").css("height", alturaDisp);
//			}
//		}
		
		if (slide != 1) {
			$('html,body').animate({ scrollTop: $("#contenedorGral").offset().top }, 500);
		}
		slideActual = slide;
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
				'scroll': false,
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
//			map.on(plugin.google.maps.event.MAP_LOADED, onMapLoaded);
	}
	catch(err) {
		alert("Error iniciar mapa: " + err);
	}
}

function onMapReady(map) {
	cambiarColorMapa('black');	
	$('html,body').animate({ scrollTop: $("#vinetaUbic").offset().top }, 500);
	refreshSlideHeight();
	acordeon.calculateAllPanelsHeight();
	
//	alert("MAPA LISTO!" + JSON.stringify(map));
}

function cambiarColorMapa(color) {
	plugin.google.maps.environment.setBackgroundColor(color);
}

function escribirDireccion() {
	refreshSlideHeight();
	$('#bbq_address').focus();
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
				if (plataforma == "ios") {
					map.moveCamera({
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
				}
				else {
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
				}
				$(".loader").fadeOut("slow");
				$("#bbq_message").focus();
				if (plataforma == "ios") {
					$("#bbq_message_ios").focus();
				}
				refreshSlideHeight();
			});
		} else {
			alert("No se localizó la dirección ingresada");
			$(".loader").fadeOut("slow");
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
		if (plataforma == "ios") {
			map.moveCamera({
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
		}
		else {
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
		}
		var request = {
		  'position': position
		};
		plugin.google.maps.Geocoder.geocode(request, function(results) {
//			$(".loader").fadeIn("slow");
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
				if (plataforma == "ios") {
					$("#bbq_message_ios").focus();
				}
				refreshSlideHeight();
			} else {
				alert("No se pudo localizar la dirección");
			}
//			$(".loader").fadeOut("slow");
		});
	});
}

//function onMapLoaded(map) {
//	alert("MAPA CARGADO!");
//}

function verificarLoginFace() {
	var emailDentro = localStorage.getItem("user_email");
	$("#facebookName2").html(localStorage.getItem("user_name"));
	if (emailDentro === '') {
		$("#msjLogin").show();
		$("#msjLogout").hide();
	}
	else {
		$("#msjLogin").hide();
		$("#msjLogout").show();
	}
	refreshSlideHeight();
}

function activarAcordeon(instanciar) {
	$("#registroUsuario").show();
	$('#botonesRegistro').show();
	$("#formRegistro").show();
	$("#facebookLogin").show();
	$("#logoutFace").show();
	$("#logoutRegistro").show();
	var emailDentro = localStorage.getItem("user_email");
	if (emailDentro !== '') {
		sliderAbiertos = [1];
		paso_actual = 1;
	}
	if (instanciar) {
		acordeon = new BadgerAccordion(".js-badger-accordion", {
			openHeadersOnLoad: sliderAbiertos
		});
	}

	$(".badger-accordion__trigger").css("background-color","#353535");

	acordeon.closeAll();
	id_activo = acordeon.headers[0].id;
	if (emailDentro === '') {
		$("#facebookLogin").hide();
		$('#botonesRegistro').show();
		acordeon.open("0");
	}
	else {
		$('#botonesRegistro').hide();
		$("#registroUsuario").hide();
		$("#facebookName").html(localStorage.getItem("user_name"));
		$("#facebookLogin").show();
		if (localStorage.getItem("user_gender") == "F") {
			$("#logoutFace").show();
			$("#logoutRegistro").hide();
		}
		else {
			$("#logoutFace").hide();
			$("#logoutRegistro").show();
		}
		acordeon.open("1");
		id_activo = acordeon.headers[1].id;
		$('html,body').animate({ scrollTop: $("#vinetaRegistro").offset().top }, 500);
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
//										id_login = response.id;
										localStorage.setItem("user_name", response.name);
										localStorage.setItem("user_email", response.email);
										localStorage.setItem("user_gender", 'F');
										activarAcordeon(false);
										verificarLoginFace();
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
//			id_login = '';
			cerrarSesion();
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
	if (!validateEmail(email)) {
		$("#continuar1").notify("Favor de ingresar un email válido", {
			position: "top right"
		});
		return;
	}
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
					localStorage.setItem("user_name", name);
					localStorage.setItem("user_email", email);
					localStorage.setItem("user_gender", gender);
					activarAcordeon(false);
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

function cerrarSesion() {
	localStorage.setItem("user_name", "");
	localStorage.setItem("user_email", "");
	localStorage.setItem("user_gender", "");
	sliderAbiertos = [0];
	paso_actual = 0;
	activarAcordeon(false);
	verificarLoginFace();
	acordeon.calculateAllPanelsHeight();
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
					if (plataforma != "ios") {
						$.notify("Ocurrió un error al almacenar los datos de la parrillada. Es necesario intentarlo nuevamente.", "error");
					}
					goToSlide(0);
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
					$.notify("Ocurrió un error al mostrar los productos con cantidades.", "error");
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
						$('html,body').animate({ scrollTop: $("#vinetaProd").offset().top }, 500);
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
					if (plataforma != "ios") {
						$.notify("No fue posible guardar la parrillada. Es necesario intentarlo nuevamente.", "error");
					}
					goToSlide(0);
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
		$("#bbq_guest_email_" + cantAmigos).focus();
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
//					$("#contenedorGral").find(".slick-slide").height("auto");
//					$("#contenedorGral").slick("setOption", '', '', true);
//					acordeon.calculateAllPanelsHeight();
					initAutocomplete('map-canvas-acordeon');
					if (gps_activo !== -1) {
						$("#modal-confirm-gps").modal("show");	
					}
					if (plataforma === "ios") {
						$("#atras_6_2").show();
						$("#contenedorGral").css("margin-top", "-800px");
						$("#bbq_message").hide();
						$("#bbq_message_ios").show();
					}
					else {
						$("#atras_6_2").hide();
						$("#contenedorGral").css("margin-top", "0px");
						$("#bbq_message").show();
						$("#bbq_message_ios").hide();
					}
					$(".loader").fadeOut("slow");
//					$("#mapaArmaParrillada").css("margin-top", $("#armaTuParrillada").height());
				}
				else {
					$(".loader").fadeOut("slow");
					if (plataforma != "ios") {
//						alert("No fue posible invitar amigos: " + data);
						$.notify("No fue posible invitar amigos", "error");
					}
					goToSlide(0);
					
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

function quitarMapaIOS() {
	$('#mapaArmaParrillada').hide();
	$("#contenedorGral").css("margin-top", "0px");
	acordeon.close("5");
	acordeon.open("4");
	paso_actual = 4;
	refreshSlideHeight();
	acordeon.calculateAllPanelsHeight();
}

function finishBbq() {
	var address = $("#bbq_address").val().trim();
	var message = $("#bbq_message").val();
	if (plataforma == "ios") {
		message = $("#bbq_message_ios").val();
	}
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
					if (plataforma == "ios") {
						$("#contenedorGral").css("margin-top", "0px");
					}
//					alert("¡Felicidades! Tu parrillada está por hacerse realidad");
					$("#msjFelicidades").show();
					refreshSlideHeight();
					$('html,body').animate({ scrollTop: $("#vinetaUbic").offset().top }, 500);
				}
				else {
//					alert("No fue posible concluir con la parrillada: " + data);
					if (plataforma != "ios") {
						$.notify("No fue posible concluir con la parrillada", "error");
					}
					goToSlide(0);
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

function selecRecipeType(idRecipe)
{
	$(".recetasClass").hide();
	$("#"+idRecipe).show();
	switch(idRecipe)
	{
		case 'recetaRes':
			$("#logoRes").attr("src","images/res2.png");
			$("#logoCerdo").attr("src","images/cerdo1.png");
			$("#logoEmbutido").attr("src","images/embutidos1.png");
			$("#logoPollo").attr("src","images/pollo1.png");
		break;
		case 'recetaCerdo':
			$("#logoCerdo").attr("src","images/cerdo2.png");
			$("#logoRes").attr("src","images/res1.png");
			$("#logoEmbutido").attr("src","images/embutidos1.png");
			$("#logoPollo").attr("src","images/pollo1.png");
		break;
		case 'recetaEmbutidos':
			$("#logoEmbutido").attr("src","images/embutidos2.png");
			$("#logoCerdo").attr("src","images/cerdo1.png");
			$("#logoRes").attr("src","images/res1.png");
			$("#logoPollo").attr("src","images/pollo1.png");
		break;
		case 'recetaPollo':
			$("#logoPollo").attr("src","images/pollo2.png");
			$("#logoEmbutido").attr("src","images/embutidos1.png");
			$("#logoCerdo").attr("src","images/cerdo1.png");
			$("#logoRes").attr("src","images/res1.png");
		break;

	}
	refreshSlideHeight();
}
function selectRecipe(idRecipe)
{
	$(".receta").hide();
	$(".subcontent").css("color", "");
	$("#txt"+idRecipe).css("color", "#DA543B");
	
//		if ($(this).width() > 480) 
//			{
//				$("#"+idRecipe).show();
//			}
//		else
//			{
			
			var identHTML="#"+idRecipe;
			$.fancybox.open({
				'type': 'html',
				'height': 'auto',
				'autoSize':false,
				'closeClick': false,
				'scrolling':'yes',
				'transitionIn': 'elastic',
				'transitionOut': 'elastic',
				'animationDuration': 1000,
				'touch':false,
				'content' : $(identHTML).html(),
				'afterClose' : function() {
					refreshSlideHeight();
					recetaAbierta = false;
					return;
				},
				'afterShow' : function() {
					recetaAbierta = true;
				}
			  });

//			}
}

function playVideoRecipe(videoId) {
	$("#linkRecipe").attr("href", "https://www.youtube.com/embed/" + videoId + "?autoplay=1&rel=0");
	$("#linkRecipe").click();
}

function cameraSuccess(imageData) {
	indexFoto++;
	var foto = $("#rowFoto").html();
	foto = foto.replace(/##num##/g, indexFoto);
	$("#listFotos").append(foto);
	var anchoImg = $(this).width() / 2;
	var altoImg = anchoImg + 20;
	var anchoProporcional = "85";
	if (anchoImg > 250) {
		anchoImg = 250;
		altoImg = anchoImg;
		anchoProporcional = "75";
	}
	$("#img_" + indexFoto).html('<div style="width: ' + anchoImg + 'px; height: ' + altoImg + 'px; margin: auto; display: flex; align-items: center;"><img alt="foto" src="data:image/jpeg;base64,' + imageData + '" style="margin: auto; padding: 15px; width: ' + anchoProporcional + '%"></div>');
	$("#txtPregPrincipal").show();
	if (indexFoto == 0) {
		isCover(0);
	}
	$("#bbq_host_message").focus();
	$("#btnMasFotoCamara").focus();
	refreshSlideHeight();
	$(".loader").fadeOut("slow");
}

function cameraError(message) {
	if (message != "No Image Selected") {
		$.notify("Ocurrió un error con la cámara: " + message, "error");
	}
	$(".loader").fadeOut("slow");
}

function addFotoGaleria() {
	if (navigator.camera) {
		addFoto(Camera.PictureSourceType.PHOTOLIBRARY);
	}
}

function addFotoCamara() {
	if (navigator.camera) {
		addFoto(Camera.PictureSourceType.CAMERA);
	}
}

function addFoto(sourceTypeAct) {
	if (navigator.camera) {
		$(".loader").fadeIn("slow");
		var cameraOptions = {
			quality: 50,
			destinationType: Camera.DestinationType.DATA_URL,
			sourceType:	sourceTypeAct,
		//			allowEdit: false,
			encodingType: Camera.EncodingType.JPEG,
			targetWidth: 1024,
		/*			targetHeight: 1080,
			mediaType: Camera.MediaType.PICTURE,*/
			correctOrientation: true,
			saveToPhotoAlbum:	true,
			cameraDirection: Camera.Direction.BACK
		}
		navigator.camera.getPicture(cameraSuccess, cameraError, cameraOptions);
	}
}

function deleteFoto(i) {
	$("#foto_" + i).remove();
	refreshSlideHeight();
}

function isCover(i) {
	$("[id^=is_cover]").html('<div style="border-radius: 10px;margin: 10px;border: 1px solid #fff;width: 45%;margin-left: 40%;">&nbsp;</div>');
	$("#is_cover_" + i).html('<span class="fa fa-check" style="font-size: 20px; color: green"></span>');
}

function getImagesBbqHost() {
	var images = [];
	for (var m=0; m <= indexFoto; m++) {
		if ($("#foto_" + m).length > 0) {
			var is_cover = ($("#is_cover_" + m + " > span").length > 0) ? 1 : 0;
			images.push({
				data: $("#img_" + m + " > div > img").attr("src"),
				isCover: is_cover
			});
		}
	}
	return images;
}

function saveBbqHost() {
	var description = $("#bbq_host_message").val();
	var images = getImagesBbqHost();
	if ($("#facebookName2").html() == "") {
		$("#btnSaveBbqHost").notify("Favor de identificarse como usuario.", {
			position: "top right"
		});
		return;
	}
	if (images.length <= 0) {
		$("#btnSaveBbqHost").notify("Favor de ingresar una imagen.", {
			position: "top right"
		});
		return;
	}
	var tc_aceptados = $("#parrillero_tc:checked").length;
	if (tc_aceptados <= 0) {
		$("#btnSaveBbqHost").notify("Favor de aceptar los términos y condiciones.", {
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
				action : 'saveBbqHost',
				message: description,
				images: images,
				session_id: session_id
			},
			success: function (data, textStatus, jqXHR) {
				if (data) {
					$(".loader").fadeOut("slow");
					$.notify("¡Gracias por participar! Recibimos tu información y si resultas ganador te avisaremos.","success");
					goToSlide(0);
					refreshSlideHeight();
				}
				else {
					$(".loader").fadeOut("slow");
//					alert("No fue posible guardar la parrillada");
					$.notify("No fue posible guardar la información de parrillero invitado. Es necesario intentarlo nuevamente.", "error");
					goToSlide(0);
				}
			},
			error: function (xhr, status, error) {
				$(".loader").fadeOut("slow");
				alert("saveBbqHost ajax error: " + error.toString());
			}

		});
	}
	catch(ex) {
		$(".loader").fadeOut("slow");
		alert("saveBbqHost catch error: " + ex.message);
	}
}

function showSearch()
{
	$("#divSearch").show();
	refreshSlideHeight();
//	$('html,body').animate({ scrollTop: $("#imgTitulo6").offset().top }, 500);
}

var ubicaciones;
var map_ubicaciones;
var marcadores = [];
var infoW = [];
var iniciarMapaTiendas = true;
var acordeonStores;
var latlngTiendas;
var zIndexTiendas = 9;
var headerIndexActual = -1;

function initAutocompleteStores(id_map) {
	try {
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
				'zoom': 12,
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
		map_ubicaciones = plugin.google.maps.Map.getMap(document.getElementById(id_map), options);
		map_ubicaciones.on(plugin.google.maps.event.MAP_READY, onMapStoresReady);
	}
	catch(err) {
		alert("Error iniciar mapa ubicaciones: " + err);
	}
}

function onMapStoresReady(map) {
	cambiarColorMapa('black');
	getUbicacionesTiendas();
	map_ubicaciones.on(plugin.google.maps.event.MAP_CLICK, function(cameraPosition) {
		indexInfoTiendas();
	});
	map_ubicaciones.on(plugin.google.maps.event.CAMERA_MOVE, function(cameraPosition) {
		indexInfoTiendas();
	});
	map_ubicaciones.on(plugin.google.maps.event.CAMERA_MOVE_END, function(cameraPosition) {
		indexInfoTiendas();
	});
}

function getUbicacionesTiendas()
{
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/mapController.php',
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
					marcadores=[];
					infoW = [];
					setUbicaciones();

				}
				else {
//					alert("No fue posible invitar amigos: " + data);
					$.notify("No fue posible obtener las ubicaciones: " + data, "error");
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

function setUbicaciones()
{
	var suc = '';
	if(gps_activo == 1)
	{
		//poner el centro en el GPS del usuario.
		latlngTiendas = ubicActual;
		createMarkerUbi(latlngTiendas, "", true);
	}
	else
	{
		latlngTiendas = {
			lat: 19.432779,
			lng: -99.133152
		};
		//latlng= new google.maps.LatLng(ubicaciones[0].latitude,ubicaciones[0].longitude);
	}
	for(var i = 0; i  < ubicaciones.length; i++)
	{
		var ubi = ubicaciones[i];
		var markerlatlng = {
			lat: ubi.latitude,
			lng: ubi.longitude
		}
//		var txtTituloMarcador = "<div style='color: black; width: 280px'><span class='fa fa-times pull-right' onclick='hideAllInfoWindows();'></span><br><span style='font-weight: bold'>Tienda:</span> "+ubi.nameSuc+" <br> <span style='font-weight: bold'>Dirección:</span> "+ubi.address+" <br> <span style='font-weight: bold'>Ciudad:</span> "+ubi.nameCity+"<br> <span style='font-weight: bold'>Estado:</span> "+ubi.nameState+"</div>";
		var txtTituloMarcador = '<div style="color: black;width: 280px;display: flex;align-items: baseline;"><span class="" style="font-weight: bold;display: inline;width: 90%;">'+ubi.nameSuc+'</span><span class="fa fa-times pull-right" onclick="hideAllInfoWindows();" style="display:inline;width: 10%;"></span></div>';
		createMarkerUbi(markerlatlng, txtTituloMarcador,false);
	}
	
	getStoresbyLocation(latlngTiendas.lat, latlngTiendas.lng);
}

function createMarkerUbi(latlng, html, is_home) 
{

	var htmlInfoWindow = new plugin.google.maps.HtmlInfoWindow();
	htmlInfoWindow.setContent(html);
	infoW.push(htmlInfoWindow);
	
	var iconImage = image;
	if (is_home) {
		iconImage = 'images/iconoMarkerHome.png';
	}
	
	position = latlng;
	map_ubicaciones.addMarker({
		'position': position,
//		'title':  html,
		'icon': {
	    	'url': iconImage
		},
		'draggable': false
	}, function(marker) {
		if (!is_home) {
			marker.addEventListener(plugin.google.maps.event.MARKER_CLICK, function() {
				hideAllInfoWindows();
//				marker.setAnimation(plugin.google.maps.Animation.BOUNCE);
				htmlInfoWindow.open(marker);
//				marker.showInfoWindow();
			});
			marcadores.push(marker);
		}
	});
}

function hideAllInfoWindows() 
{
	for(var i=0; i<infoW.length;i++)
	{
		infoW[i].close();
//		marcadores[i].hideInfoWindow();
	}
}

function armarTiendasConProductos(storesByCity) {
	var html = '';
	id_stores = [];
					
	for(var i=0; i<storesByCity.length; i++)
	{
		html += '	<dt>'
		html += '		<button type="button" class="badger-accordion__trigger js-badger-accordion-header botonesTiendas">';
		html += '			<div class="badger-accordion__trigger-title">';
		html += '				<span class="fa fa-map-marker"></span>&nbsp;';
		html += '				<label class="labelTitle">' + storesByCity[i]["name"] + '</label>';
		html += '			</div>';
		html += '			<div class="badger-accordion__trigger-icon">';
		html += '			</div>';
		html +=	'		</button>';
		html += '	</dt>';

		html += '	<dd class="badger-accordion__panel js-badger-accordion-panel">';
		html += '		<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner" id="tienda_' + storesByCity[i]["id_store"] + '">';
		html += '			<div>';
		html += '				<p style="text-align: left;padding-bottom: 15px;font-size: 16px;line-height: 1.3;">';
		html += '					' + storesByCity[i]["address"];
		html += '				</p>';
		html += '				<div class="row">';
		html += '					<div class="col-md-12">';
		html += '						<div id="store_' + storesByCity[i]["id_store"] + '" class="owl-carousel owl-carouselTiendas">'

		id_stores.push('store_' + storesByCity[i]["id_store"]);

		//ciclo de productos
		for(var j=0; j<storesByCity[i].Products.length; j++)
		{
			html += '<div class="item" id="imgT_' + storesByCity[i]["id_store"] + '_' + j + '">';
//			var imagenT = new Image();
//			imagenT.onload = function() {
//				$("#" + this.parent.id + " > div").css("height", this.naturalHeight);
//				$("#" + this.parent.id + " > div").css("width", this.naturalWidth);
//				$("#" + this.parent.id + " > div").css("margin", "auto");
//				$("#" + this.parent.id + " > div").css("opacity", "1");
//			};
//			imagenT.src = URLFrontera + "app/" + storesByCity[i].Products[j].thumb_path.replace(".png","_app.png");
//			html += imagenT;
			html += '	<img class="owl-lazy" src="' + URLFrontera + "app/" + storesByCity[i].Products[j].thumb_path.replace(".png","_app.png") + '" style="margin: auto; opacity: 1; height: inherit; width: inherit" alt="image">';
			html +=	'	<br>';
			html += '	<p style="text-align: center">';
			html += '		' + storesByCity[i].Products[j].name;
			html += '	</p>';
			html += '</div>';
		}

		html += '						</div>';
		html += '					</div>';
		html += '				</div>';
		html += '			</div>';
		html += '		</div>';
		html += '	</dd>';
	}

	html += '	</dl>';
	html += '</div>';
	
	return {
		html: html,
		id_stores: id_stores
	}
}

function getStoresbyLocation(latitude, longitude)
{
	try {

//		$(".loader").fadeOut("slow");

		map_ubicaciones.moveCamera({
			'target': {
				lat: latitude,
				lng: longitude
			},
			'zoom': 12
		}, function(){
//			$(".loader").fadeIn("slow");
			$.ajax({
				type: 'POST',
				url: URLFrontera+'app/controller/mapController.php',
				cache: false,
				dataType: "json",
				data: {
					action : 'getStoresbyLocation',
					lat : latitude,
					lon: longitude
				},
				success: function (data, textStatus, jqXHR) 
				{
					
					var html='';
					$("#divStores").hide();
					if (data) 
					{					
						html += '<div class="col-md-12" style="margin-bottom: 30px">'
						html += '	<dl class="badger-accordion js-badger-accordion2">';
						
						storesByCity=data;
						
						datosTiendas = armarTiendasConProductos(storesByCity);
						
						html += datosTiendas.html;
						id_stores = datosTiendas.id_stores;
						
						$("#divStores").html(html);
						
						for (var k=0; k<id_stores.length; k++) {
							$("#" + id_stores[k]).owlCarousel({
								dots: false,
								margin:50,
								nav: true,
								loop:true,
								center:true,
								navContainerClass: "owl-carouselPromos",
								navClass: ["owl-prevPromos","owl-nextPromos"],
								navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
								lazyLoad:true,
								responsive:{
								0:{
									items:1
								},
								480: {
									items:1
								},
								550: {
									items:3
								},
								992: {
									items:3
									}
								},
								onInitialized: indexInfoTiendas
							});
						}
						
						acordeonStores = new BadgerAccordion(".js-badger-accordion2");
						
						$("#divStores").fadeIn( "slow", function() { });

						$("#infoTiendas").css("z-index", "9999");
						
//						$('html,body').animate({ scrollTop: $("#mapaArmaParrillada").offset().top }, 500);
//						$('html,body').animate({ scrollTop: $("#imgTitulo6").offset().top }, 500);
						
						$(".loader").fadeOut("slow");
					}
					else 
					{
						$("#divStores").html(html);
						$.notify("No hay tiendas disponibles cerca de ti: " + data, "error");
					}
				},
				error: function (xhr, status, error) 
				{
					console.log(error.toString());
				}
			});
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
			latlngE = {
				lat: ubicaciones[i].latitude,
				lng: ubicaciones[i].longitude
			};
			map_ubicaciones.moveCamera({
				position: latlngE,
				zoom: 12
			}, function() {
				indexInfoTiendas();
			});
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
	$("#divStores").html("");
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
			latlngC = {
				lat: ubicaciones[i].latitude,
				lng: ubicaciones[i].longitude
			};
			map_ubicaciones.moveCamera({
				position: latlngC,
				zoom: 12
			}, function() {
				indexInfoTiendas();
			});
		}
	}
	
	getStoresyCity(id_ciudad);
}

function getStoresyCity(id_ciudad)
{
	try {
		$.ajax({
			type: 'POST',
			url: URLFrontera+'app/controller/mapController.php',
			cache: false,
			dataType: "json",
			data: {
				action : 'getStoresbyCity',
				id_ciudad : id_ciudad
			},
			success: function (data, textStatus, jqXHR) 
			{
				
				var html='';
				$("#divStores").hide();
				if (data) 
				{
					html += '<div class="col-md-12" style="margin-bottom: 30px">'
					html += '	<dl class="badger-accordion js-badger-accordion2">';
					
					storesByCity=data;
					
					datosTiendas = armarTiendasConProductos(storesByCity);
					
					html += datosTiendas.html;
					id_stores = datosTiendas.id_stores;
					
					$("#divStores").html(html);
					
					for (var k=0; k<id_stores.length; k++) {
						$("#" + id_stores[k]).owlCarousel({
							dots: false,
							margin:50,
							nav: true,
							loop:true,
							center:true,
							navContainerClass: "owl-carouselPromos",
							navClass: ["owl-prevPromos","owl-nextPromos"],
							navText : ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
							lazyLoad:true,
							responsive:{
							0:{
								items:1
							},
							480: {
								items:1
							},
							550: {
								items:3
							},
							992: {
								items:3
								}
							},
							onInitialized: indexInfoTiendas
						});
					}
					
					acordeonStores = new BadgerAccordion(".js-badger-accordion2");
					
					
					$("#divStores").fadeIn( "slow", function() { });
					
					$("#infoTiendas").css("z-index", "9999");
						
					$('html,body').animate({ scrollTop: $("#mapaArmaParrillada").offset().top }, 500);
//					$('html,body').animate({ scrollTop: $("#imgTitulo6").offset().top }, 500);
					
					$(".loader").fadeOut("slow");

//					$("[id^=store_] > div.owl-stage-outer > div.owl-stage").css("display","flex");
//					$("[id^=store_] > div.owl-stage-outer > div.owl-stage").css("align-items","center");
				}
				else 
				{
					$("#divStores").html(html);
					$.notify("No hay tiendas disponibles en la ciudad seleccionada: " + data, "error");
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

function indexInfoTiendas(event) {
	$("#infoTiendas").css("z-index", zIndexTiendas++);
}

function irAPuntosDeVenta() {
	goToSlide(8);
}

function irATiendas() {
	var displayPantalla = $("#pantallaMapa").css("display");
	if (displayPantalla == "none") {
		map_ubicaciones.setOptions({
			'gestures': {
				'scroll': false
			}
		});
		$("#btnMoverFijarMapa").html('<span class="fa fa-arrows-alt" style="padding-right: 10px;font-size: 200%;transform: rotate(45deg);"></span>Mover mapa');
		$("#pantallaMapa").css("display","block");
	}
	$('html,body').animate({ scrollTop: $("#infoTiendas").offset().top }, 500);
}

function abrirTC() {
	$("#modal-term-cond").modal("show");
}