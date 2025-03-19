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

namespace Sylius\Bundle\AdminBundle\Twig\Component\ProductOption;

use Sylius\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent]
class FormComponent
{
    use LiveCollectionTrait;

    /** @use ResourceFormComponentTrait<ProductOptionInterface> */
    use ResourceFormComponentTrait {
        initialize as public __construct;
    }

    use TemplatePropTrait;

    #[LiveAction]
    public function applyToAll(#[LiveArg] string $valueKey, #[LiveArg] string $translationKey): void
    {
        $value = $this->formValues['values'][$valueKey]['translations'][$translationKey]['value'];

        foreach ($this->formValues['values'][$valueKey]['translations'] as &$translation) {
            $translation['value'] = $value;
        }
    }
}
