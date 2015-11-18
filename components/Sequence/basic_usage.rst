Basic Usage
===========

In order to benefit from the component's features at first you need to create a basic class that will implement
the :ref:`component_sequence_model_sequence-subject-interface`. Let's assume that you would like to generate identifiers for orders in your system. Your **Order** class therefore will implement this interface
to have and ability to be a subject of sequence automatic generation by your services.

SequenceSubjectInterface
------------------------

Let's see how an exemplary class implementing **SequenceSubjectInterface** should look like.

.. code-block:: php

    <?php

    namespace AppBundle\Entity\Order;

    use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

    class Order implements SequenceSubjectInterface
    {
        const SEQUENCE_TYPE = 'order';

        /**
         * @var string
         */
        private $number;

        /**
         * {@inheritdoc}
         */
        public function getSequenceType()
        {
            return self::SEQUENCE_TYPE;
        }

        /**
         * {@inheritdoc}
         */
        public function getNumber()
        {
            return $this->number;
        }

        /**
         * {@inheritdoc}
         */
        public function setNumber($number)
        {
            $this->number = $number;
        }
    }

.. _component_sequence_number_sequential-generator-usage:

Let's now see how we can use an exemplary generator:

.. code-block:: php

    <?php

    use Sylius\Component\Sequence\Model\Sequence;
    use Sylius\Component\Sequence\Number\SequentialGenerator;
    use AppBundle\Entity\Order;

    // Prepare an object for the sequence you are going to generate, that will store its type.
    $sequence = new Sequence('order');

    $subject = new Order();
    $anotherSubject = new Order();

    // Instantiate the generator that will generate sequences of length 4 starting from 0077.
    $generator = new SequentialGenerator(4, 77);

    $generator->generate($subject, $sequence);
    $generator->generate($anotherSubject, $sequence);

    $subject->getNumber();        // returns '0077'
    $anotherSubject->getNumber(); // returns '0078'

.. hint::

   You can read more about each of the available generators in the :doc:`generators` chapter.
