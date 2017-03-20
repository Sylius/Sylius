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
        $repository = $this->container->get('sylius.repository.tax_category');
        $manager = $this->container->get('sylius.manager.tax_category');

        $cloudServerTaxCategory = $repository
            ->createNew()
            ->setName('Cloud Server')
            ->setDescription('Cloud computing.')
        ;
        $sharedHostingTaxCategory = $repository
            ->createNew()
            ->setName('Shared Hosting')
            ->setDescription('Shared hosting is not really great for you!')
        ;

        $manager->persist($cloudServerTaxCategory);
        $manager->persist($sharedHostingTaxCategory);

        $manager->flush();
    }

Categorizing the taxables
-------------------------

Second thing to do is to classify the taxables, in our example we'll get two products and assign the proper categories to them.

.. code-block:: php

    <?php

    public function configureAction()
    {
        $repository = $this->container->get('sylius.repository.tax_category');

        $cloudServerTaxCategory = $repository->findOneBy(array('name' => 'Cloud Server'));
        $sharedHostingTaxCategory = $repository->findOneBy(array('name' => 'Shared Hosting'));

        $digitalOcean = new Server();
        $taxCategory->setName('Digital Ocean');
        $taxCategory->setTaxCategory($cloudServerTaxCategory);

        $goDaddy = new Server();
        $taxCategory->setName('GoDaddy');
        $taxCategory->setTaxCategory($sharedHostingTaxCategory);

        $manager->persist($digitalOcean);
        $manager->persist($goDaddy);

        $manager->flush();
    }

Configuring the tax rates
-------------------------

Finally, you have to create appropriate tax rates for each of categories and assign tax calculator, in this case ``app.server_tax`` (which we will create in one of the following chapters).

.. code-block:: php

    <?php

    public function configureAction()
    {
        //... continue

        $repository = $this->container->get('sylius.repository.tax_rate');
        $manager = $this->container->get('sylius.repository.tax_rate');

        $cloudServerTax = $repository
            ->createNew()
            ->setName('Cloud Server Tax')
            ->setCalculator('app.server_tax')
            ->setAmount(0,15);
        ;
        $sharedHostingTax = $repository
            ->createNew()
            ->setName('Shared Hosting Tax')
            ->setCalculator('app.server_tax')
            ->setAmount(0,08);
        ;

        $manager->persist($cloudServerTax);
        $manager->persist($sharedHostingTax);

        $manager->flush();
    }


Done! See the :doc:`"Calculating Taxes" chapter </bundles/SyliusTaxationBundle/calculating_taxes>` to see how to apply these rates.
