The Contribution Guide
======================

.. note::

    This section is based on the great `Symfony documentation <http://symfony.com/doc/current>`_.

Install to Contribute
---------------------

Before you can start contributing to Sylius code or documentation, you should install Sylius locally.

To install Sylius main application from our main repository and contribute, run the following command:

.. code-block:: bash

    $ composer create-project sylius/sylius

This will create a new Sylius project in ``sylius`` directory. When all the dependencies are installed,
you should create `.env` file basing on provides `.env.dist` files. The most important parameter that need to be set,
is `DATABASE_URL`.

.. code-block:: text

    DATABASE_URL=mysql://username:password@host/database_name_%kernel.environment%

After everything is in place, run the following commands:

.. code-block:: bash

    $ cd sylius # Move to the newly created directory
    $ php bin/console sylius:install

The ``sylius:install`` command actually runs several other commands, which will ask you some questions and check if everything is setup to run Sylius properly.

This package contains our main Sylius development repository, with all the components and bundles in the ``src/`` folder.

In order to see a fully functional frontend you will need to install its assets.

**Sylius** already has a ``gulpfile.babel.js``, therefore you just need to get `Gulp`_ using `Node.js`_.

Having Node.js installed go to your project directory and run:

.. code-block:: bash

    $ yarn install

And now you can use gulp for installing views, by just running a simple command:

.. code-block:: bash

    $ yarn build

For the contributing process questions, please refer to the `Contributing Guide <http://docs.sylius.com/en/latest/contributing/index.html>`_ that comes up in the following chapters:

.. toctree::
    :maxdepth: 1

    code/index
    documentation/index
    translations/index

How to contribute using Sylius Vagrant
--------------------------------------

If you would like to contribute to the repo without worrying about environment, you can follow `the "Configure Vagrant to contribute on Sylius Core" section here <https://github.com/Sylius/Vagrant/blob/master/README.md>`_ to setup your vagrant machine.

Contributing Code
-----------------

.. toctree::
    :hidden:

    code/index

.. include:: /contributing/code/map.rst.inc

Contributing Documentation
--------------------------

.. toctree::
    :hidden:

    documentation/index

.. include:: /contributing/documentation/map.rst.inc

Contributing Translations
-------------------------

.. toctree::

    translations/index

Organization
------------

Sylius is developed by a vibrant community of commercial companies and individual developers.
The chapter describes the rules & processes we use to organize our work.

.. toctree::
    :hidden:

    organization/index

.. include:: /contributing/organization/map.rst.inc

.. _Gulp: http://gulpjs.com/
.. _Node.js: https://nodejs.org/en/download/
