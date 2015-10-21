<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Validator\Constraints;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class WrongCurrentPasswordValidator extends ConstraintValidator
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @param TokenStorageInterface   $tokenStorage
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(TokenStorageInterface $tokenStorage, EncoderFactoryInterface $encoderFactory)
    {
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($password, Constraint $constraint)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $encoder = $this->encoderFactory->getEncoder($user);
        if (!$encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            $this->context->addViolationAt(
                'currentPassword',
                $constraint->message,
                array(),
                null
            );
        }
    }
}
