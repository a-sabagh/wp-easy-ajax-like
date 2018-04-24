<?php
/*
  Plugin Name: RNG_ajaxLike
  Description: wordpress plugin like post type content in single page with ajax technology in both of way (programmically and filters)
  Version: 1.0
  Author: abolfazl sabagh
  Author URI: http://asabagh.ir
  License: GPLv2 or later
  Text Domain: rng-ajaxlike
 */

define(LJ_PRU,plugin_basename( __FILE__ ));  
define(LJ_PDU, plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-ajaxLike/
define(LJ_PRT, basename(__DIR__));          //rng-ajaxLike.php
define(LJ_PDP, plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-ajaxLike
define(LJ_TMP, LJ_PDP . "/public/");        // view OR templates System for public 
define(LJ_ADM, LJ_PDP . "/admin/");         // view OR templates System for admin panel

require_once 'includes/class.init.php';
$refresh_init = new lj_init(1.0,'rng-ajaxlike');
