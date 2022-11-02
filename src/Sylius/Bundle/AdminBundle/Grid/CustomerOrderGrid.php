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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class CustomerOrderGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_customer_order';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createByCustomerIdQueryBuilder', [
                'customerId' => '$id',
            ])
            ->extends('sylius_admin_order')
            ->addOrderBy('number', 'desc')
            ->addField(
                StringField::create('customer')
                    ->setEnabled(false)
            )
            ->addFilter(
                Filter::create('customer', 'string')
                    ->setEnabled(false)
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
