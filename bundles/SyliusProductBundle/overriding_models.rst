Overriding Models
=================

The default models are already usable, but you can customize or replace them.

Using your own Product class
----------------------------

If you want to add new fields to your Product class, you have to create your own **Product** model, living inside your application code.

We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``ShopBundle`` registered under ``Acme\ShopBundle`` namespace.

In this example, we'll add a field for product's short descriptions.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Entity/Product.php
    namespace Acme\ShopBundle\Entity;

    use Sylius\Bundle\ProductBundle\Model\Product as BaseProduct;

    class Product extends BaseProduct
    {
        private $shortDescription;

        public function getShortDescription()
        {
            return $this->shortDescription;
        }

        public function setShortDescription($shortDescription)
        {
            $this->shortDescription = $shortDescription;
        }
    }

Now define the entity mapping inside ``Resources/config/doctrine/Product.orm.xml`` of your bundle and add the new field.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\ShopBundle\Entity\Product" table="sylius_product">
            <field name="shortDescription" column="short_description" type="string" nullable="true" />
        </entity>

    </doctrine-mapping>

Configure the new model.

.. code-block:: yaml

    # app/config/config.yml

    sylius_product:
        driver: doctrine/orm
        classes:
            product:
                model: Acme\ShopBundle\Entity\Product

Update the database schema.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.

Done, now the new Product model is used.
