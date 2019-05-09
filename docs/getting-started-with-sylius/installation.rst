Installation
============

So you want to create an online shop with Sylius? Great! The first step is the most important one, so let's start with the Sylius project installation.
We will use the latest stable version of Sylius - ``1.4``.

Before installation
-------------------

There are some prerequisites that your local machine should fulfill before installation (not many of them). We recommend using Unix system (like
Linux or MacOS), you also have to have **PHP 7.2** (or higher) installed and a **MySQL** server. **Composer** and **Yarn** installed globally are also assumed.

For more details, take a look at :doc:`this chapter</book/installation/requirements>` in **The Book**.

Project setup
-------------

The easiest way to install Sylius on your local machine is to use the following command:

.. code-block:: bash

    $ composer create-project sylius/sylius-standard MyFirstShop

It will create a ``MyFirstShop`` directory with a brand new Sylius application inside.

.. warning::

    Beware! The next step includes the database setup. It's required to set your database credentials (username, password, and database name)
    in the file with an environment variable (``.env`` is the most basic one).

To launch a Sylius application, we need to configure some basic data, like an administrator account or a base locale.
Using an installation command is the quickest way to do that.

.. code-block:: bash

    $ cd MyFirstShop
    $ bin/console sylius:install

This command will do several things for you - the first two steps are checking does your system fulfills some technical requirement,
and setting the project database. You will also be asked if you want to have default fixtures loaded into your database - let's say
"No" for that, we will configure the store manually.

.. image:: /_images/getting-started-with-sylius/installation1.png

It's essential to put some attention to the 3rd installation step. There you configure your default administrator account, which
can later be used to access Sylius admin panel.

.. image:: /_images/getting-started-with-sylius/installation2.png

To derive joy from Sylius SemanticUI-based views, you should use ``yarn`` to load our assets.

.. code-block:: bash

    $ yarn install
    $ yarn build

That's it! You're ready to launch your Sylius-based web store.

Launching application
---------------------

For the testing reasons, the fastest way to start the application is using Symfony built-in server. Let's also start browsing the application
from the Admin panel.

.. code-block:: bash

    $ bin/console server:start
    $ open http://127.0.0.1:8000/admin

Great! You are one step closer to the final goal. Let's configure your application a little bit, to make it usable by some future customers.

Learn more
##########

* :doc:`Installation chapter in The Book </book/installation/index>`
