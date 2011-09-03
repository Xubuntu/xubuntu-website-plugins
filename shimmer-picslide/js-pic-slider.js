function changeImage( direction ) {
	if( direction == "prev" ) {
		/* prev */
		hasPrevChildren = jQuery( ".active" ).prev( "li" ).length;
		if( hasPrevChildren == 0 ) {
			nextItem = jQuery( ".picslide .last" );
		} else {
			nextItem = jQuery( ".active" ).prev( "li" );
		}
	} else {
		/* next */
		hasNextChildren = jQuery( ".active" ).next( "li" ).length;
		if( hasNextChildren == 0 ) {
			nextItem = jQuery( ".picslide .first" );
		} else {
			nextItem = jQuery( ".active + li" );
		}
	}

	/* hide the old element */
	jQuery( ".picslide .active p" ).fadeOut( 'slow' );
	jQuery( ".picslide .active" ).fadeOut( 2000 );
	jQuery( ".picslide .active" ).removeClass( "active" );

	/* see if we need to tweak the height or width*/
	if( nextItem.height( ) > jQuery( ".picslide" ).height( ) ) {
		jQuery( ".picslide" ).css( 'height', nextItem.height( ) )
	}
	if( nextItem.width( ) > jQuery( ".picslide" ).width( ) ) {
		jQuery( ".picslide" ).css( 'width', nextItem.width( ) )
	}


	jQuery( '.picslide .control' ).css( 'height', nextItem.children( "img" ).height( ) );
	
	/* show next picture */
	nextItem.addClass( "active" ); 
	jQuery( ".picslide .active p" ).fadeIn( 'slow' );
	jQuery( ".picslide .active" ).fadeIn( 2000 );

	/* set the timeout for next transition */
	timer = setTimeout( "changeImage( )", 8500 );
}

jQuery( function( ) {
	jQuery( ".picslide" ).css( 'height', jQuery( ".picslide .first" ).height( ) );
	jQuery( ".picslide" ).css( 'width', jQuery( ".picslide .first" ).width( ) );

	jQuery( ".picslide" ).children( "li" ).last( ).addClass( "last" );
	jQuery( '.picslide .control' ).css( 'height', jQuery( ".picslide .first img" ).height( ) );

	if( jQuery( ".picslide" ).children( ).length > 1 ) {
		timer = setTimeout( 'changeImage( "next" )', 8500 );
	}

	jQuery( '.picslide .control-left' ).click( function( e ) {
		clearTimeout( timer );
		changeImage( "prev" );
		e.preventDefault( );
	} );

	jQuery( '.picslide .control-right' ).click( function( e ) {
		clearTimeout( timer );
		changeImage( "next" );
		e.preventDefault( );
	} );
} );

