Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_resource:
        resources:
            app.user:
                driver: doctrine/orm # Also supported - doctrine/mongodb-odm.
                templates: AppBundle:User
                classes:
                    model: App\Entity\User
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repositoryl: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusResourceBundle/issues>`_.
If you have found bug, please create an issue.
