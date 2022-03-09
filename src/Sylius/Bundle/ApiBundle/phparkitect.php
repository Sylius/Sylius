<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\Extend;
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
        ->should(new Extend(AbstractContextAwareFilter::class))
        ->because('All ORM Api Platform filters should be placed in one namespace')
    ;

    $rules[] = Rule::allClasses()
        ->that(new NotResideInTheseNamespaces('Sylius\Bundle\ApiBundle\Filter\Doctrine'))
        ->should(new NotExtend(AbstractContextAwareFilter::class))
        ->because('All ORM Api Platform filters should be placed in one namespace')
    ;

    $config->add($classSet, ...$rules);
};
