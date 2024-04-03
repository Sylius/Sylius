PHP Configuration
=================

Customizing PHP-related configurations on Sylius Cloud is pivotal for enhancing your Sylius platform performance and functionality.
Whether it's fine-tuning settings in php.ini, optimizing OPcache for caching efficiency, enabling preloading for faster application startup, or facilitating debugging with Xdebug,
Sylius Cloud empowers developers to tailor their PHP environment to meet specific project needs.

PHP-FPM configuration
---------------------

PHP-FPM helps improve your app’s performance by maintaining pools of workers that can process PHP requests. This is particularly useful when your app needs to handle a high number of simultaneous requests.

Sylius Cloud doesn't allow to manage all PHP-FPM configuration keys. By default, Sylius Cloud automatically sets a maximum number of PHP-FPM workers for your Sylius platform.
The number of workers is calculated based on three parameters:

- \- **The container memory**: the amount of memory you can allot for PHP processing depending on app size.
- \- **The request memory**: the amount of memory an average PHP request is expected to require.
- \- **The reserved memory**: the amount of memory you need to reserve for tasks that aren’t related to requests.

The value is calculated by the rule:

.. code-block:: text

    `WORKERS_NUMBER = (CONTAINER_MEMORY + RESERVED_MEMORY) / REQUEST_MEMORY`.

You can setup the `request_memory` and `reserved_memory` by your own, in your `platform.app.yaml` file:

.. code-block:: yaml

    runtime:
        sizing_hints:
            request_memory: 110
            reserved_memory: 80

To determine what the optimal request memory is for your Sylius platform, you can refer to your PHP access logs:

.. code-block:: bash

    platform log --lines 5000 php.access | awk '{print $6}' | sort -n | uniq -c

The command above will output you a structured value for last 5000 requests:

.. code-block:: text

    2654 2048
    431  4096
    584  8192
    889  10240
    374  12288
     68  46384

First column determines a number of requests, which had used the memory amount specified in second column.

Enabling Opcache preloading option
----------------------------------

Enabling Symfony preloading can help improve the performance of your application by reducing the time it takes to load classes and files on each request.
By following the steps below, you can easily configure preloading for your Sylius platform and take advantage of this optimization feature provided by Sylius Cloud.

To enable preloading, please:

1. Ensure that your Symfony application is using PHP version 7.4 or higher, as preloading is supported in these versions.
2. In your project's `.platform.app.yaml` file, add or update the PHP configuration section to include the preloading directive set to true. Here's an example:

.. code-block:: yaml

    runtime:
        extensions:
            - opcache

    web:
        locations:
            "/":
                passthru: "/index.php"

        php:
            extensions:
                - opcache
            preloading: true

3. Optionally, you can customize the preload list for your Sylius application to include frequently used classes or files.
   This optimization can improve preloading performance. You can do this in your Sylius application's configuration,
   typically in the `config/packages/prod/opcache.yaml` file.

   If this value is lower than the number of files in the Sylius platform, the cache becomes less effective because it starts thrashing.

4. Optionally, add or update the PHP configuration section in your project's `.platform.app.yaml` file to include the `opcache.max_accelerated_files` directive
   with your desired value. For example:

.. code-block:: yaml

    runtime:
        extensions:
            - opcache

    web:
        locations:
            "/":
                passthru: "/index.php"

        php:
            extensions:
                - opcache
            opcache:
                max_accelerated_files: 10000

5. After updating your `.platform.app.yaml` file, commit your changes to your project's Git repository and push them to your Sylius Cloud environment.
   Sylius Cloud will automatically detect the changes and deploy your Sylius platform with preloading enabled.

Configuring php.ini file
------------------------

By configuring PHP settings in `.platform.app.yaml`, you can customize the PHP runtime environment for your application on Sylius Cloud,
ensuring it meets your specific requirements and performance considerations.

To configure php.ini settings, please add or update the PHP configuration section in your project's `.platform.app.yaml` file.
You can specify settings under the php key, using the appropriate directives as needed.

For example, if you want to set `memory_limit` and `max_execution_time`, your configuration might look like this:

.. code-block:: yaml

    web:
        php:
            memory_limit: 512M
            max_execution_time: 60

You're also able to do it by running the CLI command, as an example below:

.. code-block:: bash

    platform variable:create --level environment \
        --prefix php --name memory_limit \
        --value 256M --environment ENVIRONMENT_NAME \
        --no-interaction

