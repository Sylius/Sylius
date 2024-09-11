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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Promotion;

use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TypedLiveCollectionTrait;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
class FormComponent
{
    /** @use ResourceFormComponentTrait<PromotionInterface> */
    use ResourceFormComponentTrait, TypedLiveCollectionTrait {
        TypedLiveCollectionTrait::addCollectionItem insteadof ResourceFormComponentTrait;
        initialize as public __construct;
    }
}
