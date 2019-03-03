<?php

/*
 * Plugin Name: RNG_AjaxLike
 * Description: WordPress plugin that allow the visitor to like posts content in a single page using Ajax technology with excellent features like setting panel and widget that show posts order by like counts.
 * Version: 1.0
 * Author: Abolfazl Sabagh
 * Author URI: http://asabagh.ir
 * License: GPLv2 or later
 * Text Domain: rng-ajaxlike
 * @version             1.0
 * @license             http://www.gnu.org/licenses/gpl-2.0.html GNU Public License v2.0
 * @package             RNG-AjaxLike
 * @subpackage          Core
 */

define(LJ_PRU, plugin_basename(__FILE__));
define(LJ_PDU, plugin_dir_url(__FILE__));   //http://localhost:8888/rng-plugin/wp-content/plugins/rng-ajaxLike/
define(LJ_PRT, basename(__DIR__));          //rng-ajaxLike.php
define(LJ_PDP, plugin_dir_path(__FILE__));  //Applications/MAMP/htdocs/rng-plugin/wp-content/plugins/rng-ajaxLike
define(LJ_TMP, LJ_PDP . "/public/");        // view OR templates System for public 
define(LJ_ADM, LJ_PDP . "/admin/");         // view OR templates System for admin panel

require_once 'includes/class.init.php';
$refresh_init = new lj_init(1.0, 'rng-ajaxlike');
