<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\DependencyInjection;

use Sylius\Bundle\CoreBundle\Checkout\CheckoutRedirectListener;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutResolver;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGenerator;
use Sylius\Bundle\ShopBundle\Locale\LocaleSwitcherInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusShopExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->configureOrderPay($config['order_pay'], $container);

        $loader->load('services.xml');
        $loader->load(sprintf('services/integrations/locale/%s.xml', $config['locale_switcher']));
        $container->setAlias(LocaleSwitcherInterface::class, 'sylius_shop.locale_switcher');

        if ($container->hasParameter('kernel.bundles')) {
            $bundles = $container->getParameter('kernel.bundles');
            if (array_key_exists('SyliusAdminBundle', $bundles)) {
                $loader->load('services/integrations/sylius_admin.xml');
            }
        }

        $container->setParameter('sylius_shop.firewall_context_name', $config['firewall_context_name']);
        $container->setParameter(
            'sylius_shop.product_grid.include_all_descendants',
            $config['product_grid']['include_all_descendants'],
        );

        $this->configureCheckoutResolverIfNeeded($config['checkout_resolver'], $container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependSyliusThemeBundle($container);
    }

    private function configureCheckoutResolverIfNeeded(array $config, ContainerBuilder $container): void
    {
        if (!$config['enabled']) {
            return;
        }

        $checkoutResolverDefinition = new Definition(
            CheckoutResolver::class,
            [
                new Reference('sylius.context.cart'),
                new Reference('sylius.router.checkout_state'),
                new Definition(PathRequestMatcher::class, [$config['pattern']]),
                new Reference('sylius_abstraction.state_machine'),
            ],
        );
        $checkoutResolverDefinition->addTag('kernel.event_subscriber');

        $checkoutStateUrlGeneratorDefinition = new Definition(
            CheckoutStateUrlGenerator::class,
            [
                new Reference('router'),
                $config['route_map'],
            ],
        );

        $container->setDefinition('sylius.resolver.checkout', $checkoutResolverDefinition);
        $container->setDefinition('sylius.listener.checkout_redirect', $this->registerCheckoutRedirectListener($config));
        $container->setDefinition('sylius.router.checkout_state', $checkoutStateUrlGeneratorDefinition);
    }

    private function registerCheckoutRedirectListener(array $config): Definition
    {
        $checkoutRedirectListener = new Definition(CheckoutRedirectListener::class, [
            new Reference('request_stack'),
            new Reference('sylius.router.checkout_state'),
            new Definition(PathRequestMatcher::class, [$config['pattern']]),
        ]);

        $checkoutRedirectListener
            ->addTag('kernel.event_listener', [
                'event' => 'sylius.order.post_address',
                'method' => 'handleCheckoutRedirect',
            ])
            ->addTag('kernel.event_listener', [
                'event' => 'sylius.order.post_select_shipping',
                'method' => 'handleCheckoutRedirect',
            ])
            ->addTag('kernel.event_listener', [
                'event' => 'sylius.order.post_payment',
                'method' => 'handleCheckoutRedirect',
            ])
        ;

        return $checkoutRedirectListener;
    }

    private function prependSyliusThemeBundle(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('sylius_theme')) {
            return;
        }

        $container->prependExtensionConfig('sylius_theme', ['context' => 'sylius_shop.theme.context.channel_based']);
    }

    private function configureOrderPay(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('sylius_shop.order_pay.payment_request_pay_route', $config['payment_request_pay_route']);
        $container->setParameter('sylius_shop.order_pay.payment_request_pay_route_parameters', $config['payment_request_pay_route_parameters']);
        $container->setParameter('sylius_shop.order_pay.after_pay_route', $config['after_pay_route']);
        $container->setParameter('sylius_shop.order_pay.after_pay_route_parameters', $config['after_pay_route_parameters']);
        $container->setParameter('sylius_shop.order_pay.final_route', $config['final_route']);
        $container->setParameter('sylius_shop.order_pay.final_route_parameters', $config['final_route_parameters']);
        $container->setParameter('sylius_shop.order_pay.retry_route', $config['retry_route']);
        $container->setParameter('sylius_shop.order_pay.retry_route_parameters', $config['retry_route_parameters']);
    }
}
