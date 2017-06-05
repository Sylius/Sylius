Custom use of TaxationBundle outside of Sylius eComm
====================================================

This is a guide how to use ``TaxationBundle`` for other Symfony projects, out side of ``Sylius`` eCommerce.
Exact implementation will depend on use case.
Installation and setting up the Bundle is same as in ``Installation`` part of this Bundle's documentation.

The TaxableInterface
--------------------

In order to calculate the taxes for a model in your application, it needs to implement the ``TaxableInterface``.
It is a very simple interface, with only one method - the ``getTaxCategory()``, as every taxable has to belong to a specific tax category.

Implementing the interface
~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you are building some sort of hotel booking system (it could be car rental, real estate rental etc., principle is the same).
This application should have **Guest** entity and it should implement ``TaxableInterface``.
Basically, every entity that you want to run taxes against should implement ``TaxableInterface``.
This is not where tax calculations happen.

First step is to implement the simple interface.

.. code-block:: php

    namespace AcmeBundle\Entity;

    use Sylius\Component\Taxation\Model\TaxCategoryInterface;
    use Sylius\Component\Taxation\Model\TaxableInterface;

    class Guest implements TaxableInterface
    {
        private $name;
        private $dob;
        private $taxCategory;

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
        }

        public function getDob()
        {
            return $this->dob;
        }

        public function setDob($dob)
        {
            $this->dob = $dob;
        }

        public function getTaxCategory()
        {
            return $this->taxCategory;
        }

        public function setTaxCategory(TaxCategoryInterface $taxCategory)
        {
            $this->taxCategory = $taxCategory;
        }
    }

Second and last task is to define the relation inside ``Resources/config/doctrine/Guest.orm.xml`` of your bundle.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                      xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                                          http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

        <entity name="AcmeBundle\Entity\Guest" table="acme_guests">
            <!-- your mappings... -->

            <many-to-one
                field="taxCategory"
                target-entity="Sylius\Component\Taxation\Model\TaxCategoryInterface"
            >
                <join-column name="tax_category_id" referenced-column-name="id" nullable="false" />
            </many-to-one>
        </entity>

    </doctrine-mapping>

 Updating database schema again.

.. code-block:: bash

php app/console doctrine:schema:update --force

Done! Now your **Guest** model can be used in Sylius taxation engine.

Forms
~~~~~

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
                ->add('dob', 'text')
                ->add('taxCategory', 'sylius_tax_category_choice')
            ;
        }

        public function getName()
        {
            return 'acme_guest';
        }
    }


Configuring taxation
--------------------

To start calculating taxes, we need to configure the system. In most cases, the configuration process is done via web interface, but you can also do it programatically.

Creating the tax categories
~~~~~~~~~~~~~~~~~~~~~~~~~~~

First step is to create a new tax category.

.. code-block:: php

    <?php

    public function configureCategoryAction()
    {
        $repository = $this->container->get('sylius.repository.tax_category');
        $manager = $this->container->get('sylius.manager.tax_category');

        $guestTaxCategory = $repository
            ->createNew()
            ->setName('Guest')
            ->setDescription('Visitors to guesthouse/hostel/hotel')
        ;

        $manager->persist($guestTaxCategory);

        $manager->flush();
    }

Categorizing the taxables
~~~~~~~~~~~~~~~~~~~~~~~~~

Second thing to do is to classify the taxables, in our example we'll get three guests and assign the proper categories to them.

.. code-block:: php

    <?php

    public function configureTaxableAction()
    {
        $guestAdult = // ... Guest with age 18+
        $guestMinor = // ... Guest that is 12 - 18 years old
        $guestChild = // ... Guest is child

        $repository = $this->container->get('sylius.repository.tax_category');
        $guestTaxCategory = $repository->findOneBy(array('name' => 'Guest'));

        $guestAdult->setTaxCategory($guestTaxCategory);
        $guestMinor->setTaxCategory($guestTaxCategory);
        $guestChild->setTaxCategory($guestTaxCategory);

        // ...

        // Save the product entities.
        $manager->persist($guestAdult);
        $manager->persist($guestMinor);
        $manager->persist($guestChild);

    }

