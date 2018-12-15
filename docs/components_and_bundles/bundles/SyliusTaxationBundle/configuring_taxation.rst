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
        $taxCategoryFactory = $this->container->get('sylius.factory.tax_category');
        $taxCategoryManager = $this->container->get('sylius.manager.tax_category');

        $clothingTaxCategory = $taxCategoryFactory->createNew();
        $clothingTaxCategory->setName('Clothing');
        $clothingTaxCategory->setDescription('T-Shirts and this kind of stuff.');

        $foodTaxCategory = $taxCategoryFactory->createNew();
        $foodTaxCategory->setName('Food');
        $foodTaxCategory->setDescription('Yummy!');

        $taxCategoryManager->persist($clothingTaxCategory);
        $taxCategoryManager->persist($foodTaxCategory);

        $taxCategoryManager->flush();
    }

Categorizing the taxables
-------------------------

Second thing to do is to classify the taxables, in our example we'll get two products and assign the proper categories to them.

.. code-block:: php

    <?php

    public function configureAction()
    {
        $tshirtProduct = // ...
        $bananaProduct = // ... Some logic behind loading entities.

        $taxCategoryRepository = $this->container->get('sylius.repository.tax_category');

        $clothingTaxCategory = $taxCategoryRepository->findOneBy(['name' => 'Clothing']);
        $foodTaxCategory = $taxCategoryRepository->findOneBy(['name' => 'Food']);

        $tshirtProduct->setTaxCategory($clothingTaxCategory);
        $bananaProduct->setTaxCategory($foodTaxCategory);

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

        $clothingTaxCategory = $taxCategoryRepository->findOneBy(['name' => 'Clothing']);
        $foodTaxCategory = $taxCategoryRepository->findOneBy(['name' => 'Food']);

        $taxRateFactory = $this->container->get('sylius.factory.tax_rate');
        $taxRateManager = $this->container->get('sylius.repository.tax_rate');

        $clothingTaxRate = $taxRateFactory->createNew();
        $clothingTaxRate->setCategory($clothingTaxCategory);
        $clothingTaxRate->setName('Clothing Tax');
        $clothingTaxRate->setAmount(0.08);

        $foodTaxRate = $taxRateFactory->createNew();
        $foodTaxRate->setCategory($foodTaxCategory);
        $foodTaxRate->setName('Food');
        $foodTaxRate->setAmount(0.12);

        $taxRateManager->persist($clothingTaxRate);
        $taxRateManager->persist($foodTaxRate);

        $taxRateManager->flush();
    }


Done! See the :doc:`"Calculating Taxes" chapter </components_and_bundles/bundles/SyliusTaxationBundle/calculating_taxes>` to see how to apply these rates.
