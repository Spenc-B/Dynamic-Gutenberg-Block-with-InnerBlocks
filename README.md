# Dynamic-Gutenberg-Block-with-InnerBlocks
Dynamic Gutenberg Block with InnerBlocks (Role-Based Visibility)

---

### How to Create a Dynamic Gutenberg Block with InnerBlocks (Role-Based Visibility)

Instead of using shortcodes to wrap content, create a **dynamic Gutenberg block** that:

* Uses `InnerBlocks` for true nested block support
* Uses a `render_callback` in PHP
* Conditionally renders content based on user role/tier

#### 1. Register the Dynamic Block (PHP)

```php
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
		return '';
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
		return $content;
	}

	return '';
}
```

#### 2. Register the Block in JavaScript

```js
wp.blocks.registerBlockType('custom/partner-check', {
    title: 'Partner Check',
    icon: 'lock',
    category: 'layout',

    edit: function() {
        return wp.element.createElement(
            'div',
            { style: { border: '2px dashed #007cba', padding: '15px' } },
            wp.element.createElement(wp.blockEditor.InnerBlocks)
        );
    },

    save: function() {
        return wp.element.createElement(wp.blockEditor.InnerBlocks.Content);
    }
});
```

---

This allows:

* True nested blocks
* Clean Gutenberg UI
* No shortcode wrapping issues
* Server-side visibility control
