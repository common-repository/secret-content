<?php //æøå (utf-8 without bom preserver)
/*
Plugin Name: Secret Content
Plugin URI: http://oneconsult.dk/wordpress
Description: Adds metabox to posts/pages - Tick the checkbox to hide content from non logged in visitors.
Author: Emil Vang Arffmann
Version: 1.0
Author URI: http://oneconsult.dk
*/

// 1.0 - In english, with Danish translation

//  TODO and considerations:
//  add overview/list of all secrified posts/pages, quick change to non secret
//  add icon on post/page admin overview to indicate its for logged in members only.
//  add functionality to "quick edit" area
//  add functionality to "admin bar" area

// check RSS - verified, works!

register_activation_hook( __FILE__, 'secret_content_activation_hook' );
function secret_content_activation_hook()
{
};

register_deactivation_hook(__FILE__, 'secret_content_deactivate_hook');
function secret_content_deactivate_hook()
{
};

register_uninstall_hook(__FILE__, 'secret_content_uninstall_hook');
function secret_content_uninstall_hook()
{
};


load_plugin_textdomain('secret_textdomain', false, dirname(plugin_basename(__FILE__)) . '/languages');


/* Adds a box to the main column on the Post and Page edit screens */
add_action( 'add_meta_boxes', 'secret_meta_box' );

function secret_meta_box() {
    add_meta_box(
        'secret_sectionid',
        _x( 'Show this to logged in visitors only?', 'Headline for checkbox / activation on posts', 'secret_textdomain' ),
        'secret_render_meta_box_content',
        'post',
        'side',
        'high'
    );
    add_meta_box(
        'secret_sectionid',
        _x( 'Show this to logged in visitors only?', 'Headline for checkbox / activation on pages', 'secret_textdomain' ),
        'secret_render_meta_box_content',
        'page',
        'side',
        'high'
    );
}


function secret_render_meta_box_content( $post )
{
  wp_nonce_field( plugin_basename( __FILE__ ), 'secret_noncename' );

  $microcopy = _x('Checking this, will make this ONLY visible to registered users who are logged IN', 'Text describes the result of having activated / checked this box', 'secret_textdomain' );

  $secretthis = get_post_meta($post->ID,'_secret_new_field',true);
  if ( $secretthis )
  {
    echo '<input type="checkbox" class="checkbox" id="secret_new_field" name="secret_new_field" checked="yes" />';
  }
  else
  {
    echo '<input type="checkbox" class="checkbox" id="secret_new_field" name="secret_new_field"  />';
  }
  echo '<label for="secret_new_field">';
  echo $microcopy;
  echo '</label> ';
}

// maby switch this to jquery/AJAX instead..
add_action( 'save_post', 'secret_save_postdata' );

