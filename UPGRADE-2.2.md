# UPGRADE FROM `2.1` TO `2.2`

### API Configuration

1. **Selective API Endpoints** - You can now enable/disable admin and shop API endpoints separately for better security and performance.

   New configuration options in `sylius_api`:

   ```yaml
   sylius_api:
       enabled: true
       endpoints:
           admin_enabled: true   # Enable/disable admin API endpoints
           shop_enabled: true    # Enable/disable shop API endpoints
   ```

   **Use Cases**:

   - **Headless shop** (disable admin endpoints for security):
     ```yaml
     sylius_api:
         enabled: true
         endpoints:
             admin_enabled: false
             shop_enabled: true
     ```

   - **Admin-only application**:
     ```yaml
     sylius_api:
         enabled: true
         endpoints:
             admin_enabled: true
             shop_enabled: false
     ```

### Deprecations

1. Direct usage of `loader.svg` and `loader.gif` assets is deprecated.
   Use `@SyliusAdmin/shared/helper/loader.html.twig` or `@SyliusShop/shared/macro/loader.html.twig` instead.
