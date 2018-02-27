<?php
namespace WordPress\Plugins\Dependencies;

/**
 * Defines a single plugin dependency, auto-activating it when the main plugin
 * is activated
 */
final class Dependency
{
    
    /**
     * ID of the plugin being included as a dependency
     *
     * @var string
     */
    private $dependencyID;
    
    /**
     * ID of the main plugin that requires the dependency
     *
     * @var string
     */
    private $pluginID;
    
    
    /**
     * Create a new dependency instance
     *
     * @param string $pluginID     ID of the main plugin that requires the dependency
     * @param string $dependencyID ID of the plugin being included as a dependency
     */
    public function __construct( string $pluginID, string $dependencyID )
    {
        $this->pluginID     = $pluginID;
        $this->dependencyID = $dependencyID;
    }
}
