How to deploy Sylius to Platform.sh?
====================================

.. tip::

    Start with reading `Platform.sh documentation <https://docs.platform.sh/frameworks/symfony.html>`_.
    Also Symfony provides `a guide on deploying projects to Platform.sh <https://symfony.com/doc/current/deployment/platformsh.html>`_.

The process of deploying Sylius to Platform.sh is based on the guidelines prepared for Symfony projects in general.
In this guide you will find sufficient instructions to have your application up and running on Platform.sh.

1. Prepare a Platform.sh project
--------------------------------

* Create an account on `Platform.sh <https://platform.sh/>`_.

* Create a new project, name it (**MyFirstShop** for example) and select the **Blank project** template.

.. hint::

    **Platform.sh** offers a trial month, which you can use for testing your store deployment. If you would be asked to
    provide your credit card data nevertheless, use `this link <https://accounts.platform.sh/platform/trial/general/setup>`_
    to create your new project.

.. image:: /_images/getting-started-with-sylius/platform-sh-project.png
    :scale: 55%
    :align: center

|

* Install the Symfony-Platform.sh bridge in your application with ``composer require platformsh/symfonyflex-bridge``.

2. Make the application ready to deploy
---------------------------------------

* Create the ``.platform/routes.yaml`` file, which describes how an incoming URL is going to be processed by the server.

.. code-block:: yaml

    "https://{default}/":
        type: upstream
        upstream: "app:http"

    "https://www.{default}/":
        type: redirect
        to: "https://{default}/"

* Create the ``.platform/services.yaml`` file.

.. code-block:: yaml

    db:
        type: mysql:10.2
        disk: 2048

* Create the ``.platform.app.yaml`` file, which is the main server application configuration file (and the longest one 😉).

.. code-block:: yaml

    name: app
    type: php:7.3
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
            yarn build:prod
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
        # NOTE: this will install mariadb because platform.sh uses it instead of mysql.
        database: "db:mysql"

    dependencies:
        nodejs:
            yarn: "*"
        php:
            composer/composer: '^2'

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
    runtime:
        extensions:
            - sodium

.. warning::

    It is important to place the newly created file after importing regular parameters.yml file. Otherwise your database
    connection will not work. Also this will be the file where you should set your required parameters. Its value will
    be fetched from environmental variables.

The application secret is used in several places in Sylius and Symfony. Platform.sh allows you to deploy an environment
for each branch you have, and therefore it makes sense to have a secret automatically generated by the Platform.sh system.
The last 3 lines in the sample above will use the Platform.sh-provided random value as the application secret.

3. Add Platform.sh as a remote to your repository
-------------------------------------------------

Use the below command to add your Platform.sh project as the ``platform`` remote:

.. code-block:: bash

    git remote add platform [PROJECT-ID]@git.[CLUSTER].platform.sh:[PROJECT-ID].git

The ``PROJECT-ID`` is the unique identifier of your project,
and ``CLUSTER`` can be ``eu`` or ``us`` - depending on where are you deploying your project.

4. Commit the configuration
---------------------------

.. code-block:: bash

    git add . && git commit -m "Platform.sh configuration"

5. Push your project to the Platform.sh remote repository
---------------------------------------------------------

.. code-block:: bash

    git push platform master

The output of this command shows you on which URL your online store can be accessed.

6. Connect to the project via SSH and install Sylius
----------------------------------------------------

The SSH command can be found in your project data on Platform.sh. Alternatively use the
`Platform CLI tool <https://docs.platform.sh/gettingstarted/cli.html>`_.

When you get connected please run:

.. code-block:: bash

    php bin/console sylius:install --env prod

.. warning::

    By default platform.sh creates only one instance of the database with the ``main`` name.
    Platform.sh works with the concept of an environment per branch if activated. The idea is to mimic production settings per each branch.

.. rst-class:: plus-doc

How to deploy Sylius Plus to Platform.sh?
-----------------------------------------

`Sylius Plus <https://sylius.com/plus/>`_ is installed to Sylius like a plugin, but it needs some changes to the Platform.sh configuration presented above to deploy it properly.
First of all, make sure you have your project configured following the `Sylius Plus installation guide </book/installation/sylius_plus_installation>`_.
After that, you should modify your ``.platform.app.yaml``. Configuration from step 2 should be extended by the following lines.

.. code-block:: yaml

    # ...

    hooks:
        build: |
            set -e
            yarn install --ignore-engines # without this flag you will get error related with node version conflict
            yarn build:prod
            wkhtmltopdf -V # Sylius Plus is installed with InvoicingPlugin, so we need wkhtmltopdf to generate PDF

        deploy: |
            set -e
            rm -rf var/cache/*
            mkdir -p public/media/image
            bin/console sylius:install -n
            bin/console sylius:fixtures:load plus -n # Updating fixtures with new Sylius Plus features
            bin/console assets:install --symlink --relative public
            bin/console cache:clear

    dependencies:
        nodejs:
            yarn: "*"
        ruby:
            "wkhtmltopdf-binary": "0.12.5.1" # adding wkhtmltopdf as a one of dependencies

    # ...

In order to use the wkhtmltopdf (needed for Invoicing and Refunds) on server properly, you also need to add it to the ``config\packages'knp_snappy.yaml``:

.. code-block:: yaml

    knp_snappy:
    pdf:
        enabled:    true
        binary:     wkhtmltopdf # for local purpose was '%env(WKHTMLTOPDF_PATH)%'
        options:    []
    image:
        enabled:    true
        binary:     wkhtmltoimage # for local purpose was '%env(WKHTMLTOIMAGE_PATH)%'
        options:    []

Sylius Plus is on Private Packagist, so when you want to download it on server, you need add `authentication token` before deployment.
You can do it by UI on your project page on platform.sh or if you have platform.sh CLI you can add `authentication_token`:

.. code-block:: bash

    platform variable:create --level project --name env:COMPOSER_AUTH \
    --json true --visible-runtime false --sensitive true --visible-build true
    --value '{"http-basic": {"sylius.repo.packagist.com": {"username": "token", "password": "YOUR_AUTHENTICATION_TOKEN"}}}'

All the other steps from the Sylius deployment on Platform.sh remain unchanged.

7. Dive deeper
--------------

Add default Sylius cronjobs:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

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
~~~~~~~~~~~~~~~~

* Platform.sh can serve gzipped versions of your static assets. Make sure to save your assets in the same folder, but with a .gz suffix.

* Platform.sh comes with a `New Relic integration <https://docs.platform.sh/administration/integrations/new-relic.html>`_.

* Platform.sh comes with a `Blackfire.io integration <https://docs.platform.sh/administration/integrations/blackfire.html>`_

Learn more
----------

* Platform.sh documentation: `Configuring Symfony projects for Platform.sh <https://docs.platform.sh/frameworks/symfony.html>`_
* Symfony documentation: `Deploying Symfony to Platform.sh <https://symfony.com/doc/current/deployment/platformsh.html>`_
* :doc:`Installation Guide </book/installation/installation>`
