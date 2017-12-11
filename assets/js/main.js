( function( $ ) {
    $( document ).ready( function() {
        if ( ! Cookies.get( 'edd_cart' ) ) {
            return;
        }

        var cart = JSON.parse( Cookies.get( 'edd_cart' ) );

        if ( ! cart.quantity ) {
            return;
        }

        var $navCart = $( '.navCart' );
        $navCart.find( '.edd-cart-quantity' ).html( cart.quantity );
        $navCart.find( '.navCart-quantityText' ).html( cart.quantity === 1 ? 'item' : 'items' );
        $navCart.find( '.navCart-cartTotalAmount' ).html( cart.total );

        $.each( cart.items, function( index, download ) {
            var $form = $( '.edd_purchase_' + download.id );

            $form.find( '.edd-add-to-cart' ).hide();
            $form.find( '.edd_go_to_checkout' ).show();
        } );
    } );
} )( jQuery );