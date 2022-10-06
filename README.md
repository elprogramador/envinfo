Current version v0.1.2

This package is useful for displaying variables found in .env files in a more readable way. By accessing the url you will be able to show the variables that you have active, excluding information that is not relevant and in a non-duplicated way

Important note

In production delete from the config/services.yaml file

the lines:

Jlle\EnvInfo\:
        resource: '../vendor/jlle/env-info/src/' 

since if you do a composer install --no-dev it will give an error

You must also make sure that the envinfopass file in the root directory of the project is not in production. If this file is not present, the service cannot be used. This is necessary so that no one can see the environment variables. You should only use this file locally to activate the service.

The url to see the variables depending on the configured environment is

http://localhost/envinfo

Installation documentation
https://github.com/elprogramador/envinfo/blob/main/INSTALL
