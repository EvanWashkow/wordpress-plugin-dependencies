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
            $this->deactivatePlugin();
        }
    }
    
    
    /**
     * Deactivate the plugin
     *
     * @return bool
     */
    private function deactivatePlugin()
    {
        $this->plugin->deactivate( Sites::ALL );
        return $this->plugin->deactivate( Sites::CURRENT );
    }
}
