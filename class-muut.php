<?php
/**
 * The new-wave commenting and forums for Wordpress
 *
 * @package  Muut
 * @author   Tero Piirainen <tero@muut.com>
 * @license  MIT
 * @link    https://muut.com/docs/wordpress.html
 * @copyright 2014 Muut Inc
 */

class Muut {

  protected $version = '2.0.14';

  protected $plugin_slug = 'muut';

  protected static $instance = null;

  protected $plugin_screen_hook_suffix = null;


  private function __construct() {

	add_action( 'admin_init', array( $this, 'upgradeToMuut' ) );
	add_action( 'init', array( $this, 'disregardOldMoot' ), 2 );

    add_filter('the_content', array($this, 'default_comments'));

    add_action('wp_enqueue_scripts', array($this, 'muut_includes'));
    add_action('wp_head', array($this, 'muut_head'));
    add_action('admin_menu', array($this, 'muut_admin_menu'));
    add_action('admin_init', array($this, 'muut_settings'));

	add_shortcode('muut', array($this, 'muut_shortcode'));
	add_shortcode('no-muut', array($this, 'muut_disable'));

	// Deprecated
	add_shortcode('moot', array($this, 'muut_shortcode'));
    add_shortcode('no-moot', array($this, 'muut_disable'));
  }


  public static function get_instance() {
    if (null == self::$instance) { self::$instance = new self; }
    return self::$instance;
  }

  public function disregardOldMoot() {
	  if ( class_exists( 'Moot' ) ) {
	  	remove_filter('the_content', array(Moot::get_instance(), 'default_comments'));

	  	remove_action('wp_enqueue_scripts', array(Moot::get_instance(), 'moot_includes'));
	  	remove_action('wp_head', array(Moot::get_instance(), 'moot_head'));
	  	remove_action('admin_menu', array(Moot::get_instance(), 'moot_admin_menu'));
	  	remove_action('admin_init', array(Moot::get_instance(), 'moot_settings'));
	  }
  }

  public function upgradeToMuut() {
	  $muut_options = get_option( 'muut_options', array() );
	  if ( !isset( $muut_options['current_version'] ) || ( is_string( $muut_options['current_version'] ) && version_compare( $muut_options['current_version'], '2.0.13', '<' ) ) ) {
		  update_option( 'muut_forum_name', get_option( 'moot_forum_name', '' ) );
		  update_option( 'muut_api_key', get_option( 'moot_api_key', '' ) );
		  update_option( 'muut_secret_key', get_option( 'moot_secret_key', '' ) );
		  update_option( 'muut_language', get_option( 'moot_language', '' ) );
		  update_option( 'muut_generate', get_option( 'moot_generate', '' ) );
		  update_option( 'muut_comments_under_forums', get_option( 'moot_comments_under_forums', '' ) );

		  delete_option( 'moot_forum_name' );
		  delete_option( 'moot_api_key' );
		  delete_option( 'moot_secret_key' );
		  delete_option( 'moot_language' );
		  delete_option( 'moot_generate' );
		  delete_option( 'moot_comments_under_forums' );
	  }

	  if ( !isset( $muut_options['current_version'] ) || version_compare( $muut_options['current_version'], $this->version, '<' ) ) {
		  update_option( 'muut_options', array( 'current_version' => $this->version ) );
	  }
  }

  public function muut_head() {
    require_once(plugin_dir_path(__FILE__) . 'public.php');
  }

  public function muut_includes() {

    if (!is_home()) {
      wp_enqueue_style("moot", '//cdn.muut.com/latest/moot.css', array(), $this->version);

      $lang = get_option('muut_language');
      if ($lang == 'en') $lang = "";
      if ($lang) $lang = "." . $lang;

      wp_enqueue_script("", "//cdn.muut.com/latest/moot$lang.min.js", array('jquery'), $this->version);
    }
  }

  public function default_comments($content) {
    $forumname = get_option('muut_forum_name');

	if (!is_home() && $forumname != null && get_option('muut_generate') == "true" && in_array( get_post_type(), array( 'post', 'page' ) ) ) {
      $page_id = sanitize_title(get_the_title());
      $content .= "<a id='moot-default-comments' href='https://moot.it/i/$forumname/wordpress:$page_id'>Comments</a>";
    }

    return $content;
  }

  public function muut_settings($content) {
    register_setting('muut_options', 'muut_forum_name');
    register_setting('muut_options', 'muut_api_key');
    register_setting('muut_options', 'muut_secret_key');
    register_setting('muut_options', 'muut_language');
    register_setting('muut_options', 'muut_generate');
    register_setting('muut_options', 'muut_comments_under_forums');
  }

  // admin menu
  public function muut_admin_menu() {

    if (is_super_admin()) {
      $this->plugin_screen_hook_suffix = add_plugins_page(
        __('Muut', $this->plugin_slug),
        __('Muut', $this->plugin_slug),
        'read', $this->plugin_slug, array($this, 'muut_admin')
      );
    }

  }

  public function muut_admin() {
    include_once('settings.php');
  }

  public function muut_disable() {
    return "<span id='no-moot'></span>";
  }

  public function muut_shortcode($params) {

    extract( shortcode_atts( array(
      'forum' => false,
      'threaded' => false,
      'path' => false

    ), $params) );

    $forumname = get_option('muut_forum_name');

    if ($forumname == null) return "";

    $tag = "<a id='moot' href='https://moot.it/i/$forumname";
    $page_id = sanitize_title(get_the_title());


    // (bool ? this : that) not working
    if ($forum)   return "$tag'>$forumname forums</a>";
    if ($threaded) return "$tag/wordpress/$page_id'>Comments</a>";
    if ($path)    return "$tag/$path'>Comments are here</a>";
              return "$tag/wordpress:$page_id'>Comments</a>";

  }

}