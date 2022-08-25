<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 */
final class FixtureFactory
{
    public static function boot(ContainerInterface $container): void
    {
        AddressFactory::withModelClass($container->getParameter('sylius.model.address.class'));
        CountryFactory::withModelClass($container->getParameter('sylius.model.country.class'));
        CurrencyFactory::withModelClass($container->getParameter('sylius.model.currency.class'));
        CustomerGroupFactory::withModelClass($container->getParameter('sylius.model.customer_group.class'));
        LocaleFactory::withModelClass($container->getParameter('sylius.model.locale.class'));
        ProductAssociationTypeFactory::withModelClass($container->getParameter('sylius.model.product_association_type.class'));
        ProductAttributeFactory::withModelClass($container->getParameter('sylius.model.product_attribute.class'));
        ShopUserFactory::withModelClass($container->getParameter('sylius.model.shop_user.class'));
    }
}
