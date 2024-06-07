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

namespace Sylius\Bundle\CoreBundle;

use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
use Doctrine\Inflector\Rules\Word;
use Doctrine\ORM\Query;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility\CancelOrderStateMachineCallbackPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility\ResolveShopUserTargetEntityPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility\Symfony5AuthenticationManagerPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility\Symfony6PrivateServicesPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\CheckStatisticsOrdersTotalsProviderTypePass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\CircularDependencyBreakingErrorListenerPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\IgnoreAnnotationsPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\LazyCacheWarmupPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\LiipImageFiltersPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\OverrideResourceControllerStateMachinePass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterTaxCalculationStrategiesPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterUriBasedSectionResolverPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\SyliusPriceHistoryLegacyAliasesPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\TranslatableEntityLocalePass;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\SqlWalker\OrderByIdentifierSqlWalker;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\Metadata;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusCoreBundle extends AbstractResourceBundle
{
    public const VERSION = '1.13.3-DEV';

    public const VERSION_ID = '11303';

    public const MAJOR_VERSION = '1';

    public const MINOR_VERSION = '13';

    public const RELEASE_VERSION = '3';

    public const EXTRA_VERSION = 'DEV';

    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function boot(): void
    {
        parent::boot();

        if ($this->container->getParameter('sylius_core.order_by_identifier')) {
            $this->setDefaultOutputWalker(OrderByIdentifierSqlWalker::class);
        }

        $factory = InflectorFactory::create();
        $factory->withPluralRules(new Ruleset(
            new Transformations(),
            new Patterns(),
            new Substitutions(new Substitution(new Word('taxon'), new Word('taxons'))),
        ));
        $inflector = $factory->build();

        Metadata::setInflector($inflector);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CircularDependencyBreakingErrorListenerPass());
        $container->addCompilerPass(new IgnoreAnnotationsPass());
        $container->addCompilerPass(new LazyCacheWarmupPass());
        $container->addCompilerPass(new LiipImageFiltersPass());
        $container->addCompilerPass(new RegisterTaxCalculationStrategiesPass());
        $container->addCompilerPass(new RegisterUriBasedSectionResolverPass());
        $container->addCompilerPass(new ResolveShopUserTargetEntityPass());
        $container->addCompilerPass(new Symfony5AuthenticationManagerPass());
        $container->addCompilerPass(new Symfony6PrivateServicesPass());
        $container->addCompilerPass(new TranslatableEntityLocalePass());
        $container->addCompilerPass(new CancelOrderStateMachineCallbackPass());
        $container->addCompilerPass(new SyliusPriceHistoryLegacyAliasesPass());
        $container->addCompilerPass(new CheckStatisticsOrdersTotalsProviderTypePass());
        $container->addCompilerPass(new OverrideResourceControllerStateMachinePass(), priority: -1024);
    }

    protected function getModelNamespace(): string
    {
        return 'Sylius\Component\Core\Model';
    }

    private function setDefaultOutputWalker(string $outputWalkerClass): void
    {
        $this->container
            ->get('doctrine.orm.entity_manager')
            ->getConfiguration()
            ->setDefaultQueryHint(
                Query::HINT_CUSTOM_TREE_WALKERS,
                [$outputWalkerClass],
            )
        ;
    }
}
