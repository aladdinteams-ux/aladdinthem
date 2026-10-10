<?php
// Test only: CLI scripts act as user 1.
if ( defined( 'LS_TEST_USER' ) ) {
	add_filter( 'determine_current_user', function () { return 1; }, 99 );
}
