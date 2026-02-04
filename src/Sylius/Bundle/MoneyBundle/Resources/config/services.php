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

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatter;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\MoneyBundle\Twig\FormatMoneyExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.form.type.money', MoneyType::class)
        ->tag('form.type');

    $services->set('sylius.formatter.money', MoneyFormatter::class);

    $services->alias(MoneyFormatterInterface::class, 'sylius.formatter.money');

    $services->set('sylius.twig.extension.format_money', FormatMoneyExtension::class)
        ->args([service('sylius.formatter.money')])
        ->tag('twig.extension');
};
