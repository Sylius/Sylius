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
    #[LiveProp(fieldName: 'parent_main_hook')]
    #[ExposeInTemplate(name: 'parent_main_hook')]
    public ?string $parentMainHook = null;

    #[LiveProp(fieldName: 'parent_fallback_hook')]
    #[ExposeInTemplate(name: 'parent_fallback_hook')]
    public ?string $parentFallbackHook = null;

    #[LiveProp(fieldName: 'hookable_configuration')]
    #[ExposeInTemplate(name: 'hookable_configuration')]
    public mixed $hookableConfiguration = null;
}
