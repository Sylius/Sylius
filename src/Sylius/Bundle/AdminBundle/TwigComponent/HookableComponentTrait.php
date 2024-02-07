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

namespace Sylius\Bundle\AdminBundle\TwigComponent;

use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

trait HookableComponentTrait
{
    #[LiveProp(fieldName: 'hookable_data')]
    #[ExposeInTemplate(name: 'hookable_data')]
    public mixed $hookableData = null;

    #[LiveProp(fieldName: 'hookable_configuration')]
    #[ExposeInTemplate(name: 'hookable_configuration')]
    public mixed $hookableConfiguration = null;
}
