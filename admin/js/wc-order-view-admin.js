(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $( document ).ready(function(e) {
	 	$( '#wc-order-view-customer-search' ).select2({
	 		placeholder : "Filter by registered customer",
	 		width: '240px',
	 		minimumInputLength: 1
	 	});

	 	$( '.order-view-order-details .wc-metabox h3.fixed' ).click( function(ee) {
	 		$( this ).closest( '.wc-metabox' ).toggleClass( 'closed' ).toggleClass( 'open' );
	 		$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).slideToggle();
	 	});

	 	$( '.order-view-order-details .am_expand_text_box' ).mouseover( function(ee) {
	 		var $this = $(this);
	 		if (!$this.data('expand')) {
	 		    $this.data('expand', true);
	 		    $this.animate({
	 		      width: '+=140px',
	 		      left: '-=6px'
	 		    }, 'linear');
	 		    $this.siblings('.s').animate({
	 		      width: '-=140px',
	 		      left: '+=6px'
	 		    }, 'linear');
	 		  }
	 		  $this.focus();
	 		  $this.select();
	 	});

	 	$( '.order-view-order-details .am_expand_text_box' ).mouseout( function(ee) {
	 		var $this = jQuery(this);
	 		  $this.data('expand', false);
	 		  $this.animate({
	 		    width: '-=140px',
	 		    left: '+=6px'
	 		  }, 'linear');
	 		  $this.siblings('.s').animate({
	 		    width: '+=140px',
	 		    left: '-=6px'
	 		  }, 'linear');
	 	});

	 });

})( jQuery );
