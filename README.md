# External Environment Plugin for [PHPCI](https://www.phptesting.org)

A plugin for PHPCI to copy an external environment file (e.g. `.env` environment file used in a Laravel Application) to the appropriate build directory.
 
### Initial Situation 

When committing a application to a VCS, it is a common nature, not to include any _sensitive_ data like database passwords within the application configuration files (e.g. `.env`, `phpci.yml`, `phpunit.xml`).

This plugins copies an environment file, located on the server PHPCI is running on, to the appropriate build directory. With that approach, there is no need to include any _sensitive_ data in the application releated files.

### Install the Plugin

1. Navigate to your PHPCI root directory and run `composer require rephlux/phpci-external-env`
2. Update your `phpci.yml` in the project you want to deploy with

### Prerequisites

1. Create a environment file for your project on the server PHPCI is running on
2. Ensure that the environment file is readable. 

### Plugin Options
- **env** _[string]_ - The path to the env file
- **path** _[string, optional]_ - The path to the destination filename relative to the appropriate build directory _(default: '.env')_

### PHPCI Config

```yml
\Rephlux\PHPCI\Plugin\ExternalEnvironment:
    env: <path_to_env_file>
    path: <path_to_destination_filename>
```

example:

```yml
setup:
    \Rephlux\PHPCI\Plugin\ExternalEnvironment: 
        env: "/usr/www/phpci/.env/laravel-application.env"
```
