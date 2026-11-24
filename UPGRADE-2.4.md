# UPGRADE FROM `2.3` TO `2.4`

## Admin theme switcher

The theme switcher now offers light, dark and auto modes. No action is needed unless you have overridden
`@SyliusAdmin/shared/crud/common/navbar/items/theme_switcher.html.twig` or
`@SyliusAdmin/shared/layout/base/theme.html.twig`. In that case, align your templates with the new ones.

## Admin collapsible sidebar

The admin sidebar can now be collapsed to its icons, from a new button in the navbar, with `Ctrl`/`Cmd`+`B`
or by clicking the edge of the sidebar. The state is kept in the `sidebar_state` cookie.

Rebuild your admin assets to get the new Stimulus controllers and styles. No other action is needed unless you have
overridden one of these templates. In that case, align your templates with the new ones:

* `@SyliusAdmin/shared/crud/common/sidebar.html.twig`: the `<aside>` needs the `admin-sidebar` id,
  the `data-sidebar-state` attribute, the `@sylius/admin-bundle/sidebar` controller with its actions, and the rail button.
* `@SyliusAdmin/shared/crud/common/sidebar/menu/menu.html.twig`: the first-level links have a `title`,
  and items without an icon render their initial.
* `@SyliusAdmin/shared/crud/common/navbar/items.html.twig`: the items have the `ms-auto` class,
  so the product search stays next to the new button.
