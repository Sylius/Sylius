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

use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AddressLogEntryGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_address_log_entry';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createByObjectIdQueryBuilder', [
                'objectId' => '$id',
            ])
            ->addField(
                TwigField::create('action', '@SyliusUi/Grid/Field/logAction.html.twig')
                    ->setLabel('sylius.ui.action')
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/logAction.html.twig',
                    ])
            )
            ->addField(
                DateTimeField::create('loggedAt')
                    ->setLabel('sylius.ui.logged_at')
                    ->setOptions([
                        'format' => 'd-m-Y H:i:s',
                    ])
            )
            ->addField(
                TwigField::create('data', '@SyliusUi/Grid/Field/logData.html.twig')
                    ->setLabel('sylius.ui.changes')
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/logData.html.twig',
                    ])
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
