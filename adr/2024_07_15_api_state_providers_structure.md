# API - State Providers Structure

* Status: Accepted
* Date: 2024-07-15

In effect from version: 2.0

Related PRs: #16567

## Context and Problem Stratement

Until version 2.0, we supported API Platform version 2, which utilized
`DataProviders` to deliver data to the API. These `DataProviders` were all
located within the `Sylius\Bundle\ApiBundle\DataProvider` directory.
This structure lacked differentiation between the admin and shop sections
and did not provide clear organization for individual resources. 
As a result, it became challenging to manage and maintain the codebase
as the number of providers grew.

With Sylius 2.0, the API Platform has been upgraded to version 3.
One of the significant changes in API Platform 3 is the shift from 
`DataProviders` to `StateProviders`. This change necessitates a 
reconsideration of how we organize and structure our data-providing
classes to ensure clarity, maintainability, and scalability.

## Decision Drivers

* Moving from DataProviders to StateProviders
* Clear separation between admin and shop providers
* Clear separation between resources

## Considered Options

### Leave all StateProviders in one directory

* **Good, because:**
    * Less to refactor
* **Bad, because:**
    * Hard to see which provider is for admin and which for shop

### Separate StateProviders by resource

* **Good, because:**
    * Clear separation between resources
* **Bad, because:**
    * Need to refactor all providers
    * Hard to see which provider is for admin and which for shop

### Separate StateProviders by sections

* **Good, because:**
    * Clear separation between admin and shop providers
* **Bad, because:**
    * Need to refactor all providers
    * A lot of providers in one directory

### Separate StateProviders by sections and resources

* **Good, because:**
    * Clear separation between admin and shop providers
    * Clear separation between resources
* **Bad, because:**
    * Need to refactor all providers

## Decision Outcome

Chosen option: `Separate StateProviders by sections and resources`,
because it provides a clear and manageable way to organize state providers.

New Structure:

```
/StateProvider
    /Admin
        /Order
            ItemProvider.php
        /Product
            ItemProvider.php
            CollectionProvider.php
    /Shop
        /Order
            ItemProvider.php
            CollectionProvider.php
        /Product
            CollectionProvider.php
    /Common
        /Adjustment
            CollectionProvider.php
```

Providers for Command classes should be placed in the relevant resource
directory and named with a `Provider` suffix,
e.g., `/StateProvider/Shop/ShopUser/ResetPasswordProvider.php` for the
`ResetPassword` command.
