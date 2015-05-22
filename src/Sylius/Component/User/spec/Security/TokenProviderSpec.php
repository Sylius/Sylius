<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Security;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\Query\FilterCollection;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class TokenProviderSpec extends ObjectBehavior
{
    public function let(RepositoryInterface $repository, EntityManagerInterface $manager, GeneratorInterface $generator)
    {
        $this->beConstructedWith($repository, $manager, $generator, 12);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Security\TokenProvider');
    }

    public function it_implements_token_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Security\TokenProviderInterface');
    }

    public function it_generates_random_token($repository, $manager, FilterCollection $filter, $generator)
    {
        $manager->getFilters()->willReturn($filter);

        $filter->disable('softdeleteable')->shouldBeCalled();
        $filter->enable('softdeleteable')->shouldBeCalled();

        $repository->findOneBy(Argument::any())->willReturn(null);

        $generator->generate(12)->shouldBeCalled()->willReturn('tesToken1234');

        $this->generateUniqueToken()->shouldReturn('tesToken1234');
    }

    public function it_generates_unique_random_token($repository, $manager, $generator, FilterCollection $filter, UserInterface $user)
    {
        $manager->getFilters()->willReturn($filter);

        $filter->disable('softdeleteable')->shouldBeCalled();
        $filter->enable('softdeleteable')->shouldBeCalled();

        $repository->findOneBy(array('confirmationToken' => 'tesToken1234'))->willReturn($user);
        $repository->findOneBy(array('confirmationToken' => 'tesToken1235'))->willReturn(null);

        $generator->generate(12)->shouldBeCalled()->willReturn('tesToken1234', 'tesToken1235');

        $this->generateUniqueToken()->shouldReturn('tesToken1235');
    }
}
