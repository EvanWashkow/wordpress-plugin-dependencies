<?php
namespace WordPress\Plugins\Dependencies;

use WordPress\Plugins;
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
     * @var Plugins\Models\Plugin
     */
    private $dependency;
    
    /**
     * ID of the main plugin that requires the dependency
     *
     * @var Plugins\Models\Plugin
     */
    private $plugin;
    
    
    /**
     * Create a new dependency instance
     *
     * @param string $pluginID     ID of the main plugin that requires the dependency
     * @param string $dependencyID ID of the plugin being included as a dependency
     */
    public function __construct( string $pluginID, string $dependencyID )
    {
        // Set properties
        $this->plugin     = Plugins::Get( $pluginID );
        $this->dependency = Plugins::Get( $dependencyID );
        
        // Deactivate plugin if the dependency does not exist
        if ( null === $this->dependency ) {
            $this->plugin->deactivate( Sites::ALL );
            $this->plugin->deactivate( Sites::CURRENT );
            self::notify( "Could not activate the <b>{$this->plugin->getName()}</b> plugin because the required plugin <b>{$dependencyID}</b> does not exist." );
        }
        
        // Site-wide: try to activate the dependency if the plugin is active
        // (necessary for plugin updates)
        elseif (
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
        
        // Print queue of notifications
        add_action( 'admin_notices', function() {
            $html = '';
            foreach ( self::$notifications as $notification ) {
                $html .= "<div class='notice notice-{$notification->type}'>";
                $html .=    "<p>{$notification->message}</p>";
                $html .= '</div>';
            }
            echo $html;
        });
    }
}
