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

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\Extend;
use Arkitect\Expression\ForClasses\NotDependsOnTheseNamespaces;
use Arkitect\Expression\ForClasses\NotExtend;
use Arkitect\Expression\ForClasses\NotResideInTheseNamespaces;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\Rules\Rule;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__ . '/');
    $classSet->excludePath('vendor/**');
    $classSet->excludePath('test/**');

    $rules = [];

    /** Filtration rules */
    $rules[] = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle\ApiBundle\Filter\Doctrine'))
        ->should(new Extend(AbstractFilter::class))
        ->because('All ORM Api Platform filters should be placed in one namespace')
    ;

    $rules[] = Rule::allClasses()
        ->that(new NotResideInTheseNamespaces('Sylius\Bundle\ApiBundle\Filter\Doctrine'))
        ->should(new NotExtend(AbstractFilter::class))
        ->because('All ORM Api Platform filters should be placed in one namespace')
    ;

    $rules[] = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Sylius\Bundle\ApiBundle\CommandHandler'))
        ->should(new NotDependsOnTheseNamespaces('Symfony\Component\HttpKernel'))
        ->because('Handlers should be decoupled from any infrastructure layer')
    ;

    $config->add($classSet, ...$rules);
};
