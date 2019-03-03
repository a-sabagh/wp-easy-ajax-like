<?php

class lj_mostliked_posts_widget extends WP_Widget {

    public function __construct() {
        $widget_options = array(
            'classname' => 'mostliked-posts',
            'description' => __("show most liked posts", "rng-ajaxlike")
        );
        parent::__construct("lj_mostliked_posts", __("Most Liked Posts", "rng-ajaxlike"), $widget_options);
    }

    /**
     * output widget
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : "";
        $title = apply_filters("widget_title", $title);
        $active_post_type = get_option("lj_setting_option");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['lj_post_types'];
        }
        $post_types_widget = (!empty($instance['post_types']) and isset($instance['post_types'])) ? $instance['post_types'] : array('post');
        $post_types = array_intersect($active_post_type, $post_types_widget);
        $posts_count = (!empty($instance['posts_count'])) ? $instance['posts_count'] : 4;

        $output = $args["before_widget"];
        $output .= $args["before_title"];
        $output .= $title;
        $output .= $args["after_title"];
        ob_start();
        $query_args = array(
            'post_type' => $post_types,
            'posts_per_page' => $posts_count,
            'meta_key' => 'lj_like_wp',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        );
        $query = new WP_Query($query_args);
        ?>
        <ul class="lj-mostliked-posts">
            <?php
            if ($query->have_posts()):
                while ($query->have_posts()):
                    $query->the_post();
                    $post_id = get_the_ID();
                    $liked_count = (get_post_meta($post_id, "lj_like_wp", TRUE)) ? get_post_meta($post_id, "lj_like_wp", TRUE) : 0;
                    ?>
                    <li class="d-flex justfy-content-between">
                        <a class="flex-grow-1" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                        <span class="icon-heart like flex-grow-0">&nbsp;<span><?php echo $liked_count; ?></span></span>
                    </li>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </ul>
        <?php
        $output .= ob_get_clean();
        $output .= $args["after_widget"];
        echo $output;
    }

    /**
     * form admin panel widgt
     */
    public function form($instance) {
        //$instance = get value from admin panel fields
        //$this->get_field_id('FIELDNAME') = avoid id conflict
        //$this->get_field_name('FIELDNAME') = avoid name conflict
        $title = (!empty($instance['title'])) ? $instance['title'] : __("Most Liked Posts", "rng-ajaxlike");
        $post_types = (!empty($instance['post_types']) and isset($instance['post_types'])) ? $instance['post_types'] : array('post');
        $posts_count = (!empty($instance['posts_count'])) ? $instance['posts_count'] : 4;
        $style = (!empty($instance['style'])) ? $instance['style'] : 0;
        $active_post_type = get_option("lj_setting_option");
        if ($active_post_type == FALSE) {
            $active_post_type = array("post");
        } else {
            $active_post_type = $active_post_type['lj_post_types'];
        }
        ?>
        <p>
            <label><?php _e("Title", "rng-ajaxlike"); ?></label>
            <input type="text" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" style="width: 100%;" name="<?php echo $this->get_field_name("title"); ?>" value="<?php echo $title; ?>">
        </p>
        <p>
            <label><?php _e("Select post types", "rng-ajaxlike"); ?></label>
            <select id="<?php echo $this->get_field_id("post-types") ?>" multiple="" name="<?php echo $this->get_field_name("post_types"); ?>[]" style="width: 100%;">
                <?php
                foreach ($active_post_type as $post_type) {
                    $selected = (in_array($post_type, $post_types)) ? 'selected=""' : '';
                    ?><option <?php echo $selected; ?> value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option><?php
                }
                ?>
            </select>
        </p>        
        <p>
            <label><?php _e("Posts per page", "rng-ajaxlike"); ?></label>
            <input type="number" id="<?php echo $this->get_field_id('posts-count'); ?>" style="width: 100%;" name="<?php echo $this->get_field_name('posts_count'); ?>" value="<?php echo $posts_count; ?>" />
        </p>    
        <?php
    }

    /**
     * save admin panel fields in $instance
     */
    public function update($new_instance, $old_instance) {
        //$old_instance = old instance
        //$new_instance = new instance
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['post_types'] = $new_instance['post_types'];
        $instance['posts_count'] = $new_instance['posts_count'];
        $instance['style'] = $new_instance['style'];

        return $instance;
    }

}

/**
 * register widget main function
 */
function register_lj_mostliked_posts() {
    register_widget("lj_mostliked_posts_widget");
}

add_action("widgets_init", "register_lj_mostliked_posts");
