Configuration reference
=======================

.. code-block:: yaml

    sylius_settings:
        driver: ~ # The driver used for persistence layer.
        classes:
            parameter:
                model: Sylius\Bundle\SettingsBundle\Model\Parameter
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\SettingsBundle\Form\Type\ParameterType
