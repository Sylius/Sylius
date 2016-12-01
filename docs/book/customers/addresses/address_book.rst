.. index::
   single: Address Book

Address Book
============

The Address Book concept is a very convenient solution for the customers of your shop, that come back.
Once they provide an address it is saved in the system and can be reused the next time.

**Sylius** handles the address book in a not complex way:

The Addresses Collection on a Customer
--------------------------------------

On the Customer entity we are holding a collection of addresses:

.. code-block:: php

    /**
     * @var Collection|AddressInterface[]
     */
    protected $addresses;

We can operate on it as usually - by adding and removing objects.

Besides the Customer entity has a **default address** field that is the default address used both for shipping and billing,
the one that will be filling the form fields by default.

How to add an address to the address book manually?
---------------------------------------------------

If you would like to add an address to the collection of Addresses of a chosen customer that's all what you should do:

Create a new address:

.. code-block:: php

    /** @var AddressInterface $address */
    $address = $this->container->get('sylius.factory.address')->createNew();

    $address->setFirstName('Ronald');
    $address->setLastName('Weasley');
    $address->setCompany('Ministry of Magic');
    $address->setCountryCode('UK');
    $address->setProvinceCode('UKJ');
    $address->setCity('Otter St Catchpole');
    $address->setStreet('The Burrow');
    $address->setPostcode('000001');

Then find a customer to which you would like to assign it, and add the address.

.. code-block:: php

    $customer = $this->container->get('sylius.repository.customer')->findOneBy(['email' => 'ron.weasley@magic.com']);

    $customer->addAddress($address);

Remember to flush the customer's manager to save this change.

.. code-block:: php

    $this->container->get('sylius.manager.customer')->flush();

Learn more
----------

* :doc:`Customer & ShopUser Concept Documentation </book/customers/customer_and_shopuser>`
* :doc:`Addressing - Component Documentation </components/Addressing/index>`
* :doc:`Addressing - Bundle Documentation </bundles/SyliusAddressingBundle/index>`
