Doctrine tools
==============

This bundle allow you easy usage of two extra doctrine tools: `XmlMappingDriver <http://symfony.com/doc/current/cookbook/doctrine/mapping_model_classes.html>`_
and `ResolveDoctrineTargetEntitiesPass <http://symfony.com/doc/current/cookbook/doctrine/resolve_target_entity.html>`_.
The first one allow you to put your models (entities, document, etc) and their mappings in specific directories. The second
one define relationships between different entities without making them hard dependencies. We will explain how you can
enable them in the next chapters.

.. note::

    Caution : these tools are facultatives!

Creating a ``XmlMappingDriver``
-------------------------------

.. code-block:: php

    class MyBundle extends AbstractResourceBundle
    {
        // You need to specify a prefix for your bundle
        protected function getBundlePrefix()
        {
            return 'app_bundle_name';
        }

        // You need specify the namespace where are stored your models
        protected function getModelNamespace()
        {
            return 'MyApp\MyBundle\Model';
        }

        // You can specify the path where are stored the doctrine mapping, by default this method will returns
        // model. This path is relative to the Resources/config/doctrine/.
        protected function getDoctrineMappingDirectory()
        {
            return 'model';
        }
    }


Using the ``ResolveDoctrineTargetEntitiesPass``
-----------------------------------------------

.. code-block:: php

    class MyBundle extends AbstractResourceBundle
    {
        // You need to specify a prefix for your bundle
        protected function getBundlePrefix()
        {
            return 'app_bundle_name';
        }

        // You need to specify the mapping between your intefaces and your models. Like the following example you can
        // get the classname of your model in the container (See the following chapater for more informations).
        protected function getModelInterfaces()
        {
            return array(
                'MyApp\MyBundle\ModelInterface' => 'sylius.model.resource.class',
            );
        }
    }
