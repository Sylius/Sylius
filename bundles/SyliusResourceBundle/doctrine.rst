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

Doctrine mapping
----------------

This bundle use the ``loadClassMetadata`` doctrine event which occurs after the mapping metadata for a class has been loaded from
a mapping source (annotations/xml/yaml). Every models can be declared as **mapped superclass**, this listener will transform
them in an entity or document if they have not child.

With this following mapping, Doctrine will create the table ``my_table`` with the column ``name``.

.. code-block:: xml

    <!-- Resoource/config/Model.orm.xml-->

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:gedmo="http://gediminasm.org/schemas/orm/doctrine-extensions-mapping">
        <mapped-superclass name="My/Bundle/Model" table="my_table">
            <id name="id" column="id" type="integer">
                <generator strategy="AUTO" />
            </id>

            <field name="name" column="name" type="string" />
        <mapped-superclass>
    </doctrine-mapping>

If you want to add an extra field, you can create a new model which extends ``My/Bundle/Model`` and its doctrine mapping
like that :

    <!-- Resoource/config/NewModel.orm.xml-->

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:gedmo="http://gediminasm.org/schemas/orm/doctrine-extensions-mapping">
        <entity name="My/OtherBundle/NewModel" table="my_new_table">
            <field name="description" column="name" type="string" />
        <entity>
    </doctrine-mapping>

.. note::

    This functionality works for Doctrine ORM and ODM.