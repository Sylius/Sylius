<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 16:06
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Repository;


use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface URLRedirectRepositoryInterface extends RepositoryInterface
{
    public function getActiveRedirectForRoute(string $route): ?URLRedirect;
}