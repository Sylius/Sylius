How to deploy Sylius to Platform.sh?
====================================

.. tip::

    Start with reading `Platform.sh documentation <https://docs.platform.sh/frameworks/symfony.html>`_.
    Also Symfony provides `a guide on deploying projects to Platform.sh <http://symfony.com/doc/current/deployment/platformsh.html>`_.

The process of deploying Sylius to Platform.sh is based on the guidelines prepared for Symfony projects in general.
In this guide you will find sufficient instructions to have your application up and running on Platform.sh.

1. Prepare a Platform.sh project
--------------------------------

If you do not have it yet, go to the `Platform.sh store <https://accounts.platform.sh/platform/buy-now>`_ choose development plan
and go through checkout. Then, when you will have a project ready, give it a name and proceed to ``Import an existing site``.

.. tip::

    To investigate if Platform.sh suits your needs, you can use their **free trial**, which you can choose as a development plan.

2. Make the application ready to deploy
---------------------------------------

* Add the ``.platform.app.yaml`` file at the root of your project repository

This is how this file should look like for Sylius:

.. code-block:: yaml

    # .platform.app.yaml
    name: sylius

    type: "php:7.0"
    build:
        flavor: symfony

    relationships:
        database: "mysql:mysql"
        redis: "redis:redis"

    runtime:
        extensions:
            - msgpack
            - igbinary
            - memcached
            - redis

    web:
        document_root: "/web"
        passthru: "/app.php"

        whitelist:
          - \.css$
          - \.js$

          - \.gif$
          - \.jpe?g$
          - \.png$
          - \.tiff?$
          - \.wbmp$
          - \.ico$
          - \.jng$
          - \.bmp$
          - \.svgz?$

          - \.midi?$
          - \.mpe?ga$
          - \.mp2$
          - \.mp3$
          - \.m4a$
          - \.ra$
          - \.weba$

          - \.3gpp?$
          - \.mp4$
          - \.mpe?g$
          - \.mpe$
          - \.ogv$
          - \.mov$
          - \.webm$
          - \.flv$
          - \.mng$
          - \.asx$
          - \.asf$
          - \.wmv$
          - \.avi$

          - \.ogx$

          - \.swf$

          - \.jar$

          - \.ttf$
          - \.eot$
          - \.woff$
          - \.otf$

          - /robots\.txt$

          - \.html$
          - \.pdf$

    disk: 2048

    mounts:
        "/var/cache": "shared:files/cache"
        "/var/logs": "shared:files/logs"
        "/web/uploads": "shared:files/uploads"
        "/web/media": "shared:files/media"

    hooks:
        build: |
            rm web/app_dev.php
            rm web/app_test.php
            rm web/app_test_cached.php
            rm -rf var/cache/*
            php bin/console --env=prod --no-debug --ansi cache:clear
            php bin/console --env=prod --no-debug --ansi assets:install
            npm install
            npm run gulp
        deploy: |
            rm -rf web/media/*
            php bin/console --env=prod --no-debug sylius:fixtures:load
            rm -rf var/cache/*
    crons:
        reset:
            spec: "0 */1 * * *"
            cmd: "rm -rf web/media/* && php bin/console --env=prod --no-debug sylius:fixtures:load"

* Add ``.platform/routes.yaml`` file:

.. code-block:: yaml

    # .platform/routes.yaml
    "http://{default}/":
        type: upstream
        upstream: "sylius:http"

    "http://www.{default}/":
        type: redirect
        to: "http://{default}/"

* Add ``.platform/services.yaml`` file:

This file will load ``myslq`` and ``redis`` on your Platform.sh server.

.. code-block:: yaml

    # .platform/services.yaml
    mysql:
        type: mysql
        disk: 2048

    redis:
        type: redis:2.8

* Configure the access to the database:

In the ``app/config/parameters_platform.php`` put such code:

.. code-block:: php

    // app/config/parameters_platform.php
    <?php

    $relationships = getenv("PLATFORM_RELATIONSHIPS");

    if (!$relationships) {
        return;
    }

    $relationships = json_decode(base64_decode($relationships), true);

    foreach ($relationships['database'] as $endpoint) {
        if (empty($endpoint['query']['is_master'])) {
            continue;
        }

        $container->setParameter('database_driver', 'pdo_' . $endpoint['scheme']);
        $container->setParameter('database_host', $endpoint['host']);
        $container->setParameter('database_port', $endpoint['port']);
        $container->setParameter('database_name', $endpoint['path']);
        $container->setParameter('database_user', $endpoint['username']);
        $container->setParameter('database_password', $endpoint['password']);
        $container->setParameter('database_path', '');
    }
    foreach ($relationships['redis'] as $endpoint) {
        $container->setParameter('redis_dsn', 'redis://'.$endpoint['host'].':'.$endpoint['port']);
    }

    $container->setParameter('sylius.cache', array('type' => 'array'));

    ini_set('session.save_path', '/tmp/sessions');

Remember to have it imported in the config:

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: parameters_platform.php }

3. Add Platform.sh as a remote to your repository:
--------------------------------------------------

Use the below command to add your Platform.sh project as the ``platform`` remote:

.. code-block:: bash

    $ git remote add platform [PROJECT-ID]@git.[CLUSTER].platform.sh:[PROJECT-ID].git

The ``PROJECT-ID`` is the unique identifier of your project,
and ``CLUSTER`` can be ``eu`` or ``us`` - depending on where are you deploying your project.

4. Commit the Platform.sh specific files:
-----------------------------------------

.. code-block:: bash

    $ git add .platform.app.yaml
    $ git add .platform/*
    $ git add app/config/parameters_platform.php
    $ git add app/config/config.yml
    $ git commit -m "Platform.sh deploy configuration files."

5. Push your project to the platform remote:
--------------------------------------------

.. code-block:: bash

    $ git push platform master

6. Connect to the project via SSH and install Sylius
----------------------------------------------------

The SSH command can be found in your project data on Platform.sh.

When you get connected please run:

.. code-block:: bash

    $ php bin/console sylius:install

Learn more
----------

* Platform.sh documentation: `Configuring Symfony projects for Platform.sh <https://docs.platform.sh/frameworks/symfony.html>`_
* Symfony documentation: `Deploying Symfony to Platform.sh <http://symfony.com/doc/current/deployment/platformsh.html>`_
* :doc:`Installation Guide </book/installation/installation>`
