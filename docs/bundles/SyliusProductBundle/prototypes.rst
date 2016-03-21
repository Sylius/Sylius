Prototypes
==========

...

Prototype Builder
-----------------

Used to build product based on given prototype.

Here is an example:

.. code-block:: php

    <?php

    $prototype = $this->findOr404(array('id' => $prototypeId));
    $product = $this->get('sylius.repository.product')->createNew();

    $this
        ->get('sylius.builder.prototype')
        ->build($prototype, $product)
    ;

It will add appropriate options and variants to given product based on prototype.
