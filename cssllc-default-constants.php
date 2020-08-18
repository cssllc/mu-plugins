<?php
/**
 * Description: Set default values of constants.
 * Author URI: https://develop.calebstauffer.com
 * Author: Caleb Stauffer
 * Plugin URI: https://github.com/cssllc/mu-plugins
 */

defined( 'WP_LOCAL_DEV'        ) || define( 'WP_LOCAL_DEV',         false );
defined( 'WP_DEVELOP'          ) || define( 'WP_DEVELOP',           'production' !== wp_get_environment_type() );
defined( 'WP_DEBUG'            ) || define( 'WP_DEBUG',             WP_DEVELOP );
defined( 'WP_DEBUG_LOG'        ) || define( 'WP_DEBUG_LOG',         WP_DEVELOP );
defined( 'WP_DEBUG_DISPLAY'    ) || define( 'WP_DEBUG_DISPLAY',     false );
defined( 'SCRIPT_DEBUG'        ) || define( 'SCRIPT_DEBUG',         WP_DEVELOP );
defined( 'CONCATENATE_SCRIPTS' ) || define( 'CONCATENATE_SCRIPTS', !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'COMPRESS_SCRIPTS'    ) || define( 'COMPRESS_SCRIPTS',    !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'COMPRESS_CSS'        ) || define( 'COMPRESS_CSS',        !WP_DEVELOP || !SCRIPT_DEBUG );
defined( 'QM_DISABLED'         ) || define( 'QM_DISABLED',         !WP_DEBUG );
defined( 'QMX_DISABLED'        ) || define( 'QMX_DISABLED',         QM_DISABLED );
defined( 'ACF_LITE'            ) || define( 'ACF_LITE',            !WP_DEVELOP );

?>