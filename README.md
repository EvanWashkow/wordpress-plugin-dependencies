# WordPress Plugin Extensions

Extends WordPress lackluster plugin management into a more full-featured suite of tools


## Setup

Nothing. **This autostarts as a background daemon service**, so long as WordPress is started prior. In composer, order your dependencies so that WordPress is always loaded first.

**Last resort**. If you tried fixing your build, and absolutely cannot get this to run, you can manually initialize it like so:

```
\WordPress\Plugins\Extensions::Initialize();
```


## Plugin Dependencies

WordPress plugin dependencies done simply, the right way


### Setup

Add `Dependencies` (of the plugin folder names) to your plugin header, and continue on with your life.

```
/**
 * Name: My Plugin
 * Dependencies: jetpack, akismet
 */
```

### What it does
Forces the plugin dependencies to always remain active while the main plugin is active (i.e. the user cannot deactivate). If the plugin dependencies cannot be found, the plugin cannot be activated.


### For the critics

"Why should I use yours? There is already a very popular plugin that adds plugin dependencies!" However it is fundamentally flawed for multiple reasons:

1. Plugin names are used to define a plugin's dependencies
    * Flaw: Plugin Names **will** change

2. The dependency manager is, itself, a plugin
    * Flaw: Nothing ensures that the plugin-dependency manager plugin itself will be activated. It's not a daemon service that's always running in the background like this.
