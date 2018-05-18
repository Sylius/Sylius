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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;

/**
 * Class URLRedirectRepository
 */
class URLRedirectRepository extends EntityRepository implements URLRedirectRepositoryInterface
{
    /**
     * Gets the redirect for a route
     *
     * @param string $route
     *
     * @return URLRedirect|null
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