Optionally, you can also put the `php.ini` file in your Sylius platform root directory. Using this method isn’t recommended since it offers less flexibility and is more error-prone.
Consider using variables instead.

SMTP configuration
------------------

An SMTP configuration allows you to manage outgoing email communication from your environments.
You can turn on outgoing email for each environment separately. By default, outgoing email configuration is turned on for your production environment and disabled for other environments.

To turn it on for a specific environment, please use the CLI command:

.. code-block:: bash

    platform environment:info --environment ENVIRONMENT_NAME enable_smtp true

Changing the setting will cause rebuilding the environment.

To configure your email delivery provider with Sylius application, please setup the `MAILER_DSN` environment variable.

Environment variables configuration
-----------------------------------

Environment variables allow you to have better control over the Sylius build process and runtime environment.
You can use them in your code to not to hardcode the sensitive environment configuration.

You can use them to define the values such as database credentials, API tokens, secret keys, SMTP configuration and others.

An example of environment variables definition you can find here:

.. code-block:: yaml

    variables:
        env:
            A_SIMPLE_STRING_VALUE: "I'm simple string value"
            AN_ARRAY_VALUE:
                - 'value-1'
                - 'value-2'
            AN_OBJECT_VALUE:
                "key1": "value1"
                "key2": "value2"
        my_variables:
            AN_ARRAY_VALUE: ['value-1', 'value-2']
            AN_OBJECT_VALUE:
                key1: 'value1'
                key2: 'value2'

You can also set your environment variables using the CLI:

.. code-block:: bash

    platform variable:create --name env:foo --value bar

By using the environment variables you can define your own variables, or set up values for already defined variables used by the container:

.. code-block:: bash

    platform variable:create --level environment --prefix php --name memory_limit --value 256M --environment ENVIRONMENT_NAME

A very useful option is to define whether variables value can be visible during build or deployment process logs:

.. code-block:: bash

    platform variable:create --name env:a_sensitive_variable --value bar --visible-build=false --visible-runtime=false

Enabling PHP Extensions
-----------------------

Enabling PHP extensions on Sylius Cloud is a straightforward process.
You can do this by updating your `.platform.app.yaml` configuration file to include the required PHP extension. Here's how:

1. Determine which PHP extension your application needs. This could be extensions like pdo_mysql, gd, mbstring, or others.
2. Update `.platform.app.yaml` file. Under the runtime section, add the extensions key if it's not already present.
3. Add the name of the PHP extension you want to enable to the extensions list. For example:

.. code-block:: yaml

    runtime:
        extensions:
            - pdo_mysql
            - gd

Replace pdo_mysql and gd with the names of the extensions your application requires.

4. Save your changes to the `.platform.app.yaml` file, commit them to your Git repository, and push them to your environment.
5. After the changes have been deployed, you can verify that the PHP extension is enabled by accessing your application's environment through the CLI or web interface.

To see a complete list of the compiled PHP extensions, run the following CLI command:

.. code-block:: bash

    platform ssh "php -m"

XDebug configuration
--------------------

Xdebug is a powerful PHP debugging tool that streamlines the development process by allowing developers to identify and fix issues in their code efficiently.
Here's a general overview of how you can configure it on Sylius Cloud:

1. In your project's `.platform.app.yaml` file, add a new section for configuring Xdebug. Here's an example configuration:

.. code-block:: bash

    runtime:
        extensions:
            - xdebug

    web:
        php:
            xdebug:
                enabled: true
                remote_enable: 1
                remote_autostart: 1
                remote_host: YOUR_HOST_IP
                remote_port: 9000

2. Replace YOUR_HOST_IP with the IP address of your development machine. This configuration enables Xdebug, configures it to start automatically for each request, and sets up the remote debugging settings.

3. After updating your `.platform.app.yaml` file, commit your changes to your project's Git repository and push them to your environment. Sylius Cloud will automatically detect the changes and apply the new Xdebug configuration during deployment.

4. Finally, configure your IDE to listen for incoming Xdebug connections.
   Set up a remote debugging session in your IDE and configure it to connect to the remote host (your development machine) on the specified port (usually 9000).

.. note::

    Please keep in mind that enabling Xdebug may impact performance, so it's recommended to only enable it when needed, such as during development and testing phases.
    Additionally, consider configuring Xdebug to only start for specific environments, such as development or staging, to avoid impacting production environments.
