<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\Extend;
use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\IsNotAbstract;
use Arkitect\Expression\ForClasses\IsFinal;
use Arkitect\Expression\ForClasses\NotDependsOnTheseNamespaces;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\Rule;
use PhpSpec\ObjectBehavior;

return static function (Config $config): void
{
    $specClassSet = ClassSet::fromDir(__DIR__ . '/src/Sylius/{Behat,Component/*,Bundle/*}/spec');

    $config->add(
        $specClassSet,
        Rule::allClasses()
            ->that(new Extend(ObjectBehavior::class))
            ->should(new HaveNameMatching('*Spec'))
            ->because('Specifications should follow PHPSpec naming convention')
        ,
        Rule::allClasses()
            ->that(new Extend(ObjectBehavior::class))
            ->andThat(new IsNotAbstract())
            ->should(new IsFinal())
            ->because('Specifications should not be extendable')
        ,
    );

    $testsClassSet = ClassSet::fromDir(__DIR__ . '{/tests,/src/Sylius/Bundle/*/Tests}');

    $config->add(
        $testsClassSet,
        Rule::allClasses()
            ->that(new HaveNameMatching('*Test$'))
            ->should(new IsFinal())
            ->because('Tests should not be extendable')
        ,
    );

    $separationClassSet = ClassSet::fromDir(__DIR__ . '/src/Sylius/{Component,Bundle}');

    $config->add(
        $separationClassSet,
        Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Component'))
            ->should(new NotDependsOnTheseNamespaces('Sylius\Bundle'))
            ->because('Components should not depend on bundles')
        ,
        Rule::allClasses()
            ->except('Sylius\Component\Core')
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Component'))
            ->should(new NotDependsOnTheseNamespaces('Sylius\Component\Core'))
            ->because('Stand-alone components should not depend on Core')
        ,
        Rule::allClasses()
            ->except(
                'Sylius\Bundle\AdminBundle',
                'Sylius\Bundle\ApiBundle',
                'Sylius\Bundle\CoreBundle',
                'Sylius\Bundle\PayumBundle',
                'Sylius\Bundle\ShopBundle',
            )
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle'))
            ->should(new NotDependsOnTheseNamespaces('Sylius\Component\Core'))
            ->because('Stand-alone bundles should not depend on Core')
        ,
        Rule::allClasses()
            ->except(
                'Sylius\Bundle\AdminBundle',
                'Sylius\Bundle\ApiBundle',
                'Sylius\Bundle\CoreBundle',
                'Sylius\Bundle\ShopBundle',
            )
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle'))
            ->should(new NotDependsOnTheseNamespaces('Sylius\Bundle\CoreBundle'))
            ->because('Stand-alone bundles should not depend on CoreBundle')
        ,
        Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle\ShopBundle'))
            ->should(new NotDependsOnTheseNamespaces(
                'Sylius\Bundle\AdminBundle',
                'Sylius\Bundle\ApiBundle',
            ))
            ->because('Shop should not depend on Admin and Api')
        ,
        Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle\AdminBundle'))
            ->should(new NotDependsOnTheseNamespaces(
                'Sylius\Bundle\ApiBundle',
                'Sylius\Bundle\ShopBundle',
            ))
            ->because('Admin should not depend on Shop and Api')
        ,
        Rule::allClasses()
            ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle\ApiBundle'))
            ->should(new NotDependsOnTheseNamespaces(
                'Sylius\Bundle\AdminBundle',
                'Sylius\Bundle\ShopBundle',
            ))
            ->because('Api should not depend on Admin and Shop')
        ,
    );
};
