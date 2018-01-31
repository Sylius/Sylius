<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 11:04
 */

declare(strict_types=1);

namespace Sylius\Component\Core\URLRedirect;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;

/**
 * Gets the new URL after redirect
 *
 * Class URLRedirectProcessor
 *
 * @package Sylius\Component\Core\URLRedirect
 */
class URLRedirectProcessor implements URLRedirectProcessorInterface
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