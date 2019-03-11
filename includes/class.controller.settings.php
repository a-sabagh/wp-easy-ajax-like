<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class rajl_settings {

    public function __construct() {
        if (is_admin()) {
            add_action("admin_init", array($this, "general_settings_init"));
            add_action("admin_menu", array($this, "admin_menu"));
            add_action("admin_notices", array($this, "configure_notices"));
            add_action("admin_init", array($this, "dismiss_configuration"));
            add_filter('plugin_action_links_' . RAJL_PRU, array($this, 'add_setting_link'));
            add_action("update_option_rajl_setting_option", array($this, "add_setting_stylesheet"), 1, 2);
            add_action("add_option_rajl_setting_option", array($this, "add_setting_stylesheet"), 1, 2);
        }
    }

    /**
     * write dynamic stylesheet from static styles and settings styles 
     * @param type $old_val
     * @param type $new_val
     */
    public function add_setting_stylesheet($old_val, $new_val) {
        $file_get = RAJL_PDP . "/assets/css/style.backup.css";
        $file_put = RAJL_PDP . "/assets/css/style.css";
        $color = $new_val['rajl_btn_color'];
        $css = "\n.lj-like-wp,"
                . ".lj-like-wp:focus,"
                . ".lj-like-wp:active,"
                . ".lj-like-wp:hover {"
                . "color: {$color} !important;"
                . "}";
        $content = file_get_contents($file_get);

        $fp = fopen($file_put, 'w');
        fwrite($fp, $content . $css);
        fclose($fp);
    }

    /**
     * initial general setting with option name `rajl_setting_option`
     */
    public function general_settings_init() {
        register_setting("rajl_general_settings", "rajl_setting_option");
        add_settings_section("rajl_general_setting_top", esc_html__("general settings", "rng-ajaxlike"), array($this, "general_settings_section_top"), "rajl_general_settings");
        add_settings_field("rajl_settings_post_type", esc_html__("post type permission", "rng-ajaxlike"), array($this, "general_settings_post_types"), "rajl_general_settings", "rajl_general_setting_top", array("id" => "lj-post-types", "name" => "rajl_post_types"));
        add_settings_field("rajl_settings_show_like", esc_html__("show like button", "rng-ajaxlike"), array($this, "general_settings_show_like"), "rajl_general_settings", "rajl_general_setting_top", array("id" => "lj-show-like", "name" => "rajl_show_like"));
        add_settings_field("rajl_settings_like_color", esc_html__("like button color", "rng-ajaxlike"), array($this, "general_settings_like_color"), "rajl_general_settings", "rajl_general_setting_top", array("id" => "lj-btn-color", "name" => "rajl_btn_color"));
    }

    /**
     * general settings top section 
     */
    public function general_settings_section_top() {
        esc_html_e("most important setting is check post type that you like to show ajax like button", "rng-ajaxlike");
    }

    /**
     * add like color picker input
     * this method chain to general_settings_init
     * @param Array $args
     */
    public function general_settings_like_color($args) {
        $btn_color = get_option("rajl_setting_option");
        if ($btn_color == FALSE) {
            $btn_color = "#dd3333";
        } else {
            $btn_color = $btn_color['rajl_btn_color'];
        }
        ?>
        <input type="text" class="wp-color-picker" name="rajl_setting_option[<?php echo $args['name'] ?>]" value="<?php echo $btn_color; ?>" >    
        <?php
    }

    /**
     * add post type selectors
     * this method chain to general_settings_init
     * @param Array $args
     */
    public function general_settings_post_types($args) {
        $active_post_type = get_option("rajl_setting_option");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['rajl_post_types'];
        }
        $pt_args = array('public' => TRUE);
        $post_types = get_post_types($pt_args, 'names');
        $key = array_search("attachment", $post_types);
        unset($post_types[$key]);
        foreach ($post_types as $post_type):
            if (is_array($active_post_type)) {
                $checked = (in_array($post_type, $active_post_type)) ? "checked" : "";
            } else {
                $checked = '';
            }
            ?>
            <label>
            <?php echo $post_type ?>&nbsp;<input id="<?php echo $args['id']; ?>" type="checkbox" name="rajl_setting_option[<?php echo $args['name']; ?>][]" <?php echo $checked; ?> value="<?php echo $post_type; ?>" >
            </label>
            <br>
            <?php
        endforeach;
    }

    /**
     * add show like switch selector
     * this method chain to general_settings_init
     * @param Array $args
     */
    public function general_settings_show_like($args) {
        $show_like = get_option("rajl_setting_option");
        if ($show_like == FALSE) {
            $show_like = "1";
        } else {
            $show_like = $show_like[$args['name']];
        }
        ?>
        <select class="<?php echo $args['id'] ?>" name="rajl_setting_option[<?php echo $args['name']; ?>]">
            <option <?php echo ($show_like == "1") ? "selected=''" : ""; ?> value="1"><?php esc_html_e("Yes", "rng-ajaxlike") ?></option>
            <option <?php echo ($show_like == "0") ? "selected=''" : ""; ?> value="0"><?php esc_html_e("No", "rng-ajaxlike") ?></option>
        </select>    
        <?php
    }

    /**
     * add general settings submenu
     * @hook
     */
    public function admin_menu() {
        add_submenu_page("options-general.php", esc_html__("Ajax Like Settings", "rng-ajaxlike"), esc_html__("Ajax Like Settings", "rng-ajaxlike"), "administrator", "ajaxlike-settings", array($this, "ajaxlike_settings"));
    }

    /**
     * call setting panel view
     * chain to admin_menu
     */
    public function ajaxlike_settings() {
        include_once RAJL_ADM . "setting-panel.php";
    }

    /**
     * show admin notices
     */
    public function configure_notices() {
        $dismiss = get_option("rajl_configration_dissmiss");
        if (!$dismiss) {
            $notice = '<div class="updated"><p>' . esc_html__('RNG_ajaxLike is activated, you may need to configure it to work properly.', 'rng-ajaxlike') . ' <a href="' . admin_url('options-general.php?page=ajaxlike-settings') . '">' . esc_html__('Go to Settings page', 'rng-ajaxlike') . '</a> &ndash; <a href="' . add_query_arg(array('rajl_dismiss_notice' => 'true', 'rajl_nonce' => wp_create_nonce("rajl_dismiss_nonce"))) . '">' . esc_html__('Dismiss', 'rng-ajaxlike') . '</a></p></div>';
            echo $notice;
        }
    }

    /**
     * dismiss configuration notice action
     */
    public function dismiss_configuration() {
        $rajl_dismiss_notice = $_GET['rajl_dismiss_notice'];
        $rajl_dismiss = sanitize_text_field($_GET['rajl_dismiss']);
        $rajl_nonce = $_GET['rajl_nonce'];
        if (isset($rajl_dismiss_notice) and $rajl_dismiss = 'true' and ( isset($rajl_nonce))) {
            $verify_nonce = wp_verify_nonce($rajl_nonce, 'rajl_dismiss_nonce');
            if ($verify_nonce) {
                update_option("rajl_configration_dissmiss", 1);
            }
        } elseif (isset($_GET['page']) and $_GET['page'] == "ajaxlike-settings") {
            update_option("rajl_configration_dissmiss", 1);
        }
    }

    /**
     * add setting link to plugin screen
     * @param String $links
     * @return Array
     */
    public function add_setting_link($links) {
        $mylinks = array(
            '<a href="' . admin_url('options-general.php?page=ajaxlike-settings') . '">' . esc_html__("Settings", "rng-ajaxlike") . '</a>',
        );
        return array_merge($links, $mylinks);
    }

}

$rajl_settings = new rajl_settings();
