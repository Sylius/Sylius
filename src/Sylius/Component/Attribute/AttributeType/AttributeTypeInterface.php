<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\AttributeType;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface AttributeTypeInterface
{
    /**
     * @return string
     */
    public function getStorageType();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param AttributeValueInterface $attributeValue
     * @param ExecutionContextInterface $context
     * @param array $configuration
     */
    public function validate(AttributeValueInterface $attributeValue, ExecutionContextInterface $context, array $configuration);
}
