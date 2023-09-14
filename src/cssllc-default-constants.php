<?php
/**
 * Description: Set default values of constants.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

$is_production = ( 'production' === wp_get_environment_type() );

defined( 'WP_DEBUG'            ) || define( 'WP_DEBUG',            !$is_production );
defined( 'WP_DEBUG_LOG'        ) || define( 'WP_DEBUG_LOG',         WP_DEBUG );
defined( 'WP_DEBUG_DISPLAY'    ) || define( 'WP_DEBUG_DISPLAY',     false );
defined( 'SCRIPT_DEBUG'        ) || define( 'SCRIPT_DEBUG',        !$is_production );
defined( 'CONCATENATE_SCRIPTS' ) || define( 'CONCATENATE_SCRIPTS', !WP_DEBUG || !SCRIPT_DEBUG );
defined( 'COMPRESS_SCRIPTS'    ) || define( 'COMPRESS_SCRIPTS',    !WP_DEBUG || !SCRIPT_DEBUG );
defined( 'COMPRESS_CSS'        ) || define( 'COMPRESS_CSS',        !WP_DEBUG || !SCRIPT_DEBUG );
defined(  'QM_DISABLED'        ) || define(  'QM_DISABLED',        !WP_DEBUG );
defined( 'QMX_DISABLED'        ) || define( 'QMX_DISABLED',         QM_DISABLED );
defined( 'ACF_LITE'            ) || define( 'ACF_LITE',             $is_production );

?>
