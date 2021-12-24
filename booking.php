<?php
error_reporting(0);


/*  Plugin Name: Booking
  Plugin URI: https://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  //Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */


  function create_posttype() {
 
     register_post_type('booking',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Booking Data' ),
                'singular_name' => __( 'Booking Data' )
            ),
               'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
      
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_in_rest' => true,
         'taxonomies'       => array( 'author', 'publisher' ),
 
        )
    );


}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );

add_action( 'init', 'create_author_hierarchical_taxonomy');
 
//create a custom taxonomy name it subjects for your posts

function create_author_hierarchical_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Author', 'taxonomy general name' ),
    'singular_name' => _x( 'Author', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Author' ),
    'all_items' => __( 'All Author' ),
    'parent_item' => __( 'Parent Author' ),
    //'parent_item_colon' => __( 'Parent Author:' ),
    'edit_item' => __( 'Edit Author' ), 
    'update_item' => __( 'Update Author' ),
    'add_new_item' => __( 'Add NewAuthor' ),
    'new_item_name' => __( 'New Author Name' ),
    'menu_name' => __( 'Author' ),
  );    
 
// Now register the taxonomy
  register_taxonomy('author',array('booking'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'author' ),
  ));
 
}


add_action( 'init', 'create_publisher_hierarchical_taxonomy' );
 
//create a custom taxonomy name it subjects for your posts
 
function create_publisher_hierarchical_taxonomy() {
 
// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI
 
  $labels = array(
    'name' => _x( 'Publisher', 'taxonomy general name' ),
    'singular_name' => _x( 'Publisher', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Publisher' ),
    'all_items' => __( 'All Publisher' ),
    'parent_item' => __( 'Parent Publisher' ),
    'parent_item_colon' => __( 'Parent Publisher:' ),
    'edit_item' => __( 'Edit Publisher' ), 
    'update_item' => __( 'Update Publisher' ),
    'add_new_item' => __( 'Add New Publisher' ),
    'new_item_name' => __( 'New Publisher Name' ),
    'menu_name' => __( 'Publisher' ),
  );    
 
// Now register the taxonomy
  register_taxonomy('publisher',array('booking'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'publisher' ),
  ));
 
}

function hcf_register_meta_boxes() {
   add_meta_box( 'samplepost_meta_box', 'Custom Booking Fields', 'display_samplepost_meta_box','booking', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'hcf_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function display_samplepost_meta_box( $samplepost ) {
   
  ?>
    <h4>General Details</h4>
    <table width="100%">
        <tr>
            <td style="width: 25%">Booking Price</td>
            <td><input type="text" style="width:425px;" name="meta[book_price]" value="<?php echo esc_html( get_post_meta( $samplepost->ID, 'book_price', true ) );?>" />
            </td>
        </tr>
        
    </table>

<?php
}

add_action( 'save_post', 'add_employeedata_fields', 10, 2 );
function add_employeedata_fields($samplepost_id, $samplepost ) {
    if ( $samplepost->post_type == 'booking' ) {
        if ( isset( $_POST['meta'] ) ) {
            foreach( $_POST['meta'] as $key => $value ){
                //print_r($value);die();
                update_post_meta($samplepost->ID, $key, $value );
            }
        }
    }
}

add_filter( 'manage_booking_posts_columns', 'set_custom_booking_columns' );
function set_custom_booking_columns($columns) {
    unset( $columns['author'] );
    $columns['image'] = __( 'Featured Image', 'your_text_domain' );
      return $columns;
}

add_action( 'manage_booking_posts_custom_column' , 'custom_booking_column', 10, 2 );
function custom_booking_column( $column, $post_id ) {
    switch ( $column ) {

           case 'image':
            echo get_the_post_thumbnail($post_id, 'thumbnail');
            break;
    }
}


 add_action( 'wp_enqueue_scripts', 'emp_enque_script' );
function emp_enque_script() {
    wp_enqueue_style( 'datatables-style', 'http://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css' );
    wp_enqueue_script('datatables', 'https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js',[], null, true);
    wp_enqueue_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js',[], null, true);
     wp_enqueue_script('js', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
    wp_enqueue_script( 'custom_js', plugins_url( '/js/custom.js', __FILE__ ));
    wp_enqueue_script('datatables-sorting', 'https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js',[], null, true);
   }
function  display_data() {
   ?>
    <center>
      <br>
   	  <table id="example" class="table table-striped table-bordered" style="width:100%">
       <thead>
        <tr>
       <th>ID </th>
       <th>Title</th>
       <th>Price</th>
       <th>Featured Image</th>
    
    </tr>
    </thead>
    <tbody>
 
        <?php

       // $current_category = get_queried_object(); 
        //$cats = get_categories();
        
        $args = array(
        'post_type'      => 'booking',
        'posts_per_page' => -1,
        'post_status' => 'publish',
         'orderby'      => 'name',
        'order'          => 'ASC',
       // 'taxonomy'   => array('author','publisher'),
      //  'cat' => $current_category->cat_ID,
       
           
    );
    $parent = new WP_Query( $args );
    
 
 
    if ( $parent->have_posts() ) :  
      
         while ( $parent->have_posts() ) : $parent->the_post();
             
            $id= get_the_ID(); 

                   ?>
                <tr>
                <td><?php echo $id;?></td>
                <td><?php the_title();  ?></td>
                <td><?php echo  get_post_meta($id,'book_price',true );  ?></td>
                <td>
                   <?php
                   if (has_post_thumbnail( $id ) ):
                  $image = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail');
                  ?>
                <img src="<?php echo $image[0]; ?>">  
               <?php endif; ?>
    </td>
    
 
              </tr>
            <?php endwhile; ?>
        <?php endif; 
          wp_reset_postdata();
        ?>
       </tbody>
       </table>
      </center>
     <?php
    }

   add_shortcode('booking_data', 'display_data' );
   ?>

