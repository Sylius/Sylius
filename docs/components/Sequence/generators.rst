Generators
==========

.. _component_sequence_number_abstract-generator:

AbstractGenerator
-----------------

A custom generator model should extend this class in order to generate sequences
on a given object that implements the :ref:`component_sequence_model_sequence-subject-interface`.

.. note::
   This class implements the :ref:`component_sequence_number_generator-interface`.

   For more detailed information go to `Sylius API AbstractGenerator`_.

.. _Sylius API AbstractGenerator: http://api.sylius.org/Sylius/Component/Sequence/Number/AbstractGenerator.html

.. _component_sequence_number_sequential-generator:

SequentialGenerator
-------------------

This class is a default order number generator.

+--------------------+------------------------------+
| Property           | Description                  |
+====================+==============================+
| numberLength       | Order number max length      |
+--------------------+------------------------------+
| startNumber        | The sequence start number    |
+--------------------+------------------------------+

Below you can see a snippet on how to use it:

.. code-block:: php

    <?php

    // Instantiate the generator that will generate sequences of length 4 starting from 0077.
    $generator = new SequentialGenerator(4, 77);

    // And use it on subjects that implement the SequenceSubjectInterface
    $generator->generate($subject, $sequence); // generates '0077'
    $generator->generate($anotherSubject, $sequence); // generates '0078'

.. note::
   This generator implements the :ref:`component_sequence_number_generator-interface`
   and extends the :ref:`component_sequence_number_abstract-generator`.

   For more detailed information go to `Sylius API SequentialGenerator`_.

.. _Sylius API SequentialGenerator: http://api.sylius.org/Sylius/Component/Sequence/Number/SequentialGenerator.html

.. _component_sequence_number_hash-generator:

HashGenerator
-------------

This class generates hash numbers similar to the Amazon order identifiers (e.g. 105-3958356-3707476)
and also random hashed segments of a given length.

 Below you can see a snippet on how to use it:

.. code-block:: php

    <?php

    $subject = new Order();
    $repository = new InMemoryRepository();

    // Instantiate the generator that will generate a 3 by 7 by 7 digits number.
    $generator = new HashGenerator($repository);

    $index = /** index of the sequence **/
    $generator->generateNumber($index, $subject);

    $subject->getNumber(); // returns randomized sequence of format 'xxx-xxxxxxx-xxxxxxx'

.. note::
   This generator implements the :ref:`component_sequence_number_generator-interface`
   and extends the :ref:`component_sequence_number_abstract-generator`.

   For more detailed information go to `Sylius API HashGenerator`_.

.. _Sylius API HashGenerator: http://api.sylius.org/Sylius/Component/Sequence/Number/HashGenerator.html

.. _component_sequence_registry_generator-registry:

GeneratorRegistry
-----------------

It returns the generator used for a given entity.

.. note::
   This service extends the :ref:`component_registry_service-registry`.

   For more detailed information go to `Sylius API GeneratorRegistry`_.

.. caution::
   Throws :ref:`component_registry_non-existing-service-exception`.

.. _Sylius API GeneratorRegistry: http://api.sylius.org/Sylius/Component/Sequence/Registry/GeneratorRegistry.html

.. _component_sequence_generator_non-existing-generator-exception:

NonExistingGeneratorException
-----------------------------

This exception is thrown when your are trying to get a generator that does not exist in your system.

.. note::   This exception extends the `\\InvalidArgumentException`_.

.. _\\InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php
