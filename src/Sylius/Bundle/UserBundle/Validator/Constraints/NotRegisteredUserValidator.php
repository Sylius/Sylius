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

use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class NotRegisteredUserValidator extends ConstraintValidator
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($email, Constraint $constraint)
    {
        $existingCustomer = $this->userRepository->findOneByEmail($email);
        if (null === $existingCustomer) {
            $this->context->addViolationAt(
                'email',
                $constraint->message,
                array(),
                null
            );
        }
    }
}
