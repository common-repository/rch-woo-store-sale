<?php
class rcodehub_WC_rch_store_sale_admin {
	private $rcodehub_plugin_name;
	private $rcodehub_version;
	public function __construct( $rcodehub_plugin_name, $rcodehub_version ) {
		$this->plugin_name = $rcodehub_plugin_name;
		$this->version     = $rcodehub_version;
	}
	public function rcodehub_enqueue_scripts( $rcodehub_hook ) {
		if ( $rcodehub_hook == 'woocommerce_page_wc-settings' ) {
			wp_register_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'js/rcodehub-wc-rch-store-sale-admin.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'timepicker-addon' ),
				$this->version,
				false
			);
			wp_localize_script( $this->plugin_name, 'rcodhub_wccsss', array( 'calendar_image' => WC()->plugin_url() . '/assets/images/calendar.png' ) );
			wp_enqueue_script( $this->plugin_name );
			wp_enqueue_script(
				'timepicker-addon',
				plugin_dir_url( __FILE__ ) . '/js/rcodehub-jquery-ui-timepicker-addon.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ),
				$this->version,
				true
			);
			wp_enqueue_style( 'jquery-ui-datepicker' );
		}
	}
	public function rcodehub_add_support_link( $links, $file ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return $links;
		}
		if ( $file == 'rch-woo-store-sale/rch-woo-store-sale.php' ) {
			$links[] = '<a href="http://rcodehub.com/index.php/rch-store-sale-docs/" target="_blank">' . esc_attr__( 'Docs', 'wc_store' ) . '</a>';
		}
		return $links;
	}

	public static function rcodehub_plugin_action_links( $links,$file ) {
		$action_links = array();
		if ( $file == 'rch-woo-store-sale/rch-woo-store-sale.php' ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=rcodhub_store_sale' ) . '" aria-label="' . esc_attr__( 'View store sale settings', 'rch-woo-store-sale' ) . '">' . esc_html__( 'Settings', 'rch-woo-store-sale' ) . '</a>',
			);
		}
		return array_merge( $action_links, $links );
	}

	function rcodehub_settings_class( $rcodehub_settings ) {
		$rcodehub_settings[] = include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/rcodehub-class-rch-WC-settings-store-sale.php';
		return $rcodehub_settings;
	}

	function rcodehub_datetimepicker( $value ) {
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>				
			</th>
			<td class="formin">					
				<input type="text" 
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="<?php echo esc_attr( $value['type'] ); ?>"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					value="<?php echo esc_attr( WC_Admin_Settings::get_option( $value['id'], $value['default'] ) ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
					maxlength="16" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])[ ](0[0-9]|1[0-9]|2[0-4]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])" />				
			</td>
		</tr>
		<?php
	}

	function rcodehub_ajaxproduct( $value ) {
		global $post;
		$rcodehub_field_description = WC_Admin_Settings::get_field_description( $value );
		extract( $rcodehub_field_description );
		$rcodhub_get_option  = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
		$rcodhub_optionarray = is_array( $rcodhub_get_option ) ? $rcodhub_get_option : explode( ',', $rcodhub_get_option );
		$rcodhub_product_ids = array_filter( array_map( 'absint', $rcodhub_optionarray ) );
		$rcodhub_json_ids = array();
		foreach ( $rcodhub_product_ids as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( is_object( $product ) ) {
				$rcodhub_json_ids[ $product_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
			}
		}
		?>
		<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="formin">		
		<?php
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
			?>
				<input type="hidden" 
					class="wc-product-search " 
					id="<?php echo esc_attr( $value['id'] ); ?>"
					name="<?php echo esc_attr( $value['id'] ); ?>"
					data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'WooCommerce' ); ?>" 
					data-action="woocommerce_json_search_products" 
					data-multiple="true" 
					data-selected="
					<?php 
							echo esc_attr( json_encode( $rcodhub_json_ids ) );
					?>
					" 
					value="<?php echo implode( ',', array_keys( $rcodhub_json_ids ) ); ?>" />
				
		<?php
		} else {
?>
			<select class="wc-product-search" multiple="multiple" style="width: 50%;" id="<?php echo esc_attr( $value['id'] ); ?>" name="<?php echo esc_attr( $value['id'] ); ?>[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'WooCommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="" >
					<?php
					foreach ( $rcodhub_product_ids as $product_id ) {
						$product = wc_get_product( $product_id );
						if ( is_object( $product ) ) {
							echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
						}
					}
					?>
					</select>
				<?php } ?>
		</td>
			</tr>			
	
	<?php
	}

	function rcodehub_hiddenhash( $value ) {
		?>				
				<input type="hidden" 
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					value="<?php echo md5( microtime() ); ?>"
					 />				
		<?php
	}

	function rcodehub_sanitize_ajaxproduct_option( $value, $option, $raw_value ) {
		if ( $option['type'] == 'rcodehub_ajaxproduct' ) {
				$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
		}
		return $value;
	}
}
