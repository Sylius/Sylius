<?php

namespace spec\Sylius\Bundle\CoreBundle\Generator;

use \PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Sylius\Bundle\CoreBundle\Repository\UserRepositoryInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CustomerNumberGeneratorSpec extends ObjectBehavior
{
    public function let(UserRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Generator\CustomerNumberGenerator');
    }

    function it_implements_Sylius_customer_number_generator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Generator\CustomerNumberGeneratorInterface');
    }

    function it_generates_000000001_number_for_first_order(UserInterface $user, $repository)
    {
        $user->getNumber()->willReturn(null);

        $repository->findLastCreated()->willReturn(null);
        $user->setNumber('000000001')->shouldBeCalled();

        $this->generate($user);
    }

    function it_generates_a_correct_number_for_following_orders($repository, UserInterface $user, UserInterface $lastUser)
    {
        $user->getNumber()->willReturn(null);

        $repository->findLastCreated()->willReturn($lastUser);
        $lastUser->getNumber()->willReturn('000000469');
        $user->setNumber('000000470')->shouldBeCalled();

        $this->generate($user);
    }

    function it_starts_at_start_number_if_specified($repository, UserInterface $user)
    {
        $this->beConstructedWith($repository, 9, 123);
        $user->getNumber()->willReturn(null);
        $user->setNumber('000000123')->shouldBeCalled();

        $this->generate($user);
    }

    function it_leaves_existing_numbers_alone(UserInterface $user)
    {
        $user->getNumber()->willReturn('123');
        $user->setNumber()->shouldNotBeCalled();

        $this->generate($user);
    }
}
