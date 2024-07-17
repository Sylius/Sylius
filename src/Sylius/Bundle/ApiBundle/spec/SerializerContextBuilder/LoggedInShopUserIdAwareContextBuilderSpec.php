<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class LoggedInShopUserIdAwareContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $userContext);
    }

    function it_sets_shop_user_id_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => RequestShopUserVerification::class]])
        ;

        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getId()->willReturn(11);

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn([
                'input' => ['class' => RequestShopUserVerification::class],
                AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                    RequestShopUserVerification::class => ['shopUserId' => 11],
                ],
            ])
        ;
    }

    function it_does_nothing_if_there_is_no_input_class(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn([])
        ;

        $userContext->getUser()->shouldNotBeCalled();

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_does_nothing_if_input_class_is_no_channel_aware(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $userContext->getUser()->shouldNotBeCalled();

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn(['input' => ['class' => \stdClass::class]])
        ;
    }

    function it_does_nothing_if_there_is_no_logged_in_shop_user(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $userContext->getUser()->willReturn(null);

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn(['input' => ['class' => \stdClass::class]])
        ;
    }
}
