<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 */
final class FixtureFactoryBooter
{
    public static function boot(ContainerInterface $container): void
    {
        AddressFactory::withModelClass((string) $container->getParameter('sylius.model.address.class'));
        CountryFactory::withModelClass((string) $container->getParameter('sylius.model.country.class'));
        CurrencyFactory::withModelClass((string) $container->getParameter('sylius.model.currency.class'));
        CustomerGroupFactory::withModelClass((string) $container->getParameter('sylius.model.customer_group.class'));
        LocaleFactory::withModelClass((string) $container->getParameter('sylius.model.locale.class'));
        ProductAssociationTypeFactory::withModelClass((string) $container->getParameter('sylius.model.product_association_type.class'));
        ProductAttributeFactory::withModelClass((string) $container->getParameter('sylius.model.product_attribute.class'));
        ProductOptionFactory::withModelClass((string) $container->getParameter('sylius.model.product_option.class'));
        ShippingCategoryFactory::withModelClass((string) $container->getParameter('sylius.model.shipping_category.class'));
        ShopUserFactory::withModelClass((string) $container->getParameter('sylius.model.shop_user.class'));
        TaxCategoryFactory::withModelClass((string) $container->getParameter('sylius.model.tax_category.class'));
        TaxonFactory::withModelClass((string) $container->getParameter('sylius.model.taxon.class'));
        ZoneFactory::withModelClass((string) $container->getParameter('sylius.model.zone.class'));
        ZoneMemberFactory::withModelClass((string) $container->getParameter('sylius.model.zone_member.class'));
    }
}
