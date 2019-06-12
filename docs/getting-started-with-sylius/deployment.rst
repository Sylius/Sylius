Deployment
==========

Development usually takes most of the time in project implementation, but we should not forget about what's at the end of this process -
application deployment into the server. We believe, that it should be as easy and understandable as possible.
There are many servers which you can choose for your store deployment, but in this chapter, we will use `Platform.sh <https://platform.sh/>`_.

.. attention::

    To deploy your application to the proposed server, you need to have a git repository configured.

Process
-------

1. Create an account on `Platform.sh <https://platform.sh/>`_

2. Create a new project, naming it **MyFirstShop** and selecting **Blank project** template

.. hint::

    **Platform.sh** offers a trial month, which you can use for testing your store deployment. If you would be asked to provide
    your credit card data nevertheless, use `this link <https://accounts.platform.sh/platform/trial/general/setup>`_ for a new
    project creation.

.. image:: /_images/getting-started-with-sylius/platform-sh-project.png
    :scale: 55%
    :align: center

|

3. Install a Symfony-Platform.sh bridge in your application with ``composer require platformsh/symfonyflex-bridge``

4. Create a file ``.platform/routes.yaml``, which describes how an incoming URL is going to be processed by the server

.. code-block:: yaml

    "https://{default}/":
        type: upstream
        upstream: "app:http"

    "https://www.{default}/":
        type: redirect
        to: "https://{default}/"

5. Create a file ``.platform/services.yaml``

.. code-block:: yaml

    mysqldb:
        type: mysql:10.2
        disk: 2048

6. Create a file ``.platform.app.yaml``, which is the main server application configuration file (and the longest one :))

.. code-block:: yaml

    name: app
    type: php:7.2
    build:
        flavor: composer

    variables:
        env:
            # Tell Symfony to always install in production-mode.
            APP_ENV: 'prod'
            APP_DEBUG: 0

    # The hooks that will be performed when the package is deployed.
    hooks:
        build: |
            set -e
            yarn install
            GULP_ENV=prod yarn build
        deploy: |
            set -e
            rm -rf var/cache/*
            mkdir -p public/media/image
            bin/console sylius:install -n
            bin/console sylius:fixtures:load -n
            bin/console assets:install --symlink --relative public
            bin/console cache:clear

    # The relationships of the application with services or other applications.
    # The left-hand side is the name of the relationship as it will be exposed
    # to the application in the PLATFORM_RELATIONSHIPS variable. The right-hand
    # side is in the form `<service name>:<endpoint name>`.
    relationships:
        database: "mysqldb:mysql"

    dependencies:
        nodejs:
            yarn: "*"
            gulp-cli: "*"

    # The size of the persistent disk of the application (in MB).
    disk: 2048

    # The mounts that will be performed when the package is deployed.
    mounts:
        "/var/cache": "shared:files/cache"
        "/var/log": "shared:files/log"
        "/var/sessions": "shared:files/sessions"
        "/public/uploads": "shared:files/uploads"
        "/public/media": "shared:files/media"

    # The configuration of app when it is exposed to the web.
    web:
        locations:
            "/":
                # The public directory of the app, relative to its root.
                root: "public"
                # The front-controller script to send non-static requests to.
                passthru: "/index.php"

7. Commit the configuration with ``git add . && git commit -m "Platform.sh configuration"``

8. Add platformsh server as a remote repository

.. hint::

    Its URL is displayed in the project's description and follows the pattern ``project-hash@git.eu-2.platform.sh:project-hash.git``

9. Push changes into the remote repository (the Platform.sh server) with ``git push -u platform master``

As a result, you should see tons of logs and active status of the project when they pass:

.. image:: /_images/getting-started-with-sylius/platform-sh-project-running.png
    :scale: 55%
    :align: center

|

You have also a URL provided, which you can visit to see if your shop working well. Of course, it does not have your configuration
done locally, as well as your product... but you have an application deployed! Congratulations! You've just finished the first
stage of the first project with Sylius.

.. important::

    Of course, using the **Platform.sh** is only an example. You can use any server you want or you're familiar with.
