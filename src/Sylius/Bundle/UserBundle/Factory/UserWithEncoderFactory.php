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

namespace Sylius\Bundle\UserBundle\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

/**
 * @implements FactoryInterface<UserInterface>
 */
final class UserWithEncoderFactory implements FactoryInterface
{
    public function __construct(private FactoryInterface $decoratedUserFactory, private string $encoderName)
    {
    }

    public function createNew(): UserInterface
    {
        $user = $this->decoratedUserFactory->createNew();

        /** @var UserInterface $user */
        Assert::isInstanceOf($user, UserInterface::class);

        $user->setEncoderName($this->encoderName);

        return $user;
    }
}
