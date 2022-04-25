<?php
session_start();
require_once("FB/conectarFacebook.php");
require_once("model/Product_category.php");
require_once("config.php");

$id_login = "";
$nombre = "";
$apellido = "";
$img_profile = "./imgCF/profiles/profileDummy.jpg";
$email = "";
$gender = "";

//print_r($_SESSION);

if (isset($_SESSION["fb_access_token"])) 
{
//	echo "SESION: ";
	
	$user_info = $_SESSION["fb_user"];
	$id_login = $user_info["id"];
	$email = $user_info["email"];
	$nombre = $user_info["name"];
//	$birthday = substr($user_info["birthday"],6,4)."-".substr($user_info["birthday"],0,2)."-".substr($user_info["birthday"],3,2);
//	$_SESSION["birthday"] = $birthday;
//	$gender = ($user_info["gender"] == "male" ? "H" : "M");
//	$_SESSION["gender"] = $gender;
//	$datos_ap = explode(",", $user_info["last_name"]);
//	$apellido = $datos_ap[0];
//	if (count($datos_ap) > 1) {
//		$apellido2 = $datos_ap[1];
//	}
	$img_profile = "https://graph.facebook.com/".$id_login."/picture?type=square&height=300&width=300";
	$_SESSION["img_profile"] = $img_profile;
	unset($_SESSION["fb_access_token"]);
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Page Title -->
    <title>Frontera Carne Parrillera</title>
	
	
	<meta property="og:title" content="Frontera carne parrillera"/>
    <meta property="og:type" content="Frontera"/>
    <meta property="og:url" content="http://fronteracarneparrillera.com"/>
    <meta property="og:image" content="http://fronteracarneparrillera.com/images/ogmage.jpg"/>
    <meta property="og:site_name" content="Frontera"/>
	<meta property="og:description" content="Para ti que siempre piensas en carne y en parrilladas. ¿Ya probaste Frontera?"/>
	
	
	
	
    <!-- Favicon -->
    <link rel="icon" href="images/favicon.ico?v=2">
	
	
    <!-- Animate -->
    <link rel="stylesheet" href="css/animate.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <!-- Cube Portfolio -->
    <link rel="stylesheet" href="css/cubeportfolio.min.css">
    <!-- Fancy Box -->
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <!-- REVOLUTION STYLE SHEETS -->
    <link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css">
    <link rel="stylesheet" type="text/css" href="rs-plugin/css/navigation.css">
    <!-- Style Sheet -->
    <link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/font-style-1.css">
	<link rel="stylesheet" href="vendor/badger-accordion-master/badger-accordion-demo.css">
	<link rel="stylesheet" href="vendor/badger-accordion-master/badger-accordion.css">
	<link rel="stylesheet" href="css/arma-tu-parrillada.css">
	<link rel="stylesheet" href="vendor/bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.css?v=1">
	
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-127051431-6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-127051431-6');
</script>

	
	<!-- Facebook Pixel Code -->
<script>
 !function(f,b,e,v,n,t,s)
 {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
 n.callMethod.apply(n,arguments):n.queue.push(arguments)};
 if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
 n.queue=[];t=b.createElement(e);t.async=!0;
 t.src=v;s=b.getElementsByTagName(e)[0];
 s.parentNode.insertBefore(t,s)}(window, document,'script',
 'https://connect.facebook.net/en_US/fbevents.js');
 fbq('init', '340815089886393');
 fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
 src="https://www.facebook.com/tr?id=340815089886393&ev=PageView&noscript=1(44 B)
https://www.facebook.com/tr?id=340815089886393&ev=PageView&noscript=1
"
/></noscript>
	
	

</head>
<body data-spy="scroll" data-target=".navbar" data-offset="90" style="background-color: black;">

<!--Loader Start-->
<div class="loader" style="display: flex; background-image: url(images/backLoader.jpg); background-size: cover; background-color: black;">
	
	
	<img src="images/loader8.gif" class="img-fluid" style="margin: auto; display: block; margin:auto;"/>
	
    <div class="loader-inner" style="padding-top: 250px;">
       <img src="images/loaderv3.gif"/>
    </div>
</div>
	
<div class="loader2" style="display: none">
    <div class="loader-inner">
        <div class="loader-blocks">
            <span class="block-1"></span>
            <span class="block-2"></span>
            <span class="block-3"></span>
            <span class="block-4"></span>
            <span class="block-5"></span>
            <span class="block-6"></span>
            <span class="block-7"></span>
            <span class="block-8"></span>
            <span class="block-9"></span>
            <span class="block-10"></span>
            <span class="block-11"></span>
            <span class="block-12"></span>
            <span class="block-13"></span>
            <span class="block-14"></span>
            <span class="block-15"></span>
            <span class="block-16"></span>
        </div>
    </div>
</div>
<!--Loader End-->

<!--Header Start-->
<header class="cursor-light">

    <!--Navigation-->
    <nav class="navbar navbar-top-default navbar-expand-lg navbar-gradient nav-icon">
        <div class="container">
            <!--<a href="javascript:void(0)" title="Logo" class="logo link scroll">
                Logo Default
                <img src="images/logo-white.png" alt="logo" class="logo-dark default">
            </a>-->

            <!--Nav Links-->
            <div class="collapse navbar-collapse" id="wexim">
                <div class="navbar-nav ml-auto">
                        <a class="nav-link link scroll menuNav" href="#productos">PRODUCTOS</a>
                        <a class="nav-link link scroll menuNav" href="#tips">TIPS Y <br>RECETAS</a>
                        <a class="nav-link link scroll menuNav" href="#arma">ARMA TU <br>PARRILLADA</a>
                        <a class="nav-link link scroll menuNav" href="#venta">PUNTOS DE <br>VENTA</a>
                        <a class="nav-link link scroll menuNav" href="#contacto">CONTACTO</a>
                    <span class="menu-line"><i class="fa fa-angle-down" aria-hidden="true"></i></span>
                </div>
            </div>

            <!--Side Menu Button-->
            <a href="javascript:void(0)" class="d-inline-block parallax-btn sidemenu_btn d-lg-none" id="sidemenu_toggle">
                <div class="animated-wrap sidemenu_btn_inner">
                <div class="animated-element">
                        <span></span>
                        <span></span>
                        <span></span>
                </div>
                </div>
            </a>

        </div>
    </nav>

    <!--Side Nav-->
    <div class="side-menu">
        <div class="inner-wrapper">
            <span class="btn-close link" id="btn_sideNavClose"></span>
            <nav class="side-nav w-100">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link link scroll" href="#productos">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link scroll" href="#tips">Tips y Recetas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link scroll" href="#arma">Arma tu parrillada</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link scroll" href="#contacto">Puntos de venta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link scroll" href="#contacto">Contacto</a>
                    </li>
                   
                </ul>
            </nav>
			
			<div class="footer-social">
                    <ul class="list-unstyled" style="float: left;">
                        <li class="noStyle">
							<a class="wow fadeInUp" href="https://www.facebook.com/fronteracarneparrillera/" style="width: 50px; font-size: 50px;" target="_blank">
								<i class="fa fa-facebook" aria-hidden="true"></i></a>
						</li>
                    </ul>
					
					<p style="text-align: left; line-height: 1.3; padding-top: 10px; font-family: quadonMedium; font-size: 20px; font-weight: normal;">
						<span style="font-size: 16px;">¡Síguenos en Facebook!</span><br>
						Frontera Carne Parrillera
				</p>
				<p class="text-white">&copy; 2019 Granjas RYC.</p>
                </div>

        </div>
    </div>
    <a id="close_side_menu" href="javascript:void(0);"></a>
    <!-- End side menu -->
	
	<style>
	
	ul {
  		list-style: none;
		list-style-position: outside;
	}

	ul li::before {
	  content: "\2022";
	  color: #B32524;
	  font-weight: bold;
	  display: inline-block; 
	  width: 1.2em;
	}
		
	</style>

</header>
<!--Header end-->

