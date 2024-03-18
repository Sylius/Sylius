<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class ShopUserContext implements Context
{
    public function __construct(private UserRepositoryInterface $shopUserRepository)
    {
    }

    /**
     * @Transform :shopUser
     */
    public function getShopUserByEmail(string $email): ShopUserInterface
    {
        $shopUser = $this->shopUserRepository->findOneByEmail($email);

        Assert::notNull($shopUser, sprintf('Shop User with email "%s" does not exist', $email));

        return $shopUser;
    }
}
