<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 10:01
 */

declare(strict_types=1);

namespace Sylius\Component\Core\URLRedirect;


use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirect;

class URLRedirectService implements URLRedirectServiceInterface
{
    /**
     * @var EntityRepository
     */
    private $urlRedirectRepository;

    /**
     * @var URLRedirect|null
     */
    private $lastRedirect;

    public function __construct(EntityRepository $urlRedirectRepository)
    {
        $this->urlRedirectRepository = $urlRedirectRepository;
    }

    /**
     * Checks if the route has a redirect defined
     *
     * @param string $url
     *
     * @return bool
     */
    public function hasActiveRedirect(string $url): bool
    {
        $redirect           = $this->urlRedirectRepository->findOneBy(['oldRoute' => $url, 'enabled' => true]);
        $this->lastRedirect = $redirect;

        return !is_null($this->lastRedirect);
    }

    /**
     * Gets the URL of the redirect
     *
     * @return null|string
     */
    public function getRedirect(): ?string
    {
        if($this->lastRedirect === null){
            return null;
        }
        return $this->lastRedirect->getNewRoute();
    }

}