<!--slider-->
<section id="home" class="cursor-light p-0">
    <div id="rev_slider_19_1_wrapper" class="rev_slider_wrapper fullscreen-container" data-alias="wexim_slider_01" data-source="gallery" style="background:transparent;padding:0px;">
        <div id="rev_slider_19_1" class="rev_slider fullscreenbanner" style="display:none;" data-version="5.4.8.1">
            <ul>
            

				 <!-- SLIDE  -->
                <li data-index="rs-2" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="on"  data-easein="default" data-easeout="default" data-masterspeed="default"  data-thumb="images/slide-img1.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Slide" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
					
					
					<img src="images/dummy.png" data-lazyload="images/slider-image7.jpg"  alt=""  data-bgposition="center 50%" data-bgfit="cover" data-bgrepeat="no-repeat" data-bgparallax="2" class="rev-slidebg" data-no-retina>
					
						<!-- pleca superior con logo -->
					<div class="tp-caption tp-resizeme d-none d-xl-block"
                         id="slide-91-layer-0"
						  
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['0','0','0','0','0']"
                         data-y="['top','top','top','top','top']" 
						 data-voffset="['0','0','0','0','0']"
						 
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
						 data-visibility="['on', 'on', 'off', 'off', 'off']"

						
						 data-basealign="slide"
						 data-responsive_offset="off"
						  
						 data-frames='[{"delay": 0, "speed": 300, "from": "opacity: 1", "to": "opacity: 1"},{"delay": "wait", "speed": 300, "to": "opacity: 1"}]' 
						 style="z-index: 3;"
						 >
                        <img src="images/pleca5.png" style="min-width: 1920px; min-height: 220px;" alt=""></div>
					
					 <!-- pleca superior sin logo-->
					<div class="tp-caption d-xl-none"
                         id="slide-91-layer-1"
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['-1','-1','-500','-500','-800']"
                         data-y="['top','top','top','top','top']" 
						 data-voffset="['-1','-1','-1','-1','-1']"
						  
						 data-visibility="['off','off', 'on', 'on', 'on']"
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
                         data-whitespace="normal"

                         data-basealign="slide"
						 data-responsive_offset="off"
						 style="z-index: 3;"

                         data-frames='[{"delay": 500, "speed": 300, "from": "opacity: 0", "to": "opacity: 1"},{"delay": "wait", "speed": 300, "to": "opacity: 0"}]'>
                         <img src="images/pleca0.png" alt="" data-ww="['1920px','1920px','1920px','1920px']" data-hh="['312px','312px','312px','312px']" ></div>
					
					 <!-- pleca inferior mini-->
					<div class="tp-caption"
                        id="slide-91-layer-2"
                        data-x="['left','left','left','left','left']" 
						data-hoffset="['0','0','0','0',0]"
                        data-y="['bottom','bottom','bottom','bottom','bottom']" 
						data-voffset="['-1','-1','-1','-1','-1']"
						 
                        data-width="['auto','auto','auto','auto','auto']"
                        data-height="['auto','auto','auto','auto','auto']"
						data-visibility="['on','on', 'on', 'on', 'on']"
						data-basealign="slide"
						data-responsive_offset="on"
						style="z-index: 4;"
                        data-frames='[{"delay": 0, "speed": 300, "from": "opacity: 1", "to": "opacity: 1"}, 
                      {"delay": "wait", "speed": 300, "to": "opacity: 1"}]' 
						 
						 >
                        <img src="images/pleca4_mini.png" style="min-width: 1920px; min-height: 8px;" alt=""></div>
					
					
					  <!-- fondo img blur-->
                    <div class="tp-caption d-xl-none"
                         id="slide-91-layer-3"
                         data-x="['center','center','center','center','center']" 
						 data-hoffset="['0','0','-200','-200','-200']"
                         data-y="['top','top','top','top','middle']" 
						 data-voffset="['0','0','-450','-160','-160']"
						 
                         data-width="['1920','1920','1920','1920','1920']"
                         data-height="['1280','1280','1280','1280','1280']"
						 data-visibility="['off', 'off', 'on', 'on', 'on']"
                         data-whitespace="normal"

                         data-type="image"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1810,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.8;sY:0.8;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"
                         style="z-index: 1;">
						
                        <div class="rs-looped rs-pulse" data-zoomstart="1.05" data-zoomend="1" data-speed="5" data-easing="Default">
							<img src="images/slider-image4.jpg" alt="" style="-webkit-filter: blur(4px);"> </div></div>
					
					
					<!-- logo central unidos -->
                    <div class="tp-caption rs-parallaxlevel-2"
                         id="slide-91-layer-5"
                         data-x="['center','center','center','center','center']" 
						 data-hoffset="['0','0','0','0','0']"
                         data-y="['middle','middle','middle','middle','middle']" 
						 data-voffset="['0','0','0','0','0']"
						 
						 data-visibility="['on','on', 'on', 'on', 'on']"
                         data-width="['390','390','390','390','390']"
                         data-height="['201','201','201','201','201']"
                         data-whitespace="normal"

                         data-type="image"
                         data-responsive_offset="on"

                         data-frames='[{"delay":300,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.8;sY:0.8;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"
                         style="z-index: 10">
						
						   <div class="rs-looped rs-pulse" data-zoomstart="0.9" data-zoomend="1" data-speed="5" data-easing="Default">
							  <img src="images/unidos.png" data-rjs="images/unidos.png"></div>
					</div>
					
					
					<!-- logo central mini -->
                    <div class="tp-caption"
                         id="slide-91-layer-5"
                         data-x="['center','center','center','center','center']" 
						 data-hoffset="['0','0','0','0','0']"
                         data-y="['middle','middle','middle','middle','middle']" 
						 data-voffset="['0','0','0','0','0']"
						 
						 data-visibility="['off','off', 'on', 'on', 'on']"
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
                         data-whitespace="normal"

                         data-type="image"
                         data-responsive_offset="on"

                         data-frames='[{"delay":300,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.8;sY:0.8;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"
                         style="z-index: 10">
                        <img src="images/logo-white.png" style="min-width: 268px; min-height: 168px;"></div>
  

				 	


                </li>
				
				    <!-- SLIDE  -->
                <li data-index="rs-1" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0" data-hideslideonmobile="off"  data-easein="default" data-easeout="default" data-masterspeed="default"  data-thumb="images/slide-img1.jpg"  data-rotate="0"  data-saveperformance="off"  data-title="Slide" data-param1="" data-param2="" data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8="" data-param9="" data-param10="" data-description="">
                    <!-- MAIN IMAGE -->
                    <img src="images/slider-image2.jpg"  alt=""  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                    <!-- LAYERS -->
					
					<!-- pleca superior con logo -->
					<div class="tp-caption tp-resizeme d-none d-xl-block"
                         id="slide-91-layer-0"
						  
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['0','0','0','0','0']"
                         data-y="['top','top','top','top','top']" 
						 data-voffset="['0','0','0','0','0']"
						 
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
						 data-visibility="['on', 'on', 'off', 'off', 'off']"

						
						 data-basealign="slide"
						 data-responsive_offset="off"
						  
						 data-frames='[{"delay": 0, "speed": 300, "from": "opacity: 1", "to": "opacity: 1"},{"delay": "wait", "speed": 300, "to": "opacity: 1"}]' 
						 style="z-index: 3;"
						 >
                        <img src="images/pleca5.png" style="min-width: 1920px; min-height: 220px;" alt=""></div>
					
					 <!-- pleca superior sin logo-->
					<div class="tp-caption d-xl-none"
                         id="slide-91-layer-1"
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['-1','-1','-500','-500','-800']"
                         data-y="['top','top','top','top','top']" 
						 data-voffset="['-1','-1','-1','-1','-1']"
						  
						 data-visibility="['off','off', 'on', 'on', 'on']"
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
                         data-whitespace="normal"

                         data-basealign="slide"
						 data-responsive_offset="off"
						 style="z-index: 3;"

                         data-frames='[{"delay": 500, "speed": 300, "from": "opacity: 0", "to": "opacity: 1"},{"delay": "wait", "speed": 300, "to": "opacity: 0"}]'>
                         <img src="images/pleca0.png" alt="" data-ww="['1920px','1920px','1920px','1920px']" data-hh="['312px','312px','312px','312px']" ></div>
					
					 <!-- pleca inferior mini-->
					<div class="tp-caption"
                        id="slide-91-layer-2"
                        data-x="['left','left','left','left','left']" 
						data-hoffset="['0','0','0','0',0]"
                        data-y="['bottom','bottom','bottom','bottom','bottom']" 
						data-voffset="['-1','-1','-1','-1','-1']"
						 
                        data-width="['auto','auto','auto','auto','auto']"
                        data-height="['auto','auto','auto','auto','auto']"
						data-visibility="['on','on', 'on', 'on', 'on']"
						data-basealign="slide"
						data-responsive_offset="on"
						style="z-index: 4;"
                        data-frames='[{"delay": 0, "speed": 300, "from": "opacity: 1", "to": "opacity: 1"}, 
                      {"delay": "wait", "speed": 300, "to": "opacity: 1"}]' 
						 
						 >
                        <img src="images/pleca4_mini.png" alt="" style="min-width: 1920px; min-height: 8px;"></div>
					
					  <!-- fondo img blur-->
                    <div class="tp-caption d-xl-none"
                         id="slide-91-layer-3"
                         data-x="['center','center','center','center','center']" 
						 data-hoffset="['0','0','-200','-200','-200']"
                         data-y="['top','top','top','top','middle']" 
						 data-voffset="['0','0','-450','-160','-160']"
						 
                         data-width="['1920','1920','1920','1920','1920']"
                         data-height="['1280','1280','1280','1280','1280']"
						 data-visibility="['off', 'off', 'on', 'on', 'on']"
                         data-whitespace="normal"

                         data-type="image"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1810,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.8;sY:0.8;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"
                         style="z-index: 1;">
						
                        <div class="rs-looped rs-pulse" data-zoomstart="1.05" data-zoomend="1" data-speed="5" data-easing="Default">
							<img src="images/dummy.png" data-lazyload="images/slider-image4.jpg" alt="" style="-webkit-filter: blur(4px);"> </div></div>
					
					 <!-- fondo img HD
                    <div class="tp-caption rs-parallaxlevel-2 d-none d-xl-block"
                         id="slide-91-layer-4"
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['50','0','0','0','0']"
                         data-y="['top','top','top','top','top']" 
						 data-voffset="['-250','-280','0','0','0']"
						 
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
						 data-visibility="['on','on', 'off', 'off', 'off']"

                         data-type="image"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1810,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.8;sY:0.8;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"

                         style="z-index: 2;">
						
                        <div class="rs-looped rs-pulse" data-zoomstart="1.05" data-zoomend="1.1" data-speed="5" data-easing="Default">
							<img src="images/dummy.png" data-lazyload="images/slider-image3.jpg" alt=""> 
						
						</div>
					</div>-->
					
					<!-- logo central mini -->
                    <div class="tp-caption"
                         id="slide-91-layer-5"
                         data-x="['center','center','center','center','center']" 
						 data-hoffset="['0','0','0','0','0']"
                         data-y="['middle','middle','middle','middle','middle']" 
						 data-voffset="['0','0','0','0','0']"
						 
						 data-visibility="['off','off', 'on', 'on', 'on']"
                         data-width="['auto','auto','auto','auto','auto']"
                         data-height="['auto','auto','auto','auto','auto']"
                         data-whitespace="normal"

                         data-type="image"
                         data-responsive_offset="on"

                         data-frames='[{"delay":300,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.8;sY:0.8;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"
                         style="z-index: 10">
                        <img src="images/dummy.png" data-lazyload="images/logo-white.png" style="min-width: 268px; min-height: 168px;"></div>
  
					<!-- texto 1 -->
                    <div class="tp-caption tp-resizeme d-none d-xl-block rs-parallaxlevel-2"
                         id="slide-91-layer-6"
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['180','100','0','0','0']"
                         data-y="['middle','middle','middle','middle','middle']" 
						 data-voffset="['-55','0','0','0','0']"
                         data-fontsize="['60','60','60','60','35']"
						 data-visibility="['on', 'on', 'off', 'off', 'off']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":720,"split":"chars","splitdelay":0.1,"speed":900,"split_direction":"forward","frame":"0","from":"sX:0.8;sY:0.8;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','inherit','inherit','inherit']"
                         data-paddingtop="[0,0,0,0,0]"
                         data-paddingright="[0,0,0,0,0]"
                         data-paddingbottom="[0,0,0,0,0]"
                         data-paddingleft="[0,0,0,0,0]"

                         style="z-index: 8; white-space: nowrap; font-family: quadonNormal; font-size: 60px; font-weight: bold; font-style: italic; color: #ffffff;">¡NUEVA</div>

                    <!-- texto 2 -->
                    <div class="tp-caption  tp-resizeme d-none d-xl-block rs-parallaxlevel-2"
                         id="slide-91-layer-7"
                         data-x="['left','left','left','left','left']" 
						 data-hoffset="['170','100','0','0','0']"
                         data-y="['middle','middle','middle','middle','middle']" 
						 data-voffset="['15','70','0','0','0']"
                         data-fontsize="['100','100','100','100','100']"
						 data-visibility="['on','on', 'off', 'off', 'off']"
                         data-width="none"
                         data-height="none"
                         data-whitespace="nowrap"

                         data-type="text"
                         data-responsive_offset="on"

                         data-frames='[{"delay":1290,"speed":900,"frame":"0","from":"z:0;rX:0;rY:0;rZ:0;sX:0.9;sY:0.9;skX:0;skY:0;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":900,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                         data-textAlign="['inherit','inherit','center','center']"
                         data-paddingtop="[0,0,0,0]"
                         data-paddingright="[0,0,0,0]"
                         data-paddingbottom="[0,0,0,0]"
                         data-paddingleft="[0,0,0,0]"

                         style="z-index: 9; white-space: nowrap; font-family: quadonNormal; font-size: 150px; font-weight: bold; color: #ffffff;">IMAGEN! </div>
				
				 	<!-- pleca inferior 
                    <div class="tp-caption tp-resizeme d-none d-xl-block"
                        id="slide-91-layer-8"
						 
                        data-x="['left','left','left','left','left']" 
						data-hoffset="['-1','-1','-1','-1','-1']"
                        data-y="['bottom','bottom','bottom','bottom','bottom']" 
						data-voffset="['-10','-1','-1','-1','-1']"
						data-visibility="['on','off', 'off', 'off', 'off']"

						data-basealign="slide"
						data-responsive_offset="off"
						style="z-index: 4;"

						data-frames='[{"delay": 300, "speed": 300, "from": "opacity: 0", "to": "opacity: 1"}, 
                      {"delay": "wait", "speed": 300, "to": "opacity: 0"}]' 
						 
						 >
                        <img src="images/pleca1_2.png" alt="" style="min-width: 1915px !important;"></div>-->

                </li>
				
				
           
            </ul>
    </div>
    <!-- END REVOLUTION SLIDER -->
    </div>

    <!--slider social-->
    <div class="slider-social">
        <ul class="list-unstyled">
            <li class="animated-wrap noStyle">
				<a class="animated-element" href="https://www.facebook.com/fronteracarneparrillera/" target="_blank" style="background-color: #3b5998; min-height: 50px; min-width: 50px; border-radius: 30px;">
				<i class="fa fa-facebook" aria-hidden="true" style="font-size: 30px; margin-top: 9px; margin-left: 0px;"></i>
				</a>
			</li>

        </ul>
    </div>

    <!--scroll down-->
    <a href="#productos" class="scroll-down link scroll"><i class="fa fa-long-arrow-down"></i></a>

