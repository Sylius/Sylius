<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class UniqueCustomerEmailValidator extends ConstraintValidator
{
    /**
     * @var EntityRepository
     */
    private $customerRepository;

    /**
     * @param ObjectRepository $customerRepository
     */
    public function __construct(ObjectRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($review, Constraint $constraint)
    {
        $customer = $review->getAuthor();
        if (null !== $customer && null !== $this->customerRepository->findOneBy(array('email' => $customer->getEmail()))) {
            $this->context->addViolationAt(
                'author',
                $constraint->message,
                array(),
                null
            );
        }
    }
}
