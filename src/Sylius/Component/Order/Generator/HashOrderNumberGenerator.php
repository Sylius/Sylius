<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Generator;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Hash order number generator.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class HashOrderNumberGenerator extends OrderNumberGenerator implements OrderNumberGeneratorInterface
{
    /**
     * {@inheritdoc}
     * This generates a 3 by 7 by 7 digit order number (much like amazon's order identifier)
     * e.g. 105-3958356-3707476
     *
     */
    public function generate(OrderInterface $order)
    {
        do {
            $number = $this->generateSegment(3) . '-' . $this->generateSegment(7) . '-' . $this->generateSegment(7);
        } while ($this->numberRepository->isUsed($number));

        $order->setNumber($number);
    }

    /**
     * Generates a randomized segment
     * @param  int    $length
     * @return string Random characters
     */
    protected function generateSegment($length)
    {
        return substr(str_pad(mt_rand(), $length, 0, STR_PAD_LEFT), 0, $length);
    }
}
