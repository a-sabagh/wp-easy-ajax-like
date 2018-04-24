<?php

class lj_settings {

    public function __construct() {
        if (is_admin()) {
            add_action("admin_init", array($this, "general_settings_init"));
            add_action("admin_menu", array($this, "admin_menu"));
            add_action("admin_notices", array($this, "configure_notices"));
            add_action("admin_init", array($this, "dismiss_configuration"));
            add_filter('plugin_action_links_' . LJ_PRU, array($this, 'add_setting_link'));
        }
    }
    
    public function general_settings_section_top(){
        _e("most important setting is check post type that you like to show ajax like button","rng-ajaxlike");
    }
    
    public function general_settings_init(){
        register_setting("lj_general_settings", "lj_setting_option");
        add_settings_section("lj_general_setting_top", __("general settings","rng-ajaxlike"), array($this,"general_settings_section_top"), "lj_general_settings");
        add_settings_field("lj_settings_post_type", __("post type permission","rng-ajaxlike"), array($this,"general_settings_post_types"), "lj_general_settings", "lj_general_setting_top", array("id" => "lj-post-types","name" => "lj_post_types"));
        add_settings_field("lj_settings_show_like", __("show like button","rng-ajaxlike"), array($this,"general_settings_show_like"), "lj_general_settings", "lj_general_setting_top", array("id" => "lj-show-like","name" => "lj_show_like"));
    }
    
    public function general_settings_post_types($args){
        $active_post_type = get_option("lj_setting_option");
        if($active_post_type == FALSE){
            $active_post_type = array("post");
        }else{
            $active_post_type = $active_post_type['lj_post_types'];
        }
        $pt_args = array('public' => TRUE);
        $post_types = get_post_types($pt_args, 'names');
        $key = array_search("attachment", $post_types);
        unset($post_types[$key]);
        foreach ($post_types as $post_type):
            ?>
            <label>
                <?php echo $post_type ?>&nbsp;<input id="<?php echo $args['id']; ?>" type="checkbox" name="lj_setting_option[<?php echo $args['name']; ?>][]" <?php echo (in_array($post_type, $active_post_type)) ? "checked" : ""; ?> value="<?php echo $post_type; ?>" >
            </label>
            <br>
            <?php
        endforeach;
        
    }
    
    public function general_settings_show_like($args){
        $show_like = get_option("lj_setting_option");
        if($show_like == FALSE){
            $show_like = "1";
        }else{
            $show_like = $show_like[$args['name']];
        }
        ?>
            <select class="<?php echo $args['id']?>" name="lj_setting_option[<?php echo $args['name']; ?>]">
                <option <?php echo ($show_like == "1")? "selected=''" : ""; ?> value="1"><?php _e("Yes","rng-ajaxlike") ?></option>
                <option <?php echo ($show_like == "0")? "selected=''" : ""; ?> value="0"><?php _e("No","rng-ajaxlike") ?></option>
            </select>    
        <?php
    }
    
    public function admin_menu(){
        add_submenu_page("options-general.php", __("Ajax Like Settings" , "rng-ajaxlike"), __("Ajax Like Settings"), "administrator", "ajaxlike-settings", array($this,"ajaxlike_settings"));
    }
    
    public function ajaxlike_settings(){
        include_once LJ_ADM . "setting-panel.php";
    }
    
    public function configure_notices(){
        $dismiss = get_option("lj_configration_dissmiss");
        if(!$dismiss){
            $notice = '<div class="updated"><p>' . __('RNG_ajaxLike is activated, you may need to configure it to work properly.','rng-ajaxlike') . ' <a href="' .admin_url('admin.php?page=ajaxlike-settings') . '">' . __('Go to Settings page','rng-ajaxlike') . '</a> &ndash; <a href="' .  add_query_arg(array('lj_dismiss_notice'=>'true','lj_nonce'=> wp_create_nonce("lj_dismiss_nonce"))) . '">' . __('Dismiss','rng-ajaxlike') . '</a></p></div>';
            echo $notice;
        }
    }
    
    public function dismiss_configuration(){
        if(isset($_GET['lj_dismiss_notice']) and $_GET['lj_dismiss'] = 'true' and (isset($_GET['lj_nonce'])) ){
            $verify_nonce = wp_verify_nonce($_GET['lj_nonce'],'lj_dismiss_nonce');
            if($verify_nonce){
                update_option("lj_configration_dissmiss", 1);
            }
        }elseif(isset($_GET['page']) and $_GET['page'] == "ajaxlike-settings"){
            update_option("lj_configration_dissmiss", 1);
        }
    }
    
    public function add_setting_link($links){
        $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=ajaxlike-settings' ) . '">' . __("Settings","rng-ajaxlike") . '</a>',
        );
       return array_merge( $links, $mylinks );
    }
}

$lj_settings = new lj_settings();