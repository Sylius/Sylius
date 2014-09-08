Managing flash messages
=======================

By default the resource bundle generate default messages for each action. They use the following translation key pattern
**sylius.resource.actionName** (``actionName`` can be ``create``, ``update`` and ``delete``).

You can manage flash messages by resource, the following pattern **appName.resource.actionName** is used to translate them.

+ **appName:** the name of your application (its default value is sylius)
+ **resource:** the name of your resource
+ **actionName:** the current action (``create``, ``update`` or ``delete``)

Example:

.. code-block:: yaml

    sylius_resource:
        resources:
            my_app.entity_key:
                driver: doctrine/orm
                templates: App:User
                classes:
                    model: MyApp\Entity\EntityName
                    interface: MyApp\Entity\EntityKeyInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository

For the current this resource, you can use ``my_app.entity_key.create``, ``my_app.entity_key.update`` and ``my_app.entity_key.delete`` in your translation files.

.. note::

    Caution: The domain used for translating flash messages is flashes. You need to create your own flashes.local.yml.
