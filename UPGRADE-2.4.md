# UPGRADE FROM `2.3` TO `2.4`

## Admin theme switcher

The theme switcher now offers light, dark and auto modes. No action is needed unless you have overridden
`@SyliusAdmin/shared/crud/common/navbar/items/theme_switcher.html.twig` or
`@SyliusAdmin/shared/layout/base/theme.html.twig`. In that case, align your templates with the new ones.
