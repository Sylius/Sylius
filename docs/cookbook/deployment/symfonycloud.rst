How to deploy Sylius to SymfonyCloud?
=====================================

.. tip::

    Start with reading `SymfonyCloud documentation <https://symfony.com/cloud/doc>`_.

The process of deploying Sylius to SymfonyCloud is eased by the tools provided by SymfonyCloud for Symfony projects.
In this guide you will find sufficient instructions to have your application up and running on SymfonyCloud.

1. Create a new Sylius application
----------------------------------

This whole section can be skipped if you already have a working project.

To begin creating your new project, run this command:

.. code-block:: bash

    $ composer create-project sylius/sylius-standard acme

This will create a new Symfony project in the ``acme`` directory. Next, move to the project directory, initialize the
git repository and create your first commit:

.. code-block:: bash

    $ cd acme
    $ git init
    $ git add .
    $ git commit -m "Initial commit"

2. Install the SymfonyCloud client
----------------------------------

* Download the CLI tool from `symfony.com <https://symfony.com/download/>`_

* Login using ``symfony login``

3. Make the store ready to deploy
---------------------------------

* Initialize a default configuration for SymfonyCloud:

.. code-block:: bash

    $ symfony project:init

* Customize the ``.symfony/services.yaml`` file:

.. code-block:: yaml

    db:
        type: mysql:10.2
        disk: 1024

* Customize the ``.symfony.cloud.yaml`` file:

.. code-block:: yaml

    name: app
    type: php:7.3
    build:
        flavor: none

    runtime:
        extensions: []


    relationships:
        database: "db:mysql"

    web:
        locations:
            "/":
                root: "public"
                expires: -1
                passthru: "/index.php"
            "/assets/shop":
                expires: 2w
                passthru: false
                allow: false
                rules:
                    # Only allow static files from the assets directories.
                    '\.(css|js|jpe?g|png|gif|svgz?|ico|bmp|tiff?|wbmp|ico|jng|bmp|html|pdf|otf|woff2|woff|eot|ttf|jar|swf|ogx|avi|wmv|asf|asx|mng|flv|webm|mov|ogv|mpe|mpe?g|mp4|3gpp|weba|ra|m4a|mp3|mp2|mpe?ga|midi?)$':
                        allow: true
            "/media/image":
                expires: 2w
                passthru: false
                allow: false
                rules:
                    # Only allow static files from the assets directories.
                    '\.(jpe?g|png|gif|svgz?)$':
                        allow: true
            "/media/cache":
                expires: 2w
                passthru: false
                allow: false
                rules:
                    # Only allow static files from the assets directories.
                    '\.(jpe?g|png|gif|svgz?)$':
                        allow: true
            "/media/cache/resolve":
                passthru: "/index.php"
                scripts: false
                expires: -1
                allow: true

    disk: 1024

    mounts:
        "/var": { source: local, source_path: var }
        "/public/uploads": { source: local, source_path: uploads }
        "/public/media": { source: local, source_path: media }

    hooks:
        build: |
            set -x -e

            curl -s https://get.symfony.com/cloud/configurator | (>&2 bash)
            (>&2 symfony-build)
            (>&2 symfony console sylius:install:check-requirements)
            (>&2
                # Setup everything to use the Node installation
                unset NPM_CONFIG_PREFIX
                export NVM_DIR=${SYMFONY_APP_DIR}/.nvm
                set +x && . "${NVM_DIR}/nvm.sh" use --lts && set -x
                # Starting from here, everything is setup to use the same Node
                yarn build:prod
            )

        deploy: |
            set -x -e

            mkdir -p public/media/image var/log
            (>&2 symfony-deploy)

4. Commit the configuration
---------------------------

.. code-block:: bash

    $ git add php.ini .symfony .symfony.cloud.yaml && git commit -m "SymfonyCloud configuration"

5. Deploy the store to SymfonyCloud
-----------------------------------

The first deploy will take care of creating a new SymfonyCloud project for you.

.. code-block:: bash

    $ symfony deploy

The output of this command shows you on which URL your online store can be accessed. Alternatively, you can also use
``symfony open:remote`` to open your store in your browser.

.. hint::

    **SymfonyCloud** offers a 7 days trial, which you can use for testing your store deployment.

6. Finish Sylius installation
-----------------------------

Finish the Sylius installation by running:

.. code-block:: bash

    $ symfony ssh bin/console sylius:install

.. tip::

    You can load the predefined set of Sylius fixtures to try your new store:

    .. code-block:: bash

    $ symfony ssh bin/console sylius:fixtures:load

7. Dive deeper
--------------

Add default Sylius cronjobs:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add the example below to your ``.symfony.cloud.yaml`` file. This runs these cronjobs every 6 hours.

.. code-block:: yaml

    crons:
        cleanup_cart:
            spec: '0 */6 * * *'
            cmd: croncape /usr/bin/flock -n /tmp/lock.app.cleanup_cart symfony console sylius:remove-expired-carts --verbose
        cleanup_order:
            spec: '0 */6 * * *'
            cmd: croncape /usr/bin/flock -n /tmp/lock.app.cleanup_order symfony console sylius:cancel-unpaid-orders --verbose

Additional tips:
~~~~~~~~~~~~~~~~

* SymfonyCloud can serve gzipped versions of your static assets. Make sure to save your assets in the same folder, but with a .gz suffix.

Learn more
----------

* SymfonyCloud documentation: `Getting started <https://symfony.com/doc/master/cloud/getting-started.html>`_
* SymfonyCloud documentation: `Moving to production <https://symfony.com/doc/master/cloud/cookbooks/go_live.html>`_
* :doc:`Installation Guide </book/installation/installation>`
