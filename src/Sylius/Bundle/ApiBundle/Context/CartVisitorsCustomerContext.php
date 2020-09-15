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

namespace Sylius\Bundle\ApiBundle\Context;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/** @experimental */
final class CartVisitorsCustomerContext implements CartVisitorsCustomerContextInterface
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getCartCustomerId(): ?string
    {
        return $this->session->get('cartCustomerId');
    }

    public function setCartCustomerId(?string $id): void
    {
        $this->session->set('cartCustomerId', $id);
    }
}
