<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 13:13
 */

declare(strict_types=1);

namespace Sylius\Component\Core\URLRedirect;


use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Model\URLRedirectInterface;

/**
 * Class URLRedirectLoopDetector
 *
 * This checks if there are any redirect loops that would result in an infinite set of redirects
 *
 * @package Sylius\Component\Core\URLRedirect
 */
class URLRedirectLoopDetector implements URLRedirectLoopDetectorInterface
{
    /**
     * @var EntityRepository
     */
    private $repository;

    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }


    public function containsLoop(URLRedirectInterface $newNode): bool
    {
        $visitedNodes = [$newNode->getOldRoute()];

        $currentNode = $newNode;

        while (!is_null($currentNode)) {
            //If the current node was already redirected to, it's a loop
            $currentURL = $currentNode->getNewRoute();
            if (in_array($currentURL, $visitedNodes)) {
                return true;
            }

            $visitedNodes[] = $currentURL;
            $currentNode = $this->repository->findOneBy(['oldRoute' => $currentURL, 'enabled' => true]);
        }

        return false;
    }
}