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
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ProductFromTaxonGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product_from_taxon';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->extends('sylius_admin_product')
            ->addOrderBy('position', 'asc')
            ->addField(
                TwigField::create('position', '@SyliusAdmin/Product/Grid/Field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setPath('.')
                    ->setSortable(true, 'productTaxon.position')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Product/Grid/Field/position.html.twig',
                    ])
            )
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('update_positions', 'update_product_positions'),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
