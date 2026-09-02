# UPGRADE FROM `v1.14.19` TO `v1.14.20`

### JWTs are now bound to the API section they have been issued for

Tokens are stamped with an `aud` claim (`sylius-api-admin` or `sylius-api-shop`) and a
`principal_type` claim, and they are rejected by the other API section. This closes the case where
an administrator and a shop customer share an e-mail address, in which a shop token was accepted by
the admin API and resolved to that administrator.

The expectations are keyed by firewall name, `new_api_admin_user` and `new_api_shop_user` by default, and are
configured under `sylius_api.jwt.firewall_expectations`. If your application renamed either firewall, or serves
the API through an additional JWT firewall (e.g. a marketplace vendor API), add an entry for it, otherwise the
tokens of that firewall are rejected:

```yaml
sylius_api:
    jwt:
        firewall_expectations:
            new_api_admin_user:
                audience: sylius-api-admin
                principal: Sylius\Component\Core\Model\AdminUserInterface
            new_api_shop_user:
                audience: sylius-api-shop
                principal: Sylius\Component\Core\Model\ShopUserInterface
            new_api_vendor_user:
                audience: sylius-api-vendor
                principal: App\Entity\VendorUserInterface
```

This map is replaced, not merged, when configured — keep the built-in `new_api_admin_user`/`new_api_shop_user`
entries alongside your own. Configuring an entry here only affects JWT validation; the referenced firewall must
still be defined in `security.yaml`, and the `principal` must be an existing class or interface implemented by
the corresponding user.


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
