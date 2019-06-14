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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class AdminUserContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ExampleFactoryInterface */
    private $userFactory;

    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var ImageUploaderInterface */
    private $imageUploader;

    /** @var ObjectManager */
    private $objectManager;

    /** @var \ArrayAccess */
    private $minkParameters;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ExampleFactoryInterface $userFactory,
        UserRepositoryInterface $userRepository,
        ImageUploaderInterface $imageUploader,
        ObjectManager $objectManager,
        \ArrayAccess $minkParameters
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->imageUploader = $imageUploader;
        $this->objectManager = $objectManager;
        $this->minkParameters = $minkParameters;
    }

    /**
     * @Given there is an administrator :email identified by :password
     * @Given /^there is(?:| also) an administrator "([^"]+)"$/
     */
    public function thereIsAnAdministratorIdentifiedBy($email, $password = 'sylius')
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->userFactory->create(['email' => $email, 'password' => $password, 'enabled' => true]);

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
}
