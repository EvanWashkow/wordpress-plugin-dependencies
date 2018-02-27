<?php
namespace WordPress\Plugins;

require_once( __DIR__ . '/Dependencies/Dependency.php' );

/**
 * Defines a manager for WordPress plugin dependencies
 */
final class Dependencies
{
    
    /**
     * The plugin file's header key for listing dependencies
     *
     * @var string
     */
    const FILE_HEADER_ID = 'Dependencies';
    
    
    /**
     * Start the plugin dependency manager
     */
    public static function Initialize()
    {
        // Add custom plugin file header
        add_filter( 'extra_plugin_headers', function( array $headers ) {
            $headers[] = self::FILE_HEADER_ID;
            return $headers;
        });
    }
}
Dependencies::Initialize();
