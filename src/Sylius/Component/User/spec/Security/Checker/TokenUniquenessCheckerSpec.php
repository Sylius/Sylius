<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Security\Checker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Security\Checker\TokenUniquenessChecker;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class TokenUniquenessCheckerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'aRandomToken');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TokenUniquenessChecker::class);
    }

    function it_implements_token_uniqueness_checker_interface()
    {
        $this->shouldImplement(UniquenessCheckerInterface::class);
    }

    function it_returns_true_when_token_is_not_used(RepositoryInterface $repository)
    {
        $repository->findOneBy(['aRandomToken' => 'freeToken'])->willReturn(null);

        $this->isUnique('freeToken')->shouldReturn(true);
    }

    function it_returns_false_when_token_is_in_use(RepositoryInterface $repository)
    {
        $repository->findOneBy(['aRandomToken' => 'takenToken'])->willReturn(Argument::any());

        $this->isUnique('takenToken')->shouldReturn(false);
    }
}
