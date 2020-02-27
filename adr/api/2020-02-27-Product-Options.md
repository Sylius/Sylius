Date: 2020-02-27

# Product Options API

In power from version: 1.8.0 (?)

Related PRs: #11136, #11144, #11157

## Description

### Problem to solve

Cover with API all `ProductOption`s related functionality provided by the Admin panel. 

### Context

New Sylius API (based on API Platform), should cover in 100% or business value provided in Admin panel (and already covered
by `@ui` Behat scenarios).

Therefore, there are some compromises and decisions already taken (regarding the features themselves), that we need to include
(or not include) in the new API functionality.

### Possible solutions

- **Business value:** instead of focusing on already described scenarios, the other option was to explore the Product
   Options management domain one more time and include all determined cases in the API.

- **Translations:** the translations from collection could be embedded as objects within a `ProductOption`/`ProductOptionValue`
  resource **or** provided by [IRIs](https://en.wikipedia.org/wiki/Internationalized_Resource_Identifier)
  
- **Product option values:** similar to the translations, could be embedded as objects or IRIs   

### Decision and reasoning

- **API coverage:** for the beginning, we focus on implementing only features already covered with Behat UI scenarios.
  In the future, it would result in bigger reliability of Behat API tests and allows more straightforward iteration over missing features.
  
  - As an example - we don't provide `ProductOptionsValue`s deletion feature in API, as it's not yet provided and
    covered with Behat UI scenario (even though there is a value in this feature - and should be implemented separately for both, UI and API).
  
- **Translations:** should always be embedded as the collection of objects. They're irrelevant outside of the main object
  and does not provide any value alone.
  
- **Product option values:** should always be provided with their IRIs. They can exist outside of the `ProductOption` resource
  (e.g. be [related with](https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Product/Model/ProductVariant.php#L44) `ProductVariant`).
  Moreover, values embedded together with their translations in the `ProductOption` that already has its translations would
  result in a massive and unmanageable response.
  
- **Updates of objects:** are defined with the PUT method, to allow embedded objects (like translations) update.
  The flow of object update should always be: GET (for a response with object data) -> modification -> PUT (with the whole object).
