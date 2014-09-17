.. index::
   single: Installation

Installation
============

There are several ways to install Sylius.

Either you're installing it to contribute, in which case you may prefer Sylius/Sylius,
or you're bootstrapping a new e-commerce project, and you'd prefer using Sylius/Sylius-Standard.

.. warning::

    Why two versions ? The reason is simple: Sylius/Sylius is the central repository, where all code and commits are contributed to.
    All the other repositories are splitted from this main repository.

    Sylius-Standard is just a distribution including these splitted repositories.


Using Composer
--------------

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Otherwise, check `how to install Composer <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius # or sylius/sylius-standard
    $ cd sylius # or sylius-standard
    $ php app/console sylius:install


Using Git
---------

.. code-block:: bash

    $ git clone git@github.com:Sylius/Sylius.git # or Sylius-Standard
    $ cd Sylius # or Sylius-Standard
    $ composer install
    $ php app/console sylius:install
