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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ChannelGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_channel';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('nameAndDescription', 'asc')
            ->addField(
                TwigField::create('nameAndDescription', '@SyliusAdmin/Channel/Grid/Field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setSortable(true, 'name')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Channel/Grid/Field/name.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/Channel/Grid/Field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setPath('.')
                    ->setSortable(true, 'code')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Channel/Grid/Field/code.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('themeName', '@SyliusAdmin/Channel/Grid/Field/themeName.html.twig')
                    ->setLabel('sylius.ui.theme')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusAdmin/Channel/Grid/Field/themeName.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/enabled.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('search', 'string')
                    ->setLabel('sylius.ui.search')
                    ->setOptions([
                        'fields' => [
                            'code',
                            'name',
                        ],
                    ])
            )
            ->addFilter(
                Filter::create('enabled', 'boolean')
                    ->setLabel('sylius.ui.enabled')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('create', 'create'),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
