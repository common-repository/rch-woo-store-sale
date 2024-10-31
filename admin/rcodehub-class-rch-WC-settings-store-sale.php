<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Settings_Page' ) ) :
	class rcodehub_WC_Settings_RCH_Store_Sale extends WC_Settings_Page {
		public function __construct() {
			$this->id    = 'rcodhub_store_sale';
			$this->label = esc_html__( 'Store Sale', 'RCODEHUB-WooCommerce-Store-Sale' );
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'rcodehub_save' ) );
		}

		public function get_settings() {
			$rcodehub_product_categories_select  = array();
			$rcodehub_product_tag_select        = array();
			$rcodehub_product_categories 		= get_terms( 'product_cat' );
			$rcodehub_product_tag        		= get_terms( 'product_tag' );
			foreach ( $rcodehub_product_categories as $key => $value ) {
				$rcodehub_product_categories_select [ $value->term_id ] = $value->name;
			}
			foreach ( $rcodehub_product_tag as $key => $value ) {
				$rcodehub_product_tag_select [ $value->term_id ] = $value->name;
			}

			return apply_filters(
				'woocommerce_' . $this->id . '_settings', array(
					array(
						'title' => esc_html__( 'Store Sale Options', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'rcodehub_rch_store_options',
					),
					array(
						'title'    => esc_html__( 'Enable store sales', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'checkbox',
						'id'       => 'rcodehub_sale_enable',
						'default'  => 'no',
						'desc_tip' => true,
					),
					array(
						'title'   => esc_html__( 'Sale name', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'text',
						'id'      => 'rcodehub_sale_name',
						'default' => '',
						'placeholder'=>'Such as : Season sale, Day wise sale etc.'
					),
					array(
						'title'   => esc_html__( 'Discount type', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'radio',
						'id'      => 'rcodehub_type',
						'default' => '',
						'options' => array( '%', 'Fixed For ALL' ),
					),
					array(
						'title'   => esc_html__( 'Sale type', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'select',
						'id'      => 'rcodehub_sale_type',
						'default' => '',
						'options' => array( 'Apply For All Day', 'Day Wise' ),
					),
					array(
						'title'   => esc_html__( 'Discount amount', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount',
						'default' => '',
						'placeholder'=>'Number Only'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Monday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_1',
						'default' => '',
						'placeholder'=>'Number Only : Monday'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Tuesday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_2',
						'default' => '',
						'placeholder'=>'Number Only : Tuesday'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Wednesday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_3',
						'default' => '',
						'placeholder'=>'Number Only : Wednesday'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Thursday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_4',
						'default' => '',
						'placeholder'=>'Number Only : Thursday'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Friday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_5',
						'default' => '',
						'placeholder'=>'Number Only : Friday'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Saturday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_6',
						'default' => '',
						'placeholder'=>'Number Only : Saturday'
					),
					array(
						'title'   => esc_html__( 'Discount amount for Sunday', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'number',
						'id'      => 'rcodehub_discount_amount_7',
						'default' => '',
						'placeholder'=>'Number Only : Sunday'
					),
					array(
						'title'       => esc_html__( 'Sale starts', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'id'          => 'rcodehub_start',
						'type'        => 'rcodehub_datetimepicker',
						'class'       => 'rcodehub_datetimepicker',
						'placeholder' => esc_html__( 'From&hellip; YYYY-MM-DD HH:MM', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'default'     => '',
					),
					array(
						'title'       => esc_html__( 'Sale ends', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'id'          => 'rcodehub_end',
						'type'        => 'rcodehub_datetimepicker',
						'class'       => 'rcodehub_datetimepicker',
						'placeholder' => esc_html__( 'To&hellip; YYYY-MM-DD HH:MM', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'default'     => '',
					),
					array(
						'title'    => esc_html__( 'Exclude Sale products', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Exclude products that are already on sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'checkbox',
						'id'       => 'rcodehub_exclude_sale',
						'default'  => 'no',
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Use regular price', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Use regular price for discount price for product that are already on sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'checkbox',
						'id'       => 'rcodehub_use_regular_price',
						'default'  => 'no',
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Exclude Product type', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select product type to exclude from sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'multiselect',
						'id'       => 'rcodehub_rcodehub_exclude_type',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'options'  => wc_get_product_types(),
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Exclude Category', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select product categories to exclude from sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'multiselect',
						'id'       => 'rcodehub_rcodehub_exclude_cat',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'options'  => $rcodehub_product_categories_select,
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Include Category', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select product categories  to include in sale. Only product within this categories will be on sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'multiselect',
						'id'       => 'rcodehub_rcodehub_include_cat',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'options'  => $rcodehub_product_categories_select,
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Exclude tag', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select product tag to exclude from sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'multiselect',
						'id'       => 'rcodehub_rcodehub_exclude_tag',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'options'  => $rcodehub_product_tag_select,
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Include tag', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select product tags  to include in sale. Only product within this tags will be on sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'multiselect',
						'id'       => 'rcodehub_rcodehub_include_tag',
						'default'  => '',
						'class'    => 'chosen_select_nostd',
						'options'  => $rcodehub_product_tag_select,
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Exclude products', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select product to exclude from sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'rcodehub_ajaxproduct',
						'id'       => 'rcodehub_rcodehub_exclude_product',
						'default'  => '',
						'desc_tip' => true,
					),
					array(
						'title'    => esc_html__( 'Include products', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Select products to include in sale. Only selsected products will be on sale.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'rcodehub_ajaxproduct',
						'id'       => 'rcodehub_rcodehub_include_product',
						'default'  => '',
						'desc_tip' => true,

					),
					array(
						'title'    => esc_html__( 'Exclude SKU', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Enter SKU to exclude from sale. Use comma for delimiter.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'text',
						'id'       => 'rcodehub_rcodehub_exclude_sku',
						'default'  => '',
						'desc_tip' => true,
						'css'      => 'width:100%;',
					),
					array(
						'title'    => esc_html__( 'Include SKU', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'desc'     => esc_html__( 'Enter SKU to include in sale. Only entered SKU will be on sale. Use comma for delimiter.', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'     => 'text',
						'id'       => 'rcodehub_rcodehub_include_sku',
						'default'  => '',
						'desc_tip' => true,
						'css'      => 'width:100%;',

					),
					array(
						'title'   => esc_html__( 'Enable store sale notice', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'checkbox',
						'id'      => 'rcodehub_notice_enable',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Sale store notice', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'textarea',
						'id'      => 'rcodehub_notice',
						'default' => '',
						'css'     => 'width:100%',
					),
					array(
						'title'   => esc_html__( 'Enable store before sale notice', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'checkbox',
						'id'      => 'rcodehub_before_notice_enable',
						'default' => 'no',
					),
					array(
						'title'   => esc_html__( 'Sale store notice befor sale starts', 'RCODEHUB-WooCommerce-Store-Sale' ),
						'type'    => 'textarea',
						'id'      => 'rcodehub_notice_before',
						'default' => '',
						'css'     => 'width:100%',
					),
					array(

						'type' => 'rcodehub_hiddenhash',
						'id'   => 'rcodehub_rcodehub_hiddenhash',

					),
					array(
						'type' => 'sectionend',
						'id'   => 'rcodehub_rch_store_options',
					),

				)
			); // End pages settings
		}
		public function rcodehub_save() {
			global $rcodehub_current_section;
			$rcodehub_settings = $this->get_settings();
			WC_Admin_Settings::save_fields( $rcodehub_settings );
			do_action( 'woocommerce_update_options_' . $this->id );
		}
	}
	return new rcodehub_WC_Settings_RCH_Store_Sale();
endif;
