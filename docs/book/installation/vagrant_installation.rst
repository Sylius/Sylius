.. index::
   single: Installation via Vagrant

Sylius installation via Vagrant
===============================

.. warning::

    This article assumes you're familiar with `Composer`_, a dependency manager
    for PHP. It also assumes you have `Composer installed globally`_.
    Basic knowledgle about `Vagrant <https://www.vagrantup.com/about.html>`_ is also required,
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

1. Create a new `Sylius-Standard <https://github.com/Sylius/Sylius-Standard>`_ project using composer in **no interaction mode** (``-n`` flag):

.. code-block:: bash

    $ composer create-project -s beta -n sylius/sylius-standard acme

.. note::

    The no interaction mode results in having null database password, what lets Vagrant put its own password(``vagrant``) into that parameter.
    It is not mandatory. You can change the database password in the ``parameters.yml`` to ``vagrant`` also later on.

2. Inside your new project directory clone the `Sylius/Vagrant <https://github.com/Sylius/Vagrant>`_ repository into the ``/vagrant/`` directory:

.. code-block:: bash

    $ git clone git@github.com:Sylius/Vagrant.git vagrant

3. Move to the ``/vagrant/`` directory and build Vagrant:

.. code-block:: bash

    $ cd vagrant
    $ vagrant up

4. Add an entry for sylius.dev to the ``etc/hosts`` file:

.. code-block:: bash

    # etc/hosts
    10.0.0.200      sylius.dev www.sylius.dev

From now on you will be able to access running Sylius application at `<http://sylius.dev/app_dev.php>`_.

.. _Composer: http://packagist.org
.. _`Composer installed globally`: http://getcomposer.org/doc/00-intro.md#globally