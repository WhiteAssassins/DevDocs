<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Error en la Base de Datos</title>

<style type="text/css"> 
		
		article, aside, details, figcaption, figure, 
		footer, header, hgroup, menu, nav, section {
			display: block;
		}
		ol, ul {
			list-style: none;
		}
		blockquote, q {
			quotes: none;
		}
		blockquote:before, blockquote:after,
		q:before, q:after {
			content: '';
			content: none;
		}
		table {
			border-collapse: collapse;
			border-spacing: 0;
		}
		html {
		  min-height: 100%;
		}

		body {
		  box-sizing: border-box;
		  height: 100%;
		  background-color: #000000;
		  background-image: radial-gradient(#11581E, #041607), url("/error.gif");
		  background-repeat: no-repeat;
		  background-size: cover;
		  font-family: 'Inconsolata', Helvetica, sans-serif;
		  font-size: 1.5rem;
		  line-height: 1;
		  color: rgba(128, 255, 128, 0.8);
		  text-shadow:
			  0 0 1ex rgba(51, 255, 51, 1),
			  0 0 2px rgba(255, 255, 255, 0.8);
		}

		.noise {
		  pointer-events: none;
		  position: absolute;y 
		  width: 100%;
		  height: 100%;
		  background-image: url("/error.gif");
		  background-repeat: no-repeat;
		  background-size: cover;
		  z-index: -1;
		  opacity: .02;
		}

		.overlay {
		  pointer-events: none;
		  position: absolute;
		  width: 100%;
		  height: 100%;
		  background:
			  repeating-linear-gradient(
			  180deg,
			  rgba(0, 0, 0, 0) 0,
			  rgba(0, 0, 0, 0.3) 50%,
			  rgba(0, 0, 0, 0) 100%);
		  background-size: auto 4px;
		  z-index: 1;
		}

		.overlay::before {
		  content: "";
		  pointer-events: none;
		  position: absolute;
		  display: block;
		  top: 0;
		  left: 0;
		  right: 0;
		  bottom: 0;
		  width: 100%;
		  height: 100%;
		  background-image: linear-gradient(
			  0deg,
			  transparent 0%,
			  rgba(32, 128, 32, 0.2) 2%,
			  rgba(32, 128, 32, 0.8) 3%,
			  rgba(32, 128, 32, 0.2) 3%,
			  transparent 100%);
		  background-repeat: no-repeat;
		  animation: scan 7.5s linear 0s infinite;
		}

		@keyframes scan {
		  0%        { background-position: 0 -100vh; }
		  35%, 100% { background-position: 0 100vh; }
		}

		.terminal {
		  box-sizing: inherit;
		  position: absolute;
		  height: 100%;
		  width: 1000px;
		  max-width: 100%;
		  padding: 4rem;
		  text-transform: uppercase;
		}

		.output {
		  line-height: 1.6em;
		  color: rgba(128, 255, 128, 0.8);
		  text-shadow:
			  0 0 1px rgba(51, 255, 51, 0.4),
			  0 0 2px rgba(255, 255, 255, 0.8);
		}

		.output::before {
		  content: "> ";
		}

		a {
		  color: #fff;
		  text-decoration: none;
		}

		a::before {
		  content: "[";
		}

		a::after {
		  content: "]";
		}

		.errorcode {
		  color: white;
		}
		h1{
			color:rgba(128, 255, 128, 0.8);
			display:block;
			font-family:Inconsolata, Helvetica, sans-serif;
			font-size:48px;
			font-weight:700;
			line-height:55.2px;
			margin-block-end:32.16px;
			margin-block-start:32.16px;
			margin-bottom:32.16px;
			margin-inline-end:0px;
			margin-inline-start:0px;
			margin-left:0px;
			margin-right:0px;
			margin-top:32.16px;
			text-shadow:rgb(51, 255, 51) 0px 0px 10.968px, rgba(255, 255, 255, 0.8) 0px 0px 2px;
			text-size-adjust:100%;
		}
		p{
			margin:24px 0;
			display: contents;

		}
		.p2{
			margin:24px 0;
			

		}
		h6{
			font-size: 25px;
		}
		
		@media only screen and (max-width: 600px) {
		  .output {
			line-height: 1.3em;
			font-size: 20px;
		  }
		}
	
	</style>
</head>
<body>
<body>

	

</body>
		
<?php $ip = $_SERVER['REMOTE_ADDR'];?>
<div class="noise"></div>
<div class="overlay"></div>
<div class="terminal">
<h1><span class="errorcode">Error</span> ;)</h1>
<h6 class="output p2"><?php echo $heading; ?></h6>
<div class="output inline"> <?php echo "$message"; ?> </div>
<h6 class="output p2">Tu Direccion ip es: <span class="errorcode"><?php echo $ip; ?></span></h6>
<h6 class="output p2">Si Piensa que es un Error Contacte a <a href="http://netlab.freedom.snet/user/whiteassassins" title="WhiteAssassins" rel="dofollow">WhiteAssassins</a></h6>


</div>
	
</body>
</html>