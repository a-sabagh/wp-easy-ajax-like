<?php

/*
 * Plugin Name: rng-ajaxlike
 * Description: WordPress plugin that allow the visitor to like posts content in a single page using Ajax technology with excellent features like setting panel and widget that show posts order by like counts.
 * Version: 1.0
 * Author: Abolfazl Sabagh
 * Author URI: http://asabagh.ir
 * License: GPLv2 or later
 * Text Domain: rng-ajaxlike
 */

$prefix = "rajl_";

define(RAJL_PRU, plugin_basename(__FILE__));
define(RAJL_PDU, plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-ajaxLike/
define(RAJL_PRT, basename(__DIR__));          //rng-ajaxLike.php
define(RAJL_PDP, plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-ajaxLike
define(RAJL_TMP, RAJL_PDP . "/public/");        // view OR templates System for public 
define(RAJL_ADM, RAJL_PDP . "/admin/");         // view OR templates System for admin panel

require_once 'includes/class.init.php';
$refresh_init = new rajl_init(1.0, 'rng-ajaxlike');
