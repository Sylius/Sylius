<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Validator\Constraints\NotRegisteredUser;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class NotRegisteredUserValidatorSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, ExecutionContextInterface $context)
    {
        $this->beConstructedWith($userRepository);
        $this->initialize($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Validator\Constraints\NotRegisteredUserValidator');
    }

    function it_extends_symfony_constraint_validator()
    {
        $this->shouldHaveType('Symfony\Component\Validator\ConstraintValidator');
    }

    function it_add_violation_if_user_with_given_mail_does_not_exist($userRepository, $context, NotRegisteredUser $notRegisteredUserConstraint)
    {
        $userRepository->findOneByEmail('not_registered@user.com')->willReturn(null);
        $notRegisteredUserConstraint->message = 'This email is not registered. Please register first.';

        $context->addViolationAt('email', 'This email is not registered. Please register first.', array(), null)->shouldBeCalled();

        $this->validate('not_registered@user.com', $notRegisteredUserConstraint);
    }

    function it_does_nothing_if_user_with_given_mail_exists($userRepository, $context, NotRegisteredUser $notRegisteredUserConstraint, UserInterface $user)
    {
        $userRepository->findOneByEmail('registered@user.com')->willReturn($user);

        $context->addViolationAt(Argument::any())->shouldNotBeCalled();

        $this->validate('registered@user.com', $notRegisteredUserConstraint);
    }
}
