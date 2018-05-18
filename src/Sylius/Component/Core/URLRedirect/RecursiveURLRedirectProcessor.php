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

namespace Sylius\Component\Core\URLRedirect;

use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;

/**
 * Class RecursiveURLRedirectProcessor
 *
 * Gets the new URL after redirect
 */
final class RecursiveURLRedirectProcessor implements URLRedirectProcessorInterface
{
    /**
     * @var URLRedirectRepositoryInterface
     */
    private $urlRedirectRepository;

    public function __construct(URLRedirectRepositoryInterface $urlRedirectRepository)
    {
        $this->urlRedirectRepository = $urlRedirectRepository;
    }

    public function redirectRoute(string $oldRoute): string
    {
        /** @var URLRedirectInterface|null $route */
        $route = $this->urlRedirectRepository->getActiveRedirectForRoute($oldRoute);

        if ($route !== null) {
            return $this->redirectRoute($route->getNewRoute());
        }

        return $oldRoute;
    }
}
