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
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class CustomerOrderGrid implements CustomerOrderGridInterface
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
            ->withFields(
                StringField::create('customer')
                    ->setEnabled(false),
            )
            ->withFilters(
                Filter::create('customer', 'string')
                    ->setEnabled(false),
            );
    }
}
