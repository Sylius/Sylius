Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/taxonomies-bundle:*

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\TaxonomiesBundle\SyliusTaxonomiesBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }

.. note::

    Please register the bundle before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

Creating your entities
----------------------

Bundle provides default entities for you. There are `DefaultTaxonomy` and `DefaultTaxon` entities.
So, for this example, we will use them to categorize products.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``DemoBundle`` registered under ``Acme\DemoBundle`` namespace.

.. code-block:: php

    <?php

    // src/Acme/DemoBundle/Entity/Product.php
    namespace Acme\DemoBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;

    /**
     * @ORM\Entity
     */
    class Product
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToMany(targetEntity="Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface")
         * @ORM\JoinTable(
         *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
         *     inverseJoinColumns={@ORM\JoinColumn(name="taxon_id", referencedColumnName="id")}
         * )
         */
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

Product is just an example where we have many to many relationship with taxons,
which will make it possible to categorize products and list them by taxon later.

Creating your forms
-------------------

To be able to apply taxonomies on your products, or whatever you are categorizing or tagging,
it is handy to use `sylius_taxon_selection` form type:

.. code-block:: php

    <?php

    // src/Acme/DemoBundle/Form/ProductType.php
    namespace Acme\DemoBundle\Form;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;

    class ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('taxons', 'sylius_taxon_selection');
        }

        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'data_class' => 'Acme\DemoBundle\Entity\Product'
                ))
            ;
        }
    }

This `sylius_taxon_selection` type will add a select input field for watch taxonomy, with select option for each taxon.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_taxonomies:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.

And configure doctrine extensions which are used in taxonomies bundle:

.. code-block:: yaml

    stof_doctrine_extensions:
        orm:
            default:
                tree: true
                sluggable: true

Routing configuration
---------------------

We will show an example here, how you can configure routing.
Routing is based on `SyliusResourceBundle`.

Add following to your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_taxonomies:
        resource: @SyliusTaxonomiesBundle/Resources/config/routing.yml
        prefix: /taxonomies

Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
