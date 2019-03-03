<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
//delte options
$options = array(
    "lj_setting_option",
    "lj_configration_dissmiss",
    "widget_lj_mostliked_posts"
);
foreach ($options as $option) {
    if (get_option($option)) {
        delete_option($option);
    }
}
// drop a metadata
global $wpdb;
$wpdb->query("DELETE * FROM {$wpdb->prefix}postmeta WHERE meta_key = 'lj_like_wp'");
