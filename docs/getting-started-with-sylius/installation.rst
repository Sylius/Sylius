Installation
============

So you want to try creating an online shop with Sylius? Great! The first step is the most important one, so let's start
with the Sylius project installation via Composer. We will be using the latest stable version of Sylius.

Before installation
-------------------

There are some prerequisites that your local environment should fulfill before installation (not many of them).

.. image:: ../_images/installation_checklist.png
    :align: center
    :scale: 60%

|

For more details, take a look at :doc:`this chapter</book/installation/requirements>` in **The Book**.

Project setup
-------------

The easiest way to install Sylius on your local machine is to use the following command:

.. code-block:: bash

    composer create-project sylius/sylius-standard MyFirstShop

It will create a ``MyFirstShop`` directory with a brand new Sylius application inside.

.. note::

    Are you familiar with Docker? Check out the :doc:`Sylius Installation Guide with Docker </book/installation/installation_with_docker>`

.. warning::

    Beware! The next step includes the database setup. It will set your database credentials
    (username, password, and database name) in the file with environment variables (``.env`` is the most basic one).

.. warning::
    Specific Sylius versions may support various Symfony versions. To make sure the correct Symfony version will be
    installed (Symfony 6.0 for example) use:

    .. code-block:: bash

        composer config extra.symfony.require "^6.0"
        composer update

    Otherwise, you may face the problem of having Symfony components of the wrong version installed.

To launch a Sylius application initial data has to be set up: an administrator account and base locale.
Run the Sylius installation command to do that.

.. code-block:: bash

    cd MyFirstShop
    bin/console sylius:install

This command will do several things for you - the first two steps are checking if your environment fulfills technical requirements,
and setting the project database. You will also be asked if you want to have default fixtures loaded into your database - let's say
"No" to that, we will configure the store manually.
You will be also asked if you want to generate API Tokens.

.. image:: /_images/getting-started-with-sylius/installation1.png

It's essential to put some attention to the 3rd installation step. There you configure your default administrator account, which
will be later used to access Sylius admin panel.

.. image:: /_images/getting-started-with-sylius/installation2.png

To derive joy from Sylius SemanticUI-based views, you should use ``yarn`` to load our assets.

.. code-block:: bash

    yarn install
    yarn build

That's it! You're ready to launch your empty Sylius-based web store.

Launching application
---------------------

For the testing reasons, the fastest way to start the application is using Symfony binary. It can be downloaded from `here <https://symfony.com/download>`_. Let's also start
browsing the application from the Admin panel.

.. code-block:: bash

    symfony serve
    open http://127.0.0.1:8000/admin

Great! You are closer to the final goal. Let's configure your application a little bit, to make it usable by some future customers.

Learn more
##########

* :doc:`Installation chapter in The Book </book/installation/index>`

