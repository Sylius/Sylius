# UPGRADE FROM `v1.13.X` TO `v1.14.0`

1. The following old parameters have been deprecated and will be removed in Sylius 2.0. Use the corresponding new parameters instead:

   | Old parameter                                | New parameter                            |
   |----------------------------------------------|------------------------------------------|
   | `sylius.security.new_api_route`              | `sylius.security.api_route`              |
   | `sylius.security.new_api_regex`              | `sylius.security.api_regex`              |
   | `sylius.security.new_api_admin_route`        | `sylius.security.api_admin_route`        |
   | `sylius.security.new_api_admin_regex`        | `sylius.security.api_admin_regex`        |
   | `sylius.security.new_api_shop_route`         | `sylius.security.api_shop_route`         |
   | `sylius.security.new_api_shop_regex`         | `sylius.security.api_shop_regex`         |
   | `sylius.security.new_api_user_account_route` | `sylius.security.api_shop_account_route` |
   | `sylius.security.new_api_user_account_regex` | `sylius.security.api_shop_account_regex` |

1. The following configuration parameters have been deprecated and will be removed in 2.0:

   - `sylius_api.legacy_error_handling`
   - `sylius_api.serialization_groups.skip_adding_read_group`
   - `sylius_api.serialization_groups.skip_adding_index_and_show_groups`
