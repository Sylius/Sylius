How to deploy Sylius with Docker?
=================================

The simplest way to deploy your Sylius store with Docker is to use the template provided in the Sylius-Standard ``docker-compose.prod.yml`` configuration file.

.. tip::

    When using a Virtual Private Server (VPS) we recommend having at least 2GB of RAM memory.

1. Install Docker on your VPS
-----------------------------

.. code-block:: bash

    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh

2. Execute Docker Compose Configuration
---------------------------------------

.. code-block:: bash

    export MYSQL_PASSWORD=SLyPJLaye7
    docker compose -f docker-compose.prod.yml up -d

.. tip::

    Deploying the database on the same machine as the application is not the best practice. **Use Managed Database solution instead.**

Learn more
----------

* `Check out Docker learning recommendations! <https://docs.docker.com/get-started/resources/#self-paced-online-learning>`_
