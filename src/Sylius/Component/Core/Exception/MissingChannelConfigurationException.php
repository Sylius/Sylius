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

namespace Sylius\Component\Core\Exception;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class MissingChannelConfigurationException extends \RuntimeException
{
    public function __construct(string $message, ?\Exception $previousException = null)
    {
        parent::__construct($message, 0, $previousException);
    }

    public static function getMissingChannelProductVariantPriceMessage(ChannelInterface $channel, ProductVariantInterface $productVariant): string
    {
        $message = sprintf('Channel %s has no price defined for product variant', $channel->getName());
        if ($productVariant->getName() !== null) {
            return $message . sprintf(' %s (%s)', $productVariant->getName(), $productVariant->getCode());
        }

        return $message . sprintf(' with code %s', $productVariant->getCode());
    }
}
