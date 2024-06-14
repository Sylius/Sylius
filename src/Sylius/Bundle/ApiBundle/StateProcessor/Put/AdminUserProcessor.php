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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Put;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<AdminUserInterface> */
final readonly class AdminUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private PasswordUpdaterInterface $passwordUpdater,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, AdminUserInterface::class);
        Assert::isInstanceOf($operation, Put::class);

        $this->passwordUpdater->updatePassword($data);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
