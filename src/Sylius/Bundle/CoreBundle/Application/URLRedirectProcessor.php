<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 11:04
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Application;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirectInterface;

class URLRedirectProcessor implements URLRedirectProcessorInterface
{
    /**
     * @var EntityRepository
     */
    private $urlRedirectRepository;

    public function __construct(EntityRepository $urlRedirectRepository)
    {
        $this->urlRedirectRepository = $urlRedirectRepository;
    }

    public function redirectRoute(string $oldRoute): string
    {
        /** @var URLRedirectInterface|null $route */
        $route = $this->urlRedirectRepository->findOneBy(['oldRoute' => $oldRoute]);

        if ($route !== null) {
            return $route->getNewRoute();
        }

        return $oldRoute;
    }
}