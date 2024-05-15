# UPGRADE FROM `v1.13.X` TO `v1.14.0`

### Deprecations

1. The `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension` and `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension` have been deprecated and will be removed in Sylius 2.0.
   Starting with this version, form types will be extended using the parent form instead of through form extensions.