</section>
<!--slider end-->

	
<div style="background-image: url(images/back1_2.jpg); background-repeat: repeat; background-size: 100%;">
	
		<!--productos-->
		<section class="d-none d-sm-block wow slideInRight" id="productos" style="padding-top: 40px; padding-bottom: 0px;">
			<div class="container">
				<img src="images/titulo1.png" alt="image" style="margin: auto; display: block; margin-bottom: 40px;">
					  <div id="product_slider" class="owl-carousel">
							<div class="item">
									<img class="owl-lazy" data-src="images/items/p1.png" alt="image">
							 </div>
							<div class="item">
									<img class="owl-lazy" data-src="images/items/p2.png?v=2" alt="image">
							 </div>
							<div class="item">
									<img class="owl-lazy" data-src="images/items/p3.png" alt="image">
							 </div>
						  <div class="item">
									<img class="owl-lazy" data-src="images/items/p4.png" alt="image">
							 </div>
						  <div class="item">
									<img class="owl-lazy" data-src="images/items/p5.png" alt="image">
						  </div>
						   <div class="item">
									<img class="owl-lazy" data-src="images/items/p6.png" alt="image">
						  </div>
						    <!--
						   <div class="item">
									<img class="owl-lazy" data-src="images/items/p7.png" alt="image">
						  </div>
							-->
						</div>
			</div>
		</section>
		<!--productos End-->
	
		<!--productos mobile-->
		<section class="d-block d-sm-none slideInRight" id="productos" style="padding-top: 40px; padding-bottom: 0px;">
			<div class="container">
				<img src="images/titulo1.png" alt="image" style="margin: auto; display: block; margin-bottom: 40px;">
					  <div id="product_slider_mobile" class="owl-carousel">
							<div class="item">
									<img class="owl-lazy" data-src="images/items/p1_v.png" alt="image">
							 </div>
							<div class="item">
									<img class="owl-lazy" data-src="images/items/p2_v.png?v=2" alt="image">
							 </div>
							<div class="item">
									<img class="owl-lazy" data-src="images/items/p3_v.png" alt="image">
							 </div>
						  <div class="item">
									<img class="owl-lazy" data-src="images/items/p4_v.png" alt="image">
							 </div>
						  <div class="item">
									<img class="owl-lazy" data-src="images/items/p5_v.png" alt="image">
						  </div>
						   <div class="item">
									<img class="owl-lazy" data-src="images/items/p6_v.png" alt="image">
						  </div>
						  <!--
						   <div class="item">
									<img class="owl-lazy" data-src="images/items/p7_v.png" alt="image">
						  </div>-->
						</div>
			</div>
		</section>
		<!--productos End-->

		<!--tips Start-->
		<section class="p-0" id="tips" style="margin-top: 40px;">
			<div class="container">
				<div class="row">
					<!--testimonial-->
					<div class="col-md-12 wow fadeInLeft">
					   <img src="images/titulo2.png" alt="image" style="margin: auto; display: block;  margin-bottom: 40px;" class="img-fluid">
						
						<div id="tip_slider" class="owl-carousel">
							<div class="item">
									<a class="modalProds" rel="fancybox-button" href="images/tips/tip1.jpg" title="Arrachera marinada">
										<img class="owl-lazy" data-src="images/tips/thumb1.png" data-src-retina="images/tips/thumb1@2x.png" alt="image">
									</a>
							 </div>
							<div class="item">
									<a class="modalProds" rel="fancybox-button" href="images/tips/tip2.jpg" title="Arrachera marinada">
										<img class="owl-lazy" data-src="images/tips/thumb2.png" data-src-retina="images/tips/thumb2@2x.png" alt="image">
									</a>
							 </div>
							<div class="item">
									<a class="modalProds" rel="fancybox-button" href="images/tips/tip3.jpg" title="Arrachera marinada">
										<img class="owl-lazy" data-src="images/tips/thumb3.png" data-src-retina="images/tips/thumb3@2x.png" alt="image">
									</a>
							 </div>
							
							<div class="item">
									<a class="modalProds" rel="fancybox-button" href="images/tips/tip4.jpg" title="Arrachera marinada">
										<img class="owl-lazy" data-src="images/tips/thumb4.png" data-src-retina="images/tips/thumb4@2x.png" alt="image">
									</a>
							 </div>
							
							<div class="item">
									<a class="modalProds" rel="fancybox-button" href="images/tips/tip5.jpg" title="Arrachera marinada">
										<img class="owl-lazy" data-src="images/tips/thumb5.png" data-src-retina="images/tips/thumb5@2x.png" alt="image">
									</a>
							 </div>
						
						</div>

						

					</div>

				
				</div>
					
				<div class="row">

					 <div class="col-md-6 d-none d-sm-block" style="margin-top: 150px;">
						 
						<div id="resReceta1" class="receta wow slideInLeft" style="border: 3px #FFF solid;">
							 <div>
								  <img src="images/recipes/res1.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								
								 <!--
								 <img src="images/recipes/res1.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								-->
								 
								 <h4 class="tituloReceta">Hamburguesa Carnívoro</h4>
								  <h5 class="subtituloReceta">4 piezas</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
								 <img src="images/recipes/pepito.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">

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
								 <img src="images/recipes/alambre.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								 <h4 class="tituloReceta">ALAMBRE DE ARRACHERA</h4>
								  <h5 class="subtituloReceta">5 personas</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
												 <img src="images/recipes/tacos.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">

												 <h4 class="tituloReceta">TACOS VILLAMELÓN</h4>
												  <h5 class="subtituloReceta">10 tacos</h5>
												  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">

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
								  <img src="images/recipes/cerdo1.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								 <h4 class="tituloReceta">AGUACHILE DE CECINA</h4>
								 <h5 class="subtituloReceta">10 porciones</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
								<img src="images/recipes/tecolota.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								 <h4 class="tituloReceta">TECOLOTA DE CECINA A LA PARRILLA</h4>
								 <h5 class="subtituloReceta">4 tortas</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
								 <img src="images/recipes/cerdo3.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">

								 <h4 class="tituloReceta">EMPALMES NORTEÑOS DE ADOBADA</h4>
								 <h5 class="subtituloReceta">6 porciones</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
								<img src="images/recipes/cerdo4.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								 <h4 class="tituloReceta">HUARACHE DE CECINA ENCHILADA</h4>
								 <h5 class="subtituloReceta">4 porciones</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
								<img src="images/recipes/cerdo5.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								 <h4 class="tituloReceta">MOLCAJETE DE LONGANIZA Y SALSA TATEMADA</h4>
								 <h5 class="subtituloReceta">4 porciones</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
								<img src="images/recipes/cerdo6.jpg" alt="image" style="margin: auto; display: block;" class="img-fluid">
								 <h4 class="tituloReceta">TACOS DE CHORIZO POBLANO EN TORTILLA AZUL</h4>
								 <h5 class="subtituloReceta">4 porciones</h5>
								  <img src="images/recipes/dots.jpg" alt="image" style="margin: auto; display: block; margin-bottom: 15px;">
								 
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
	
					</div>

					 <div class="col-md-6  wow slideInRight" style="margin-top: 100px; text-align: center;">
						<img src="images/titulo3.png" alt="image" style="margin: auto; display: block; padding-bottom: 50px;" class="img-fluid">
						 
						 <div class="img-container" style="margin:auto;">
							 <div class="row positioning" style="padding-top: 15px; padding-left: 15px; padding-right: 15px;">
								  <div class="col-md-6 col-6" style="padding-left: 0px; padding-right: 0px;">
										<img id="logoRes" src="images/res1.png" class="img-fluid recetaPointer" onClick="selecRecipeType('recetaRes');" />
								 </div>
								 
								  <div class="col-md-6 col-6" style="padding-left: 0px; padding-right: 0px;">
										<img id="logoCerdo" src="images/cerdo1.png" class="img-fluid recetaPointer" onClick="selecRecipeType('recetaCerdo');"/>
								 </div>
								 
								 <!--
								 <div class="col-md-3 col-3" style="padding-left: 0px; padding-right: 0px;">
										<img id="logoPollo" src="images/pollo1.png" class="img-fluid recetaPointer" onClick="selecRecipeType('recetaPollo');"/>								
								 </div>
								 
								 
								  <div class="col-md-3 col-3" style="padding-left: 0px; padding-right: 0px;">
										<img id="logoEmbutido" src="images/embutidos1.png" class="img-fluid recetaPointer" onClick="selecRecipeType('recetaEmbutidos');"/>								
								 </div>
								 -->
								 
								 
							 </div>
							
						
							<img src="images/carnes.png" />
						</div>


						 <div id="recetaRes" class="recetasClass">
								<!--<img src="images/carnes.png" alt="image" style="margin: auto; display: block;" class="img-fluid">-->
								<p class="txtRecetas">
									<span id="txtresReceta1" class="subcontent recetaPointer" onClick="selectRecipe('resReceta1');">Hamburguesa carnívoro</span><br><br>
									<span class="subcontent _1977recetaPo" onClick="selectRecipe('cerdoReceta2');">Tecolota de cecina a la parrilla</span><br><br>
									<span id="txtresReceta2" class="subcontent recetaPointer" onClick="selectRecipe('cerdoReceta1');">Aguachile de cecina</span><br><br>
									<span class="subcontent recetaPointer" onClick="selectRecipe('resReceta2');">Pepito "empapelado" de arrachera y 3 quesos</span><br><br>
									<span class="subcontent recetaPointer" onClick="selectRecipe('resReceta3');">Alambre de arrachera</span><br><br>
									<span class="subcontent recetaPointer" onClick="selectRecipe('resReceta4');">Tacos Villamelón</span><br><br>
								</p>
						 </div>
						 
						 <div id="recetaCerdo" class="recetasClass" style="display: none;">
							 <!--<img src="images/carnes.png" alt="image" style="margin: auto; display: block;" class="img-fluid">-->
								<p class="txtRecetas">
									<span class="subcontent recetaPointer" onClick="selectRecipe('cerdoReceta3');">Empalmes norteños de adobada</span><br><br>
									<span class="subcontent recetaPointer" onClick="selectRecipe('cerdoReceta4');">Huarache de cecina enchilada</span><br><br>
									<span class="subcontent recetaPointer" onClick="selectRecipe('cerdoReceta5');">Molcajete de longaniza y salsa tatemada</span><br><br>
									<span class="subcontent recetaPointer" onClick="selectRecipe('cerdoReceta6');">Tacos de chorizo poblano en tortilla azul</span><br><br>
								</p>
						 </div>
						 
						 <div id="recetaEmbutidos" class="recetasClass" style="display: none;">
							 <p class="txtRecetas">
									<span class="subcontent recetaPointer" onClick="selectRecipe('resReceta1');">Molcajete de longaniza y salsa tatemada</span><br><br>
								 <span class="subcontent recetaPointer" onClick="selectRecipe('resReceta1');">Costra de chistorra a las brasas</span><br><br>
								 <span class="subcontent recetaPointer" onClick="selectRecipe('resReceta1');">Choripan clásico</span><br><br>
								</p>
						 </div>
						 
						 
						  <div id="recetaPollo" class="recetasClass" style="display: none;">
							 <p class="txtRecetas">
									<span class="subcontent recetaPointer" onClick="selectRecipe('polloReceta1');">Burrito de pollo parrillero, queso chihuahua y frijoles puercos</span><br><br>
								 <span class="subcontent recetaPointer" onClick="selectRecipe('polloReceta1');">Pollo parrillero con pico de gallo de frijol y piña</span><br><br>
								 
								</p>
						 </div>
						



					</div>
				</div>
			</div>
		</section>
		<!--tips End-->

		<!--App Section-->
		<section id="arma">
			<div class="container">
				<!--Heading-->
				<div class="row">
					<div class="col-md-12 text-center wow fadeIn">
						<div class="title d-inline-block">
							<img src="images/titulo4.png" alt="image" style="margin: auto; display: block; margin-bottom: 20px" class="img-fluid tamLogo">
							<p style="font-size: 20px">
								<span style="font-weight: bold">¿Cómo empiezo?</span><br>
								Arma una deliciosa parrillada de forma rápida y sencilla.<br>
								<span style="font-weight: bold">Frontera</span> te dice cómo.</p>
						</div>
					</div>
					<div class="col-md-12 text-center wow fadeIn">
						<form id="formBbq" class="contact-form">
						<dl class="badger-accordion js-badger-accordion badger-accordion--initalised" style="margin-bottom: 100px;">
							<dt class="badger-accordion__header">
								<button type="button" class="badger-accordion__trigger js-badger-accordion-header" id="badger-accordion-header-172354" aria-controls="badger-accordion-panel-172354" aria-label="Open accordion panel" data-badger-accordion-header-id="0" aria-expanded="false">
									<div class="badger-accordion__trigger-title">
										<img alt="vineta" src="images/VinetasAcordeon.png" id="vinetaRegistro" class="imgTitle" />&nbsp;
										<label class="labelTitle">REGISTRO</label>
									</div>
									<div class="badger-accordion__trigger-icon">
									</div>
								</button>
							</dt>
							<dd class="badger-accordion__panel js-badger-accordion-panel -ba-is-hidden" id="badger-accordion-panel-172354" aria-labeledby="badger-accordion-header-172354">
								<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner">
									<div id="registroUsuario">
										<p style="text-align: left; padding-bottom: 15px;; font-size: 20px">
											Identifícate como el anfitrión de tu parrillada:
										</p>
										<div class="row" id="botonesRegistro">
											<div class="col-md-2">&nbsp;</div>
											<div class="col-md-4">
												<a class="btn social-link facebook-link margenInferior" style="font-size: 16px; background-image: url('images/botonFace.png'); text-transform: inherit;" href="<?php echo $loginUrl; ?>">Continuar con <i class="fa fa-facebook"></i></a>
											</div>
											<div class="col-md-4">
												<a class="btn social-link" style="font-size: 16px; background-image: url('images/botonRegistro.png'); text-transform: inherit;" href="javascript:$('#formRegistro').show(); $('#botonesRegistro').hide(); acordeon.close(0); acordeon.open(0);">Regístrate con email</a>
											</div>
											<div class="col-md-2">&nbsp;</div>
										</div>
										<div class="row" id="formRegistro">
											<div class="col-md-12">
												<input type="text" class="form-control" placeholder="Nombre" name="user_name" id="user_name" value="" />
												<input type="email" class="form-control" placeholder="Correo" name="user_email" id="user_email" value="" />
				<!--
												<input type="password" class="form-control" placeholder="Contraseña" name="user_pass_1" id="user_pass_1" value="" />
												<input type="password" class="form-control" placeholder="Repetir contraseña" name="user_pass_2" id="user_pass_2" value="" />
				-->
												<label style="display: inline-block; text-align: left; color: #a5a5a5" class="form-control">
													Sexo:
													<input type="radio" name="user_gender" id="user_genderM" class="form-control" value="M" style="display:inline-block; width: 6%; height: 20px"><span style="display:inline-block;">Mujer</span>
													<input type="radio" name="user_gender" id="user_genderH" class="form-control" value="H" style="display:inline-block; width: 6%; height: 20px"><span style="display:inline-block;">Hombre</span>
												</label>
											</div>
											<div class="col-md-12">
												<a class="btn social-link pull-left" style="font-size: 16px; width: 20%" href="javascript:$('#formRegistro').hide(); $('#botonesRegistro').show(); acordeon.close(0); acordeon.open(0);">Atrás</a>
												<a class="btn social-link pull-right" id="continuar1" style="font-size: 16px; background-image: url('images/botonRegistro.png');" href="javascript:registrarUsuario()">Continuar</a>
											</div>
										</div>
									</div>
								</div>
							</dd>
							<dt class="badger-accordion__header">
								<button type="button" class="badger-accordion__trigger js-badger-accordion-header" id="badger-accordion-header-704277" aria-controls="badger-accordion-panel-704277" aria-label="Open accordion panel" data-badger-accordion-header-id="1" aria-expanded="false">
									<div class="badger-accordion__trigger-title">
										<img alt="vineta" src="images/VinetaNombre.png" id="vinetaNombre" class="imgTitle" />&nbsp;
										<label class="labelTitle">PONLE NOMBRE Y FECHA A TU PARRILLADA</label>
									</div>
									<div class="badger-accordion__trigger-icon">
									</div>
								</button>
							</dt>
							<dd class="badger-accordion__panel js-badger-accordion-panel -ba-is-hidden" id="badger-accordion-panel-704277" aria-labeledby="badger-accordion-header-704277">
								<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner">
									<div class="row">
										<div class="col-md-4">
											<input type="text" class="form-control" placeholder="Nombre (ej: Reyes del Asador)" name="bbq_name" id="bbq_name" value="" />
										</div>
										<div class="col-md-4">
											<input type="text" id="datetimepicker" readonly style="cursor:pointer" class="form-control" placeholder="Fecha" title="Fecha" name="datetimepicker" value="" />
										</div>
										<div class="col-md-4">
											<input type="text" class="form-control" readonly placeholder="Hora" title="Hora" name="bbq_time" id="bbq_time" />
										</div>
										<div class="col-md-12" style="padding-top: 15px;">
											<a class="btn social-link pull-right" id="continuar2" style="font-size: 16px; background-image: url('images/botonRegistro.png');" href="javascript:startBbq()">Continuar</a>
										</div>
									</div>
								</div>
							</dd>
							<dt class="badger-accordion__header">
								<button type="button" class="badger-accordion__trigger js-badger-accordion-header" id="badger-accordion-header-86703" aria-controls="badger-accordion-panel-86703" aria-label="Open accordion panel" data-badger-accordion-header-id="2" aria-expanded="false">
									<div class="badger-accordion__trigger-title">
										<img alt="vineta" src="images/VinetaInvitados.png" id="vinetaInvitados" class="imgTitle" />&nbsp;
										<label class="labelTitle">¿CUÁNTOS INVITADOS?</label>
									</div>
									<div class="badger-accordion__trigger-icon">
									</div>
								</button>
							</dt>
							<dd class="badger-accordion__panel js-badger-accordion-panel -ba-is-hidden" id="badger-accordion-panel-86703" aria-labeledby="badger-accordion-header-86703">
								<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner">
									<div class="row">
										<div class="col-md-6">
											<div id="panelIIzq" class="row">
												<div class="col-md-6 margenSmall" style="display: flex; align-items: center;">
													<img src="images/menosInvitados.png" style="padding-right: 8px; cursor: pointer" onclick="modifInvitados('Invitados', -1)">
													<label id="numInvitados" class="numInv">0</label>
													<img src="images/masInvitados.png" style="padding-left: 15px; cursor: pointer" onclick="modifInvitados('Invitados', 1)">
													<img src="images/Invitados0.png" id="imgInvitadosSmall" class="visibleSmall" alt="invitados" width="120" height="132" style="margin-top:12px; margin-left: 15px" />
												</div>
												<div class="col-md-6 invisibleSmall">
													<img src="images/Invitados0.png" id="imgInvitados" class="invisibleSmall" alt="invitados" width="120" height="132" style="margin-top:12px;" />
												</div>
												<div class="col-md-12" style="margin-top: 20px;">
													<label style="font-size: 26px">Parrilleros</label>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div id="panelIDer" class="row">
												<div class="col-md-6 margenSmall" style="display: flex; align-items: center;">
													<img src="images/menosInvitados.png" style="padding-right: 8px; cursor: pointer" onclick="modifInvitados('Invitadas', -1)">
													<label id="numInvitadas" class="numInv">0</label>
													<img src="images/masInvitados.png" style="padding-left: 15px; cursor: pointer" onclick="modifInvitados('Invitadas', 1)">
													<img src="images/Invitados0.png" id="imgInvitadasSmall" class="visibleSmall" alt="invitados" width="120" height="132" style="margin-top:12px; margin-left: 15px" />
												</div>
												<div class="col-md-6 invisibleSmall">
													<img src="images/Invitados0.png" id="imgInvitadas" class="invisibleSmall" alt="invitados" width="120" height="132" style="margin-top:12px" />
												</div>
												<div class="col-md-12" style="margin-top: 20px;">
													<label style="font-size: 26px">Parrilleras</label>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<a class="btn social-link pull-right" id="continuar3" style="font-size: 16px; background-image: url('images/botonRegistro.png');" href="javascript:calculateProd()">Continuar</a>
										</div>
									</div>
								</div>
							</dd>
							<dt class="badger-accordion__header">
								<button type="button" class="badger-accordion__trigger js-badger-accordion-header" aria-controls="badger-accordion-panel-86703" aria-label="Open accordion panel" data-badger-accordion-header-id="2" aria-expanded="false">
									<div class="badger-accordion__trigger-title">
										<img alt="vineta" src="images/VinetaProd.png" id="vinetaProd" class="imgTitle" />&nbsp;
										<label class="labelTitle">ELIGE DE NUESTROS PRODUCTOS</label>
									</div>
									<div class="badger-accordion__trigger-icon">
									</div>
								</button>
							</dt>
							<dd class="badger-accordion__panel js-badger-accordion-panel -ba-is-hidden" aria-labeledby="badger-accordion-header-86703">
								<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner">
									<div class="row">
										<div class="col-md-9">
											<p style="text-align: left; font-size: 20px">
												De acuerdo al número de invitados, te sugerimos de nuestros productos Frontera un consumo de:
											</p>
										</div>
										<div class="col-md-3" align="center">
											<div style="background-image: url('images/ResumenKg.png'); border-radius: 10px; padding: 10px; font-size: 30px; background-size: cover; width: 90%" id="kgProdSugerido"></div>	
										</div>
									</div>
									<div class="row" style="border-bottom: 1px dashed; height: 25px;">
										<div class="col-md-12">
										</div>
									</div>
									<p style="text-align: left; padding-bottom: 15px; padding-top: 25px; font-size: 20px">
										Personaliza tu parrillada y elige los productos que deseas:
									</p>
									<div class="row invisibleSmall" style="display: flex; align-items: flex-end; padding-bottom: 25px;">
										<?php
										$categorias = $db->rawQuery("SELECT * FROM CAT_CATEGORY_PRODUCT WHERE status = 1");
										$col = 12 / count($categorias);
										$col = round($col);
										$html_prod_cat = array();
