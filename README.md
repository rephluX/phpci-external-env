# External Environment Plugin for [PHPCI](https://www.phptesting.org)

[![Build Status](https://travis-ci.org/rephluX/phpci-external-env.svg?branch=master)](https://travis-ci.org/rephluX/phpci-external-env)
[![Latest Stable Version](https://poser.pugx.org/rephlux/phpci-external-env/v/stable.svg)](https://packagist.org/packages/rephlux/phpci-external-env)
[![License](https://poser.pugx.org/rephlux/phpci-external-env/license.svg)](https://packagist.org/packages/rephlux/phpci-external-env)

A plugin for PHPCI to copy an external environment file (e.g. `.env` environment file used in a Laravel Application) to the appropriate build directory.
 
### Initial Situation 

When committing a application to a VCS, it is a common nature, not to include any _sensitive_ data like database passwords within the application configuration files (e.g. `.env`, `phpci.yml`, `phpunit.xml`).

This plugins copies an environment file, located on the server PHPCI is running on, to the appropriate build directory. With that approach, there is no need to include any _sensitive_ data in the application releated files.

Each branch in the VCS can be configured separately to support different settings for each branch:

* master branch -> production settings
* development branch -> stage settings

### Install the Plugin

1. Navigate to your PHPCI root directory and run `composer require rephlux/phpci-external-env`
2. Update your `phpci.yml` in the project you want to deploy with

### Prerequisites

1. Create a environment file for your project on the server PHPCI is running on
2. Ensure that the environment file is readable. 

### Plugin Options

- **branch** _[array]_ - The specific branch for the project
    - **env** _[string]_ - The path to the env file
    - **path** _[string, optional]_ - The path to the destination filename relative to the appropriate build directory _(default: '.env')_

### PHPCI Config

```yml
\Rephlux\PHPCI\Plugin\ExternalEnvironment:
    <branch>:
        env: <path_to_env_file>
        path: <path_to_destination_filename>
```

example:

```yml
setup:
    \Rephlux\PHPCI\Plugin\ExternalEnvironment:
        master:
            env: "/usr/www/phpci/.env/laravel-application-production.env"
        development:
            env: "/usr/www/phpci/.env/laravel-application-stage.env"    
```