Configure calculator
~~~~~~~~~~~~~~~~~~~~

Third thing to do is to create calculator that will calculate the taxes on taxable entities.
The calculator needs to implement ``Sylius\Component\Taxation\Calculator\CalculatorInterface`` and only one method called ``calculate``.
Calculate method takes 2 params, ``$base`` and ``TaxRateInterface $rate``.

.. code-block:: php

    <?php

    namespace Amce\Tax\Calculator;

    use Sylius\Component\Taxation\Calculator\CalculatorInterface;

    public Class TouristTaxCalculator implements CalculatorInterface
    {
        public function calculate($base, TaxRateInterface $rate)
        {
            return $base * $rate->getAmount();
        }
    }

.. note::
Then you need to create a service for this calculator

.. code-block:: yaml

    app.tax_calculator.toursist_tax:
        class: AppBundle\Taxes\Calculator\TouristTaxCalculator
        tags:
            - { name: sylius.tax_calculator, calculator: app.tourist_tax }

Its very important to tag the service with ``name: sylius.tax_calculator`` and with ``calculator: app.tourist_tax``.
Later we'll use ``app.tourist_tax`` to set calculator to tax category.

Configuring the tax rates
~~~~~~~~~~~~~~~~~~~~~~~~~

Finally, you have to create appropriate tax rates for each of categories.
Here you'll set calculator and use calculator's name ``app.tourist_tax``.

.. code-block:: php

    <?php

    public function configureTaxAction()
    {
        $taxCategoryRepository = $this->container->get('sylius.repository.tax_category');

        $clothing = $taxCategoryRepository->findOneBy(array('name' => 'Clothing'));
        $food = $taxCategoryRepository->findOneBy(array('name' => 'Food'));

        $repository = $this->container->get('sylius.repository.tax_rate');
        $manager = $this->container->get('sylius.repository.tax_rate');

        $adultTaxCalc = $repository
            ->createNew()
            ->setName('Adult')
            ->setCategory($taxCategory)
            ->setCalculator('app.toursist_tax')
            ->setAmount(0.8)
        ;
        $minorTaxCalc = $repository
            ->createNew()
            ->setName('Minor')
            ->setCategory($taxCategory)
            ->setCalculator('app.toursist_tax')
            ->setAmount(0.4)
        ;

        $childTaxCalc = $repository
            ->createNew()
            ->setName('Child')
            ->setCalculator('app.toursist_tax')
            ->setCategory($taxCategory)
            ->setAmount(0)
        ;

        $manager->persist($adultTaxCalc);
        $manager->persist($minorTaxCalc);
        $manager->persist($childTaxCalc);

        $manager->flush();
    }

Calculate Taxes
~~~~~~~~~~~~~~~

In order to calculate tax amount for given taxable, we need to find out the applicable tax rate.
The tax rate resolver service is available under sylius.tax_rate_resolver id, while the delegating tax calculator is accessible via sylius.tax_calculator name.


.. code-block:: php

    <?php

    namespace Acme\Tax\Taxation

    use Acme\Entity\Booking;
    use Sylius\Bundle\TaxationBundle\Calculator\CalculatorInterface;
    use Sylius\Bundle\TaxationBundle\Resolver\TaxRateResolverInterface;

    class TaxApplicator
    {
        private $calculator;
        private $taxRateResolver;

        public function __construct(
            CalculatorInterface $calculator,
            TaxRateResolverInterface $taxRateResolver,
        )
        {
            $this->calculator = $calculator;
            $this->taxRateResolver = $taxRateResolver;

        }

        public function applyTaxes(Booking $booking)
        {
            $tax = 0;
            $bookingDuration = 7; // ... implement own calculation
            $guests = $booking->getGuests()

            foreach ($guests as $guest) {
                $rate = $this->taxRateResolver->resolve($guest, $this->applyTaxRate($guest));

                $tax += $this->calculator->calculate($bookingDuration, $rate);
            }

            $booking->setTotalTax($tax); // Set the calculated taxes.
        }


        public function applyTaxRate(Guest $guest)
        {
            // ... some logic to distinguish different tax rates for different age group
        }
    }
