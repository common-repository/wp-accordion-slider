<?php header('Content-type: text/css'); 

global $wp_accordion_settings;
?>

/*
PRINT $wp_accordion_settings
<?php print $wp_accordion_settings['test_css'];  ?>
*/

/*
#<?php print $_GET['ul']; ?> .slide_handle {
	background:url(../images/handles.png);
	bottom:0;
	cursor:pointer;
	left:0;
	position:absolute;
	top:0;
	width:40px;
}
#<?php print $_GET['ul']; ?> .slide2 .slide_handle { background-position:-40px 0; }
#<?php print $_GET['ul']; ?> .slide3 .slide_handle { background-position:-80px 0; }
#<?php print $_GET['ul']; ?> .slide4 .slide_handle { background-position:-120px 0; }
*/
#<?php print $_GET['ul']; ?> {
	color:#fff;
	text-shadow:0 1px 0 #333;
	list-style:none;
	margin: 0 auto;
	overflow:scroll;
	padding:0;
}

#<?php print $_GET['ul']; ?> li {
	position:relative;
	overflow: hidden;
	background: #FFF;
	}

#<?php print $_GET['ul']; ?> .slide_handle {
	bottom:0;
	cursor:pointer;
	left:0;
	position:absolute;
	top:0;
	width:40px;
	background: #E6E6E6;
}

#<?php print $_GET['ul']; ?> .wpa-slide-content {
	float:left; 
	padding:0 20px; 
	width:213px;
	color: #333;
	text-shadow: none;
}

#<?php print $_GET['ul']; ?> .wpa-slides-content p {
	float:left; 
	padding:0 10px; 
	color: #333;
	text-shadow: none;
}

#<?php print $_GET['ul']; ?> .wpa-slides-content h2, h3 {
	float:left; 
	color: #333;
}

#<?php print $_GET['ul']; ?> p.css-vertical-text {
	color:#333;
	border:0px solid red;
	writing-mode:tb-rl;
	-webkit-transform:rotate(-90deg);
	-moz-transform:rotate(-90deg);
	-o-transform: rotate(-90deg);
	white-space:nowrap;
	display:block;
	bottom:0;
	width:220px;
	height:205px;
	font-family: ‘Trebuchet MS’, Helvetica, sans-serif;
	font-size:22px;
	font-weight:normal;
	text-shadow: 0px 0px 1px #333;
	filter: flipv fliph;
}

#<?php print $_GET['ul']; ?> p, h2 {
	text-shadow: none;
	color:#333;
}

#<?php print $_GET['ul']; ?> .slide_handle div {
	background:url(../images/arrows.gif);
	bottom:16px;
	height:7px;
	left:16px;
	position:absolute;
	width:7px;
}
#<?php print $_GET['ul']; ?> .slide_opened .slide_handle { cursor:default; }
#<?php print $_GET['ul']; ?> .slide_opened .slide_handle div { background-position:0 -7px; }
#<?php print $_GET['ul']; ?> .slide_content {
	bottom:0;
	left:40px; /* Matches the width of .slide_handle */
	position:absolute;
	right:0;
	top:0;
}
#<?php print $_GET['ul']; ?> .slide_content a img { border:0; }
#<?php print $_GET['ul']; ?> .slide_caption {
	bottom:0;
	left:0;
	padding:10px 20px;
	position:absolute;
	right:0;
	/* To change the height of the caption, set the captionHeight option in script.js */
}
#<?php print $_GET['ul']; ?> .slide_caption_toggle {
	cursor:pointer;
	height:10px;
	left:0;
	position:absolute;
	right:0;
	top:0;
}
#<?php print $_GET['ul']; ?> .slide_caption_toggle div {
	background:url(../images/toggle-caption.png) no-repeat 50% -10px;
	height:100%;
}
#<?php print $_GET['ul']; ?> .slide_caption_toggle:hover { background:url(../images/black-30pct.png); }
#<?php print $_GET['ul']; ?> .slide_caption_collapsed .slide_caption_toggle div { background-position:50% 0; }
#<?php print $_GET['ul']; ?> a, #<?php print $_GET['ul']; ?> .slide_caption p {
	background:none;
	color:#fff;
	text-shadow:0 1px 0 #333;
}
#<?php print $_GET['ul']; ?> a:hover { text-decoration:none; }
#<?php print $_GET['ul']; ?> .slide4 .slide_content { background:url(../images/digital-noise.png) 100% 0; }
