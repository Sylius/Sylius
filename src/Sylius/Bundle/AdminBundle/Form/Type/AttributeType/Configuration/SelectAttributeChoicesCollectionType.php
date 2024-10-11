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

namespace Sylius\Bundle\AdminBundle\Form\Type\AttributeType\Configuration;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType as BaseSelectAttributeChoicesCollectionType;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class SelectAttributeChoicesCollectionType extends BaseSelectAttributeChoicesCollectionType
{
    public function getParent(): string
    {
        return LiveCollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_select_attribute_choices_collection';
    }
}
