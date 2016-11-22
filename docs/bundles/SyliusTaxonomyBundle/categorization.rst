Categorization
==============

In this example, we will use taxonomies to categorize products and build a nice catalog.

We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``ShopBundle`` registered under ``Acme\ShopBundle`` namespace.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Entity/Product.php
    namespace Acme\ShopBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;

    class Product
    {
        protected $taxons;

        public function __construct()
        {
            $this->taxons = new ArrayCollection();
        }

        public function getTaxons()
        {
            return $this->taxons;
        }

        public function setTaxons(Collection $taxons)
        {
            $this->taxons = $taxons;
        }
    }

You also need to define the doctrine mapping with a many-to-many relation between Product and Taxons.
Your product entity mapping should live inside ``Resources/config/doctrine/Product.orm.xml`` of your bundle.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="Acme\ShopBundle\Entity\Product" table="sylius_product">
            <id name="id" column="id" type="integer">
                <generator strategy="AUTO" />
            </id>

            <!-- Your other mappings. -->

            <many-to-many field="taxons" target-entity="Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface">
                <join-table name="sylius_product_taxon">
                    <join-columns>
                        <join-column name="product_id" referenced-column-name="id" nullable="false" />
                    </join-columns>
                    <inverse-join-columns>
                        <join-column name="taxon_id" referenced-column-name="id" nullable="false" />
                    </inverse-join-columns>
                </join-table>
            </many-to-many>
        </entity>

    </doctrine-mapping>

Product is just an example where we have many to many relationship with taxons,
which will make it possible to categorize products and list them by taxon later.

You can classify any other model in your application the same way.

Creating your forms
-------------------

To be able to apply taxonomies on your products, or whatever you are categorizing or tagging,
it is handy to use `sylius_taxon_choice` form type:

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Form/ProductType.php
    namespace Acme\ShopBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('taxons', 'sylius_taxon_choice');
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'data_class' => 'Acme\ShopBundle\Entity\Product'
                ))
            ;
        }
    }

This `sylius_taxon_choice` type will add a select input field for each taxonomy, with select option for each taxon.
