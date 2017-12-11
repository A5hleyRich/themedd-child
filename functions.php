<?php
/**
 * Themedd child theme.
 */
function themedd_child_styles() {
    wp_enqueue_style( 'themedd', get_template_directory_uri() . '/style.css' );

    $path = get_stylesheet_directory_uri() . '/assets/js';
    wp_register_script( 'themedd-js-cookie', "{$path}/js.cookie.js", array(), '2.2.0', true );
    wp_enqueue_script( 'themedd-child', "{$path}/main.js", array( 'jquery', 'themedd-js-cookie' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'themedd_child_styles' );

/**
 * Hide default cart.
 */
add_filter( 'themedd_edd_show_cart', '__return_false' );

/**
 * Show custom cart in navigation.
 */
add_action( 'template_redirect',  function() {
    add_action( 'themedd_secondary_menu', function() {
        ?>
        <a class="navCart empty" href="<?php echo edd_get_checkout_uri(); ?>">
            <div class="navCart-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="1.414"><path fill="none" d="M0 0h24v24H0z"></path><path d="M5.1.5c.536 0 1 .37 1.12.89l1.122 4.86H22.35c.355 0 .688.163.906.442.217.28.295.644.21.986l-2.3 9.2c-.128.513-.588.872-1.116.872H8.55c-.536 0-1-.37-1.12-.89L4.185 2.8H.5V.5h4.6z" fill-rule="nonzero"></path><circle cx="6" cy="20" r="2" transform="matrix(-1.14998 0 0 1.14998 25.8 -1.8)"></circle><circle cx="14" cy="20" r="2" transform="matrix(-1.14998 0 0 1.14998 25.8 -1.8)"></circle></svg>
            </div>
            <span class="navCart-cartQuantityAndTotal">
                <span class="navCart-quantity">
                    <span class="edd-cart-quantity">0</span>
                    <span class="navCart-quantityText"> items</span>
                </span>
                <span class="navCart-total">
                    <span class="navCart-cartTotalSeparator"> - </span>
                    <span class="navCart-cartTotalAmount">$0.00</span>
                </span>
            </span>    
        </a>
        <?php
    } );
}, 10 );

/**
 * Ensure EDD adds cookie to AJAX responses.
 */
function themedd_set_cookie() {
    if ( headers_sent() || ! function_exists( 'edd_get_cart_contents' ) ) {
        return;
    }

    $items = edd_get_cart_contents();

    if ( ! empty( $items ) ) {
        $cart = array(
            'quantity' => edd_get_cart_quantity(),
            'total'    => edd_cart_total( false ),
            'items'    => $items,
        );
    
        setcookie( 'edd_cart', json_encode( $cart ), time() + 30 * 60, COOKIEPATH, COOKIE_DOMAIN );
    } else if ( isset( $_COOKIE['edd_cart'] ) ) {
        setcookie( 'edd_cart', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
    }
}
add_action( 'init', 'themedd_set_cookie', 1 );
// add_action( 'edd_post_add_to_cart', 'themedd_set_cookie' );

/**
 * Always show 'Purchase' buttons.
 */
add_filter( 'edd_purchase_download_form', function( $purchase_form, $args ) {
    $html = new DOMDocument();
    libxml_use_internal_errors( true );
    $html->loadHTML( $purchase_form );

    foreach( $html->getElementsByTagName( 'a' ) as $link ) {
        $class = $link->getAttribute( 'class' );
        
        if ( false !== strpos( $class, 'edd-add-to-cart' ) ) {
            $link->removeAttribute( 'style' );
        }
        
        if ( false !== strpos( $class, 'edd_go_to_checkout' ) ) {
            $link->setAttribute( 'style', 'display: none;' );
        }
    }
    
    libxml_clear_errors();
    
    return $html->saveHTML();
}, 10, 2 );