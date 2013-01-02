Using the services
==================

When using the bundle, you have access to several handy services.

Managers and Repositories
-------------------------

.. note::

    Sylius uses ``Doctrine\Common\Persistence`` interfaces.

You have access to following services which are used to manage and retrieve resources.

This set of default services is shared across almost all Sylius bundles, but this is just a convention.
You're interacting with them like you usually do with own entities in your project.

.. code-block:: php

    <?php

    // ...
    public function saveAction(Request $request)
    {
        // ObjectManager which is capable of managing the Address resource.
        // For *doctrine/orm* driver it will be EntityManager.
        $this->get('sylius_addressing.manager.address'); 

        // ObjectRepository for the Address resource, it extends the base EntityRepository.
        // You can use it like usual entity repository in project.
        $this->get('sylius_addressing.repository.address'); 

        // Same pair for other resources Country, Province, Zone...

        // Those repositories have some handy default methods, for example...
        $address = $addressRepository->createNew();
    }

ZoneMatcher
-----------

Since zones are usually used for tax and shipping calculations, you can use this service for getting best matching zone for given address.
Then you can apply tax calculation for matched zone.

.. code-block:: php

    <?php

    // ...
    $zoneMatcher = $this->get('sylius_addressing.zone_matcher');

    $zone = $zoneMatcher->match($address);