//										$html_cat_responsive = "<div class='col-md-12'>";
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
											
											$html_prod_cat[$objCat->getId_category()] = '<div class="row" style="margin-top: 20px; padding: 15px 0px 0px 30px; border-top: 1px dashed"><div class="col-md-12">
												<img src="./images/'.strtolower($categorias[$i]["name"]).'4.png" style="height: 85%" />
												<label>'.$categorias[$i]["name"].'</label>
											</div></div>';
											
											for($j=0; $j<count($objCat->getProducts()); $j++) {
												$margen_sup = ' style="margin-top: 10px"';
//												if ($j==0) {
//													$margen_sup = ' style="margin-top: 25px"';
//												}
												$html_prod_cat[$objCat->getId_category()] .= '<div class="row">
													<div class="col-md-12"'.$margen_sup.'>
														<div style="width: 70%; display: inline-block; padding-left: 35px">
															'.$objCat->getProducts()[$j]->getName().'
															<br>
															<span style="font-size: 85%; margin-top: -10px">'.$objCat->getProducts()[$j]->getDescription().'</span>
                                                        </div>
                                                        <div style="width: 30%; display: inline-block; text-align:center;" class="pull-right">
                                                            <img src="images/menosInvitados.png" style="cursor:pointer; width: 14px" onclick="menosProductos('.$objCat->getProducts()[$j]->getId_product().')" />
                                                            <label style="text-align: center; font-size: 25px; color: #d9543b; width: 35%;" id="prod_quantity_'.$objCat->getProducts()[$j]->getId_product().'">0</label>
                                                            <img src="images/masInvitados.png" style="cursor:pointer; width: 14px" onclick="masProductos('.$objCat->getProducts()[$j]->getId_product().')" />
                                                        </div>
													</div>
												</div>';
											}
										}
										$html_cat_responsive .= "</div>";
										?>
									</div>
