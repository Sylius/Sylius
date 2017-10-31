<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Attribute\Factory;

use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeSelectOptionInterface;
use Sylius\Component\Resource\Factory\TranslatableFactoryInterface;

/**
 * @author Asier Marqués <asiermarques@gmail.com>
 */
interface AttributeSelectOptionFactoryInterface extends TranslatableFactoryInterface
{

    public function createForAttribute(AttributeInterface $attribute): AttributeSelectOptionInterface;

}