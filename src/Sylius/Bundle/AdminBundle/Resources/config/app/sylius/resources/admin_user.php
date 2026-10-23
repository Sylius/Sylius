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

use Sylius\Bundle\AdminBundle\Form\Type\AdminUserType;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Update;

return (new ResourceMetadata())
    ->withRoutePrefix('/%sylius_admin.path_name%')
    ->withClass('%sylius.model.admin_user.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin/shared/crud')
    ->withFormType(AdminUserType::class)
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('admin_admin_user')")
    ->withOperations(new Operations([
        new Create(
            path: 'users/new',
            routeName: '_sylius_admin_admin_user_create',
            redirectToRoute: 'sylius_admin_admin_user_index',
        ),
        new Update(
            path: 'users/{id}/edit',
            routeName: '_sylius_admin_admin_user_update',
            redirectToRoute: 'sylius_admin_admin_user_index',
        ),
        new Delete(
            path: 'users/{id}/delete',
            routeName: '_sylius_admin_admin_user_delete',
            redirectToRoute: 'sylius_admin_admin_user_index',
        ),
        new BulkDelete(
            path: 'users/bulk-delete',
            routeName: '_sylius_admin_admin_user_bulk_delete',
            redirectToRoute: 'sylius_admin_admin_user_index',
        ),
        new Index(
            path: 'users',
            routeName: '_sylius_admin_admin_user_index',
            grid: 'sylius_admin_admin_user',
        ),
    ]))
;
