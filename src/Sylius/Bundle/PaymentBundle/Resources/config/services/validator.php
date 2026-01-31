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

use Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayFactoryExistsValidator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.validator.gateway_factory_exists', GatewayFactoryExistsValidator::class)
        ->args(['%sylius.gateway_factories%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_gateway_factory_exists_validator']);
};
