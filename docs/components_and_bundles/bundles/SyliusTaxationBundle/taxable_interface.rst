The TaxableInterface
====================

In order to calculate the taxes for a model in your application, it needs to implement the ``TaxableInterface`` 
It is a very simple interface, with only one method - the ``getTaxCategory()``, as every taxable has to belong to a specific tax category.

Implementing the interface
--------------------------

Let's assume that you have a **Server** entity in your application. Every server has it's price and other parameters, but you would like to calculate the tax included in price.
You could calculate the math in a simple method, but it's not enough when you have to handle multiple tax rates, categories and zones.

First step is to implement the simple interface.

.. code-block:: php

    namespace AcmeBundle\Entity;

    use Sylius\Component\Taxation\Model\TaxCategoryInterface;
    use Sylius\Component\Taxation\Model\TaxableInterface;

    class Server implements TaxableInterface
    {
        private $name;
        private $taxCategory;

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function getTaxCategory()
        {
            return $this->taxCategory;
        }

        public function setTaxCategory(TaxCategoryInterface $taxCategory) // This method is not required.
        {
            $this->taxCategory = $taxCategory;
        }
    }

Second and last task is to define the relation inside ``Resources/config/doctrine/Server.orm.xml`` of your bundle.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="AcmeBundle\Entity\Server" table="acme_server">
            <!-- your mappings... -->

            <many-to-one field="taxCategory" target-entity="Sylius\Component\Taxation\Model\TaxCategoryInterface">
                <join-column name="tax_category_id" referenced-column-name="id" nullable="false" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

Done! Now your **Server** model can be used in Sylius taxation engine.

Forms
-----

If you want to add a tax category selection field to your model form, simply use the ``sylius_tax_category_choice`` type.

.. code-block:: php

    namespace AcmeBundle\Form\Type;

    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\AbstractType;

    class ServerType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('name', 'text')
                ->add('taxCategory', 'sylius_tax_category_choice')
            ;
        }

        public function getName()
        {
            return 'acme_server';
        }
    }