<!--
									<div class="row visibleSmall">
										<?php 
//											echo $html_cat_responsive; 
										?>
									</div>
-->
									<div class="row">
										<div class="col-md-7 margenSupSmall" style="overflow: auto; height: 340px; text-align: left">
											<?php
											if (count($html_prod_cat) > 0) {
												foreach($html_prod_cat as $id_category=>$productos) {
													echo '<div id="productos_'.$id_category.'">'.$productos.'</div>';
												}	
											}

											?>
										</div>
										<div class="col-md-5 margenSupSmall" style="padding-left: 30px;padding-right: 30px;">
											<div class="row" style="min-height: 30px;">
												<div class="col-md-12" style="background-image: url('images/PlecaResumen.png')">
													Productos seleccionados
												</div>
											</div>
											<div class="row">
												<div class="col-md-12" id="resumenProductos" style="background-image: url('images/FondoResumen.png');height: 315px;border-radius: 0px 0px 20px 20px; padding-top: 10px; background-color: #000000d9;">
													<div class="row">
														<div class="col-md-12" id="listaProd" style="text-align: left;overflow-y: scroll;height: 215px;">
														</div>
													</div>
													<div class="row">
														<div class="col-md-12">
															<small>Con la selección actual, llevas:</small>
														</div>
													</div>
													<div class="row">
														<div class="col-md-2 invisibleSmall">
															&nbsp;
														</div>
														<div class="col-md-8" style="background-image: url('images/ResumenKg.png'); border-radius: 10px; padding: 10px; font-size: 30px; background-size: cover" id="kgProd">
														</div>
														<div class="col-md-2">
															&nbsp;
														</div>										
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row" style="padding-top: 15px">
										<div class="col-md-8 margenNotaSmall" style="text-align: left">
											<small style="font-style: italic;">
												<p style="line-height: 20px;">
													* El número de productos por defecto indica la cantidad sugerida.<br>
    												* Los complementos no son cuantificables
												</p>
											</small>
										</div>
										<div class="col-md-4">
											<a class="btn social-link pull-center" id="continuar4" style="font-size: 16px; background-image: url('images/botonRegistro.png'); width:60%" href="javascript:saveBbq()">Continuar</a>
										</div>
									</div>
								</div>
							</dd>
							<dt class="badger-accordion__header">
								<button type="button" class="badger-accordion__trigger js-badger-accordion-header" id="badger-accordion-header-704277" aria-controls="badger-accordion-panel-704277" aria-label="Open accordion panel" data-badger-accordion-header-id="1" aria-expanded="false">
									<div class="badger-accordion__trigger-title">
										<img alt="vineta" src="images/VinetaAmigos.png" id="vinetaAmigos" class="imgTitle" />&nbsp;
										<label class="labelTitle">INVITA AMIGOS</label>
									</div>
									<div class="badger-accordion__trigger-icon">
									</div>
								</button>
							</dt>
							<dd class="badger-accordion__panel js-badger-accordion-panel -ba-is-hidden" id="badger-accordion-panel-704277" aria-labeledby="badger-accordion-header-704277">
								<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner">
									<p style="text-align: left; font-size: 20px">
										Ingresa los nombres y correos electrónicos de tus invitados:
									</p>
									<div class="row" style="height: 200px; overflow-y: scroll;">
										<div class="col-md-12" id="listaAmigos">

										</div>
									</div>
									<div class="row" style="padding-top: 15px">
										<input type="hidden" id="cantAmigos" name="cantAmigos" value="-1" />
										<div class="col-md-6">
											<a class="btn social-link pull-center widthBtnSmall" style="font-size: 16px; background-image: url('images/botonRegistro.png'); width:50%; text-transform: inherit;" href="javascript:moreFriends()">+ Amigos</a>
										</div>
										<div class="col-md-6">
											<a class="btn social-link pull-center widthBtnSmall margenSupSmall" id="continuar5" style="font-size: 16px; background-image: url('images/botonRegistro.png'); width:50%" href="javascript:saveFriends()">Continuar</a>
										</div>
									</div>
								</div>
							</dd>
							<dt class="badger-accordion__header">
								<button type="button" class="badger-accordion__trigger js-badger-accordion-header" id="badger-accordion-header-704277" aria-controls="badger-accordion-panel-704277" aria-label="Open accordion panel" data-badger-accordion-header-id="1" aria-expanded="false">
									<div class="badger-accordion__trigger-title">
										<img alt="vineta" src="images/VinetaUbic.png" id="vinetaUbic" class="imgTitle" />&nbsp;
										<label class="labelTitle">¿EN DÓNDE LA CARNITA ASADA?</label>
									</div>
									<div class="badger-accordion__trigger-icon">
									</div>
								</button>
							</dt>
							<dd class="badger-accordion__panel js-badger-accordion-panel -ba-is-hidden" id="badger-accordion-panel-704277" aria-labeledby="badger-accordion-header-704277">
								<div class="badger-accordion__panel-inner text-module js-badger-accordion-panel-inner">
									<p style="text-align: left; font-size: 20px; padding-bottom: 25px">
										Escribe la dirección donde será la parrillada y selecciona del listado que te aparezca la ubicación que más se acerque al lugar:
									</p>
									<div class="row">
										<div class="col-md-5">
				<!--							<textarea class="form-control" placeholder="Dirección" name="bbq_address" id="bbq_address" rows="3"></textarea>-->
											<input type="text" class="form-control" placeholder="Dirección" name="bbq_address" id="bbq_address" />
											<textarea class="form-control" placeholder="Mensaje" name="bbq_message" id="bbq_message" rows="3"></textarea>
											<a class="btn social-link pull-right invisibleSmall" id="continuar6" style="font-size: 16px; background-image: url('images/botonRegistro.png'); width: 40%; margin-top: 15px;" href="javascript:finishBbq()">Enviar</a>
										</div>
										<div class="col-md-7 paddingTopMapa">
											<div id="map-canvas" class="heightMapaSmall" style=" width:100%; height: 294px; margin: 0px; padding: 0px;"></div>
											<small>* Puedes arrastrar el pin resultante a tu ubicación exacta</small>
											<input type="hidden" id="latitude" value="0" />    
											<input type="hidden" id="longitude" value="0" />
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<a class="btn social-link pull-right visibleSmall" id="continuar6_2" style="font-size: 16px; background-image: url('images/botonRegistro.png'); width: 40%; margin-top: 15px;" href="javascript:finishBbq()">Enviar</a>
										</div>
									</div>
								</div>
							</dd>
						</dl>
						</form>
					</div>
					<div id="msjFelicidades" class="col-md-12 text-center" style="font-size: 50px;    line-height: 55px;    border: 1px solid;    border-radius: 30px; display: none; padding-top: 20px; padding-bottom: 20px;  background-color: #000000AA;">
						<span style="color:#da543b">¡Felicidades!</span><br>
						Tu parrillada está a punto de hacerse realidad
					</div>
				</div>
			</div>
		</section>
		<!--App Section End-->

		<!--Address Start-->
		<section id="venta" class="p-0">
			<div class="container" style="margin-bottom: 50px;">
				
				<div class="row align-items-center containerContact" style="background-color: rgba(0, 0, 0, 0.5);">
					<div class="col-md-12 p-0 wow fadeInLeft">
						<img src="images/titulo6.png" alt="image" style="margin: auto; display: block; padding-bottom: 50px;" class="img-fluid">
					</div>
					
					<div class="col-md-6 p-0 wow fadeInRight" style="min-height: 600px;">
						<div style="margin: auto; display: block; width: 90%;">
							<button type="button" class="btn btn-large btn-gradient btn-rounded" style="margin-bottom: 30px;" onClick="getLocation();"><i class="fa fa-map-marker" aria-hidden="true" style="font-size: 20px; margin-right: 10px;"></i> <span>Localizar tienda cerca de mí</span></button>
							<button type="button" class="btn btn-large btn-gradient btn-rounded" style="margin-bottom: 30px;" onClick="showSearch();"><i class="fa fa-search" aria-hidden="true" style="font-size: 20px; margin-right: 10px;" ></i> <span>Buscar por ciudad</span></button>
						</div>
						
						<!-- Cuadro de búsqueda por ciudad -->
						<div class="row" style="display: none;" id="divSearch">
							
							<div class="col-md-6" style="margin-bottom: 15px;">
								 <select name="selectEstado" id=selectEstado class="form-control">
									   <?php
											$estados = $db->rawQuery("SELECT CS.id_state, CS.name FROM CAT_STATE CS, TBL_STORE TS WHERE CS.id_state=TS.id_state GROUP BY CS.id_state ORDER BY CS.name");
									  		$ids_edos = array();
									  		echo'<option value="0" id="txtOpcSelec">Selecciona tu estado</option>';			
											for($i=0; $i < count($estados); $i++) 
											{
												echo'<option value='.$estados[$i]["id_state"].'>'.$estados[$i]["name"].'</option>';
												$ids_edos[] = "'".$estados[$i]["name"]."': ".$estados[$i]["id_state"];
											}
									  		$var_edos_js = "{".implode(",",$ids_edos)."}";
										?>
									  
									</select>
							</div>
							<div class="col-md-6" style="margin-bottom: 15px;">
								 <select name="selectCiudades" id=selectCiudades class="form-control" style="margin-bottom: 0px">
									   <?php
