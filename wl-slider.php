<?php  
/* 
Plugin Name: WebLabs Slider 
Plugin URI: 
Description: A simple plugin that integrates Different Sliders (http://flex.madebymufffin.com/) with WordPress using custom post types! 
Author: Abdelmomen Bauomy
Version: 1.0 
Author URI: wordpress.weblabs.me/wlslider
*/  

define('WL_PLG_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );  
define('WL_PLG_NAME', "WebLabs FlexSlider");

include("wl-slider-post-type.php");

$flex_version=get_option('wl_slider_version');

if(empty($flex_version))
    $flex_version=1;

// the right way to enqueue_scripts after wp do his enqueues
function thematic_enqueue_scripts(){

    global $flex_version;

    if($flex_version==1){
        wp_enqueue_style('flexslider_css', WL_PLG_PATH.'flexslider_1.css');
        wp_enqueue_script('flexslider', WL_PLG_PATH.'jquery.flexslider-min_1.js', array('jquery'));
    }
    else {
        wp_enqueue_style('flexslider_css', WL_PLG_PATH.'flexslider.css');
        wp_enqueue_script('flexslider', WL_PLG_PATH.'jquery.flexslider-min.js', array('jquery'));
    }
}

// check if not in the admin pages 
if(! is_admin())
    add_action('wp_enqueue_scripts', 'thematic_enqueue_scripts');

function wl_add_scripts(){

    global $flex_version;

    if($flex_version == 1 )
        $animation="fade";
    else 
        $animation="slide";

    // Version 2 slider options
    // https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties
?>
<!-- invoke the slides -->
<script type='text/javascript'> 
/* important to use window.load() 
to ensure the content of the page is loaded before the plugin initializes.
*/
jQuery(window).load(function () {
      jQuery('.flexslider').flexslider(
        {
            animation: "<?php echo $animation ;?>",
        }); 
});
</script>

<?php
}// End wl_add_scripts
add_action('wp_head', 'wl_add_scripts');


function wl_slider($width=null,$height=null,$txm=''){  

    $style="style='";

    if(!empty($width))
        $style.="width:{$width}px;";
    if(!empty($height))
        $style.="height:{$height}px;";
        
    $style.="'";
        
    $slider= "<div class='flexslider' $style > 
      <ul class='slides'>";

    // Create The Query Associative array
    $slides_query = array();

    $slides_query["post_type"] = CPT_TYPE;

    if(!empty($txm))
        $slides_query[WL_TXNM_TYPE] = $txm;
    
    query_posts($slides_query);

    if (have_posts()) : 
        while (have_posts()) : the_post();  
	    
    	$values = get_post_custom(get_the_ID());
    	
   		$url = isset( $values['my_meta_box_url'] ) ? esc_attr( $values['my_meta_box_url'][0] ) : '';
   		
   		$args=array(
        	'alt'=>get_the_title(),
       		'title'=>get_the_title(),
        );
        
        $slider.= '<li>';
        if(!empty($url))
        	$slider.= '<a href="'.$url.'">';
        // Now add the image
        $slider.=get_the_post_thumbnail(null,null,$args);// the Null to get the width as it uploaded
        
        if(!empty($url))
        	$slider.= '</a>';
        	
        $slider.= '</li>';
        
        endwhile; 
    endif; 
    wp_reset_query();

    $slider.= '</ul> 
    </div>';

    return $slider;
}

// Add Wordpress short code as (PHP 5.3) anonymous function
add_shortcode('wl_slider', function ($atts){
    $width=$height=$txm=0;

    if(!empty($atts['width']))
        $width=$atts['width'];

    if(!empty($atts['height']))
        $height=$atts['height'];

    if(!empty($atts['txm']))
        $txm=$atts['txm'];

    return wl_slider($width,$height,$txm);
});

// Adding Admin Custom Setting
include("wl-slider-admin-menu.php");
