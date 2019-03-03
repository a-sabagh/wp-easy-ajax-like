<?php

class lj_init{
    public $version;
    public $slug;
    
    public function __construct($version,$slug){
        $this->version = $version;
        $this->slug = $slug;
        add_action('plugins_loaded', array($this, 'add_text_domain'));
        add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts',array($this,'public_enqueue_scripts'));
        $this->load_modules();
    }
    
    public function add_text_domain(){
        load_plugin_textdomain($this->slug, FALSE, LJ_PRT . "/languages");
        include trailingslashit(__DIR__) . "translate.php";
    }
    
    public function admin_enqueue_scripts($hooks){
        wp_enqueue_style( 'wp-color-picker' ); 
        wp_enqueue_style("lj-admin-style",LJ_PDU . "/assets/css/admin-style.css");
        wp_enqueue_script("lj-admin-script",LJ_PDU . "assets/js/admin-script.js",array("jquery","wp-color-picker"), "" , TRUE);
    }
    
    public function public_enqueue_scripts(){
        wp_enqueue_style("lj-style",LJ_PDU . "/assets/css/style.css");
        wp_enqueue_script("lj-script",LJ_PDU . "/assets/js/script.js",array("jquery"), "" , TRUE);
    }
    
    private function load_modules(){
        include trailingslashit(__DIR__) . "class.controller.settings.php";
        include trailingslashit(__DIR__) . "class.controller.like.php";
        include trailingslashit(__DIR__) . "widgets/mostliked-posts.php";
    }
}