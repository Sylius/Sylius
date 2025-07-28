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

use Sylius\Bundle\AdminBundle\Form\Type\PromotionType;
use Sylius\Bundle\ResourceBundle\Form\Type\ArchivableType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.promotion.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(PromotionType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_promotion')")
    ->withOperations(new Operations(operations: [
        new Create(
            routeName: '_sylius_admin_promotion_create',
            redirectToRoute: 'sylius_admin_promotion_update',
        ),
        new Update(
            routeName: '_sylius_admin_promotion_update',
            redirectToRoute: 'sylius_admin_promotion_update',
        ),
        new Delete(
            routeName: '_sylius_admin_promotion_delete',
            redirectToRoute: 'sylius_admin_promotion_index',
        ),
        new BulkDelete(
            routeName: '_sylius_admin_promotion_bulk_delete',
            redirectToRoute: 'sylius_admin_promotion_index',
        ),
        new Index(
            routeName: '_sylius_admin_promotion_index',
            grid: 'sylius_admin_promotion',
        ),
        new Update(
            methods: ['PATCH', 'POST'],
            routeName: '_sylius_admin_promotion_archive',
            shortName: 'archive',
            formType: ArchivableType::class,
            notificationMessage: 'sylius.resource.update',
            redirectToRoute: 'sylius_admin_promotion_index',
            redirectArguments: [
                'criteria' => [
                    'archival' => 0,
                ],
            ],
        )
    ]))
;
