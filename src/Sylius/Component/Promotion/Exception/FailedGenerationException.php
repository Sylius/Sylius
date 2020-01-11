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

namespace Sylius\Component\Promotion\Exception;

use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;

final class FailedGenerationException extends \InvalidArgumentException
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        PromotionCouponGeneratorInstructionInterface $instruction,
        int $exceptionCode = 0,
        ?\Exception $previousException = null
    ) {
        $message = sprintf(
            'Invalid coupon code length or coupons amount. It is not possible to generate %d unique coupons with %d code length',
            $instruction->getAmount(),
            $instruction->getCodeLength()
        );

        parent::__construct($message, $exceptionCode, $previousException);
    }
}
