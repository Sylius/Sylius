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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Sylius\Component\Core\Model\OrderInterface;

/** @experimental */
final class OrderTokenValueAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        /** @var OrderInterface $cart */

        if(key_exists('object_to_populate',$context)){
            $cart = $context['object_to_populate'];
            $tokenValue = $cart->getTokenValue();
        }else if(property_exists($object,'orderTokenValue')){
            $tokenValue = $object->orderTokenValue;
        }else{
            throw new \Exception('Token value could not be found');
        }

        $object->setOrderTokenValue($tokenValue);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof OrderTokenValueAwareInterface;
    }
}
