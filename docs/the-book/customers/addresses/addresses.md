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

# Addresses

Every address in Sylius is represented by the **Address** model. It has a few important fields:

* `firstName`
* `lastName`
* `phoneNumber`
* `company`
* `countryCode`
* `provinceCode`
* `street`
* `city`
* `postcode`

{% hint style="info" %}
The Address has a relation to a **Customer** - which is useful during the [Checkout](../../carts-and-orders/checkout.md) addressing step.
{% endhint %}

## How to create an Address programmatically?

To create a new address, use a factory. Then complete your address with the required data.

```php
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
```
