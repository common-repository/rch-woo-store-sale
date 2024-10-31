<?php
/*
Plugin Name: RCH Store Sale for WooCommerce
Description: An eCommerce store sale for WooCommerce products such as day wise OR weekly.
Version: 1.0.0
Author: woostoresale            
WC requires at least: 3.2.0
WC tested up to: 4.2
Tested up to: 5.4
License: GPLv2 or later
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	require plugin_dir_path( __FILE__ ) . 'classes/rcodehub-class-rch-WC-store-sale.php';
	function rcodehub_run_WC_store_sale() {
		global $rcodehub_WC_rch_store_sale;
		$rcodehub_WC_rch_store_sale = new rcodehub_WC_rch_store_sale();
		$rcodehub_WC_rch_store_sale->rcodehub_run();
	}
	add_action( 'woocommerce_init', 'rcodehub_run_WC_store_sale' );
} else {
	add_action( 'admin_notices', 'rcodehub_WC_rch_store_sale' );
	function rcodehub_WC_rch_store_sale() {
		global $current_screen;
		if ( $current_screen->parent_base == 'plugins' ) {
			echo '<div class="error"><p>WooCommerce Store Sale ' . esc_html__( 'requires <a href="http://www.woothemes.com/WooCommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="' . admin_url( 'plugin-install.php?tab=search&type=term&s=WooCommerce' ) . '" target="_blank">WooCommerce</a> first.', 'wc_storesale' ) . '</p></div>';
		}
	}
	$plugin = plugin_basename( __FILE__ );
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( $plugin ) ) {
		deactivate_plugins( $plugin );
	}
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}
