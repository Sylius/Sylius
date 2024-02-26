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

namespace Sylius\Bundle\ShopBundle\Form\Type;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as BaseAddressType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class AddressType extends AbstractResourceType
{
    /**
     * @param string[] $validationGroups
     */
    public function __construct(string $dataClass, array $validationGroups, private EventSubscriberInterface $buildAddressFormSubscriber)
    {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber($this->buildAddressFormSubscriber)
        ;
    }

    public function getParent(): string
    {
        return BaseAddressType::class;
    }
}
