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

namespace Sylius\Component\Core\TokenAssigner;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class UniqueIdBasedOrderTokenAssigner implements OrderTokenAssignerInterface
{
    /**
     * @var RandomnessGeneratorInterface
     */
    private $generator;

    /**
     * @param RandomnessGeneratorInterface $generator
     */
    public function __construct(RandomnessGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function assignTokenValue(OrderInterface $order)
    {
        $order->setTokenValue($this->generator->generateUriSafeString(10));
    }
}
