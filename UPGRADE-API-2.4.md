# UPGRADE FROM `2.3` TO `2.4`

## Admin orders can be filtered by number

`GET /api/v2/admin/orders` now accepts a `number` query parameter, which matches orders by a partial order number
(e.g. `GET /api/v2/admin/orders?number=000001`). The `number` property is also listed in the `hydra:search` mapping.
The filter uses the `partial` strategy on the `sylius_api.search_filter.admin.order` service; if you have overridden that
configuration, add the property to your version as well.
