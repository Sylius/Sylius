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

use Sylius\Bundle\AdminBundle\Form\Type\CurrencyType;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;

return (new ResourceMetadata())
    ->withRoutePrefix('/admin')
    ->withClass('%sylius.model.currency.class%')
    ->withSection('admin')
    ->withTemplatesDir('@SyliusAdmin\\shared\\crud')
    ->withFormType(CurrencyType::class)
    ->withOperations(new Operations(operations: [
        new Create(redirectToRoute: 'sylius_admin_currency_create'),
        new Index(grid: 'sylius_admin_currency'),
    ]))
;
