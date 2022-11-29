<?php
function lee_register_footer(){
  $labels = array(
    'name' => __( 'Footer', 'ltheme_domain' ),
    'singular_name' => __( 'Footer', 'ltheme_domain' ),
    'add_new' => __( 'Add New Footer', 'ltheme_domain' ),
    'add_new_item' => __( 'Add New Footer', 'ltheme_domain' ),
    'edit_item' => __( 'Edit Footer', 'ltheme_domain' ),
    'new_item' => __( 'New Footer', 'ltheme_domain' ),
    'view_item' => __( 'View Footer', 'ltheme_domain' ),
    'search_items' => __( 'Search Footers', 'ltheme_domain' ),
    'not_found' => __( 'No Footers found', 'ltheme_domain' ),
    'not_found_in_trash' => __( 'No Footers found in Trash', 'ltheme_domain' ),
    'parent_item_colon' => __( 'Parent Footer:', 'ltheme_domain' ),
    'menu_name' => __( 'Footers', 'ltheme_domain' ),
  );

  $args = array(
      'labels' => $labels,
      'hierarchical' => true,
      'description' => 'List Footer',
      'supports' => array( 'title', 'editor' ),
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 5,
      'show_in_nav_menus' => false,
      'publicly_queryable' => false,
      'exclude_from_search' => false,
      'has_archive' => true,
      'query_var' => true,
      'can_export' => true,
      'rewrite' => false
  );

  register_post_type( 'footer', $args );

  if($options = get_option('wpb_js_content_types')){
    $check = true;
    foreach ($options as $key => $value) {
      if($value=='footer') $check=false;
    }
    if($check)
      $options[] = 'footer';
  }else{
    $options = array('page','footer');
  }
  update_option( 'wpb_js_content_types',$options );

}

add_action('init','lee_register_footer', 0);