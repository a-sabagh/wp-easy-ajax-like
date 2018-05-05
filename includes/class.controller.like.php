<?php

class lj_like {

    public function __construct() {
        add_filter("the_content", array($this, "output_content_like"));
        add_action("wp_enqueue_scripts", array($this, "localize_like_script"));
        add_action("wp_ajax_lj_liked", array($this, "lj_liked"));
        add_action("wp_ajax_nopriv_lj_liked", array($this, "lj_liked"));
        $legal_pts = $this->legal_post_type();
        foreach($legal_pts as $legal_pt){
            add_filter("manage_{$legal_pt}_posts_columns" , array($this,'add_like_posts_column'),10,1);
            add_action("manage_{$legal_pt}_posts_custom_column" , array($this,'add_like_custom_column'),10,2);
        }
        
    }
    
    public function legal_post_type(){
        $lj_setting = get_option('lj_setting_option');
        return $lj_setting['lj_post_types'];
    }
    public function lj_liked() {
        if ($_POST['liked'] == "0") {
            $this->add_post_like($_POST['post_id']);
            echo "add";
        } elseif ($_POST['liked'] == "1") {
            $this->remove_post_like($_POST['post_id']);
            echo "remove";
        } else {
            echo "false";
        }
        wp_die();
    }

    public function add_post_like($post_id) {
        $cookie_name = "lj_like_wp" . $post_id;
        setcookie($cookie_name, $post_id, time() + YEAR_IN_SECONDS, "/");
        $like_count = get_post_meta($post_id, "lj_like_wp", TRUE);
        if ($like_count) {
            update_post_meta($post_id, "lj_like_wp", $like_count + 1);
        } else {
            add_post_meta($post_id, "lj_like_wp", 1);
        }
    }

    public function remove_post_like($post_id) {
        $cookie_name = "lj_like_wp" . $post_id;
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
        $like_count = get_post_meta($post_id, "lj_like_wp", TRUE);
        if (isset($like_count) and $like_count > 0) {
            update_post_meta($post_id, "lj_like_wp", $like_count - 1);
        } else {
            update_post_meta($post_id, "lj_like_wp", 0);
        }
    }

    public function localize_like_script() {
        $data = array(
            "post_id" => get_the_ID(),
            "user_id" => get_current_user_id(),
            "admin_url" => admin_url("admin-ajax.php")
        );
        wp_localize_script("lj-script", like_obj, $data);
    }

    public function output_content_like($content) {
        $lj_setting = get_option('lj_setting_option');
        $post_id = get_the_ID();
        $like_count = (get_post_meta($post_id, "lj_like_wp", TRUE)) ? get_post_meta($post_id, "lj_like_wp", TRUE) : 0;
        $cookie_name = 'lj_like_wp' . get_the_ID();
        $cookie = $_COOKIE[$cookie_name];
        $class = (isset($cookie)) ? "liked" : "";
        if (in_array(get_post_type(), $lj_setting['lj_post_types']) and $lj_setting['lj_show_like'] == "1" and is_single()) {
            ob_start();
            ?>
            <div class="lj-like-wrapper">
                <a href="#" class="lj-like-wp <?php echo $class; ?>" title="<?php esc_attr_e("like this", "rng-ajaxlike"); ?>" >
                    <?php if (isset($cookie)): ?>
                        <i class="icon-heart"></i>
                    <?php else: ?>
                        <i class="icon-heart-o"></i>
                    <?php endif; ?>
                </a>&nbsp;<span class="lj-post-like-count"><?php echo $like_count; ?></span>
            </div>
            <?php
            $output = ob_get_clean();
            return $content . $output;
        }

        return $content;
    }

    public static function content_like() {
        $lj_setting = get_option('lj_setting_option');
        $post_id = get_the_ID();
        $like_count = (get_post_meta($post_id, "lj_like_wp", TRUE)) ? get_post_meta($post_id, "lj_like_wp", TRUE) : 0;
        $cookie_name = 'lj_like_wp' . get_the_ID();
        $cookie = $_COOKIE[$cookie_name];
        $class = (isset($cookie)) ? "liked" : "";
        if (in_array(get_post_type(), $lj_setting['lj_post_types']) and $lj_setting['lj_show_like'] == "1" and is_single()) {
            ob_start();
            ?>
            <div class="lj-like-wrapper">
                <a href="#" class="lj-like-wp <?php echo $class; ?>" title="<?php esc_attr_e("like this", "rng-ajaxlike"); ?>" >
                    <?php if (isset($cookie)): ?>
                        <i class="icon-heart"></i>
                    <?php else: ?>
                        <i class="icon-heart-o"></i>
                    <?php endif; ?>
                </a>&nbsp;<span class="lj-post-like-count"><?php echo $like_count; ?></span>
            </div>
            <?php
            $output = ob_get_clean();
        }

        echo $output;
    }
    
    public function add_like_posts_column($columns){
        return array_merge($columns,array('lj_like' => '<span class="dashicons dashicons-heart"></span>'));
    }
    
    public function add_like_custom_column($column, $post_id){
        if($column == "lj_like"){
            $like = (get_post_meta($post_id,"lj_like_wp",TRUE))? get_post_meta($post_id,"lj_like_wp",TRUE) : "0";
            echo $like;
        }
    }

}

$lj_like = new lj_like();
