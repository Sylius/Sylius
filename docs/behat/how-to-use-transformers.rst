How to use transformers?
========================

Behat provides many awesome features, and one of them are definitely **transformers**. They can be used to transform (usually widely used) parts of steps and return some values from them,
to prevent unnecessary duplication in many steps' definitions.

Basic transformer
-----------------

Example is always the best way to clarify, so let's look at this:

.. code-block:: php

    /**
     * @Transform /^"([^"]+)" shipping method$/
     * @Transform /^shipping method "([^"]+)"$/
     * @Transform :shippingMethod
     */
    public function getShippingMethodByName($shippingMethodName)
    {
        $shippingMethod = $this->shippingMethodRepository->findOneByName($shippingMethodName);

        Assert::notNull(
            $shippingMethod,
            sprintf('Shipping method with name "%s" does not exist', $shippingMethodName)
        );

        return $shippingMethod;
    }

This transformer is used to return ``ShippingMethod`` object from proper repository using it's name. It also throws exception if such a method does not exist. It can be used in plenty of steps,
that have shipping method name in it.

.. note::

    In the example above a `Webmozart assertion`__ library was used, to assert a value and throw an exception if needed.

__ https://github.com/webmozart/assert

But how to use it? It is as simple as that:

.. code-block:: php

    /**
     * @Given /^(shipping method "[^"]+") belongs to ("[^"]+" tax category)$/
     */
    public function shippingMethodBelongsToTaxCategory(
        ShippingMethodInterface $shippingMethod,
        TaxCategoryInterface $taxCategory
    ) {
        // some logic here
    }

If part of step matches transformer definition, it should be surrounded by parenthesis to be handled as whole expression. That's it! As it is shown in the example, many transformers can be
used in the same step definition. Is it all? No! The following example will also work like charm:

.. code-block:: php

    /**
     * @When I delete shipping method :shippingMethod
     * @When I try to delete shipping method :shippingMethod
     */
    public function iDeleteShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        // some logic here
    }

It is worth to mention, that in such a case, transformer would be matched depending on a name after ':' sign. So many transformes could be used when using this signature also.
This style gives an opportunity to write simple steps with transformers, without any regex, which would boost context readability.

.. note::

    Transformer definition does not have to be implemented in the same context, where it is used. It allows to share them between many different contexts.

Transformers implemented in Sylius
------------------------------------------

Specified
#########

There are plenty of transformers already implemented in *Sylius*. Most of them, are returns specific resources from theirs repository, for example:

- ``tax category "Fruits"`` -> find tax category in their repository with name "Fruits"
- ``"Chinese banana" variant of product "Banana"`` -> find variant of specific product

etc. You're free to use them in your own behat scenarios.

.. note::

    All transformers definitions are currently kept in ``Sylius\Behat\Context\Transform`` namespace.

.. warning::

    Remember to include contexts with transformers in custom suite to be able to use them!

Generic
#######

Moreover, there are also some more generic transformers, that could be useful in many different cases. They are now placed in two contexts: ``LexicalContext`` and ``SharedStorageContext``.
What are they so awesome? Let's describe them one by one:

**LexicalContext**

- ``@Transform /^"(?:€|£|\$)((?:\d+\.)?\d+)"$/`` -> tricky transformer used to parse price string with currency into integer (used to represent price in *Sylius*). It is used in steps like ``this promotion gives "€30.00" fixed discount to every order``

- ``@Transform /^"((?:\d+\.)?\d+)%"$/`` -> similar one, transforming percentage string into float (example: ``this promotion gives "10%" percentage discount to every order``)

**SharedStorageContext**

.. note::

    ``SharedStorage`` is kind of container used to keep objects, which can be shared between steps. It can be used, for example, to keep newly created promotion,
    to use its name in checking existence step.

- ``@Transform /^(it|its|theirs)$/`` -> amazingly useful transformer, that returns last resource saved in ``SharedStorage``. It allows to simplify many steps used after creation/update (and so on) actions. Example: instead of writing ``When I create "Wade Wilson" customer/Then customer "Wade Wilson" should be registered`` just write ``When I create "Wade Wilson" customer/Then it should be registered``

- ``@Transform /^(?:this|that|the) ([^"]+)$/`` -> similar to previous one, but returns resource saved with specific key, for example ``this promotion`` will return resource saved with ``promotion`` key in ``SharedStorage``