//											$ciudades = $db->get("CAT_CITY");
											echo'<option value="0" id="txtOpcSelec2">Seleccione su ciudad</option>';			
//											for($i=0; $i < count($ciudades); $i++) 
//											{
//												echo'<option value='.$ciudades[$i]["id_city"].'>'.$ciudades[$i]["name"].'</option>';			
//											}
										?>
									  
									</select>
							</div>
							<input type="hidden" id="id_state_selec" name="id_state_selec" value="0" />
							
						</div>
						<div id="map-ubicaciones" style=" height:400px;"></div>
						

					</div>
					
					
					
					<div class="col-md-6 p-0 wow fadeInRight" id="divBrands">		
						
						<img src="images/logos.png" alt="image" style="margin: auto; display: block; margin-bottom: 20px; padding-left: 50px; padding-right: 50px;" class="img-fluid" >
					</div>
					
					
					<div class="col-md-6 p-0" id="divStores" style="display: none; padding-left: 30px !important; padding-right: 30px !important; max-height: 500px; overflow-y: auto;">
						
					</div>
					
					
					
					
					
					<div class="col-md-12 p-0 wow fadeInLeft" style="min-height: 600px; margin-top: 150px;" id="contacto"> 
							
						 <!--contact us-->
							<form class="contact-form containerContactForm" method="post">
								
								<img src="images/titulo5.png" alt="image" style="margin: auto; display: block; padding-bottom: 50px;" class="img-fluid">
								<div class="row">

									<div class="col-sm-12" id="result"></div>

									<div class="col-md-12 col-sm-12">
										<div class="form-group">
											<input class="form-control" type="text" placeholder="Nombre completo" required="" id="first_name" name="first_name" style="padding-left: 20px;">
										</div>
									</div>

									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<input class="form-control" type="email" placeholder="Email:" required="true" id="email" name="email" style="padding-left: 20px;">
										</div>
									</div>
									<div class="col-md-6 col-sm-6">
										<div class="form-group">
											<input class="form-control" type="tel" placeholder="Teléfono:" id="phone" name="phone" style="padding-left: 20px;">
										</div>
									</div>
									<div class="col-md-12 col-sm-12">
										<div class="form-group">
											<textarea class="form-control" placeholder="Mensaje" id="message" name="message" style="padding-left: 20px;"></textarea>
										</div>
									</div>
									<div class="col-sm-12">
										<button type="submit" class="btn btn-large btn-gradient btn-rounded mt-4" id="submit_btn" style="margin-bottom: 30px;"><i class="fa fa-spinner fa-spin mr-2 d-none" aria-hidden="true"></i> <span>Enviar</span></button>
									</div>
								</div>
							</form>
					</div>
					
					
					</div>
				</div>
			</div>
		</section>
		<!--Address End-->

