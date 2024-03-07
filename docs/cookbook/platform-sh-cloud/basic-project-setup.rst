Basic project setup
===================

Sylius is a typical Symfony application, so all the steps below can be used for a clean Symfony application as well.

Prerequisites
-------------

To work with Sylius Cloud on Platform.sh you'll need the `platform` command on your computer. To install it, please run the script:

.. code-block:: bash
    curl -fsSL https://raw.githubusercontent.com/platformsh/cli/main/installer.sh | bash

There are several methods of installing the `platform` application in your machine. To see them, please visit the `Platform.sh documentation <https://docs.platform.sh/administration/cli.html#1-install>`_

Pushing Sylius to Platform.sh
-----------------------------

Let's assume you're extending the Sylius-Standard in version including the Platform.sh configuration.

First step you need to do is to create a Platform.sh project. To do it, please log in to your Platform.sh account and click "Create a new project" button from the Platform.sh dashboard.
Note down the project ID and region as you'll need them later. For the convenience the environment name should be same as your main application branch. Let's say it's `main`.

The next thing you need to do is to connect your git remote with your Platform.sh project:

.. code-block:: bash
    platform project:set-remote <PROJECT_ID>
    git remote add platform <PROJECT_ID>>@git.<REGION>.platform.sh:<PROJECT_ID>.git

After this step all you need to do is to commit and push your code into the `platform` remote:

.. code-block:: bash
    git add .
    git commit -m "Initial Sylius Cloud on Platform.sh configuration"
    git push platform main

This will trigger a deployment to Platform.sh. The Platform.sh CLI will automatically detect your Sylius application and set up the environment accordingly.

Once the deployment is complete, your Symfony application will be accessible via a unique URL provided by Platform.sh with format described below:

.. code-block:: bash
    <ENVIRONMENT_NAME>-<PROJECT_ID>.<REGION>.platformsh.site

That's it! You've successfully pushed your Sylius application to Platform.sh. You can now continue to develop, test, and manage your application directly from the Platform.sh dashboard or CLI.

Database and other services configuration on Platform.sh
--------------------------------------------------------

In Platform.sh, there is a special `PLATFORM_RELATIONSHIPS` environment variable that contains connection information for services defined in the `.platform.app.yaml` file.
This variable is commonly used in Symfony applications like Sylius, particularly with Doctrine, to automatically configure database connections based on the services provided by Platform.sh.

In your `.platform.app.yaml` and `.platform/services.yaml` files you define services such as databases, caches, or search engines. These services are provisioned by Platform.sh and are accessible to your application as environment variables.
The `PLATFORM_RELATIONSHIPS` environment variable contains a JSON-encoded string representing the relationships between your application and the services provisioned by Platform.sh. Each service is represented as an array containing connection details such as host, port, username, password, and database name.
In Sylius you can configure database connections using the `PLATFORM_RELATIONSHIPS` variable. Instead of hardcoding connection parameters in the Symfony configuration files, you can dynamically retrieve them from the `PLATFORM_RELATIONSHIPS` variable at runtime.

Example Doctrine configuration:

.. code-block:: yaml
doctrine:
    dbal:
        driver: 'pdo_mysql'
        host: '%env(resolve:PLATFORM_RELATIONSHIPS:mysql:host)%'
        port: '%env(resolve:PLATFORM_RELATIONSHIPS:mysql:port)%'
        dbname: '%env(resolve:PLATFORM_RELATIONSHIPS:mysql:database)%'
        user: '%env(resolve:PLATFORM_RELATIONSHIPS:mysql:username)%'
        password: '%env(resolve:PLATFORM_RELATIONSHIPS:mysql:password)%'


To summarize, by using the `PLATFORM_RELATIONSHIPS` environment variable with Doctrine in Symfony applications deployed on Platform.sh, you can ensure that database connections are automatically configured and managed based on the services provisioned by the platform, leading to more flexible and portable application deployments.

Setting up cron configuration
-----------------------------

Setting up cron jobs (scheduled tasks) in Sylius Cloud on Platform.sh involves defining them in the `.platform.app.yaml` file of your Sylius project.
Here's how you can set up cron jobs:

.. code-block:: yaml
hooks:
    cron:
        # Run the `php bin/console my:command` command every day at 2:00 AM.
        - name: daily-cron
            schedule: '0 2 * * *'
            command: 'php bin/console my:command'

To fully integrate your Sylius application with Platform.sh infrastructure, you need to configure at least three cron commands:

.. code-block:: bash
    bin/console sylius:cancel-unpaid-orders
    bin/console sylius:promotion:generate-coupons
    bin/console sylius:remove-expired-carts

So the hooks section may look like below:

.. code-block:: yaml
hooks:
    cron:
        - name: cancel-unpaid-orders
            schedule: '0 2 * * *'
            command: 'php bin/console sylius:cancel-unpaid-orders'
        - name: generate-promotion-coupons
            schedule: '0 2 * * *'
            command: 'php bin/console sylius:promotion:generate-coupons'
        - name: remove-expired-carts
            schedule: '0 2 * * *'
            command: 'php bin/console sylius:remove-expired-carts'


The frequency of running these commands depends on your business requirements.

Verify the cron jobs
--------------------

Once your changes are deployed, Platform.sh will automatically set up the cron jobs according to the schedule you defined.
You can verify that the cron jobs are set up correctly by accessing the environment's SSH console and checking the crontab:

.. code-block:: bash
    platform ssh
    crontab -l

Configuring Symfony Messenger workers
-------------------------------------

Running workers on Sylius Cloud on Platform.sh involves setting up background processes to handle tasks asynchronously, such as queue processing,
background jobs, or event-driven tasks. Workers are typically configured using the `.platform.app.yaml` file.

To fully integrate with Sylius application with Platform.sh, you'll need to configure the worker for catalog promotions:

.. code-block:: bash
    bin/console messenger:consume main main_failed catalog_promotion_removal catalog_promotion_removal_failed

The full documentation regarding Platform.sh workers you can find in the `Platform.sh documentation <https://docs.platform.sh/create-apps/workers.html>`_

The workers section for Sylius project may look like the one below:

.. code-block:: yaml
workers:
    catalog_promotions:
        commands:
            start: |
                bin/console messenger:consume main main_failed catalog_promotion_removal catalog_promotion_removal_failed

The important information from `Platform.sh documentation<https://docs.platform.sh/create-apps/app-reference.html#workers>`_ is that crashed workers are automatically restarted.
