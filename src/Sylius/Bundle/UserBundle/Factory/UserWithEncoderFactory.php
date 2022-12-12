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

/**
 * @deprecated since 1.13 as UserInterface::setEncoderName() is deprecated as well
 */
final class UserWithEncoderFactory implements FactoryInterface
{
    public function __construct(private FactoryInterface $decoratedUserFactory, private string $encoderName)
    {
        trigger_deprecation('sylius/user-bundle', '1.13', 'The "%s" class is deprecated as "%s::setEncoderName()" is deprecated as well', UserWithEncoderFactory::class, UserInterface::class);
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
