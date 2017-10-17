.. index::
   single: Installation via Vagrant

Sylius installation via Vagrant
===============================

.. warning::

    This article assumes you're familiar with `Composer`_, a dependency manager
    for PHP. It also assumes you have `Composer installed globally`_.
    Basic knowledge about `Vagrant <https://www.vagrantup.com/about.html>`_ is also required,
    and of course `installed Vagrant <https://www.vagrantup.com/docs/installation/>`_.

What's Vagrant?
---------------

Vagrant is a tool for building complete development environments, that in case of Sylius
will help you to quickly have full application running on your machine.

.. tip::

    Learn more about `Vagrant <https://www.vagrantup.com/about.html>`_.
    Vagrant `installation <https://www.vagrantup.com/docs/installation/>`_ info.

How to install Sylius using Vagrant?
------------------------------------

2. Clone the `Sylius/Vagrant <https://github.com/Sylius/Vagrant>`_ repository into the ``/sylius/`` directory:

.. code-block:: bash

    $ git clone git@github.com:Sylius/Vagrant.git sylius

3. Move to the ``/sylius/`` directory and build Vagrant:

.. code-block:: bash

    $ cd sylius
    $ vagrant up

4. Add an entry for sylius.dev to the ``etc/hosts`` file:

.. code-block:: bash

    # etc/hosts
    10.0.0.200      sylius.dev www.sylius.dev

From now on you will be able to access running Sylius application at ``http://sylius.dev/app_dev.php``.

.. _Composer: http://packagist.org
.. _`Composer installed globally`: http://getcomposer.org/doc/00-intro.md#globally
