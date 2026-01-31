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

use Sylius\Bundle\PaymentBundle\Console\Command\GenerateEncryptionKeyCommand;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.console.command.generate_encryption_key', GenerateEncryptionKeyCommand::class)
        ->args([
            service('filesystem'),
            '%env(resolve:SYLIUS_PAYMENT_ENCRYPTION_KEY_PATH)%',
        ])
        ->tag('console.command');
};
