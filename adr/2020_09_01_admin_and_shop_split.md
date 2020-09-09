# Separation of admin and shop routes

* Status: accepted
* Date: 2020-09-31

## Context and Problem Statement
While developing the new, unified API, there weren't clear guidelines for structuring new API endpoints. The first approach
was introducing two different endpoint prefixes, similar to what is currently done in a regular shop. On the 30th of April,
we have merged a unification of our endpoints in https://github.com/Sylius/Sylius/pull/11397. This move was dictated by 
the fact that we wanted to push unification even further. Depending on the currently logged in user, we had to expose different
data for available entities in both contexts. Besides, it ought to simplify API usage (as it would reduce the number of endpoints)
and simplify the relations between resources. However, this change rose a lot of new issues to solve:

 * Different serialization for different users is not trivial. Some relations between objects are also contextual, so 
admin can see many more links than the regular visitor or shop user. 
 * Endpoints like "Product Show" are resolved differently depending on the context as well. The shop products are determined 
based on the slug (taking into account the current channel), while the administrator is browsing products by code. This
separation blurs the product identifier, and it is not clear which route should be considered as IRI. 
 * the split was not complete. Two separate log in endpoints have to be preserved, due to our user architecture and the
decision that we want to be explicit, which user is trying to log in.
 
Using one unified identifier in both admin and shop panel is a no go as well. In the shop we should use a `slug`, to be able
to expose product data based on URL (or provide redirect to proper resource based on the channel and locale), while in admin 
resources are channel and locale agnostic, and we should use `codes` instead.

## Decision Drivers

* Driver 1: There is not strict requirement to have a direct 1 to 1 correlation between entities in database and exposed API Resources.
One resource in the database may aggregate several API Resources
* Driver 2: There should be a clear way to determine an IRI for each of exposed API Resources
* Driver 3: A clear separation of available resources and paths for visitor and admin may be expected

## Considered Options

Every option will be described considering products example fetched by shop user and administrator:

### Staying with the current solution

`GET /new-api/products/knitted-wool-blend-green-cap` for Visitor (using product slug) which will return
`{"id": 1, "slug": 2, "prices": {} }`
`GET /new-api/products/KNITTED_WOOL_BLEND_GREEN_CAP` for admin user (using product code) which will return
`{"id": 1, "slug": 2, "prices": {}, "admin_related_information" : "" }`

* Good, because there is only one endpoint for the products.
* Bad, because depending on the currently logged in user, the IRIs between related products (or on the product list) have been
changed (between slug and code).
* Bad, because it is not clear how to operate on endpoint before jumping into documentation. (For example tax rates 
endpoint is not exposed for shop users).
* Bad, because we have to declare authorization rules on endpoint basis, while some of them will have really complicated 
rules (different strategies for admin, shop user and guest).

### Adding additional suffix for path

`GET /new-api/products/knitted-wool-blend-green-cap` for Visitor (using product slug) which will return
`{"id": 1, "slug": 2, "prices": {} }`
`GET /new-api/products/KNITTED_WOOL_BLEND_GREEN_CAP/admin` for admin user (using product code) which will return
`{"id": 1, "slug": 2, "prices": {}, "admin_related_information" : "" }`

* Good, because we can treat two different presentations as two separate resources and generate two different IRIs for both of them.
* Good, because it is clear which endpoint should be used in admin and shop contexts.
* Good, because the authorization of admin and shop users is more straightforward.
* Bad, because there are two endpoints for the products.
* Bad, because it results with n paths with `admin` prefix only, while on others, we should be cautious which path we are using.
* Bad, because we have to declare authorization rules on endpoint basis, while some of them will have really complicated 
rules (different strategies for admin, shop user, and guest)

### Moving back to prefixed paths for back-office and shop 

`GET /new-api/shop/products/knitted-wool-blend-green-cap` for Visitor (using product slug) which will return
`{"id": 1, "slug": 2, "prices": {} }`
`GET /new-api/admin/products/KNITTED_WOOL_BLEND_GREEN_CAP` for admin user (using product code) which will return
`{"id": 1, "slug": 2, "prices": {}, "admin_related_information" : "" }`

* Good, because we can treat two different presentations as two separate resources and generate two different IRIs for both of them.
* Good, because it is clear which endpoint should be used in admin and shop contexts.
* Good, because the authorization of admin and shop users is more straightforward.
* Good, because we can take advantage of Symfony firewalls and hide all admin endpoints behind one firewall. 
* Bad, because there are two endpoints for the products.

## Decision Outcome

Chosen option: "Moving back to prefixed paths for back-office and shop". This option was selected because it allows us to
easily leverage the Symfony Security component's benefits like firewalls and ACL. The only second and third option provides
predictable behavior and clear separation of concerns for admin and shop. Still, suffixes on most of the routes seem like
a worse solution compared to the common prefix. The common prefix will indicate that these endpoints are grouped in the 
same context.