</div>
	

<!--Footer Start-->
<section class="text-center" style="background-color: #B32524; padding: 1rem 0;">
   
    <div class="container-fluid">
        <div class="row">
			<div class="col-md-9">
				<img src="images/logoGranjas.png" alt="image" style="float: left; max-width: 100px; margin-right: 50px; margin-left: 5%;" class="img-fluid">
				<p style="text-align: left; line-height: 1.5; font-size: 12px; padding-top: 10px; font-family: quadonMedium; font-weight: normal;"><span style="font-weight: bold"><a href="http://www.rycalimentos.com/APrivacidad.pdf" target="_blank" class="linkFooter">AVISO DE PRIVACIDAD</a></span><br>
				<a href="http://www.rycalimentos.com" target="_blank" class="linkFooter">rycalimentos.com</a></p>
			</div>
            <div class="col-md-3">
                <div class="footer-social">
                    <ul class="list-unstyled" style="float: left;">
                        <li class="noStyle"><a class="wow fadeInUp" href="https://www.facebook.com/fronteracarneparrillera/" target="_blank" style="color: #ffffff;"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                    </ul>
					
					<p style="text-align: left; line-height: 0.5; padding-top: 15px; font-family: quadonMedium; font-size: 20px; font-weight: normal;">
						<span style="font-size: 16px;">¡Síguenos en Facebook!</span><br>
						<a href="https://www.facebook.com/fronteracarneparrillera/" target="_blank" class="linkFooter" style="background-color: transparent;">Frontera Carne Parrillera</a>
				</p>
                </div>
               
            </div>
        </div>
    </div>
