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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class PickupCartLocaleValidator extends ConstraintValidator
{
    private ChannelRepositoryInterface $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var PickupCart $value */
        Assert::isInstanceOf($value, PickupCart::class);

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($value->getChannelCode());

        $locales = $channel->getLocales();

        $locale = $locales->filter(function (LocaleInterface $locale) use ($value): bool {
            return $locale->getCode() === $value->localeCode;
        });

        if ($locale->isEmpty()) {
            $this->context->addViolation($constraint->notExist, ['%localeCode%' => $value->localeCode]);
        }
    }
}
