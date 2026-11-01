<?php
/**
 * Resolve stored tokens ({{theme}}, {{route:..}}) when content is output, so the site stays portable.
 *
 * @package PayabSanatAsa
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'the_content', 'aladdin_payab_sanat_asa_resolve_tokens', 99 );
add_filter( 'elementor/frontend/the_content', 'aladdin_payab_sanat_asa_resolve_tokens', 99 );
add_filter( 'elementor/widget/render_content', 'aladdin_payab_sanat_asa_resolve_tokens', 99 );
add_filter( 'elementor/frontend/container/render_content', 'aladdin_payab_sanat_asa_resolve_tokens', 99 );
