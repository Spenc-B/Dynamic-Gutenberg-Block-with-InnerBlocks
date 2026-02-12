<?php
// Shortcode: [partner_check role="partner" tier="platinum,gold" shortcode="partner_flyer"]
// If user has the role, render the shortcode provided; otherwise render login form.
function partner_check_shortcode( $atts, $content = '' ) {
	$atts = shortcode_atts(
		array(
			'role'      => 'partner,administrator',
			'tier'      => '',
			'shortcode' => '',
		),
		$atts,
		'partner_check'
	);

	// Debug helpers (uncomment as needed).
	// var_dump( $atts );
	// error_log( print_r( $atts, true ) );
	$user = wp_get_current_user();
	if ( is_user_logged_in() ) {
		$roles = array_filter( array_map( 'trim', explode( ',', $atts['role'] ) ) );
		$has_role = ! empty( array_intersect( $roles, (array) $user->roles ) );
		$is_admin = in_array( 'administrator', (array) $user->roles, true );
		$tiers = array_filter( array_map( 'trim', explode( ',', (string) $atts['tier'] ) ) );
		$current_tier = get_user_meta( $user->ID, 'partner_tier', true );
		$tier_allowed = in_array( 'all', $tiers, true );

		if ( ! $tier_allowed ) {
			if ( empty( $tiers ) ) {
				$tier_allowed = ! empty( $current_tier );
			} else {
				$tier_allowed = ! empty( $current_tier ) && in_array( $current_tier, $tiers, true );
			}
		}

		if ( $is_admin || ( $has_role && $tier_allowed ) ) {
			if ( ! empty( $atts['shortcode'] ) ) {
				$shortcode_value = trim( $atts['shortcode'] );
				if ( '[' !== substr( $shortcode_value, 0, 1 ) ) {
					$shortcode_value = '[' . $shortcode_value . ']';
				}
				return do_shortcode( $shortcode_value );
			}

			if ( ! empty( $content ) ) {
				$rendered_content = function_exists( 'do_blocks' ) ? do_blocks( $content ) : $content;
				return do_shortcode( $rendered_content );
			}

			return '';
		}
	} else {
		return apply_filters( 'the_content', '[ws_form id="2"]' );
	}

	return '';
}

add_shortcode( 'partner_check', 'partner_check_shortcode' );
