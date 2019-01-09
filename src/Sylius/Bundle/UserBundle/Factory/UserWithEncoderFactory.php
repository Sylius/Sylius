<?php

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;

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

        $user->setEncoderName($this->encoderName);

        return $user;
    }
}
