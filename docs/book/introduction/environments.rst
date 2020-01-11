.. index::
   single: Environments

Understanding Environments
==========================

Every Sylius application is the combination of code and a set of configuration that dictates how that code should function. The configuration may define the database being used, whether or not something should be cached, or how verbose logging should be. In Symfony, the idea of "environments" is the idea that the same codebase can be run using multiple different configurations. For example, the dev environment should use configuration that makes development easy and friendly, while the prod environment should use a set of configuration optimized for speed.

Development
-----------

Development environment or ``dev``, as the name suggests, should be used for development purposes. It is much slower than production, because it uses much less aggressive caching and does a lot of processing on every request.
However, it allows you to add new features or fix bugs quickly, without worrying about clearing the cache after every change.

Sylius console runs in ``dev`` environment by default. You can access the website in dev mode via the ``/index.php`` file in the ``public/`` directory. (under your website root)

Production
----------

Production environment or ``prod`` is your live website environment. It uses proper caching and is much faster than other environments. It uses live APIs and sends out all e-mails.

To run Sylius console in ``prod`` environment, add the following parameters to every command call:

.. code-block:: bash

   $ bin/console --env=prod --no-debug cache:clear

You can access the website in production mode via the ``/index.php`` file in your website root (``public/``) or just ``/`` path. (on Apache)

Staging
-------

Staging environment or ``staging`` is the last line before the shop will go to the production. Here you should test all new features to ensure that everything works as expected.
It's almost an exact copy of production environment but with different database and turned off e-mails.

To run Sylius console in ``staging`` environment, add the following parameters to every command call:

.. code-block:: bash

   $ bin/console --env=staging --no-debug cache:clear

You can access the website in staging mode via the ``/index.php`` file in your website root (``public/``) or just ``/`` path. (on Apache)

Test
----

Test environment or ``test`` is used for automated testing. Most of the time you will not access it directly.

To run Sylius console in ``test`` environment, add the following parameters to every command call:

.. code-block:: bash

   $ bin/console --env=test cache:clear

Final Thoughts
--------------

You can read more about Symfony environments in `this cookbook article <http://symfony.com/doc/current/cookbook/configuration/environments.html>`_.
