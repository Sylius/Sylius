.. index::
   single: Addresses

Addresses
=========

Every address in Sylius is represented by the **Address** model.
It has a few important fields:

* ``firstName``
* ``lastName``
* ``phoneNumber``
* ``company``
* ``countryCode``
* ``provinceCode``
* ``street``
* ``city``
* ``postcode``

.. note::

   The Address has a relation to a **Customer** - which is really useful during the :doc:`Checkout addressing step </book/orders/checkout>`.

How to create an Address programmatically?
------------------------------------------

In order to create a new address, use a factory. Then complete your address with required data.

.. code-block:: php

   /** @var AddressInterface $address */
   $address = $this->container->get('sylius.factory.address')->createNew();

   $address->setFirstName('Harry');
   $address->setLastName('Potter');
   $address->setCompany('Ministry of Magic');
   $address->setCountryCode('UK');
   $address->setProvinceCode('UKJ');
   $address->setCity('Little Whinging');
   $address->setStreet('4 Privet Drive');
   $address->setPostcode('000001');

   // and finally having the address you can assign it to any Order
   $order->setShippingAddress($address);

Learn more
----------

* :doc:`Addressing - Component Documentation </components/Addressing/index>`
* :doc:`Addressing - Bundle Documentation </bundles/SyliusAddressingBundle/index>`
