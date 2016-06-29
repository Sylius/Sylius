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
                    model: Sylius\SettingsBundle\Model\Parameter
                    interface: Sylius\SettingsBundle\Model\ParameterInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