</section>
<!--Footer End-->

<!--Scroll Top-->
<a class="scroll-top-arrow" href="javascript:void(0);"><i class="fa fa-angle-up"></i></a>
<!--Scroll Top End-->

<!--Animated Cursor-->
<div id="aimated-cursor">
    <div id="cursor">
        <div id="cursor-loader"></div>
    </div>
</div>

<div id="unAmigo" style="display: none">
	<div class="row margenNotaSmall" id="amigo_##num##">
		<div class="col-md-5">
			<input type="text" class="form-control" placeholder="Nombre de tu amigo" name="bbq_guest_name_##num##" id="bbq_guest_name_##num##" value="" />
		</div>
		<div class="col-md-6">
			<input type="email" class="form-control" placeholder="Email de tu amigo" name="bbq_guest_email_##num##" id="bbq_guest_email_##num##" value="" />
		</div>
		<div class="col-md-1">
			<span onClick="lessFriends(##num##)" id="elim_amigo_##num##" style="cursor: pointer" class="invisibleSmall"><i class="fa fa-times"></i></span>
			<span onClick="lessFriends(##num##)" id="elim_amigo_##num##_small" class="visibleSmall" style="color: #a5a5a5; cursor: pointer; text-align: right">Eliminar</span>
		</div>
	</div>
</div>
	
<!-- Optional JavaScript -->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.appear.js"></script>
<!-- isotop gallery -->
<script src="js/isotope.pkgd.min.js"></script>
<!-- cube portfolio gallery -->
<script src="js/jquery.cubeportfolio.min.js"></script>
<!-- owl carousel slider -->
<script src="js/owl.carousel.min.js"></script>
<!-- text rotate -->
<script src="js/morphext.min.js"></script>
<!-- particles -->
<script src="js/particles.min.js"></script>
<!-- parallax Background -->
<script src="js/parallaxie.min.js"></script>
<!-- fancybox popup -->
<script src="js/jquery.fancybox.min.js"></script>
<!-- wow animation -->
<script src="js/wow.js"></script>
<!-- tween max animation -->
<script src="js/TweenMax.min.js"></script>
<!-- REVOLUTION JS FILES -->
<script src="rs-plugin/js/jquery.themepunch.tools.min.js"></script>
<script src="rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
<!-- SLIDER REVOLUTION EXTENSIONS -->
<script src="rs-plugin/js/extensions/revolution.extension.actions.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.carousel.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.kenburn.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.migration.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.navigation.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.parallax.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.slideanims.min.js"></script>
<script src="rs-plugin/js/extensions/revolution.extension.video.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/retina.js/1.0.1/retina.js"></script>


<!-- map -->
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyActy3aEPLZwpVu6LuOSAjRr1L5yZIkGt8&libraries=places"></script>-->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC2wwq1DcOf6QND3WsX-bif4T07NegF0tU&libraries=places"></script>
<script src="js/map.js"></script>
<!-- custom script -->
<script src="js/script.js"></script>
<script src="vendor/badger-accordion-master/badger-accordion.js"></script>
<script src="vendor/bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.js"></script>
<script src="vendor/bootstrap-datetimepicker-master/js/locales/bootstrap-datetimepicker.es.js"></script>
<script src="vendor/notify/notify.js"></script>
<script type="text/javascript" src="js/retina.js"></script> 
<script src="js/mapLocation.js"></script>


<script>
	
function selecVideotip(idVideo)
	{
		
		$(".tip").hide();
		$(".subcontent").css("color", "");
		$("#txt"+idVideo).css("color", "#DA543B");
		//si es tablet o desktop
		if ($(this).width() > 480) 
			{
				$("#"+idVideo).show();
			}
		else
			{
				var identHTML="#"+idVideo;
				$.fancybox.open({
					 'type': 'html',
					 'height': 'auto',
					 'autoSize':false,
					 'closeClick': false,
					 'scrolling':'yes',
					 'transitionIn': 'elastic',
					 'transitionOut': 'elastic',
					 'speedIn': 500,
					 'speedOut': 300,
					 'touch':false,
					 'content' : $(identHTML).html()
				  });
			}
		
		
		
	}
	
	
	function selectRecipe(idRecipe)
	{
		$(".receta").hide();
		$(".subcontent").css("color", "");
		$("#txt"+idRecipe).css("color", "#DA543B");
		
		if ($(this).width() > 480) 
			{
				$("#"+idRecipe).show();
			}
		else
			{
				
				var identHTML="#"+idRecipe;
				$.fancybox.open({
					 'type': 'html',
					 'height': 'auto',
					 'autoSize':false,
					 'closeClick': false,
					 'scrolling':'yes',
					 'transitionIn': 'elastic',
					 'transitionOut': 'elastic',
					 'speedIn': 500,
					 'speedOut': 300,
					 'touch':false,
					 'content' : $(identHTML).html()
				  });

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
				$("#logoEmbutido").attr("src","images/embutidos1.png");
				$("#logoCerdo").attr("src","images/cerdo1.png");
				$("#logoRes").attr("src","images/res1.png");
				$("#logoPollo").attr("src","images/pollo2.png");
			break;
				
		}
		
	}
	
	var id_login = '<?php echo $id_login ?>';
	var sliderAbiertos = [0];
	var paso_actual = 0;
	var acordeon;
	var id_activo;
	
	$( document ).ready(function() {
		$( "#logoRes" ).click();
		$('.fancybox-media').fancybox({
			openEffect  : 'none',
			closeEffect : 'none',
			helpers : {
				media : {}
			}
		});
		$(".modalProds").fancybox(
		{
			
			helpers		: {
				title	: { type : 'inside' },
				buttons	: {}
			}
		});
		
		activarAcordeon();
		
//		$("#formRegistro").hide();
		$('#datetimepicker').datetimepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			minView: 2,
			maxView: 2,
			language: 'es',
			fontAwesome: 'fa'
		});
		var hoy = '<?php echo date('Y-m-d'); ?>'
		$('#datetimepicker').datetimepicker('setStartDate', hoy);
		
		$("#bbq_time").datetimepicker({
			startView: 0,
			minView: 0,
			maxView: 0,
			autoclose: true,
			format: 'hh:ii',
			language: 'es',
			fontAwesome: 'fa'
		});
				
		var tomorrow = '<?php echo date('Y-m-d', strtotime($day . " +1 days")); ?>';
		$('#bbq_time').datetimepicker('setStartDate', hoy);
		$('#bbq_time').datetimepicker('setEndDate', tomorrow);
		
		google.maps.event.addDomListener(window, 'load', initAutocomplete);
			
		$('#selectEstado').on('change', function() 
		{
			  eligeEstado();
		});
			
		$('#selectCiudades').on('change', function() 
			{
				eligeCiudad();
			});
		
		
		
		
	});
	
	$(window).on("load", function(){
		activarAcordeon();
		initAutocomplete();
	});
	
	function activarAcordeon() {
		$("#formRegistro").show();
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
			id_activo = acordeon.headers[1].id;
			$('html,body').animate({ scrollTop: $("#vinetaRegistro").offset().top }, 500);
		}
		$("#" + id_activo).css("background-color","#B32524");
		$("#formRegistro").hide();
//		google.maps.event.addDomListener(window, 'load', initAutocomplete);		
	}
	
</script>
<script src="js/configBbq.js?v=1"></script>
<script>
	moreFriends();
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

	function initAutocomplete() {
		var map = new google.maps.Map(document.getElementById('map-canvas'), {
		  center: {lat: 20.314223981308153, lng: -99.87218074500561},
		  zoom: 4,
		  streetViewControl: false,	
		  mapTypeId: 'roadmap',
		  mapTypeControl: false,
		  key: 'AIzaSyActy3aEPLZwpVu6LuOSAjRr1L5yZIkGt8'
		});

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
			//console.log("Lat:" + newLat + ", Long: " + newLong);
			if (marker) 
			{
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
			  console.log("Returned place contains no geometry");
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

			if (place.geometry.viewport) 
			{
			  // Only geocodes have viewport.
			  bounds.union(place.geometry.viewport);
			} else 
			{
			  bounds.extend(place.geometry.location);
			}
		  });
		  map.fitBounds(bounds);
		});
	  }
</script>



</body>
</html>