Regsitry
========

A registry object acts as a collection of objects. The sylius registry allows you to store object which implements
a specific interface. Let's see how it works!

.. code-block:: php

    // First you need to create the regsitry object and tell what kind of inteface that you want to work with.
    $registry = new ServiceRegistry('Sylius\Component\Pricing\Calculator\CalculatorInterface');

    // After that you can register a object.
    // The first argument is key which identify your object and the second one is the object that you want to register
    $registry->register('volume_based', new VolumeBasedCalculator());
    $registry->register('standard', new StandardCalculator());

    // You can unregister a object too
    $registry->unregister('standard');

    // You can check if your object is registered, the following method method return a boolean
    $registry->has('volume_based');

    // And finally, you can get the instance of your object
    $registry->get('volume_based');

.. note::

    If you try to register a object which not implement the right interface or is not a object a `InvalidArgumentException`
    will be thrown.

    If you try to register an existing object a `ExistingServiceException` will be thrown

    If you try to unregister a non existing object a `NonExistingServiceException` will be thrown