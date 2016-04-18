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
use Doctrine\ORM\Query\FilterCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Sylius\Component\User\Security\TokenProviderInterface;

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
        $this->shouldImplement(TokenProviderInterface::class);
    }

    public function it_generates_random_token($repository, $generator)
    {
        $repository->findOneBy(Argument::any())->willReturn(null);

        $generator->generate(12)->shouldBeCalled()->willReturn('tesToken1234');

        $this->generateUniqueToken()->shouldReturn('tesToken1234');
    }

    public function it_generates_unique_random_token($repository, $generator, UserInterface $user)
    {
        $repository->findOneBy(['confirmationToken' => 'tesToken1234'])->willReturn($user);
        $repository->findOneBy(['confirmationToken' => 'tesToken1235'])->willReturn(null);

        $generator->generate(12)->shouldBeCalled()->willReturn('tesToken1234', 'tesToken1235');

        $this->generateUniqueToken()->shouldReturn('tesToken1235');
    }
}
