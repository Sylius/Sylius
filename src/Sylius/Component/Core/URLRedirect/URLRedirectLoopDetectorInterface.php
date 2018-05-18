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

/**
 * Interface URLRedirectLoopDetectorInterface
 *
 * Classes that detect loops in URL redirects can implement that.
 */
interface URLRedirectLoopDetectorInterface
{
    /**
     * Checks if adding the new node creates a redirect loop
     *
     * @param URLRedirectInterface $newNode
     *
     * @return bool
     */
    public function containsLoop(URLRedirectInterface $newNode): bool;
}
