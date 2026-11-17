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
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.locale.class%', name: self::NAME)]
final class LocaleGrid implements LocaleGridInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setLimits([10, 25, 50])
            ->addOrderBy('code', 'asc')
            ->withFields(
                TwigField::create('name', '@SyliusAdmin/locale/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.'),
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
            )
            ->withFilters(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code'),
            )
            ->withMainActions(
                CreateAction::create(),
            )
            ->withItemActions(
                DeleteAction::create(),
            );
    }
}
