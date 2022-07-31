.. index::
   single: Installation

Installation with Docker
========================

Docker
------

Docker is an open-sourced platform for developing, delivering, and running applications. Docker allows you to separate your
application from your infrastructure, simplifies software delivery. Docker allows you to manage infrastructure in the
same way that applications are managed. Implementing the platform methodology, enables fast code delivery,
testing, and implementation. Docker significantly reduces the delay between writing the code and running it in the production environment.

.. note::

    Make sure you have `Docker <https://docs.docker.com/get-docker/>`_ installed on your local machine.

Development
-----------

Sylius Standard comes with the `multi-stage build <https://docs.docker.com/develop/develop-images/multistage-build/>`_.
You can execute it via the ``docker-compose up -d`` command in your favorite terminal. Please note that the speed of building images
and initializing containers depends on your local machine and internet connection - it may take some time. Then enter ``localhost`` in your browser or execute ``open localhost`` in your terminal.

.. code-block:: bash

    docker-compose up -d
    open localhost

.. tip::

    :doc:`Learn how to deploy Sylius-Standard production ready Docker Compose configuration </cookbook/deployment/docker>`
