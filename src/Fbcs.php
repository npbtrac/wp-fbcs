<?php
/**
 * Created by PhpStorm.
 * User: tracnguyen
 * Date: 2/15/18
 * Time: 9:59 PM
 */

namespace Enpii\WpPlugin\Fbcs;


use Enpii\WpPlugin\Fbcs\Base\Admin;
use Enpii\WpPlugin\Fbcs\Base\Main;

class Fbcs {
	const OPTION_KEY = 'fbcs';
	const OPTION_GROUP_NAME = 'fbcs_options';
	const COMMENT_META_KEY_FB_COMMENT_ID = "fb_comment_id";
	const COMMENT_META_KEY_FB_AUTHOR_AVATAR_URL = "fb_author_avatar_url";

	/**
	 * @var null|Fbcs
	 */
	static protected $_instance = null;

	/**
	 * @var null|Admin
	 */
	public $admin = null;

	/**
	 * @var null|Main
	 */
	public $main = null;
	public $wp_cli_command = null;

	public $text_domain = null;
	public $plugin_dir_path = null;
	public $plugin_dir_url = null;

	/**
	 * @var mixed|null options of the plugin from database
	 */
	public $options = null;

	/**
	 * Fbcs constructor.
	 * Only invoked for singleton object
	 *
	 * @param null $config
	 */
	private function __construct( $config = null ) {
		foreach ( $config as $config_key => $config_val ) {
			if ( property_exists( $this, $config_key ) ) {
				$this->$config_key = $config_val;
			}
		}

		$this->options = get_option( static::OPTION_KEY );
	}

	/**
	 * Get singleton instance
	 *
	 * @param $config null|array config params for the instance
	 *
	 * @return static|null
	 */
	public static function instance( $config = null ) {
		if ( null === static::$_instance ) {
			static::$_instance = new static( $config );
		}

		return static::$_instance;
	}

	/**
	 * Get the text domain of the plugin
	 *
	 * @return null|string
	 */
	public static function text_domain() {
		return static::instance()->text_domain;
	}

	/**
	 * Get the plugin directory
	 *
	 * @return null|string
	 */
	public static function plugin_dir_path() {
		return static::instance()->plugin_dir_path;
	}

	/**
	 * Get the plugin url
	 *
	 * @return null|string
	 */
	public static function plugin_dir_url() {
		return static::instance()->plugin_dir_url;
	}

	/**
	 * Add more links to plugin links in Admin Plugin screen
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public static function plugin_action_links( $links ) {
		$file_name     = 'options-general.php';
		$settings_link = '<a href="' . $file_name . '?page=fbcs">' . __( 'Settings', static::text_domain() ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Get default option values for the plugin
	 *
	 * @return array
	 */
	public static function default_options() {
		return [
			'fb_app_id'            => null,
			'fb_app_secret'        => null,
			'fb_moderators'        => null,
			'fbjs'                 => 1,
			'insert_after_content' => 1,
			'onlyfb'               => null,
			'both'                 => null,
			'posts'                => null,
			'pages'                => null,
			'homepage'             => null,
			'language'             => 'en_US',
			'num'                  => 12,
			'scheme'               => 'light',
			'order'                => 'reverse_time',
			'width'                => '100%',
			'title_text'           => __( 'Facebook Comments', static::text_domain() ),
			'title_class'          => null,
			'title_id'             => null,
		];
	}

	public function init() {
		if ( is_admin() ) {
			if ( null === static::$_instance->admin ) {
				static::$_instance->admin = new Admin();
			}
		}

		if ( null === static::$_instance->main ) {
			static::$_instance->main = new Main();
		}

		add_action( 'wp_head', [ $this->main, 'fb_graph_info' ] );
		add_action( 'wp_head', [ $this->main, 'fb_init_top' ], 100 );
		add_action( 'wp_head', [ $this->main, 'fb_init_top_ajax' ], 100 );
		add_action( 'wp_footer', [ $this->main, 'fb_comments_ajax' ], 100 );

		add_filter( 'the_content', [ $this->main, 'add_fb_comment_box' ], 100 );

		// Ajax actions
		add_filter( 'wp_ajax_fbcs_sync_latest_posts', [ $this->main, 'ajax_sync_latest_posts' ], 100 );
		add_filter( 'wp_ajax_fbcs_fb_comment_created', [ $this->main, 'ajax_fb_comment_created' ], 100 );
		add_filter( 'wp_ajax_fbcs_fb_comment_removed', [ $this->main, 'ajax_fb_comment_removed' ], 100 );
	}
}