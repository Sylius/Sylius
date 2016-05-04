Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_settings:
        # The driver used for persistence layer.
        driver: ~
        resources:
            parameter:
                classes:
                    model: Sylius\Bundle\SettingsBundle\Model\Parameter
                    interface: Sylius\Bundle\SettingsBundle\Model\ParameterInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
