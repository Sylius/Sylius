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

use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;

/**
 * Class URLRedirectLoopDetector
 *
 * This checks if there are any redirect loops that would result in an infinite set of redirects
 */
class URLRedirectLoopDetector implements URLRedirectLoopDetectorInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(URLRedirectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /** {@inheritdoc} */
    public function containsLoop(URLRedirectInterface $newNode): bool
    {
        $visitedNodes = [$newNode->getOldRoute()];

        $currentNode = $newNode;

        while (null !== $currentNode) {
            $currentURL = $currentNode->getNewRoute();

            //If the current node was already redirected to, it's a loop
            if (in_array($currentURL, $visitedNodes)) {
                return true;
            }

            $visitedNodes[] = $currentURL;
            $currentNode = $this->repository->getActiveRedirectForRoute($currentURL);
        }

        return false;
    }
}
