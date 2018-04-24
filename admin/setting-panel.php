<?php
if(!current_user_can("manage_options")){
    return;
}
if(isset($_GET['settings-updated']) and $_GET['settings-updated'] == TRUE){
    add_settings_error("lj_general_setting", "lj_general_setting" , __("Settings saved","rng-ajaxlike") , "updated");
}elseif(isset($_GET['settings-updated']) and $_GET['settings-updated'] == FALSE){
    add_settings_error("lj_general_setting", "lj_general_setting" , __("Error with saving","rng-ajaxlike"));
}
?>
<div class="wrap">
    <h1><?php echo get_admin_page_title(); ?></h1>
    <form action="options.php" method="post">
        <?php 
        settings_fields("lj_general_settings");
        do_settings_sections("lj_general_settings");
        submit_button(__("save","rng-ajaxlike"));
        ?>
    </form>
</div>

