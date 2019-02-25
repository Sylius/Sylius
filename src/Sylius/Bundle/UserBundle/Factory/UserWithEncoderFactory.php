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

namespace Sylius\Bundle\UserBundle\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Webmozart\Assert\Assert;

final class UserWithEncoderFactory implements FactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedUserFactory;

    /** @var string */
    private $encoderName;

    public function __construct(FactoryInterface $decoratedUserFactory, string $encoderName)
    {
        $this->decoratedUserFactory = $decoratedUserFactory;
        $this->encoderName = $encoderName;
    }

    public function createNew(): UserInterface
    {
        /** @var UserInterface $user */
        $user = $this->decoratedUserFactory->createNew();
        Assert::isInstanceOf($user, UserInterface::class);

        $user->setEncoderName($this->encoderName);

        return $user;
    }
}
