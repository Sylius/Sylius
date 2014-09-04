Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_settings:
        # The driver used for persistence layer.
        driver: ~
        classes:
            parameter:
                model: Sylius\Bundle\SettingsBundle\Model\Parameter
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\SettingsBundle\Form\Type\ParameterType

Tests
-----

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.