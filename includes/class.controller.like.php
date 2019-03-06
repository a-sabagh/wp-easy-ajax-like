<?php

class rajl_like {

    public function __construct() {
        add_filter("the_content", array($this, "output_content_like"));
        add_action("wp_enqueue_scripts", array($this, "localize_like_script"));
        add_action("wp_ajax_rajl_liked", array($this, "rajl_liked"));
        add_action("wp_ajax_nopriv_rajl_liked", array($this, "rajl_liked"));
        $legal_pts = $this->legal_post_type();
        foreach ($legal_pts as $legal_pt) {
            add_filter("manage_{$legal_pt}_posts_columns", array($this, 'add_like_posts_column'), 10, 1);
            add_action("manage_{$legal_pt}_posts_custom_column", array($this, 'add_like_custom_column'), 10, 2);
        }
    }

    /**
     * get default settings
     * @return Array
     */
    public function get_settings() {
        $rajl_setting = get_option('rajl_setting_option');
        if (isset($rajl_setting) and !empty($rajl_setting)) {
            $legal_post_types = $rajl_setting['rajl_post_types'];
            $show_like_switch = (isset($rajl_setting['rajl_show_like'])) ? $rajl_setting['rajl_show_like'] : "1";
            return array(
                'legal_post_types' => $legal_post_types,
                'show_like_switch' => $show_like_switch
            );
        } else {
            return array(
                'legal_post_types' => array('post'),
                'show_like_switch' => "1"
            );
        }
    }

    /**
     * check is legal post type based on settings
     * @return Array
     */
    public function legal_post_type() {
        $params = $this->get_settings();
        return $params['legal_post_types'];
    }

    /**
     * response to ajax call in wordpress. in other word handle wordpress hooks
     */
    public function rajl_liked() {
        $liked = intval($_POST['liked']);
        $post_id = intval($_POST['post_id']);

        if ($liked == 0 and is_numeric($post_id)) {
            $this->add_post_like($post_id);
            echo "add";
        } elseif ($liked == 1 and is_numeric($post_id)) {
            $this->remove_post_like($post_id);
            echo "remove";
        } else {
            echo "false";
        }
        wp_die();
    }

    /**
     * add post meta with meta_key rajl_like_wp and meta_value like count
     * @param type $post_id
     */
    public function add_post_like($post_id) {
        $cookie_name = "rajl_like_wp" . $post_id;
        setcookie($cookie_name, $post_id, time() + YEAR_IN_SECONDS, "/");
        $like_count = (int) get_post_meta($post_id, "rajl_like_wp", TRUE);

        if ($like_count > 0) {
            update_post_meta($post_id, "rajl_like_wp", $like_count + 1);
        } elseif ($like_count === 0) {
            update_post_meta($post_id, "rajl_like_wp", 1);
        } else {
            add_post_meta($post_id, "rajl_like_wp", 1);
        }
    }

    /**
     * decrease post like count
     * @param type $post_id
     */
    public function remove_post_like($post_id) {
        $cookie_name = "rajl_like_wp" . $post_id;
        unset($_COOKIE[$cookie_name]);
        setcookie($cookie_name, '', time() - 3600, '/');
        $like_count = (int) get_post_meta($post_id, "rajl_like_wp", TRUE);
        if ($like_count > 0) {
            update_post_meta($post_id, "rajl_like_wp", $like_count - 1);
        } else {
            update_post_meta($post_id, "rajl_like_wp", 0);
        }
    }

    /**
     * localize params to script
     */
    public function localize_like_script() {
        $data = array(
            "post_id" => get_the_ID(),
            "user_id" => get_current_user_id(),
            "admin_url" => admin_url("admin-ajax.php")
        );
        wp_localize_script("lj-script", like_obj, $data);
    }

    /**
     * add like button end of post content
     * @param String $content
     * @return String
     */
    public function output_content_like($content) {
        $post_id = get_the_ID();
        $like_count = (get_post_meta($post_id, "rajl_like_wp", TRUE)) ? get_post_meta($post_id, "rajl_like_wp", TRUE) : 0;
        
        $params = $this->get_settings();
        extract($params);
        
        $cookie_name = 'rajl_like_wp' . get_the_ID();
        $cookie = $_COOKIE[$cookie_name];
        $class = (isset($cookie)) ? "liked" : "";
        if (in_array(get_post_type($post_id), $legal_post_types) and $show_like_switch == "1" and is_singular()) {
            ob_start();
            ?>
            <div class="lj-like-wrapper">
                <a href="#" class="lj-like-wp <?php echo esc_attr($class); ?>" title="<?php esc_attr_e("like this", "rng-ajaxlike"); ?>" >
                    <?php if (isset($cookie)): ?>
                        <i class="icon-heart"></i>
                    <?php else: ?>
                        <i class="icon-heart-o"></i>
            <?php endif; ?>
                </a>&nbsp;<span class="lj-post-like-count"><?php echo esc_html($like_count); ?></span>
            </div>
            <?php
            $output = ob_get_clean();
            return $content . $output;
        }

        return "it work" . $content;
    }

    /**
     * static functionn to show like button and like count for programmers
     */
    public static function content_like() {
        $post_id = get_the_ID();
        $like_count = (get_post_meta($post_id, "rajl_like_wp", TRUE)) ? get_post_meta($post_id, "rajl_like_wp", TRUE) : 0;
        
        $params = $this->get_settings();
        extract($params);
        
        $cookie_name = 'rajl_like_wp' . get_the_ID();
        $cookie = $_COOKIE[$cookie_name];
        $class = (isset($cookie)) ? "liked" : "";
        if (in_array(get_post_type(), $rajl_setting['rajl_post_types']) and $rajl_setting['rajl_show_like'] == "1" and is_single()) {
            ob_start();
            ?>
            <div class="lj-like-wrapper">
                <a href="#" class="lj-like-wp <?php echo esc_attr($class); ?>" title="<?php esc_attr_e("like this", "rng-ajaxlike"); ?>" >
                    <?php if (isset($cookie)): ?>
                        <i class="icon-heart"></i>
                    <?php else: ?>
                        <i class="icon-heart-o"></i>
            <?php endif; ?>
                </a>&nbsp;<span class="lj-post-like-count"><?php echo esc_html($like_count); ?></span>
            </div>
            <?php
            $output = ob_get_clean();
        }

        echo $output;
    }

    /**
     * add like count header to post list in admin panel
     * @param type $columns
     * @return type
     */
    public function add_like_posts_column($columns) {
        return array_merge($columns, array('rajl_like' => '<span class="dashicons dashicons-heart"></span>'));
    }

    /**
     * add like count to post list in admin panel
     * @param type $column
     * @param type $post_id
     */
    public function add_like_custom_column($column, $post_id) {
        if ($column == "rajl_like") {
            $like = (get_post_meta($post_id, "rajl_like_wp", TRUE)) ? get_post_meta($post_id, "rajl_like_wp", TRUE) : "0";
            echo $like;
        }
    }

}

$rajl_like = new rajl_like();
