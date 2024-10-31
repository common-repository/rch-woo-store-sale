<?php
class rcodehub_WC_rch_store_sale_public {
	private $rcodehub_plugin_name;
	private $rcodehub_version;
	public  $rcodehub_onsale_page_transient;
	public function __construct( $rcodehub_plugin_name, $rcodehub_version, $rcodehub_onsale_page_transient ) {
		$this->plugin_name           = $rcodehub_plugin_name;
		$this->version               = $rcodehub_version;
		$this->onsale_page_transient = $rcodehub_onsale_page_transient;

	}
	public function rcodehub_run_rch_sales() {
		$rcodehub_sale_enable 		= get_option( 'rcodehub_sale_enable', 'no' );
		//Need to day wise
		$rcodehub_sale_type 	= get_option( 'rcodehub_sale_type' );
		if($rcodehub_sale_type==1){ //day wise
		    $rcodehub_totayDay  = date("l");
			if($rcodehub_totayDay=='Monday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_1');
			}else if($rcodehub_totayDay=='Tuesday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_2');
			}else if($rcodehub_totayDay=='Wednesday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_3');
			}else if($rcodehub_totayDay=='Thursday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_4');
			}else if($rcodehub_totayDay=='Friday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_5');
			}else if($rcodehub_totayDay=='Saturday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_6');
			}else if($rcodehub_totayDay=='Sunday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_7');
			}else{
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount', '' );
			}
		}else{ //for all days
			$rcodehub_discount    = get_option( 'rcodehub_discount_amount', '' );
		}
	    //Check Sale
		if ( $rcodehub_sale_enable === 'yes' && ! empty( $rcodehub_discount ) && $this->rcodehub_check_sale_start() && ! $this->rcodehub_check_sale_end() ) {
			return true;
		} else {
			return false;
		}
	}
	public function rcodehub_check_sale_start() {
		$rcodehub_sale_from = get_option( 'rcodehub_start', false );
		if ( isset( $rcodehub_sale_from ) && ! empty( $rcodehub_sale_from ) ) {
			$rcodehub_date1 = new DateTime( $rcodehub_sale_from );
			$rcodehub_date2 = new DateTime( current_time( 'mysql' ) );
			return ( $rcodehub_date1 < $rcodehub_date2 );
		} else {
			return false;
		}
	}
	public function rcodehub_check_sale_end() {
		$rcodehub_sale_to = get_option( 'rcodehub_end', false );
		if ( isset( $rcodehub_sale_to ) && ! empty( $rcodehub_sale_to ) ) {
			$rcodehub_date1 = new DateTime( $rcodehub_sale_to );
			$rcodehub_date2 = new DateTime( current_time( 'mysql' ) );
			return ( $rcodehub_date1 < $rcodehub_date2 );
		} else {
			return false;
		}
	}
	public function rcodehub_filterprice( $rcodehub_price, $product, $parent_product = null ) {
		$rcodehub_exclude_sale = get_option( 'rcodehub_exclude_sale', 'no' );
		$product_regular_price = get_post_meta( $product->get_id(), '_regular_price', true );
		$product_sale_price    = get_post_meta( $product->get_id(), '_sale_price', true );
		$product_price         = get_post_meta( $product->get_id(), '_price', true );
		$rcodehub_is_product_on_sale    = $product_price == $product_sale_price;
		if ( $rcodehub_exclude_sale == 'yes' && $rcodehub_is_product_on_sale ) {
			return $rcodehub_price;
		}
		if ( $rcodehub_price !== $product_price && ( current_filter() == 'woocommerce_product_get_price' || current_filter() == 'woocommerce_product_variation_get_price' ) ) {
			return $rcodehub_price;
		}
		if ( $rcodehub_price !== $product_sale_price && ( current_filter() == 'woocommerce_product_get_sale_price' || current_filter() == 'woocommerce_product_variation_get_sale_price' ) ) {
			return $rcodehub_price;
		}
		$orginal_product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
		
		//Need to day wise
		$rcodehub_sale_type = get_option( 'rcodehub_sale_type' );
		if($rcodehub_sale_type==1){ //day wise
		    $rcodehub_totayDay  = date("l");
			if($rcodehub_totayDay=='Monday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_1');
			}else if($rcodehub_totayDay=='Tuesday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_2');
			}else if($rcodehub_totayDay=='Wednesday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_3');
			}else if($rcodehub_totayDay=='Thursday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_4');
			}else if($rcodehub_totayDay=='Friday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_5');
			}else if($rcodehub_totayDay=='Saturday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_6');
			}else if($rcodehub_totayDay=='Sunday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_7');
			}else{
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount', '' );
			}
		}else{ //for all days
			$rcodehub_discount    = get_option( 'rcodehub_discount_amount', '' );
		}

		$rcodehub_type = get_option( 'rcodehub_type', '0' );
		if ( $parent_product ) {
			$product_id = method_exists( $parent_product, 'get_id' ) ? $parent_product->get_id() : $parent_product->id;
		} elseif ( $product->is_type( 'variation' ) && $parent_product == null ) {
			$product_id = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : '';
		} else {
			$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
		}
		$rcodehub_product_ids_on_sale = $this->onsale_page_transient;
		if ( false === $rcodehub_product_ids_on_sale && is_array( $rcodehub_product_ids_on_sale ) ) {
			return $rcodehub_price;
		}
		if ( ! in_array( $product_id, $rcodehub_product_ids_on_sale ) ) {
			return $rcodehub_price;
		}
		if ( empty( $rcodehub_price ) ) {
			$rcodehub_price = get_post_meta( $orginal_product_id, '_price', true );
		}

		if ( $rcodehub_is_product_on_sale && get_option( 'rcodehub_use_regular_price', 'no' ) == 'yes' ) {
			$rcodehub_price = $product_regular_price;
		}
		if ( $rcodehub_type == '0' ) {  			//Discount type -> %
			$newprice = $rcodehub_price - ( $rcodehub_price * ( $rcodehub_discount / 100 ) );
		} elseif ( $rcodehub_type == '1' ) {  	//Discount type -> Fixed
			global $woocommerce_wpml;
			$newprice = $rcodehub_price - $this->rcodehub_wpml_covert_price( $rcodehub_discount );
		} else {
			do_action( 'Wc_rch_store_sale_calculate_price_' . $rcodehub_type, $rcodehub_price, $product );
		}

		if ( $newprice > 0 && $newprice < $rcodehub_price ) {
			return $newprice;
		} else {
			return $rcodehub_price;
		}
	}

	public function rcodehub_sale_notice() {
		if ( $this->rcodehub_run_rch_sales() ) {
			$rcodehub_notice_enable = get_option( 'rcodehub_notice_enable', 'no' );
			$rcodehub_sale_name     = get_option( 'rcodehub_sale_name', '' );
			$rcodehub_notice        = get_option( 'rcodehub_notice', '' ).' : '.$rcodehub_sale_name;
			if ( $rcodehub_notice_enable == 'yes' && ! empty( $rcodehub_notice ) ) {
				echo apply_filters( 'woocommerce_demo_store', '<p class="woocommerce-store-notice demo_store store_sales">' . wp_kses_post( $rcodehub_notice ) . '</p>' );
			}
		} elseif ( ! $this->rcodehub_check_sale_start() && ! $this->rcodehub_check_sale_end() ) {
			$rcodehub_notice_enable = get_option( 'rcodehub_before_notice_enable', 'no' );
			$rcodehub_sale_name     = get_option( 'rcodehub_sale_name', '' );
			$rcodehub_notice        = get_option( 'rcodehub_notice_before', '' ).' : '.$rcodehub_sale_name;
			if ( $rcodehub_notice_enable == 'yes' && ! empty( $rcodehub_notice ) ) {
				echo apply_filters( 'woocommerce_demo_store', '<p class="woocommerce-store-notice demo_store store_sales store_sales_before">' . wp_kses_post( $rcodehub_notice ) . '</p>' );
			}
		}
	}
	function rcodehub_get_main_wpml_id( $id, $rcodehub_type ) {
		global $sitepress;
		if ( function_exists( 'icl_object_id' ) && function_exists( 'pll_default_language' ) ) { 
			$id = icl_object_id( $id, $rcodehub_type, false, pll_default_language() );
		} elseif ( function_exists( 'icl_object_id' ) && method_exists( $sitepress, 'get_default_language' ) ) { 
			$id = icl_object_id( $id, $rcodehub_type, false, $sitepress->get_default_language() );
		}
		return $id;
	}
	function rcodehub_wpml_covert_price( $rcodehub_price ) {
		global $woocommerce_wpml;
		if ( function_exists( 'icl_object_id' ) && isset( $woocommerce_wpml ) && isset( $woocommerce_wpml->multi_currency->prices ) ) {
			$rcodehub_price = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $rcodehub_price );
		}
		return $rcodehub_price;
	}
	function rcodehub_add_discount_woocommerce_get_variation_prices_hash( $rcodehub_hash ) {
		$rcodehub_hidden_hash = get_option( 'rcodehub_rcodehub_hiddenhash', '' );
		//Need to day wise
		$rcodehub_sale_type = get_option( 'rcodehub_sale_type' );
		if($rcodehub_sale_type==1){ //day wise
		    $rcodehub_totayDay  = date("l");
			if($rcodehub_totayDay=='Monday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_1');
			}else if($rcodehub_totayDay=='Tuesday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_2');
			}else if($rcodehub_totayDay=='Wednesday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_3');
			}else if($rcodehub_totayDay=='Thursday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_4');
			}else if($rcodehub_totayDay=='Friday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_5');
			}else if($rcodehub_totayDay=='Saturday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_6');
			}else if($rcodehub_totayDay=='Sunday'){
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount_7');
			}else{
				$rcodehub_discount    = get_option( 'rcodehub_discount_amount', '' );
			}
		}else{ //for all days
			$rcodehub_discount    = get_option( 'rcodehub_discount_amount', '' );
		}
		$rcodehub_hash = $rcodehub_discount . '-' . WC()->session->client_currency . '-' . $rcodehub_type = get_option( 'rcodehub_type', '0' ) . $rcodehub_hidden_hash . $rcodehub_hash[0];
		return $rcodehub_hash;
	}
	public function rcodehub_add_wc_onsale_page_product_ids_on_sale( $rcodehub_product_ids_on_sale ) {
		if ( $this->rcodehub_run_rch_sales() ) {
			$added_ids_on_sale = get_transient( 'rcodehub_wc_onsale_page_products_onsale' );
			if ( false !== $added_ids_on_sale && is_array( $added_ids_on_sale ) ) {
				$rcodehub_product_ids_on_sale = array_merge( $rcodehub_product_ids_on_sale, $added_ids_on_sale );
			}
		}
		return $rcodehub_product_ids_on_sale;
	}
}
