<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 05/02/18
 * Time: 10:59
 */

declare(strict_types=1);

namespace Sylius\Component\Addressing\Repository;


use Sylius\Component\Addressing\Model\PostalCodeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PostCodeRepositoryInterface extends RepositoryInterface
{
    /**
     *
     * @param string $code
     *
     * @return null|PostalCodeInterface
     */
    public function findOneByCode(string $code): ?PostalCodeInterface;
}