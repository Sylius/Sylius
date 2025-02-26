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
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.admin_user.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(AdminUserType::class)
    ->withOperations(new Operations([
        new Create(path: 'users/new', redirectToRoute: 'sylius_admin_admin_user_index'),
        new Update(path: 'users/{id}/edit', redirectToRoute: 'sylius_admin_admin_user_index'),
        new Delete(path: 'users/{id}/delete'),
        new BulkDelete(path: 'users/bulk_delete'),
        new Index(path: 'users', grid: 'sylius_admin_admin_user'),
    ]))
;
