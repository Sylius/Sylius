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

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.channel_pricing_log_entry.class%', name: self::NAME)]
final class ChannelPricingLogEntryGrid implements ChannelPricingLogEntryGridInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createByChannelPricingIdListQueryBuilder', [
                '$channelPricingId',
            ])
            ->setLimits([10, 25, 50])

            ->withFields(
                TwigField::create('price', '@SyliusAdmin/channel_pricing_log_entry/grid/field/price.html.twig')
                    ->setLabel('sylius.ui.price')
                    ->setPath('.'),
                TwigField::create('originalPrice', '@SyliusAdmin/channel_pricing_log_entry/grid/field/original_price.html.twig')
                    ->setLabel('sylius.ui.original_price')
                    ->setPath('.'),
                DateTimeField::create('loggedAt')
                    ->setLabel('sylius.ui.logged_at'),
            )

            ->withFilters(
                DateFilter::create('loggedAt')
                    ->setLabel('sylius.ui.logged_at'),
            )
        ;
    }
}
