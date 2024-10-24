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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AdminUserContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ExampleFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
        private ImageUploaderInterface $imageUploader,
        private ObjectManager $objectManager,
        private \ArrayAccess $minkParameters,
    ) {
    }

    /**
     * @Given there is an administrator :email identified by :password
     * @Given /^there is(?:| also) an administrator "([^"]+)"$/
     */
    public function thereIsAnAdministratorIdentifiedBy($email, $password = 'sylius')
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->userFactory->create(['email' => $email, 'password' => $password, 'enabled' => true, 'api' => true]);

        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }

    /**
     * @Given there is an administrator with name :username
     */
    public function thereIsAnAdministratorWithName($username)
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->userFactory->create(['username' => $username]);
        $adminUser->setUsername($username);

        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }

    /**
     * @Given /^(this administrator) has the "([^"]*)" image as avatar$/
     */
    public function thisAdministratorHasTheImageAsAvatar(AdminUserInterface $administrator, string $avatarPath): void
    {
        $this->iHaveTheImageAsMyAvatar($avatarPath, $administrator);
    }

    /**
     * @Given /^(this administrator) account is disabled$/
     * @When /^(this administrator) account becomes disabled$/
     */
    public function thisAccountIsDisabled(AdminUserInterface $administrator): void
    {
        $administrator->setEnabled(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this administrator) is using ("[^"]+" locale)$/
     * @Given /^(I) am using ("[^"]+" locale) for my panel$/
     */
    public function thisAdministratorIsUsingLocale(AdminUserInterface $adminUser, $localeCode)
    {
        $adminUser->setLocaleCode($localeCode);

        $this->userRepository->add($adminUser);
        $this->sharedStorage->set('administrator', $adminUser);
    }

    /**
     * @Given /^I have the "([^"]*)" image as (my) avatar$/
     */
    public function iHaveTheImageAsMyAvatar(string $avatarPath, AdminUserInterface $administrator): void
    {
        $filesPath = $this->minkParameters['files_path'];

        $avatar = new AvatarImage();
        $avatar->setFile(new UploadedFile($filesPath . $avatarPath, basename($avatarPath)));

        $this->imageUploader->upload($avatar);

        $administrator->setAvatar($avatar);
        $this->objectManager->flush();

        $this->sharedStorage->set($avatarPath, $avatar->getPath());
    }

    /**
     * @Given /^(I) have already received an administrator's password resetting email$/
     */
    public function iHaveAlreadyReceivedAnAdministratorsPasswordResettingEmail(AdminUserInterface $administrator): void
    {
        $administrator->setPasswordResetToken('token');
        $administrator->setPasswordRequestedAt(new \DateTime());

        $this->objectManager->flush();
    }

    /**
     * @Given /^(my) password reset token has already expired$/
     */
    public function myPasswordResetTokenHasAlreadyExpired(AdminUserInterface $administrator): void
    {
        $administrator->setPasswordRequestedAt(new \DateTime('-1 year'));

        $this->objectManager->flush();
    }
}
