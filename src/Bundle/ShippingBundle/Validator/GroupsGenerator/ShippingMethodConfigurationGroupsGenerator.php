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

namespace Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator;

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Form\FormInterface;
use Webmozart\Assert\Assert;

/** @internal */
class ShippingMethodConfigurationGroupsGenerator
{
    /** @param array<string, array<string, string>> $validationGroups */
    public function __construct(private array $validationGroups)
    {
    }

    /**
     * @param FormInterface|ShippingMethodInterface $object
     *
     * @return array<string>
     */
    public function __invoke($object): array
    {
        if ($object instanceof FormInterface) {
            $object = $object->getData();
        }

        Assert::isInstanceOf($object, ShippingMethodInterface::class);

        return $this->validationGroups[$object->getCalculator()] ?? ['sylius'];
    }
}
