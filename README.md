# Dachi Web Framework
Dachi is a PHP web framework.

# Installation

1. [Install Node.JS](https://nodejs.org/download/) (for bower and grunt)
2. [Install Composer](https://getcomposer.org/doc/00-intro.md) (for back-end package management)
3. `npm install -g bower` (for front-end package management)
4. `npm install -g grunt` (for automation of tasks)

### Existing Project

**Linux/Mac:** `composer install && vendor/bin/dachi dachi:all`

**Windows:** `composer install && vendor\bin\dachi dachi:all`

### New Project
To create a new Dachi project just create a `composer.json` file with the following in:

	{
		"minimum-stability": "dev",
		"prefer-stable": true,
		"autoload": {
			"psr-4": {
				"SampleClient\\SampleProject\\": "src/"
			}
		},
		"require": {
			"ouropencode/dachi": "^3.0"
		}
	}

**You should replace `SampleClient\\SampleProject\\` with the correct name-space for your project.**

After creating this file, just run `composer install`. This will then download Dachi and all it's dependencies.

You should then run the following command to generate project files:

**Linux/Mac:** `vendor/bin/dachi dachi:create`

**Windows:** `vendor\bin\dachi dachi:create`

Dachi and your project are now ready. You should now configure Dachi (see 'Configuration').

# Command Line Tool
Dachi provides a command line tool to help with development.

## Refresh Everything
Although most of the commands can be run separately, it is often best to ensure all cache files and internal requirements are correct. You can run all of the required commands using:

**Linux/Mac:** `vendor/bin/dachi dachi:all`

**Windows:** `vendor\bin\dachi dachi:all`

This will run the following commands:

1. `npm install`
2. `bower install`
3. `dachi:route`
4. `dachi:modules`
5. `dachi:config`
6. `grunt --no-color`

## Creating a project
After installation Dachi does nothing. If you are not using an existing project, you must first create a project using the CLI tool:

**Linux/Mac:** `vendor/bin/dachi dachi:create`

**Windows:** `vendor\bin\dachi dachi:create`

## Generating Routes
Before Dachi can route requests, routing information will need to be generated. This is done via the CLI tool:

**Linux/Mac:** `vendor/bin/dachi dachi:route`

**Windows:** `vendor\bin\dachi dachi:route`

This will generate a `cache/dachi.routes.json` file that is used internally for routing requests. This command must be run again if url routing changes.

## Generating Configuration
Dachi provides many JSON files for configuration. To speed up requests when deployed, these files must be concatenated into a single quick-to-load file. This is done via the CLI tool:

**Linux/Mac:** `vendor/bin/dachi dachi:route`

**Windows:** `vendor\bin\dachi dachi:route`

This will generate a `cache/dachi.config.json` file that is used internally for configuration. This command must be run again if the configuration files are changed.

## Generating Module Information
Dachi uses a system to provide features, such as shortcuts, to modules. Shortcuts allow you to use `SampleModule:Model` instead of `SampleClient\SampleProject\SampleModule\Model` in database and templating. This system may be used in future for providing other features to modules. You can generate the modules information file using the CLI:

**Linux/Mac:** `vendor/bin/dachi dachi:modules`

**Windows:** `vendor\bin\dachi dachi:modules`

This will generate a `cache/dachi.modules.json` file that is used internally for setting up modules. This command must be run again if a module is added/removed or renamed.

## Generating Documentation
Dachi is completely documented using [ApiGen](http://www.apigen.org). Documentation can be generated using the CLI tool:

**Linux/Mac:** `vendor/bin/dachi dachi:document`

**Windows:** `vendor\bin\dachi dachi:document`

This tool will generate documentation in a `documentation` folder. This documentation will include documentation for your project. It is advised all projects also use the ApiGen format. The tool can be passed the `--internal` argument, this will then generate documentation useful for debugging the Dachi core.

# Configuration
Before you can deploy Dachi, you should confirm your configuration is correct. The default values are often insufficient.

### Dachi Configuration
The core configuration files for Dachi are stored in the `config` directory. The config directory contains three folders, one for each environment:

1. `production`
2. `development`
3. `local`

The three levels act as overrides. `production` settings will be used as the defaults, with `development` and `local` stacked onto them. This means you can omit configuration files you don't need to override. Detailed information regarding the configuration options can be found at [http://this.doesnt.exist.yet](http://this.doesnt.exist.yet).

At the very least you should confirm the `dachi.json` file is correct and the `database.json` file is correct.

**If you change configuration, you must delete the `cache/dachi.config.json` file and run the `dachi:config` CLI command.**

### Apache Configuration
Dachi uses Apache at the core to redirect all URIs into the main `index.php` file. You should confirm that the `RewriteBase` is correctly set in the `.htaccess` file. (Other web servers can be used so long as they support PHP and you redirect all requests to `src/index.php?dachi_uri=THE_URI_OMITTING_DOMAIN` (i.e. `src/index.php?dachi_uri=/samples/13/view`)

### Grunt Configuration
Grunt is now implemented and currently used for generating a concatenated bower css/js file. There is currently no support for per-project grunt files. Grunt is configured used files prefixed with `grunt.` in the standard 'Dachi Configuration' folders (see above).

# Reference Documents
- [ApiGen (Documentation Generation)](http://www.apigen.org)
- [Doctrine (Database Engine)](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/getting-started.html)
- [Twig.JS (Javascript Template Engine)](https://github.com/justjohn/twig.js/)
- [Twig (PHP Template Engine)](http://twig.sensiolabs.org/)
- [Composer (Back-end Component Management)](https://getcomposer.org/)
- [Bower (Front-end Component Management)](http://bower.io/)
- [Grunt (task automation)](http://gruntjs.com/)

# Sublime Integration
- [Twig Helper Bundle](https://github.com/Anomareh/PHP-Twig.tmbundle) (can be installed via sublime package manager)

# Notes
- @route-render must be higher than @route-uri (i.e.   `@route-uri /x/y @route-render /x`  is fine.   `@route-uri /x/y @route-render /z` is bad, `@route-uri /x/y @route-render /x/y/z` is also bad)
- when called via @route-render, the request URI variables will be the initial requests variables (`@route-uri /x/:id/delete @route-render /x/:id` will work, `@route-uri /x/:cool_id/delete @route-render /x/:diff_id` will not!)
