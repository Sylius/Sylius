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

    Make sure you have `Docker <https://docs.docker.com/get-docker/>`_ and `make <https://www.gnu.org/software/make/manual/make.html/>`_ installed on your local machine.

Project Setup
-------------

Clone Sylius-Standard repository or if you are using GitHub you can use the *Use this template* button that will create new repository
with Sylius-Standard content.

.. code-block:: bash

    git clone git@github.com:Sylius/Sylius-Standard.git your_project_name

Development
-----------

`Sylius Standard <https://github.com/Sylius/Sylius-Standard>`_ comes with the `docker compose <https://docs.docker.com/compose/>`_ configuration.
You can start the development environment via the ``make init`` command in your favorite terminal. Please note that the speed of building images
and initializing containers depends on your local machine and internet connection - it may take some time.
Then enter ``localhost`` in your browser or execute ``open http://localhost/`` in your terminal.

.. code-block:: bash

    make init
    open http://localhost/
