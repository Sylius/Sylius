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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Checker\CustomerOrderCancellationCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerCancelOrderExtension extends \Twig_Extension implements CustomerCancelOrderExtensionInterface
{
    /** @var CustomerOrderCancellationCheckerInterface */
    private $customerOrderCancellationChecker;

    public function __construct(CustomerOrderCancellationCheckerInterface $customerOrderCancellationChecker)
    {
        $this->customerOrderCancellationChecker = $customerOrderCancellationChecker;
    }

    public function getFunctions(): array
    {
        return [new \Twig_SimpleFunction('can_customer_cancel_order', [$this, 'canOrderBeCancelled'])];
    }

    public function canOrderBeCancelled(OrderInterface $order)
    {
        return $this->customerOrderCancellationChecker->check($order);
    }
}
