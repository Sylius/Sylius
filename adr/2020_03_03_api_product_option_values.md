# API - Product Option Value

* Status: accepted
* Date: 2020-03-03

In power from version: 1.8.0

Related PRs: #11136, #11144, #11157

## Description

### Problem to solve

Cover with API all `ProductOption`s related functionality provided by the Admin panel. 

### Context

During the development of a new Sylius API (based on API Platform) for Product Options, we had to decide how we should handle
Product Option's values collection, to make it efficient and easy to use.

### Possible solutions

Values from collection could be embedded as objects within a `ProductOption` resource **or** provided by
[IRIs](https://en.wikipedia.org/wiki/Internationalized_Resource_Identifier)

### Decision and reasoning

Product option values should always be provided with their IRIs. They can exist outside of the `ProductOption` resource 
(e.g. be [related with](https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Product/Model/ProductVariant.php#L44) `ProductVariant`).
Moreover, values embedded together with their translations in the `ProductOption` that already has its translations would
result in a massive and unmanageable response.
