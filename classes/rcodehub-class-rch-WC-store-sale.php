<?php
class rcodehub_WC_rch_store_sale {
	protected $rcodhub_basic;
	protected $rcodehub_plugin_name;
	protected $rcodehub_version;
	public $rcodhub_notices = array();
	protected $rcodhub_min_wc_version;
	public function __construct() {

		$this->plugin_name    = 'RCODEHUB-WooCommerce-Store-Sale';
		$this->version        = '1.0.0';
		$this->min_wc_version = '2.4';

		$this->rcodehub_load_dependencies();
		$this->rcodehub_define_admin_hooks();
		$this->rcodehub_define_public_hooks();
		add_action( 'admin_notices', array( $this, 'rcodehub_admin_notices' ), 15 );
	}

	private function rcodehub_load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'classes/rcodehub-class-rch-WC-store-sale-basic.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/rcodehub-class-rch-WC-store-sale-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/rcodehub-class-rch-WC-store-sale-public.php';
		$this->rcodehub_basic = new rcodehub_WC_rch_store_sale_basic();
	}

	private function rcodehub_define_admin_hooks() {
		$rcodhub_plugin_admin = new rcodehub_WC_rch_store_sale_admin( $this->rcodehub_get_plugin_name(), $this->rcodehub_get_version() );
		$this->rcodehub_basic->rcodehub_add_action( 'admin_enqueue_scripts', $rcodhub_plugin_admin, 'rcodehub_enqueue_scripts' );
		$this->rcodehub_basic->rcodehub_add_action( 'woocommerce_admin_field_rcodehub_datetimepicker', $rcodhub_plugin_admin, 'rcodehub_datetimepicker' );
		$this->rcodehub_basic->rcodehub_add_action( 'woocommerce_admin_field_rcodehub_ajaxproduct', $rcodhub_plugin_admin, 'rcodehub_ajaxproduct' );
		$this->rcodehub_basic->rcodehub_add_action( 'woocommerce_admin_field_rcodehub_hiddenhash', $rcodhub_plugin_admin, 'rcodehub_hiddenhash' );
		$this->rcodehub_basic->rcodehub_add_filter( 'plugin_row_meta', $rcodhub_plugin_admin, 'rcodehub_add_support_link', 10, 2 );
		$this->rcodehub_basic->rcodehub_add_filter( 'plugin_action_links', $rcodhub_plugin_admin, 'rcodehub_plugin_action_links',10,2 );
		$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_get_settings_pages', $rcodhub_plugin_admin, 'rcodehub_settings_class', 20 );
		$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_update_options_rch_store_sale', $this, 'rcodehub_set_onsale_page_transient', 90 );
		$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_delete_product_transients', $this, 'rcodehub_delete_onsale_page_transient', 90 );
		if ( ! version_compare( WC_VERSION, '2.7', '<' ) ) {
			$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_admin_settings_sanitize_option', $rcodhub_plugin_admin, 'rcodehub_sanitize_ajaxproduct_option', 20, 3 );
		}
	}

	private function rcodehub_define_public_hooks() {

		$rcodehub_plugin_public = new rcodehub_WC_rch_store_sale_public( $this->rcodehub_get_plugin_name(), $this->rcodehub_get_version(), $this->rcodehub_get_onsale_page_transient() );

		$this->rcodehub_basic->rcodehub_add_action( 'wp_footer', $rcodehub_plugin_public, 'rcodehub_sale_notice' );
		if ( $rcodehub_plugin_public->rcodehub_run_rch_sales() && ! is_admin() ) {

			if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
				$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_get_sale_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 2 );
				$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_get_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 2 );
			} else {
				$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_product_get_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 2 );
				$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_product_get_sale_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 2 );
				$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_product_variation_get_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 2 );
				$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_product_variation_get_sale_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 2 );
			}
			$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_variation_prices_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 15, 3 );
			$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_variation_prices_sale_price', $rcodehub_plugin_public, 'rcodehub_filterprice', 10, 3 );
			$this->rcodehub_basic->rcodehub_add_filter( 'wc_onsale_page_product_ids_on_sale', $rcodehub_plugin_public, 'rcodehub_add_wc_onsale_page_product_ids_on_sale', 10, 1 );
			$this->rcodehub_basic->rcodehub_add_filter( 'woocommerce_get_variation_prices_hash', $rcodehub_plugin_public, 'rcodehub_add_discount_woocommerce_get_variation_prices_hash', 15, 2 );
		}
	}

	public function rcodehub_run() {
		$rcodehub_environment_warning = self::rcodehub_get_environment_warning();
		if ( $rcodehub_environment_warning ) {
			$this->rcodehub_add_admin_notice( 'bad_environment', 'error', $rcodehub_environment_warning );
		} else {
			$this->rcodehub_basic->rcodehub_run();
		}
	}

	public function rcodehub_get_plugin_name() {
		return $this->plugin_name;
	}

	public function rcodehub_get_basic() {
		return $this->rcodehub_basic;
	}

	public function rcodehub_get_version() {
		return $this->version;
	}

	function rcodehub_get_environment_warning() {
		if ( version_compare( WC_VERSION, $this->min_wc_version, '<' ) ) {
			$rcodehub_message = esc_html__( 'WooCommerce Store Sale - The minimum WooCommerce version required for this plugin is %1$s. You are running %2$s.', 'wc-sbd', 'wc-sbd' );
			return sprintf( $rcodehub_message, $rcodhub_min_wc_version, WC_VERSION );
		}
		return false;
	}
	

	public function rcodehub_add_admin_notice( $rcodehub_slug, $rcodehub_class, $rcodehub_message ) {
		$this->notices[ $rcodehub_slug ] = array(
			'class'   => $rcodehub_class,
			'message' => $rcodehub_message,
		);
	}

	public function rcodehub_admin_notices() {
		foreach ( (array) $this->notices as $notice_key => $notice ) {
			echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
			echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
			echo '</p></div>';
		}
	}

	function rcodehub_set_onsale_page_transient() {

		$rcodehub_tax_query = array();
		$rcodehub_metaquery = array();

		$rcodehub_excludetype        = get_option( 'rcodehub_rcodehub_exclude_type', '' );
		$rcodehub_excludecat         = get_option( 'rcodehub_rcodehub_exclude_cat', '' );
		$rcodehub_includecat         = get_option( 'rcodehub_rcodehub_include_cat', '' );
		$rcodehub_excludetags        = get_option( 'rcodehub_rcodehub_exclude_tag', '' );
		$rcodehub_includetags        = get_option( 'rcodehub_rcodehub_include_tag', '' );
		$rcodehub_excludeproduct_tmp = get_option( 'rcodehub_rcodehub_exclude_product', '' );
		$rcodehub_includeproduct_tmp = get_option( 'rcodehub_rcodehub_include_product', '' );
		$rcodehub_includesku_tmp     = get_option( 'rcodehub_rcodehub_include_sku', '' );
		$rcodehub_excludesku_tmp     = get_option( 'rcodehub_rcodehub_exclude_sku', '' );

		if ( ! empty( $rcodehub_excludetype ) ) {
			$rcodehub_tax_query[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => $rcodehub_excludetype, 
				'operator' => 'NOT IN',
			);
		}

		if ( ! empty( $rcodehub_excludecat ) ) {
			$rcodehub_tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'id',
				'terms'    => $rcodehub_excludecat, 
				'operator' => 'NOT IN',
			);
		}

		if ( ! empty( $rcodehub_includecat ) ) {
			$rcodehub_tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'id',
				'terms'    => $rcodehub_includecat, 
				'operator' => 'IN',
			);
		}

		if ( ! empty( $rcodehub_excludetags ) ) {
			$rcodehub_tax_query[] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'id',
				'terms'    => $rcodehub_excludetags, 
				'operator' => 'NOT IN',
			);
		}
		if ( ! empty( $rcodehub_includetags ) ) {
			$rcodehub_tax_query[] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'id',
				'terms'    => $rcodehub_includetags, 
				'operator' => 'IN',
			);
		}
		if ( ! empty( $rcodehub_includesku_tmp ) ) {
			$rcodehub_metaquery[] =
				array(
					'key'     => '_sku',
					'value'   => explode( ',', $rcodehub_includesku_tmp ),
					'compare' => 'IN',
				);
		}
		if ( ! empty( $rcodehub_excludesku_tmp ) ) {
			$rcodehub_metaquery[] = array(
				'key'     => '_sku',
				'value'   => explode( ',', $rcodehub_excludesku_tmp ),
				'compare' => 'NOT IN',
			);
		}

		if ( ! empty( $rcodehub_excludeproduct_tmp ) ) {
			$query_args['post__not_in'] = $rcodehub_excludeproduct_tmp;
		}
		if ( ! empty( $rcodehub_includeproduct_tmp ) ) {
			$query_args['post__in'] = $rcodehub_includeproduct_tmp;
		}
		if ( ! empty( $rcodehub_tax_query ) ) {
			$query_args['tax_query'] = $rcodehub_tax_query;
		}
		if ( ! empty( $rcodehub_metaquery ) ) {
			$query_args['meta_query'] = $rcodehub_metaquery;
		}
			$query_args['post_type']      = 'product';
			$query_args['posts_per_page'] = '-1';

		$query = new WP_Query( $query_args );

		$rcodehub_product_ids_on_sale = wp_parse_id_list( array_merge( wp_list_pluck( $query->posts, 'ID' ), array_diff( wp_list_pluck( $query->posts, 'post_parent' ), array( 0 ) ) ) );
		set_transient( 'rcodehub_wc_onsale_page_products_onsale', $rcodehub_product_ids_on_sale, DAY_IN_SECONDS * 30 );
		return $rcodehub_product_ids_on_sale;
	}

	function rcodehub_delete_onsale_page_transient() {
		delete_transient( 'rcodehub_wc_onsale_page_products_onsale' );
		$this->rcodehub_set_onsale_page_transient();
	}

	function rcodehub_get_onsale_page_transient() {
		$rcodehub_product_ids_on_sale = get_transient( 'rcodehub_wc_onsale_page_products_onsale' );
		if ( false !== $rcodehub_product_ids_on_sale ) {
			return $rcodehub_product_ids_on_sale;
		} else {
			$rcodehub_product_ids_on_sale = $this->rcodehub_set_onsale_page_transient();
			return $rcodehub_product_ids_on_sale;
		}
	}
}
