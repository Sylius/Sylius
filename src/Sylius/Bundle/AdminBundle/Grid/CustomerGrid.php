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

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class CustomerGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_customer';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('createdAt', 'desc')
            ->addField(
                StringField::create('firstName')
                    ->setLabel('sylius.ui.first_name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('lastName')
                    ->setLabel('sylius.ui.last_name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('email')
                    ->setLabel('sylius.ui.email')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('sylius.ui.registration_date')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'd-m-Y H:i',
                    ])
            )
            ->addField(
                TwigField::create('enabled', '@SyliusAdmin/Customer/Grid/Field/enabled.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Customer/Grid/Field/enabled.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('verified', '@SyliusAdmin/Customer/Grid/Field/verified.html.twig')
                    ->setLabel('sylius.ui.verified')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Customer/Grid/Field/verified.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('search', 'string')
                    ->setLabel('sylius.ui.search')
                    ->setOptions([
                        'fields' => [
                            'email',
                            'firstName',
                            'lastName',
                        ],
                    ])
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create()
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                    ShowAction::create(),
                    UpdateAction::create(),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
