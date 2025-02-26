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

use Sylius\Bundle\AdminBundle\Form\Type\LocaleType;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.locale.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(LocaleType::class)
    ->withOperations(new Operations(operations: [
        new Create(redirectToRoute: 'sylius_admin_locale_index'),
        new Index(grid: 'sylius_admin_locale'),
        new Delete(),
    ]))
;
