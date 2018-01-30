<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 16:07
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;


use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;

/**
 * Class URLRedirectRepository
 *
 * @package Sylius\Bundle\CoreBundle\Doctrine\ORM
 */
class URLRedirectRepository extends EntityRepository implements URLRedirectRepositoryInterface
{

    /**
     * Gets the redirect for a route
     *
     * @param string $route
     *
     * @return null|URLRedirect
     *
     * @throws    NonUniqueResultException
     */
    public function getActiveRedirectForRoute(string $route): ?URLRedirect
    {
        return $this->createQueryBuilder('r')
            ->where('r.oldRoute = :route')
            ->andWhere('r.enabled = 1')
            ->setParameter(':route', $route)
            ->getQuery()
            ->getOneOrNullResult();
    }
}