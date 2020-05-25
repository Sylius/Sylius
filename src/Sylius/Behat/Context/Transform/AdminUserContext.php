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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class AdminUserContext implements Context
{
    /** @var RepositoryInterface */
    private $adminUserRepository;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(RepositoryInterface $adminUserRepository, SharedStorageInterface $sharedStorage)
    {
        $this->adminUserRepository = $adminUserRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Transform :adminUser
     */
    public function getAdminUserByEmail(string $email): AdminUserInterface
    {
        $adminUser = $this->adminUserRepository->findOneBy(['email' => $email]);

        Assert::notNull($adminUser, sprintf('Administrator with email "%s" does not exist', $email));

        return $adminUser;
    }

    /**
     * @Transform /^(I|my)$/
     */
    public function getLoggedAdminUser()
    {
        return $this->sharedStorage->get('administrator');
    }
}
