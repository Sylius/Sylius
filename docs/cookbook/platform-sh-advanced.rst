Advanced Platform.sh configurations
===================================

The basic set-up let's you easily set-up a Platform.sh project running your Sylius application. It should give you an
environment suitable for testing Platform.sh in combination with Sylius.

In this guide additional tips will be given in order to benefit in a production environment.

Keep sessions between deployments
---------------------------------

The default configuration saves PHP sessions into ``/tmp/sessions``. Platform.sh functions in such way that each
deployment spins up a new container instance and therefore the temporary folder holding sessions will be gone.

In order to save the PHP sessions on disk, the following steps need to be followed:

* In ``platform.app.yml`` add the following under the mount property:

.. code-block:: yaml

    mount:
        "/app/sessions": "shared:files/sessions"


* In the ``app/config/parameters_platform.php`` replace the session path:

.. code-block:: php

    ini_set('session.save_path', '/app/app/sessions');

Alternatively you can use a ``php.ini``` file in the root of your project:

.. code-block:: ini

    session.save_path = "/app/app/sessions"

Use Redis for Doctrine caching:
-------------------------------

Want to use the metacache, query cache or result cache Symfony and Doctrine have to offer? It comes with a caveat.
Platform.sh doesn't allow you to connect to all your services yet from inside the `build` hook. The following
tutorial will guide you through this and make use of Redis. In the default example Redis is already activated.

* In your ``app/config/parameters.yml.dist`` add:

.. code-block:: yaml

    parameters:
        metacache_driver: []
        querycache_driver: []
        resultcache_driver: []
        redis_dsn: ~
        redis_host: ~
        redis_port: ~

* In the  ``app/config/parameters_platform.php`` file, under the part where the database credentials are set, add:

.. code-block:: php

    foreach ($relationships['redis'] as $endpoint) {
        $container->setParameter('metacache_driver', 'redis');
        $container->setParameter('querycache_driver', 'redis');
        $container->setParameter('resultcache_driver', 'redis');

        $container->setParameter('redis_dsn', 'redis://'.$endpoint['host'].':'.$endpoint['port']);
        $container->setParameter('redis_host', $endpoint['host']);
        $container->setParameter('redis_port', $endpoint['port']);
    }

.. tip::

    Your Redis connection credentials are now available, which you can also use for the default Symfony cache.

* In your ``app/config/config_prod.yml`` file add:

.. code-block:: yaml

    doctrine:
        orm:
            metadata_cache_driver:
                type: "%metacache_driver%"
                database: 1
                host: "%redis_host%"
                port: "%redis_port%"
            query_cache_driver:
                type: "%querycache_driver%"
                database: 2
                host: "%redis_host%"
                port: "%redis_port%"
            result_cache_driver:
                type: "%resultcache_driver%"
                database: 3
                host: "%redis_host%"
                port: "%redis_port%"

* If you want to empty the cache on deployment, adjust the deploy hook in ``.platform.app.yaml``:

.. code-block:: yaml

    hooks:
        deploy: |
            rm -rf var/cache/*
            php bin/console --env=prod doctrine:cache:clear-metadata
            php bin/console --env=prod doctrine:cache:clear-query
            php bin/console --env=prod doctrine:cache:clear-result
            php bin/console --env=prod doctrine:migrations:migrate --no-interaction

Add default Sylius cronjobs:
----------------------------

Add the example below to your ``.platform.app.yaml`` file. This runs these cronjobs every 6 hours.

.. code-block:: yaml

    crons:
        cleanup_cart:
            spec: '0 */6 * * *'
            cmd: '/usr/bin/flock -n /tmp/lock.app.cleanup_cart bin/console sylius:remove-expired-carts --env=prod --verbose'
        cleanup_order:
            spec: '0 */6 * * *'
            cmd: '/usr/bin/flock -n /tmp/lock.app.cleanup_order bin/console sylius:cancel-unpaid-orders --env=prod --verbose'

Additional tips:
----------------

* Platform.sh can serve gzipped versions of your static assets. Make sure to save your assets in the same folder, but with
    a .gz suffix. The ``gulp-gzip`` node package comes very helpful integrating saving of .gz versions of your assets.

* Platform.sh comes with a `New Relic integration <https://docs.platform.sh/administration/integrations/new-relic.html>`_.

* Platform.sh comes with a `Blackfire.io integration <https://docs.platform.sh/administration/integrations/blackfire.html>`_
