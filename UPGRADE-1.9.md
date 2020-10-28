# UPGRADE FROM `v1.8.X` TO `v1.9.0`

1. Add new parameters, new access control configuration and reorder it:

    ```diff
        parameters:
    +       sylius.security.new_api_user_account_route: "%sylius.security.new_api_shop_route%/account"
    +       sylius.security.new_api_user_account_regex: "^%sylius.security.new_api_user_account_route%"

        security:
            access_control:
    +           - { path: "%sylius.security.new_api_user_account_regex%/.*", role: ROLE_USER }
    -           - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
                - { path: "%sylius.security.new_api_route%/shop/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
    +           - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
        ```
