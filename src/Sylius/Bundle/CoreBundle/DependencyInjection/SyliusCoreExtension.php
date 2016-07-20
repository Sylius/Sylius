<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusCoreExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $bundles = [
        'sylius_addressing',
        'sylius_api',
        'sylius_attribute',
        'sylius_channel',
        'sylius_contact',
        'sylius_currency',
        'sylius_inventory',
        'sylius_locale',
        'sylius_order',
        'sylius_payment',
        'sylius_payum',
        'sylius_product',
        'sylius_promotion',
        'sylius_review',
        'sylius_report',
        'sylius_sequence',
        'sylius_settings',
        'sylius_shipping',
        'sylius_mailer',
        'sylius_taxation',
        'sylius_taxonomy',
        'sylius_user',
        'sylius_variation',
        'sylius_rbac',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
            'controller.xml',
            'context.xml',
            'form.xml',
            'api_form.xml',
            'templating.xml',
            'twig.xml',
            'reports.xml',
            'state_machine.xml',
            'email.xml',
            'metadata.xml',
            'sitemap.xml',
            'dashboard.xml',
        ];

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $configFiles[] = 'test_services.xml';
        }

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $definition = $container->findDefinition('sylius.context.currency');
        $definition->replaceArgument(0, new Reference($config['currency_storage']));

        $this->overwriteRuleFactory($container);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, $this->bundles)) {
                $container->prependExtensionConfig($name, ['driver' => $config['driver']]);
            }
        }

        $container->prependExtensionConfig('sylius_theme', ['context' => 'sylius.theme.context.channel_based']);

        $container->setParameter('sylius.sitemap', $config['sitemap']);
        $container->setParameter('sylius.sitemap_template', $config['sitemap']['template']);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function overwriteRuleFactory(ContainerBuilder $container)
    {
        $baseFactoryDefinition = new Definition(Factory::class, [new Parameter('sylius.model.promotion_rule.class')]);
        $promotionRuleFactoryClass = $container->getParameter('sylius.factory.promotion_rule.class');
        $decoratedPromotionRuleFactoryDefinition = new Definition($promotionRuleFactoryClass, [$baseFactoryDefinition]);

        $container->setDefinition('sylius.factory.promotion_rule', $decoratedPromotionRuleFactoryDefinition);
    }
}
