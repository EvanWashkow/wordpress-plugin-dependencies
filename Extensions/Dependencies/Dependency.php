<?php
namespace WordPress\Plugins\Extensions\Dependencies;

use WordPress\Plugins;
use WordPress\Plugins\Models\Plugin;
use WordPress\Sites;

/**
 * Defines a single plugin dependency, auto-activating it when the main plugin
 * is activated
 */
final class Dependency
{
    
    /**
     * Notification messages to print to the admin screen
     *
     * @var array
     */
    private static $notifications = [];
    
    /**
     * ID of the plugin being included as a dependency
     *
     * @var Plugin
     */
    private $dependency;
    
    /**
     * ID of the main plugin that requires the dependency
     *
     * @var Plugin
     */
    private $plugin;
    
    
    /**
     * Create a new dependency instance
     *
     * @param Plugin $plugin       Plugin that requires the dependency
     * @param string $dependencyID ID of the plugin being included as a dependency
     */
    public function __construct( Plugin $plugin, string $dependencyID )
    {
        // Set properties
        $this->plugin = $plugin;
        
        // Dependency exists
        if ( Plugins::IsValidID( $dependencyID )) {
            $this->dependency = Plugins::Get( $dependencyID );
            
            // Site-wide: try to activate the dependency if the plugin is active
            // (necessary for plugin updates)
            if (
                $this->plugin->isActive(      Sites::ALL ) &&
                !$this->dependency->isActive( Sites::ALL )
            ) {
                $this->activateDependency( Sites::ALL );
            }
            
            // Single-site: try to activate the dependency if the plugin is active
            // (necessary for plugin updates)
            elseif (
                $this->plugin->isActive(      Sites::CURRENT ) &&
                !$this->dependency->isActive( Sites::CURRENT )
            ) {
                $this->activateDependency( Sites::CURRENT );
            }
        }
        
        // Dependency missing: deactivate plugin
        else {
            $this->plugin->deactivate( Sites::ALL );
            $this->plugin->deactivate( Sites::CURRENT );
            self::notify( "Deactivating the plugin <b>{$this->plugin->getName()}</b> because the required plugin <b>{$dependencyID}</b> does not exist." );
        }
    }
    
    
    /**
    * Activate this dependency. On failure, deactivate the plugin.
    *
    * @param int $siteID The site ID or \WordPress\Sites constant
    * @return bool
    */
    private function activateDependency( int $siteID )
    {
        $isActive = $this->dependency->activate( $siteID );
        if ( !$isActive ) {
            $this->plugin->deactivate( $siteID );
            self::notify( "Deactivating the plugin <b>{$this->plugin->getName()}</b> because the required plugin <b>{$this->dependency->getName()}</b> could not be activated." );
        }
        return $isActive;
    }
    
    
    /**
     * Notify the administrator
     *
     * @param string $message Message to print
     * @param string $type    The type of notification to send
     */
    private static function notify( string $message, string $type = 'error' )
    {
        // Add notification message to queue
        $notification = new \stdClass();
        $notification->message = $message;
        $notification->type    = $type;
        self::$notifications[] = $notification;
        
        // Only register notification handler once
        if ( 2 <= count( self::$notifications )) {
            return;
        }
        
        // Draw notification queue
        add_action( 'network_admin_notices', function() {
            echo self::drawNotifications();
        });
        add_action( 'admin_notices', function() {
            echo self::drawNotifications();
        });
    }
    
    
    /**
     * Draw the admin notifications
     *
     * @return string
     */
    private static function drawNotifications()
    {
        $html = '';
        foreach ( self::$notifications as $notification ) {
            $html .= "<div class='notice notice-{$notification->type}'>";
            $html .=    "<p>{$notification->message}</p>";
            $html .= '</div>';
        }
        return $html;
    }
}
