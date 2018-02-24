<?php
/**
 * Created by PhpStorm.
 * Author: npbtrac@yahoo.com
 * Date time: 1/26/18 11:20 AM
 */

namespace Enpii\WpPlugin\Fbcs\Base;


use Enpii\WpPlugin\Fbcs\Fbcs;

class Admin extends BaseObject {

	/**
	 * Admin constructor.
	 * Initialize all hook related to admin
	 *
	 * @param null $config
	 */
	public function __construct( $config = null ) {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );

//		add_action('wp_ajax_fbcs_sync_latest_posts', [WpPlugin::instance(), 'ajax_sync_latest_posts']);
//		add_action('wp_ajax_fbcs_sync_top_posts', [WpPlugin::instance(), 'ajax_sync_top_posts']);
	}

	/**
	 * Hook to attach to admin_init action
	 * Import old options to group option
	 */
	function admin_init() {
		register_setting( Fbcs::OPTION_GROUP_NAME, Fbcs::OPTION_KEY );
	}

	/**
	 * Hook to attach to admin_menu action
	 * Add more menu item to admin menu
	 */
	function admin_menu() {
		add_submenu_page( 'options-general.php', 'Enpii - Facebook Comments Sync', 'Enpii - FBCS Options', 'manage_options', 'fbcs', [
			$this,
			'display_options_page'
		] );
	}

	/**
	 * Display options page in Admin Panel
	 */
	function display_options_page() {
		$options = get_option( Fbcs::OPTION_KEY );
		if ( empty($options)  ) {
			$options = Fbcs::default_options();
		}

		include( Fbcs::plugin_dir_path() . '/views/admin/options-page.php' );
	}

	/**
	 * Hook to attach to admin_menu action
	 * Add more menu item to admin menu
	 */
	function display_options() {
		add_menu_page( 'Facebook Comments Sync Options', 'Shopback - Facebook Comments Sync', 'manage_options', 'fbcs', [
			$this,
			'options'
		] );
	}

}