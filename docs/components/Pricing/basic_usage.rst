Basic Usage
===========

In all examples we use an exemplary class implementing **PriceableInterface**, which looks like this:

.. code-block:: php

    <?php

    use Sylius\Component\Pricing\Model\PriceableInterface;

    class Book implements PriceableInterface
    {
        /**
         * @var int
         */
        private $price;

        /**
         * @var string
         */
        private $calculator;

        /**
         * @var array
         */
        private $configuration;

        /**
         * {@inheritdoc}
         */
        public function getPrice()
        {
            return $this->price;
        }

        /**
         * {@inheritdoc}
         */
        public function setPrice($price)
        {
            $this->price = $price;
        }

        /**
         * {@inheritdoc}
         */
        public function getPricingCalculator()
        {
           return $this->calculator;
        }

        /**
         * {@inheritdoc}
         */
        public function setPricingCalculator($calculator)
        {
            $this->calculator = $calculator;
        }

        /**
         * {@inheritdoc}
         */
        public function getPricingConfiguration()
        {
            return $this->configuration;
        }

        /**
         * {@inheritdoc}
         */
        public function setPricingConfiguration(array $configuration)
        {
            $this->configuration = $configuration;
        }
    }


Standard Calculator
-------------------

**StandardCalculator** class calculates the unit price of a subject.

.. code-block:: php

    <?php

    use Sylius\Component\Pricing\Calculator\StandardCalculator;

    $standardCalculator = new StandardCalculator();
    $book = new Book();
    $book->setPrice(1099);
    $book->setPricingConfiguration(array());
    $standardCalculator->calculate($book, $book->getPricingConfiguration()); // returns 1099

Volume Based Calculator
-----------------------

**VolumeBasedCalculator** class calculates unit price depending on the quantity of subjects.

.. code-block:: php

    <?php

    use Sylius\Component\Pricing\Calculator\VolumeBasedCalculator;

    $volumeCalculator = new VolumeBasedCalculator();
    $configuration = array(
        array(            // if quantity is between 2-9 the price is for each 300
            'min' => 2,
            'max' => 9,
            'price' => 300,
        ),
        array(
            'min' => 10, // if is more than 10 then price is 200
            'max' => null,
            'price' => 500,
        ),
    );// else is 599 (because the price from book is 599)

    $book = new Book();
    $book->setPricingConfiguration($configuration);
    $book->setPrice(599);

    // if you don't pass $context to calculate method then quantity will be 1
    $context = array('quantity' => 4);

    $volumeCalculator->calculate($book, $book->getPricingConfiguration(), $context); // returns 300
    // If the quantity of subjects are not in the ranges from $configuration, then the price
    // will be the same as price, which was set in book.

Delegating Calculator
---------------------

**DelegatingCalculator** class delegates the calculation of charge for particular subject to a correct calculator
instance, based on the type defined on the subject.

.. code-block:: php

    <?php

    use Sylius\Component\Pricing\Calculator\StandardCalculator;
    use Sylius\Component\Pricing\Calculator\VolumeBasedCalculator;
    use Sylius\Component\Pricing\Calculator\DelegatingCalculator;
    use Sylius\Component\Pricing\Calculator\CalculatorInterface;
    use Sylius\Component\Registry\ServiceRegistry;

    $standardCalculator = new StandardCalculator();
    $volumeBasedCalculator = new VolumeBasedCalculator();

    $serviceRegistry =
    new ServiceRegistry(CalculatorInterface::class);
    $serviceRegistry->register(Calculators::STANDARD, $standardCalculator);
    $serviceRegistry->register(Calculators::VOLUME_BASED, $volumeBasedCalculator);

    $delegatingCalculator = new DelegatingCalculator($serviceRegistry);

    $book = new Book();
    $book->setPrice(398);
    $book->setPricingCalculator(Calculators::STANDARD);
    $book->setPricingConfiguration(array());

    $delegatingCalculator->calculate($book); // returns 398

    $configuration = array(
        array(
            'min' => 1,
            'max' => 9,
            'price' => 300,
        ),
        array(
            'min' => 10,
            'max' => null,
            'price' => 200,
        ),
    );

    $context = array('quantity' => 4);
    $book->setPricingConfiguration($configuration);
    $book->setPricingCalculator(Calculators::VOLUME_BASED);

    // returns 200, because the pricing calculator was changed
    $delegatingCalculator->calculate($book, $context);
