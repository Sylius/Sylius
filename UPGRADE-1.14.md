# UPGRADE FROM `v1.13.X` TO `v1.14.0`

### Deprecations

1. The `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension` and `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension` have been deprecated and will be removed in Sylius 2.0.
   Starting with this version, form types will be extended using the parent form instead of through form extensions, like it's done in the `Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScopeType` and `Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionActionType` classes.

1. The class `Sylius\Bundle\CoreBundle\Twig\StateMachineExtension` has been deprecated and will be removed in Sylius 2.0. Use `Sylius\Abstraction\StateMachine\Twig\StateMachineExtension` instead.
1. The class `Sylius\Bundle\CoreBundle\Console\Command\ShowAvailablePluginsCommand` has been deprecated and will be removed in Sylius 2.0.
1. The class `Sylius\Bundle\CoreBundle\Console\Command\Model\PluginInfo` has been deprecated and will be removed in Sylius 2.0.
