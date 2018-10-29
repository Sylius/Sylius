How to deploy Sylius to Platform.sh?
====================================

.. tip::

    Start with reading `Platform.sh documentation <https://docs.platform.sh/frameworks/symfony.html>`_.
    Also Symfony provides `a guide on deploying projects to Platform.sh <http://symfony.com/doc/current/deployment/platformsh.html>`_.

The process of deploying Sylius to Platform.sh is based on the guidelines prepared for Symfony projects in general.
In this guide you will find sufficient instructions to have your application up and running on Platform.sh.

1. Prepare a Platform.sh project
--------------------------------

If you do not have it yet, go to the `Platform.sh store <https://accounts.platform.sh/platform/buy-now>`_, choose development plan
and go through checkout. Then, when you will have a project ready, give it a name and proceed to ``Import an existing site``.

.. tip::

    To investigate if Platform.sh suits your needs, you can use their **free trial**, which you can choose as a development plan.

2. Make the application ready to deploy
---------------------------------------

* Add the ``.platform.app.yaml`` file at the root of your project repository

This is how this file should look like for Sylius (tuned version of the default Platform.sh example):

.. code-block:: yaml

    # .platform.app.yaml
    name: app

    type: "php:7.2"

    build:
        flavor: composer

    relationships:
        database: "mysql:mysql"
        redis: "redis:redis"

    variables:
        env:
            APP_ENV: 'prod'
            APP_DEBUG: 0

    runtime:
        extensions:
            - msgpack
            - igbinary
            - memcached
            - redis

    dependencies:
        nodejs:
            yarn: "*"
            gulp-cli: "*"

    web:
        locations:
            '/':
                root: "public"
                passthru: "/index.php"
                allow: true
                expires: -1
                scripts: true
            '/assets/shop':
                expires: 2w
                passthru: true
                allow: false
                rules:
                    # Only allow static files from the assets directories.
                    '\.(css|js|jpe?g|png|gif|svgz?|ico|bmp|tiff?|wbmp|ico|jng|bmp|html|pdf|otf|woff2|woff|eot|ttf|jar|swf|ogx|avi|wmv|asf|asx|mng|flv|webm|mov|ogv|mpe|mpe?g|mp4|3gpp|weba|ra|m4a|mp3|mp2|mpe?ga|midi?)$':
                        allow: true
            '/media/image':
                expires: 2w
                passthru: true
                allow: false
                rules:
                    # Only allow static files from the assets directories.
                    '\.(jpe?g|png|gif|svgz?)$':
                        allow: true
            '/media/cache/resolve':
                passthru: "/index.php"
                expires: -1
                allow: true
                scripts: true
            '/media/cache':
                expires: 2w
                passthru: true
                allow: false
                rules:
                    # Only allow static files from the assets directories.
                    '\.(jpe?g|png|gif|svgz?)$':
                        allow: true

    disk: 4096

    mounts:
        "/var/cache": "shared:files/cache"
        "/var/log": "shared:files/logs"
        "/public/uploads": "shared:files/uploads"
        "/public/media": "shared:files/media"

    hooks:
        build: |
            rm public/index.php
            rm -rf var/cache/*
            php bin/console --env=prod --no-debug --ansi cache:clear --no-warmup
            php bin/console --env=prod --no-debug --ansi cache:warmup
            php bin/console --env=prod --no-debug --ansi assets:install
            # Next command is only needed if you are using themes
            php bin/console --env=prod --no-debug --ansi sylius:theme:assets:install  
            yarn install
            GULP_ENV=prod yarn build
        deploy: |
            rm -rf var/cache/*
            php bin/console --env=prod doctrine:migrations:migrate --no-interaction

The above configuration includes tuned cache expiration headers for static files. The cache lifetimes can be adjusted for your site if desired.

* Add ``.platform/routes.yaml`` file:

.. code-block:: yaml

    # .platform/routes.yaml
    "http://{default}/":
        type: upstream
        upstream: "app:http"

    "http://www.{default}/":
        type: redirect
        to: "http://{default}/"

* Add ``.platform/services.yaml`` file:

This file will load ``mysql`` and ``redis`` on your Platform.sh server.

.. code-block:: yaml

    # .platform/services.yaml
    mysql:
        type: mysql
        disk: 1024

    redis:
        type: redis:3.0

* Configure the access to the database:

In the ``app/config/parameters_platform.php`` file, put the following code:

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

    if (getenv('PLATFORM_PROJECT_ENTROPY')) {
        $container->setParameter('secret', getenv('PLATFORM_PROJECT_ENTROPY'));
    }

Remember to have it imported in the config:

.. code-block:: yaml

    # app/config/config.yml
    imports:
        # - { resource: parameters.yml } <- Has to be placed before our new file
        - { resource: parameters_platform.php }

.. warning::

    It is important to place newly created file after importing regular parameters.yml file. Otherwise your database connection will not work.
    Also this will be the file where you should set your required parameters. Its value will be fetched from environmental variables.

The application secret is used in several places in Sylius and Symfony. Platform.sh allows you to deploy an environment for each branch you have, and therefore it makes sense to have a secret automatically generated by the Platform.sh system. The last 3 lines in the sample above will use the Platform.sh-provided random value as the application secret.

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

The output of this command shows you on which URL your online store can be accessed.

6. Connect to the project via SSH and install Sylius
----------------------------------------------------

The SSH command can be found in your project data on Platform.sh. Alternatively use the `Platform CLI tool <https://docs.platform.sh/gettingstarted/cli.html>`_.

When you get connected please run:

.. code-block:: bash

    $ php bin/console sylius:install --env prod

.. warning::

    By default platform.sh creates only one instance of a database with the `main` name.
    Platform.sh works with the concept of an environment per branch if activated. The idea is to mimic production settings per each branch.

7. Dive deeper
--------------

Learn some more specific topics related to Sylius & Symfony on our :doc:`Advanced Platform.sh Cookbook </cookbook/deployment/platform-sh-advanced>`

Learn more
----------

* Platform.sh documentation: `Configuring Symfony projects for Platform.sh <https://docs.platform.sh/frameworks/symfony.html>`_
* Symfony documentation: `Deploying Symfony to Platform.sh <http://symfony.com/doc/current/deployment/platformsh.html>`_
* :doc:`Installation Guide </book/installation/installation>`
