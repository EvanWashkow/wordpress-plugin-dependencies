<?php
namespace WordPress\Plugins;

/**
 * Loads and sets up WordPress plugin extensions
 */
final class Extensions
{
    
    /**
     * Whether plugin extensions are already initialized
     *
     * @var bool
     */
    private static $isInitialized = false;
    
    
    /**
     * Load and set up WordPress plugin extensions
     */
    public static function Initialize()
    {
        // Exit. WordPress is not yet initialized.
        if ( !defined( 'ABSPATH' )) {
            return;
        }
        
        // Exit. Already initialized.
        if ( self::$isInitialized ) {
            return;
        }
        
        // Mark as initialized
        self::$isInitialized = true;
    }
}

// Try to auto-start extensions
Extensions::Initialize();
