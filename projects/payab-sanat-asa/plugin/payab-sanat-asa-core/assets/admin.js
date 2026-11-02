/* Aladdin Payab admin: filter the texts table. */
( function () {
	var input = document.getElementById( 'ap-filter' );
	if ( ! input ) { return; }
	input.addEventListener( 'input', function () {
		var q = input.value.trim().toLowerCase();
		document.querySelectorAll( '.ap-texts tbody tr' ).forEach( function ( tr ) {
			var hay = tr.textContent.toLowerCase() + ' ' + Array.prototype.map.call( tr.querySelectorAll( 'input' ), function ( i ) { return i.value.toLowerCase(); } ).join( ' ' );
			tr.style.display = ! q || hay.indexOf( q ) !== -1 ? '' : 'none';
		} );
	} );
} )();
