Configuring taxation
====================

To start calculating taxes, we need to configure the system. In most cases, the configuration process is done via web interface, but you can also do it programatically.

Creating the tax categories
---------------------------

First step is to create a new tax category.

.. code-block:: php

    <?php

    public function configureAction()
    {
        $factory = $this->container->get('sylius.factory.tax_category');
        $manager = $this->container->get('sylius.manager.tax_category');

        $clothing = $factory
            ->createNew()
            ->setName('Clothing')
            ->setDescription('T-Shirts and this kind of stuff.')
        ;
        $food = $factory
            ->createNew()
            ->setName('Food')
            ->setDescription('Yummy!')
        ;

        $manager->persist($clothing);
        $manager->persist($food);

        $manager->flush();
    }

Categorizing the taxables
-------------------------

Second thing to do is to classify the taxables, in our example we'll get two products and assign the proper categories to them.

.. code-block:: php

    <?php

    public function configureAction()
    {
        $tshirt = // ...
        $banana = // ... Some logic behind loading entities.

        $repository = $this->container->get('sylius.repository.tax_category');

        $clothing = $repository->findOneBy(array('name' => 'Clothing'));
        $food = $repository->findOneBy(array('name' => 'Food'));

        $tshirt->setTaxCategory($clothing);
        $food->setTaxCategory($food);

        // Save the product entities.
    }

Configuring the tax rates
-------------------------

Finally, you have to create appropriate tax rates for each of categories.

.. code-block:: php

    <?php

    public function configureAction()
    {
        $taxCategoryRepository = $this->container->get('sylius.repository.tax_category');

        $clothing = $taxCategoryRepository->findOneBy(array('name' => 'Clothing'));
        $food = $taxCategoryRepository->findOneBy(array('name' => 'Food'));

        $factory = $this->container->get('sylius.factory.tax_rate');
        $manager = $this->container->get('sylius.repository.tax_rate');

        $clothingTax = $factory
            ->createNew()
            ->setName('Clothing Tax')
            ->setAmount(0,08)
        ;
        $foodTax = $factory
            ->createNew()
            ->setName('Food')
            ->setAmount(0,12)
        ;

        $manager->persist($clothingTax);
        $manager->persist($foodTax);

        $manager->flush();
    }


Done! See the :doc:`"Calculating Taxes" chapter </bundles/SyliusTaxationBundle/calculating_taxes>` to see how to apply these rates.
