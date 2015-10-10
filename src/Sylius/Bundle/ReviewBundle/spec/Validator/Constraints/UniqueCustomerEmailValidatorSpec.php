<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ReviewBundle\Validator\Constraints\UniqueCustomerEmail;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Validator\ExecutionContext;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UniqueCustomerEmailValidatorSpec extends ObjectBehavior
{
    function let(ObjectRepository $customerRepository, ExecutionContext $context)
    {
        $this->beConstructedWith($customerRepository);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Validator\Constraints\UniqueCustomerEmailValidator');
    }

    function it_extends_contraint_validator_class()
    {
        $this->shouldHaveType('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_validates_if_customer_with_given_email_is_already_registered($customerRepository, $context, UniqueCustomerEmail $constraint, ReviewInterface $review, CustomerInterface $customer, CustomerInterface $existingCustomer)
    {
        $review->getAuthor()->willReturn($customer);
        $customer->getEmail()->willReturn('john.doe@example.com');
        $customerRepository->findOneBy(array('email' => 'john.doe@example.com'))->willReturn($existingCustomer)->shouldBeCalled();
        $constraint->message = 'This email is already registered. Please log in.';

        $context->addViolationAt('author', 'This email is already registered. Please log in.', array(), null)->shouldBeCalled();

        $this->validate($review, $constraint);
    }
}
