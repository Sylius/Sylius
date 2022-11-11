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

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ShipmentGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private string $channelResourceClass,
        private string $shippingMethodResourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_shipment';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder')
            ->addOrderBy('createdAt', 'desc')
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('sylius.ui.created_at')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'd-m-Y H:i:s',
                    ])
            )
            ->addField(
                DateTimeField::create('shippedAt')
                    ->setLabel('sylius.ui.shipped_at')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'd-m-Y H:i:s',
                    ])
            )
            ->addField(
                TwigField::create('number', '@SyliusAdmin/Order/Grid/Field/number.html.twig')
                    ->setLabel('sylius.ui.order')
                    ->setPath('order')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/number.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('channel', '@SyliusAdmin/Order/Grid/Field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setPath('order.channel')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/channel.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/Common/Label/shipmentState.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Common/Label/shipmentState.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('state', 'select')
                    ->setLabel('sylius.ui.state')
                    ->setFormOptions([
                        'choices' => [
                            'sylius.ui.cancelled' => 'cancelled',
                            'sylius.ui.ready' => 'ready',
                            'sylius.ui.shipped' => 'shipped',
                        ],
                    ])
            )
            ->addFilter(
                Filter::create('channel', 'entity')
                    ->setLabel('sylius.ui.channel')
                    ->setOptions([
                        'fields' => [
                            'order.channel',
                        ],
                    ])
                    ->setFormOptions([
                        'class' => $this->channelResourceClass,
                    ])
            )
            ->addFilter(
                Filter::create('method', 'entity')
                    ->setLabel('sylius.ui.shipping_method')
                    ->setFormOptions([
                        'class' => $this->shippingMethodResourceClass,
                    ])
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('ship', 'ship_with_tracking_code'),
                    ShowAction::create(),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
