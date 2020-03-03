Date: 2020-03-03

# API - Coverage

In power from version: 1.8.0 (?)

Related PRs: #11136, #11144, #11157

## Description

### Problem to solve

Cover with API all functionality provided by the Admin panel. 

### Context

New Sylius API (based on API Platform), should cover in 100% business value provided in Admin panel (and already covered
by `@ui` Behat scenarios).

Therefore, there are some compromises and decisions already taken (regarding the features themselves), that we need to include
(or not include) in the new API functionality.

### Possible solutions

Instead of focusing on already described scenarios, the other option was to explore the resources management domains one
more time and include all determined cases in the API.

### Decision and reasoning

For the beginning, we focus on implementing only features already covered with Behat UI scenarios. In the future, it
would result in bigger reliability of Behat API tests and allows more straightforward iteration over missing features.
  
As an example - we don't provide `ProductOptionsValue`s deletion feature in API, as it's not yet provided and covered with
Behat UI scenario (even though there is a value in this feature - and should be implemented separately for both, UI and API).
