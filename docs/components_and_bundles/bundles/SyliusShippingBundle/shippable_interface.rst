The ShippableInterface
======================

In order to handle your merchandise through the Sylius shipping engine, your models need to implement **ShippableInterface**.

Implementing the interface
--------------------------

Let's assume that you have a **Book** entity in your application.

First step is to implement the simple interface, which contains few simple methods.

.. code-block:: php

    namespace Acme\Bundle\ShopBundle\Entity;

    use Sylius\Component\Shipping\Model\ShippableInterface;
    use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

    class Book implements ShippableInterface
    {
        private $shippingCategory;

        public function getShippingCategory()
        {
            return $this->shippingCategory;
        }

        public function setShippingCategory(ShippingCategoryInterface $shippingCategory) // This method is not required.
        {
            $this->shippingCategory = $shippingCategory;

            return $this;
        }

        public function getShippingWeight()
        {
            // return integer representing the object weight.
        }

        public function getShippingWidth()
        {
            // return integer representing the book width.
        }

        public function getShippingHeight()
        {
            // return integer representing the book height.
        }

        public function getShippingDepth()
        {
            // return integer representing the book depth.
        }
    }

Second and last task is to define the relation inside ``Resources/config/doctrine/Book.orm.xml`` of your bundle.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\ShopBundle\Entity\Book" table="acme_book">
            <!-- your mappings... -->

            <many-to-one field="shippingCategory" target-entity="Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface">
                <join-column name="shipping_category_id" referenced-column-name="id" nullable="false" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

Done! Now your **Book** model can be used in Sylius shippingation engine.

Forms
-----

If you want to add a shipping category selection field to your model form, simply use the ``sylius_shipping_category_choice`` type.

.. code-block:: php

    namespace Acme\ShopBundle\Form\Type;

    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\AbstractType;

    class BookType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('title', 'text')
                ->add('shippingCategory', 'sylius_shipping_category_choice')
            ;
        }
    }
