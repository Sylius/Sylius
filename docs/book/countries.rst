.. index::
   single: Countries

Countries
=========

**Countries** are a part of the :doc:`Addressing </book/addresses>` concept.
The **Country** entity represents a real country that your shop is willing to sell its goods in (for example the UK).
It has an ISO code to be identified easily (ISO 3166-1 alpha-2).

Countries might also have **Provinces**, which is in fact a general name for an administrative division, within a country.
Therefore we understand provinces as states of the USA, voivodeships of Poland, cantons of Belgium or bundesländer of Germany.

How to add a country?
---------------------

To give you a better insight into Countries, let's have a look how to prepare and add a Country to the system programatically.
We will do it together with a province at once.

Firstly you will need factories for countries and for provinces. Create new objects with them.
To the newly created objects assign codes. The provinces are added to a country as a collection.
Finally using a repository for countries - add the country to the system.
From now on it will be available to use.

.. code-block:: php

    <?php

    // Firstly you will need factories for countries and for provinces:
    /** @var FactoryInterface $countryFactory **/
    $countryFactory = $this->get('sylius.factory.country');

    /** @var FactoryInterface $provinceFactory */
    $provinceFactory = $this->get('sylius.factory.province');

    // Create new objects with the factories:
    /** @var CountryInterface $country */
    $country = $countryFactory->createNew();

    /** @var ProvinceInterface $province */
    $province = $provinceFactory->createNew();

    // To the newly created objects assign proper codes.
    // US - the United States of America
    $country->setCode('US');
    // US_CA - California
    $province->setCode('US_CA');

    // Provinces may be added to a country via a collection. Create one and add the province object to it.
    $provinces = new ArrayCollection();
    $provinces->add($province);

    // and using the prepared collection add the province to the Country.
    $country->setProvinces($provinces);

    // You can of course simply add single province:
    // $country->addProvince($province);

    // Finally you will need a repository for countries to add the country to your system.
    /** @var RepositoryInterface $countryRepository */
    $countryRepository = $this->get('sylius.repository.country');

    $countryRepository->add($country);
    // From now on the country will be available to use in your system

Learn more
----------

* :doc:`Addressing - Bundle Documentation </bundles/SyliusAddressingBundle/index>`
* :doc:`Addressing - Component Documentation </components/Addressing/index>`
