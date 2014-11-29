VariantGenerator
================

It is used to create all possible combinations of object options and create Variant models from them.

**Example:**

If object has two options with 3 possible values each, this generator will create 9 Variant's and assign them to the object.
It ignores existing and invalid variants.

.. code-block:: php

    $variantRepository = /*...*/
    $variable = /*...*/

    $generator = new VariantGenerator($variantRepository);

    // Generate all possible variants if they don't exist currently.
    $generator->generate($variable)

.. note::

    This model implements ``VariantGeneratorInterface``.