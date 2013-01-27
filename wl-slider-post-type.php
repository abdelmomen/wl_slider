<?php

if(get_bloginfo( 'language' )=='ar')
{
    define('CPT_NAME', "عرض الشرائح");
    define('CPT_SINGLE', "شريحة");
    define('CPT_ADD', "اضف شريحة جديدة");
    define('WL_TXNM_NAME','نوعيات الشرائح');
    define('WL_TXNM_SINLGE','نوعية الشرائح');
}
else
{
    define('CPT_NAME', "Slider Images");
    define('CPT_SINGLE', "Slider Image");
    define('CPT_ADD', "Add New Slide");
    define('WL_TXNM_NAME','Slides Category');
    define('WL_TXNM_SINLGE','Slide Category');
}
    define('CPT_TYPE', "slider-image");
    define('WL_TXNM_TYPE', "slider-txnm");
    

add_theme_support('post-thumbnails', array(CPT_TYPE));

function efs_register() {

    $labels = array(
        'name'               => CPT_NAME,
        'singular_name'      => CPT_SINGLE,
        'add_new'            => CPT_ADD,
        'add_new_item'       => CPT_ADD,
        'menu_name'          => CPT_NAME
    );
    $args = array(
        'labels'    => $labels,
        'public'    => true,  
        'show_ui'   => true,  
        'capability_type'   => 'post',  
        'hierarchical'      => false,  
        'rewrite'   => true,  
        'supports'  => array('title', 'editor', 'thumbnail')  
       );  
    register_post_type(CPT_TYPE , $args );  
}
add_action('init', 'efs_register');  


function add_custom_taxonomies() {
    // Add new "Locations" taxonomy to slider-image
    register_taxonomy(WL_TXNM_TYPE, CPT_TYPE, array(
        // Not Hierarchical taxonomy (like tags)
        'hierarchical' => false,
        // This array of options controls the labels displayed in the WordPress Admin UI
        'labels' => array(
            'name' => WL_TXNM_NAME,
            'singular_name' => WL_TXNM_SINLGE,
            'menu_name' => WL_TXNM_NAME,
        ),
        // Control the slugs used for this taxonomy
        'rewrite' => array(
            'slug' => WL_TXNM_TYPE, // This controls the base slug that will display before each term
            'with_front' => false, // Don't display the category base before "/locations/"
            'hierarchical' => false // This will allow URL's like "/locations/boston/cambridge/"
        ),
    ));
}
add_action( 'init', 'add_custom_taxonomies', 0 );

// Add the Meta Box
function cd_meta_box_add()
{
    add_meta_box( 'my-meta-box-id',
        'My First Meta Box',
        'cd_meta_box_cb',
        'slider-image' ,
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cd_meta_box_add' );

function cd_meta_box_cb( $post )
{
    $values = get_post_custom( $post->ID );

    $text = isset( $values['my_meta_box_text'] ) ? esc_attr( $values['my_meta_box_text'][0] ) : '';

    $selected = isset( $values['my_meta_box_select'] ) ? esc_attr( $values['my_meta_box_select'][0] ) : '';

    $check = isset( $values['my_meta_box_check'] ) ? esc_attr( $values['my_meta_box_check'][0] ) : '';

    wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );

    include("wblp-fields-tpl.php");
}

function cd_meta_box_save( $post_id )
{
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;
    
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
    
    // now we can actually save the data
    $allowed = array( 
        'a' => array( // on allow a tags
            'href' => array() // and those anchords can only have href attribute
        )
    );
    
    // Probably a good idea to make sure your data is set
    if( isset( $_POST['my_meta_box_text'] ))
        update_post_meta( $post_id, 'my_meta_box_text', wp_kses( $_POST['my_meta_box_text'], $allowed ) );
        
    if( isset( $_POST['my_meta_box_select'] ))
        update_post_meta( $post_id, 'my_meta_box_select', esc_attr( $_POST['my_meta_box_select'] ) );
        
    // This is purely my personal preference for saving checkboxes
    $chk = ( isset( $_POST['my_meta_box_check'] ) && $_POST['my_meta_box_check'] ) ? 'on' : 'off';
        update_post_meta( $post_id, 'my_meta_box_check', $chk );
}

add_action( 'save_post', 'cd_meta_box_save' );
