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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\MoneyBundle\Twig\ConvertMoneyExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.twig.extension.convert_money', ConvertMoneyExtension::class)
        ->private()
        ->args([service('sylius.converter.currency')])
        ->tag('twig.extension');
};
