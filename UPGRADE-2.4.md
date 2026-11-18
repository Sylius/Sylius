# UPGRADE FROM `2.3` TO `2.4`

## Admin

1. Admin grids now display a "Columns" selector allowing administrators to hide and show columns.

   The chosen visibility is kept in the `visibility` query parameter (e.g. `?visibility[total]=hidden`),
   and fields can be hidden by default with the new `visible` grid field option:

   ```php
   StringField::create('currencyCode')
       ->setVisible(false)
   ```

   ```yaml
   sylius_grid:
       grids:
           sylius_admin_order:
               fields:
                   currencyCode:
                       visible: false
   ```

   `visible: false` only sets the default visibility: administrators can still display the column.
   Use `enabled: false` to remove a field from the grid.

   The selector is rendered by the new `sylius_admin.common.index.content.grid.data_table#toolbar` hook,
   so it can be disabled for all grids or for a given resource:

   ```yaml
   sylius_twig_hooks:
       hooks:
           'sylius_admin.order.index.content.grid.data_table#toolbar':
               column_selector:
                   enabled: false
   ```

   It requires `sylius/grid-bundle` with `Sylius\Component\Grid\View\GridView::isFieldVisible()`.

   The `headers` and `row` macros of `@SyliusAdmin/shared/helper/table.html.twig` now call `grid.isFieldVisible()`,
   so the `grid` argument passed to them must be a `GridView` (or implement `isFieldVisible()`).
