Then, require the ``vendor/autoload.php`` file to enable the autoloading mechanism
provided by Composer. Otherwise, your application won't be able to find the classes
of this Sylius component.

.. code-block:: php

    <?php

    require dirname(__DIR__) . '/vendor/autoload.php';

    // your php code

