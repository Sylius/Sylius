Basic project setup
===================

Sylius is a typical Symfony application, so all the steps below can be used for a clean Symfony application as well.

Prerequisites
-------------

To work with Sylius Cloud you'll need the `platform` (CLI) command on your computer. To install it, please run the script:

.. code-block:: bash

    curl -fsSL https://raw.githubusercontent.com/platformsh/cli/main/installer.sh | bash

There are several methods of installing the `platform` application in your machine. To see them, please visit the `documentation <https://docs.platform.sh/administration/cli.html#1-install>`_

Pushing Sylius to Sylius Cloud
------------------------------

Let's assume you're extending the Sylius-Standard in version including the Sylius Cloud configuration.

First step you need to do is to create a Sylius Cloud project. To do it, please log in to your Sylius Cloud account and click "Create a new project" button from the dashboard.
You can also do it by running the CLI command:

.. code-block:: bash

    platform project:create

When running the command, you'll be asked for a few information like project title, infrastructure region, default Git branch, pricing acceptance, etc.
For the convenience the environment name should be same as your main application branch. Let's say it's `main`.

After the project is created, you'll be able to access the information like:

- \- Project ID
- \- Production environment URL
- \- Git URL

If you create the project using Console, you'll need to switch to it with your CLI:

.. code-block:: bash

    platform get <PROJECT_ID>

After this step all you need to do is to commit and push your code into the `platform` remote:

.. code-block:: bash

    git add .
    git commit -m "Initial Sylius Cloud configuration"
    platform push --environment=<BRANCH_NAME>

This will trigger a build and deployment to Sylius Cloud. The CLI will automatically detect your Sylius application and set up the environment accordingly.

Once the deployment is complete, your Symfony application will be accessible via a unique URL provided by Sylius Cloud with format described below:

.. code-block:: bash

    <ENVIRONMENT_NAME>-<PROJECT_ID>.<REGION>.platformsh.site

That's it! You've successfully pushed your Sylius application to Sylius Cloud. You can now continue to develop, test, and manage your application directly from the dashboard or the CLI.

Database and other services configuration on Sylius Cloud
---------------------------------------------------------

In Sylius Cloud, there is a special `PLATFORM_RELATIONSHIPS` environment variable that contains connection information for services defined in the `.platform.app.yaml` file.
This variable is commonly used in Symfony applications like Sylius, particularly with Doctrine, to automatically configure database connections based on the services provided by Sylius Cloud.

In your `.platform.app.yaml` and `.platform/services.yaml` files you define services such as databases, caches, or search engines. These services are provisioned by Sylius Cloud and are accessible to your application as environment variables.
The `PLATFORM_RELATIONSHIPS` environment variable contains a JSON-encoded string representing the relationships between your application and the services provisioned by Sylius Cloud. Each service is represented as an array containing connection details such as host, port, username, password, and database name.
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


To summarize, by using the `PLATFORM_RELATIONSHIPS` environment variable with Doctrine in Symfony applications deployed on Sylius Cloud, you can ensure that database connections are automatically configured and managed based on the services provisioned by the platform, leading to more flexible and portable application deployments.

Setting up cron configuration
-----------------------------

Setting up cron jobs (scheduled tasks) in Sylius Cloud involves defining them in the `.platform.app.yaml` file of your Sylius project.
Here's how you can set up cron jobs:

.. code-block:: yaml

    crons:
        # Run the `php bin/console my:command` command every day at 2:00 AM.
        daily-cron:
            spec: '0 2 * * *'
            cmd: 'php bin/console my:command'

To fully integrate your Sylius application with Sylius Cloud infrastructure, you need to configure at least three cron commands:

.. code-block:: bash

    bin/console sylius:cancel-unpaid-orders
    bin/console sylius:promotion:generate-coupons
    bin/console sylius:remove-expired-carts

So the crons section may look like below:

.. code-block:: yaml

    crons:
        cancel-unpaid-orders:
            spec: "0 2 * * *"
            cmd: "php bin/console sylius:cancel-unpaid-orders"
        generate-promotion-coupons:
            spec: "0 2 * * *"
            cmd: "php bin/console sylius:promotion:generate-coupons"
        remove-expired-carts:
            spec: "0 2 * * *"
            cmd: "php bin/console sylius:cancel-unpaid-orders"


The frequency of running these commands depends on your business requirements.

Verify the cron jobs
--------------------

Once your changes are deployed, Sylius Cloud will automatically set up the cron jobs according to the schedule you defined.
You can verify that the cron jobs are set up correctly by accessing the environment's SSH console and checking the crontab:

.. code-block:: bash

    platform ssh
    crontab -l

Configuring Symfony Messenger workers
-------------------------------------

Running workers on Sylius Cloud involves setting up background processes to handle tasks asynchronously, such as queue processing,
background jobs, or event-driven tasks. Workers are typically configured using the `.platform.app.yaml` file.

To fully integrate with Sylius application with Sylius Cloud, you'll need to configure the worker for catalog promotions:

.. code-block:: bash

    bin/console messenger:consume main main_failed catalog_promotion_removal catalog_promotion_removal_failed

The full documentation regarding workers you can find in `the documentation <https://docs.platform.sh/create-apps/workers.html>`_

The workers section for Sylius project may look like the one below:

.. code-block:: yaml

    workers:
        catalog_promotions:
            commands:
                start: |
                    bin/console messenger:consume main main_failed catalog_promotion_removal catalog_promotion_removal_failed

The important information from `docs <https://docs.platform.sh/create-apps/app-reference.html#workers>`_ is that crashed workers are automatically restarted.
