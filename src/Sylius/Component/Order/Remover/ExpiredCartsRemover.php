<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Remover;

use Sylius\Component\Order\Repository\OrderRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ExpiredCartsRemover implements ExpiredCartsRemoverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var string
     */
    private $expirationPeriod;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param string $expirationPeriod
     */
    public function __construct(OrderRepositoryInterface $orderRepository, $expirationPeriod)
    {
        $this->orderRepository = $orderRepository;
        $this->expirationPeriod = $expirationPeriod;
    }

    public function remove()
    {
        $expiredCarts = $this->orderRepository->findCartsNotModifiedSince(new \DateTime('-'.$this->expirationPeriod));
        foreach ($expiredCarts as $expiredCart) {
            $this->orderRepository->remove($expiredCart);
        }
    }
}
