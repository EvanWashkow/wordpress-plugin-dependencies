<?php
namespace WordPress\Plugins;

// Include vendor files if on a local build
if ( file_exists( __DIR__ . '/vendor/autoload.php' )) {
    require_once( __DIR__ . '/vendor/autoload.php' );
    return;
}

/**
 * Defines a manager for WordPress plugin dependencies
 */
class Dependencies
{
    
}
