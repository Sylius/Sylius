---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Countries

**Countries** are a part of the Addressing concept. The **Country** entity represents a real country that your shop is willing to sell its goods in (for example the UK). It has an ISO code to be identified easily ([ISO 3166-1 alpha-2](https://www.iso.org/iso/country\_codes)).

Countries might also have **Provinces**, which are in fact a general name for an administrative division, within a country. Therefore we understand provinces as states of the USA, voivodeships of Poland, or Bundesländer of Germany.

## How to add a country?

To give you a better insight into Countries, let’s have a look on how to prepare and add a Country to the system programmatically. We will do it with a province at once.

You will need factories for countries and provinces to create them:

```php
/** @var CountryInterface $country */
$country = $this->container->get('sylius.factory.country')->createNew();

/** @var ProvinceInterface $province */
$province = $this->container->get('sylius.factory.province')->createNew();
```

To the newly created objects assign codes.

```php
// US - the United States of America
$country->setCode('US');
// US_CA - California
$province->setCode('US_CA');
```

Provinces may be added to a country via a collection. Create one and add the province object to it and using the prepared collection add the province to the Country.

```php
$provinces = new ArrayCollection();
$provinces->add($province);

$country->setProvinces($provinces);
```

You can of course simply add a single province:

```php
$country->addProvince($province);
```

Finally, you will need a repository for countries to add the country to your system.

```php
/** @var RepositoryInterface $countryRepository */
$countryRepository = $this->get('sylius.repository.country');

$countryRepository->add($country);
```

From now on the country will be available to use in your system.
