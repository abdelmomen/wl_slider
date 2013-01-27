<?php 
//--------------------------------------------------------------
// create custom plugin settings menu

function wl_create_menu() {

    //create new top-level menu
    add_menu_page('WL Slider ',
        'WL Slider ',
        'administrator',
        __FILE__,
        'wl_settings_page',
        plugins_url('/images/icon.png', __FILE__)// TODO ADD ICON
    );

    //call register settings function
    add_action( 'admin_init', function (){
        //register our settings
        register_setting( 'wl-settings-group', 'wl_slider_color' );
        register_setting( 'wl-settings-group', 'wl_slider_version' );
    });
}
add_action('admin_menu', 'wl_create_menu');


function wl_settings_page() {
?>
<div class="wrap">
    <h2> <?php echo WL_PLG_NAME;?> Setting</h2>

    <form method="post" action="options.php">

    <?php settings_fields( 'wl-settings-group' ); ?>
    <p>
        <label for="wl_slider_version"> Slider Version </label>
        <select name="wl_slider_version" id="wl_slider_version">
            <option value="1" <?php selected( get_option('wl_slider_version') , 1 ); ?>>1</option>
            <option value="2" <?php selected( get_option('wl_slider_version') , 2 ); ?>>2</option>
        </select>
    </p>

    <p>
        <label for="wl_slider_color"> Slider Color </label>
        <input type="text" name="wl_slider_color" value="<?php echo get_option('wl_slider_color'); ?>" />
    </p>
    <?php submit_button(); ?>

    </form>
</div>
<?php 
} // End wl_settings_page
?>