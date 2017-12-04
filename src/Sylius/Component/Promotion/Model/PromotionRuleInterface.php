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

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface PromotionRuleInterface extends ResourceInterface, ConfigurablePromotionElementInterface
{
    /**
     * @param string|null $type
     */
    public function setType(?string $type): void;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration): void;

    /**
     * @param PromotionInterface|null $promotion
     */
    public function setPromotion(?PromotionInterface $promotion): void;
}
