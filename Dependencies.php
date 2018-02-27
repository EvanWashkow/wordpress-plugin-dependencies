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
        
        // Listen for plugins load to enforce plugin dependencies
        add_action( 'plugins_loaded', function() {
            self::buildDependencies();
        });
    }
    
    
    /**
     * Build dependencies for all plugins
     */
    private static function buildDependencies()
    {
        // For each plugin, extract its dependencies from its file header
        $plugins = \WordPress\Plugins::Get();
        foreach ( $plugins as $plugin ) {
            
            // Lookup the dependency IDs
            $dependencyIDs = $plugin->get( self::FILE_HEADER_ID );
            $dependencyIDs = trim( $dependencyIDs );
            if ( '' === $dependencyIDs ) {
                continue;
            }
            $dependencyIDs = explode( ',', $dependencyIDs );
            
            // For each dependency, create a new dependency instance
            foreach ( $dependencyIDs as $dependencyID ) {
                $dependencyID = trim( $dependencyID );
                if ( '' !== $dependencyID ) {
                    new Dependencies\Dependency( $plugin->getID(), $dependencyID );
                }
            }
        }
    }
}
Dependencies::Initialize();