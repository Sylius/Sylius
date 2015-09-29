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
use Sylius\Component\User\Context\CustomerContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisteredUserValidator extends ConstraintValidator
{
    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param RepositoryInterface      $customerRepository
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(RepositoryInterface $customerRepository, CustomerContextInterface $customerContext)
    {
        $this->customerRepository = $customerRepository;
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($customer, Constraint $constraint)
    {
        $existingCustomer = $this->customerRepository->findOneBy(array('email' => $customer->getEmail()));
        if (null !== $existingCustomer && null !== $existingCustomer->getUser() && $this->customerContext->getCustomer() !== $customer) {
            $this->context->addViolationAt(
                'email',
                $constraint->message,
                array(),
                null
            );
        }
    }
}
