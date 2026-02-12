<?php 
add_action( 'init', function() {

	register_block_type( 'custom/partner-check', array(
		'render_callback' => 'render_partner_check_block',
		'attributes'      => array(
			'role' => array(
				'type'    => 'string',
				'default' => 'partner,administrator',
			),
			'tier' => array(
				'type'    => 'string',
				'default' => '',
			),
		),
	) );

} );

function render_partner_check_block( $attributes, $content ) {

	if ( ! is_user_logged_in() ) {
		return apply_filters( 'the_content', '[ws_form id="2"]' );
	}

	$user = wp_get_current_user();

	$roles     = array_map( 'trim', explode( ',', $attributes['role'] ) );
	$tiers     = array_map( 'trim', explode( ',', $attributes['tier'] ) );
	$is_admin  = in_array( 'administrator', (array) $user->roles, true );
	$has_role  = ! empty( array_intersect( $roles, (array) $user->roles ) );

	$current_tier = get_user_meta( $user->ID, 'partner_tier', true );
	$tier_allowed = empty( $tiers )
		? ! empty( $current_tier )
		: in_array( $current_tier, $tiers, true );

	if ( $is_admin || ( $has_role && $tier_allowed ) ) {
		return $content; // InnerBlocks content
	}

	return '';
}
