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

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: 'sylius_admin_customer_order')]
final class CustomerOrderGrid extends AbstractGrid implements CustomerOrderGridInterface
{
    public function __construct(
        private readonly string $orderClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->orderClass)
            ->setRepositoryMethod('createByCustomerIdCriteriaAwareQueryBuilder', [
                'criteria' => null,
                'customerId' => '$id',
            ])
            ->extends('sylius_admin_order')
            ->addOrderBy('number', 'desc')
            ->addField(
                StringField::create('customer')
                    ->setEnabled(false),
            )
            ->addFilter(
                Filter::create('customer', 'string')
                    ->setEnabled(false),
            );
    }
}
