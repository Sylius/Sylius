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

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class CountryGrid implements CountryGridInterface
{
    public function __construct(
        private readonly string $countryClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->countryClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('code', 'asc')

            // -- Field
            ->addField(
                TwigField::create('name', '@SyliusAdmin/country/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setSortable(true, 'code'),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )

            // -- Filter
            ->addFilter(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code'),
            )
            ->addFilter(
                Filter::create('enabled', 'boolean')
                    ->setLabel('sylius.ui.enabled'),
            )

            // -- Actions
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create(),
                ),
            );
    }
}
