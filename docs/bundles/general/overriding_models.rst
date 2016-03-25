Overriding Models
=================

...

Extending base Models
---------------------

All Sylius models live in ``Sylius\Component\Xyz\Model`` namespace together with the interfaces.
As an example, for **Sylius Taxation Component** it's *TaxCategory* and *TaxRate*.

Let's assume you want to add "zone" field to the Sylius tax rates.

Firstly, you need to create your own ``TaxRate`` class, which will extend the base model.

.. code-block:: php

    namespace Acme\Bundle\ShopBundle\Entity;

    use Sylius\Component\Addressing\Model\ZoneInterface;
    use Sylius\Component\Taxation\Model\TaxRate as BaseTaxRate;

    class TaxRate extends BaseTaxRate
    {
        private $zone;

        public function getZone()
        {
            return $this->zone;
        }

        public function setZone(ZoneInterface $zone)
        {
            $this->zone = $zone;

            return $this;
        }
    }

Secondly, define the entity mapping inside ``Resources/config/doctrine/TaxRate.orm.xml`` of your bundle.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\ShopBundle\Entity\TaxRate" table="sylius_tax_rate">
            <many-to-one field="zone" target-entity="Sylius\Component\Addressing\Model\ZoneInterface">
                <join-column name="zone_id" referenced-column-name="id" nullable="false" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

Finally, you configure your class in ``app/config/config.yml`` file.

.. code-block:: yaml

    sylius_taxation:
        driver: doctrine/orm
        classes:
            tax_rate:
                model: Acme\ShopBundle\Entity\TaxRate # Your tax rate entity.

Done! Sylius will now use your **TaxRate** model!

What has happened?

* Parameter ``sylius.model.tax_rate.class`` contains ``Acme\\Bundle\\ShopBundle\\Entity\\TaxRate``.
* ``sylius.repository.tax_rate`` represents Doctrine repository for your new class.
* ``sylius.manager.tax_rate`` represents Doctrine object manager for your new class.
* ``sylius.controller.tax_rate`` represents the controller for your new class.
* All Doctrine relations to ``Sylius\\Component\\Taxation\\Model\\TaxRateInterface`` are using your new class as *target-entity*, you do not need to update any mappings.
* ``TaxRateType`` form type is using your model as ``data_class``.
* ``Sylius\\Component\\Taxation\\Model\\TaxRate`` is automatically turned into Doctrine Mapped Superclass.