function secret_save_postdata( $post_id )
{
  // awfully lot of security for just saving a checkbox eh?!

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      return;

  if ( !wp_verify_nonce( $_POST['secret_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  if ( 'page' == $_POST['post_type'] )
  {
    if ( !current_user_can( 'edit_page', $post_id ) )
        return;
  }
  else
  {
    if ( !current_user_can( 'edit_post', $post_id ) )
        return;
  }

  $secretthis = $_POST['secret_new_field'];
  update_post_meta($post_id, '_secret_new_field', $secretthis);
}


// limit the visibility of posts/pages if they have the meta value/key pair..
//add_filter('parse_query', 'secret_modified_query');   // both works
add_filter('pre_get_posts', 'secret_modified_query');   // both works

function secret_modified_query( $q )
{
  global $wpdb;
  if ( !is_admin() )
  {
    if ( !is_user_logged_in() )
    {
      $secrefied = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_secret_new_field' AND meta_value = 'on' ");
      $q->set('post__not_in', $secrefied );
    }
  }
  return $q;
}


// remove from wp_list_pages
add_filter('wp_list_pages_excludes', 'secret_wp_list_pages_exclude_array' );

function secret_wp_list_pages_exclude_array( $exclude_array )
{
  global $wpdb;
  if ( !is_admin() )
  {
    if ( !is_user_logged_in() )
    {
      $secrefied = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_secret_new_field' AND meta_value = 'on' ");
      $exclude_array = array_merge( $exclude_array, $secrefied );
    }
  }
  return $exclude_array;
}


// and remove from wp_nav_menu
// add_filter('wp_nav_menu_excludes', 'secret_wp_nav_menu_exclude_array'); // this filter dosent work
add_filter( 'wp_get_nav_menu_items', 'secret_wp_nav_menu_exclude_array', null, 3 );

function secret_wp_nav_menu_exclude_array( $items, $menu, $args )
{
  global $wpdb;
  $secrefied = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_secret_new_field' AND meta_value = 'on' ");
  $secreted_parents = array();

  if ( !is_admin() )
  {
    if ( !is_user_logged_in() )
    {
      // Iterate over the items to search and destroy
      foreach ( $items as $key => $item )
      {
        if ( in_array( $item->object_id, $secrefied ) )
        {
          $secreted_parents[] = $item->ID;
          unset( $items[$key] );
        }

        // if the parent is secret, then so are the children
        if ( in_array( $item->menu_item_parent, $secreted_parents ) )
        {
          $secreted_parents[] = $item->ID;
          unset( $items[$key] );
        }
      }
    }
  }
  return $items;
}


// more removal.. // for custom queries, like pages listing subpages.. ( I need this :)
add_filter('the_posts', 'secret_the_posts');

function secret_the_posts( $posts )
{
  if ( !is_admin() ) // apparently this does NOT work in ADMIN.. thus the last return $posts are needed.
  {
    if ( !is_user_logged_in() )
    {
      global $wpdb;
      $secrefied = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_secret_new_field' AND meta_value = 'on' ");

      foreach ( $posts as $key => $item )
      {
        if ( in_array( $item->ID, $secrefied ) )
        {
          unset( $posts[$key] );
          $posts = array_values($posts);  // rearrange the array
        }
      }
    }
  }
  return $posts;
};


// remove link from next / prev post link navigation... sigh...
add_filter( 'get_previous_post_where', 'secret_navigation_exclude_where' );
add_filter( 'get_next_post_where', 'secret_navigation_exclude_where' );

function secret_navigation_exclude_where( $where )
{
  if ( !is_admin() )
  {
    if ( !is_user_logged_in() ) // if NOT logged in :)
    {
      global $wpdb;
      $secrefied = $wpdb->get_col("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_secret_new_field' AND meta_value = 'on' ");

      if ( $secrefied )
      {
        $secretwhere = '';
        foreach ( $secrefied as $pid )
        {
          $secretwhere .= ' AND p.id not like \''. $pid . '\' ';
        }
        $where = $where . $secretwhere;
      }
    }
  }
  return $where;
}



/* links -----------------------------------------------------------------------

// LOCALIZATION
   http://codex.wordpress.org/I18n_for_WordPress_Developers
   http://blog.wpessence.com/localizing-your-plugin-with-load_plugin_textdomain/
   http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
   http://urbangiraffe.com/articles/translating-wordpress-themes-and-plugins/

// MODIFYING QUERYS
   http://wordpress.stackexchange.com/questions/13424/modify-main-wordpress-loop-with-a-parse-query-filter
   http://wordpress.stackexchange.com/questions/19073/is-there-a-way-to-exclude-posts-based-on-meta-values
   http://wordpress.org/support/topic/parse_query-filter-changed-in-31?replies=11
   http://wordpress.stackexchange.com/questions/2204/how-to-create-a-page-that-isnt-accessible-via-menus
   http://wordpress.stackexchange.com/questions/5453/how-do-i-remove-pages-from-search
   http://ehsanis.me/2010/11/30/wordpress-wp_nav_menu-to-exclude-pages/
   http://wordpress.stackexchange.com/questions/31748/dynamically-exclude-menu-items-from-wp-nav-menu
   http://advancedcustomfields.com/support/discussion/863/custom-single-post-prevnext-link-sorting-by-acf-order/p1

// OTHER
   not directly related, but stuff I cann consider when progressing..
   http://wordpress.stackexchange.com/questions/1482/restricting-users-to-view-only-media-library-items-they-have-uploaded
   http://wordpress.stackexchange.com/questions/578/adding-a-taxonomy-filter-to-admin-list-for-a-custom-post-type

   if doing some group thing in the future..
   http://wordpress.stackexchange.com/questions/20368/filter-all-queries-with-a-specific-taxonomy

   fun with wp repository
   http://wpdevel.wordpress.com/2011/12/21/been-giving-a-lot-of-thought-to-how/
   http://wp.smashingmagazine.com/2011/11/23/improve-wordpress-plugins-readme-txt/

*/
?>