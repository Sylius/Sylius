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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Account\Address;

use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent]
class FormComponent
{
    /** @use ResourceFormComponentTrait<AddressInterface> */
    use ResourceFormComponentTrait {
        initialize as public __construct;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(
            $this->formClass,
            $this->resource,
            ['add_build_address_form_subscriber' => false],
        );
    }
}