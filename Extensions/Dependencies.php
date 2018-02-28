<?php
namespace WordPress\Plugins\Extensions;

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
     * Has the dependency manager been started?
     *
     * @var bool
     */
    private static $isInitializeed = false;
    
    
    /**
     * Initialize the plugin dependency manager
     */
    public static function Initialize()
    {
        // Exit. Cannot run automatically. This function must be called when ready.
        if ( !function_exists( 'add_filter' )) {
            return;
        }
        
        // Exit. This has already been started.
        elseif ( self::$isInitializeed ) {
            return;
        }
        
        // Add custom plugin file header
        add_filter( 'extra_plugin_headers', function( array $headers ) {
            $headers[] = self::FILE_HEADER_ID;
            return $headers;
        });
        
        // Listen for plugin initialization
        add_action( 'init', function() {
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

// Try to auto-start the dependencies manager
Dependencies::Initialize();
