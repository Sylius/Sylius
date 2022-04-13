<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PickupCartLocale;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class PickupCartLocaleValidatorSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_does_not_add_violation_if_locale_code_exists(
        ExecutionContextInterface $executionContext,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        LocaleInterface $locale
    ): void {
        $constraint = new PickupCartLocale();
        $this->initialize($executionContext);

        $value = new PickupCart('token', 'en_US');
        $value->setChannelCode('code');

        $channelRepository->findOneByCode('code')->willReturn($channel);

        $locale->getCode()->willReturn('en_US');
        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $executionContext->addViolation('sylius.locale.not_exist', ["%localeCode%" => "en_US"])->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_does_not_add_violation_if_locale_code_is_not_set(
        ExecutionContextInterface $executionContext,
        ChannelRepositoryInterface $channelRepository
    ): void {
        $constraint = new PickupCartLocale();
        $this->initialize($executionContext);

        $value = new PickupCart('token', null);
        $value->setChannelCode('code');

        $channelRepository->findOneByCode('code')->shouldNotBeCalled();

        $executionContext->addViolation('sylius.locale.not_exist', ["%localeCode%" => "en_US"])->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_locale_code_does_not_exist(
        ExecutionContextInterface $executionContext,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        LocaleInterface $locale
    ): void {
        $constraint = new PickupCartLocale();
        $this->initialize($executionContext);

        $value = new PickupCart('token', 'en');
        $value->setChannelCode('code');

        $channelRepository->findOneByCode('code')->willReturn($channel);

        $locale->getCode()->willReturn('en_US');
        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));

        $executionContext->addViolation('sylius.locale.not_exist', ["%localeCode%" => "en"])->shouldBeCalled();

        $this->validate($value, $constraint);
    }
}
