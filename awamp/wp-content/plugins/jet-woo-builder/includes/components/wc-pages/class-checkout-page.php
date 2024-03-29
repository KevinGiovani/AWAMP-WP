<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_Checkout_Page {

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		// Define Locale for countries with no specific locale will use default.
		add_filter( 'woocommerce_get_country_locale_default', [ $this, 'prepare_country_locale' ] );
		add_filter( 'woocommerce_get_country_locale_base', [ $this, 'prepare_country_locale' ] );
		// Get country locale settings.
		add_filter( 'woocommerce_get_country_locale', [ $this, 'get_country_locale' ] );

		add_filter( 'woocommerce_billing_fields', [ $this, 'billing_fields' ], 99999, 2 );
		add_filter( 'woocommerce_shipping_fields', [ $this, 'shipping_fields' ], 99999, 2 );

	}

	/**
	 * Handle locale to override fields in get_address_fields().
	 *
	 * @param $fields
	 *
	 * @return array|mixed
	 */
	public function prepare_country_locale( $fields ) {

		if ( is_array( $fields ) ) {
			foreach ( $fields as $key => $props ) {
				if ( isset( $props['label'] ) ) {
					unset( $fields[ $key ]['label'] );
				}

				if ( isset( $props['placeholder'] ) ) {
					unset( $fields[ $key ]['placeholder'] );
				}

				if ( isset( $props['class'] ) ) {
					unset( $fields[ $key ]['class'] );
				}

				if ( isset( $props['priority'] ) ) {
					unset( $fields[ $key ]['priority'] );
				}
			}
		}

		return $fields;

	}

	/**
	 * Handle country locale settings.
	 *
	 * @param $locale
	 *
	 * @return array|mixed
	 */
	public function get_country_locale( $locale ) {

		$countries = array_merge( WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries() );
		$countries = array_keys( $countries );

		if ( is_array( $locale ) && is_array( $countries ) ) {
			foreach ( $countries as $country ) {
				if ( isset( $locale[ $country ] ) ) {
					$locale[ $country ] = $this->prepare_country_locale( $locale[ $country ] );
				}
			}
		}

		return $locale;

	}

	/**
	 * Customizing checkout billing form fields
	 *
	 * @param $fields
	 * @param $country
	 *
	 * @return array|false|mixed
	 */
	public function billing_fields( $fields, $country ) {
		if ( is_wc_endpoint_url( 'edit-address' ) ) {
			return $fields;
		} else {
			return $this->prepare_address_fields( get_option( 'jet_woo_builder_wc_fields_billing' ), $fields, 'billing', $country );
		}
	}

	/**
	 * Customizing checkout shipping form fields
	 *
	 * @param $fields
	 * @param $country
	 *
	 * @return array|false|mixed
	 */
	public function shipping_fields( $fields, $country ) {
		if ( is_wc_endpoint_url( 'edit-address' ) ) {
			return $fields;
		} else {
			return $this->prepare_address_fields( get_option( 'jet_woo_builder_wc_fields_shipping' ), $fields, 'shipping', $country );
		}
	}

	/**
	 * Handle country and location
	 *
	 * @param array  $fieldset
	 * @param false  $original_fieldset
	 * @param string $fields_group
	 * @param string $country
	 *
	 * @return array|false|mixed
	 */
	public function prepare_address_fields( $fieldset = [], $original_fieldset = false, $fields_group = 'billing', $country = '' ) {
		if ( is_array( $fieldset ) && ! empty( $fieldset ) ) {
			$locale = WC()->countries->get_country_locale();

			if ( isset( $locale[ $country ] ) && is_array( $locale[ $country ] ) ) {
				$states = WC()->countries->get_states( $country );

				foreach ( $locale[ $country ] as $key => $value ) {
					$field_name = $fields_group . '_' . $key;

					if ( is_array( $value ) && isset( $fieldset[ $field_name ] ) ) {
						if ( isset( $value['required'] ) ) {
							$fieldset[ $field_name ]['required'] = $value['required'];
						}

						if ( 'state' === $key ) {
							if ( is_array( $states ) && empty( $states ) ) {
								$fieldset[ $field_name ]['hidden'] = true;
							}
						} else {
							if ( isset( $value['hidden'] ) ) {
								$fieldset[ $field_name ]['hidden'] = $value['hidden'];
							}
						}
					}
				}
			}

			$fieldset = $this->prepare_checkout_fields( $fieldset, $original_fieldset );

			return $fieldset;
		} else {
			return $original_fieldset;
		}
	}

	/**
	 * Prepare form fields for output
	 *
	 * @param $fields
	 * @param $original_fields
	 *
	 * @return array
	 */
	public function prepare_checkout_fields( $fields, $original_fields ) {
		if ( is_array( $fields ) && ! empty( $fields ) ) {

			foreach ( $fields as $name => $field ) {
				$new_field = false;

				if ( $original_fields && isset( $original_fields[ $name ] ) ) {
					$new_field = $original_fields[ $name ];

					$new_field['label']       = isset( $field['label'] ) ? esc_html__( $field['label'], 'jet-woo-builder' ) : '';
					$new_field['default']     = isset( $field['default'] ) ? $field['default'] : '';
					$new_field['placeholder'] = isset( $field['placeholder'] ) ? esc_html__( $field['placeholder'], 'jet-woo-builder' ) : '';
					$new_field['class']       = isset( $field['class'] ) && is_array( $field['class'] ) ? $field['class'] : [];
					$new_field['label_class'] = [];
					$new_field['validate']    = isset( $field['validate'] ) && is_array( $field['validate'] ) ? $field['validate'] : [];
					$new_field['required']    = isset( $field['required'] ) ? $field['required'] : 0;
					$new_field['priority']    = isset( $field['priority'] ) ? $field['priority'] : '';
				} else {
					$new_field = $field;
				}

				$type = isset( $new_field['type'] ) ? $new_field['type'] : 'text';

				$new_field['class'][] = 'jet-woo-builder-field-wrapper';
				$new_field['class'][] = 'jet-woo-builder-field-' . $type;

				if ( $type === 'select' || $type === 'radio' ) {
					if ( isset( $new_field['options'] ) ) {
						$options_arr = $this->prepare_field_options( $new_field['options'] );
						$options     = [];
						foreach ( $options_arr as $key => $value ) {
							$options[ $key ] = esc_html__( $value, 'jet-woo-builder' );
						}
						$new_field['options'] = $options;
					}
				}

				$fields[ $name ] = $new_field;
			}
			return $fields;
		} else {
			return $original_fields;
		}
	}

	/**
	 * Handle specific field types
	 *
	 * @param $options
	 *
	 * @return array
	 */
	public function prepare_field_options( $options ) {
		if ( is_string( $options ) ) {
			$options = array_map( 'trim', explode( '|', $options ) );
		}

		return is_array( $options ) ? $options : [];
	}

}