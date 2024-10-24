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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Administrator\UpdatePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;

final class ManagingAdministratorLocalesContext implements Context
{
    public function __construct(
        private UpdatePageInterface $updatePage,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I change my locale to :localeCode
     */
    public function iChangeMyLocaleTo(string $localeCode): void
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $this->updatePage->open(['id' => $adminUser->getId()]);
        $this->updatePage->changeLocale($localeCode);
        $this->updatePage->saveChanges();
    }
}
