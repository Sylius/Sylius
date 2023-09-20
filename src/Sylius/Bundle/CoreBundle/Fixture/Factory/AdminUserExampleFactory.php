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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $userFactory,
        private string $localeCode,
        private ?FileLocatorInterface $fileLocator = null,
        private ?ImageUploaderInterface $imageUploader = null,
        private ?FactoryInterface $avatarImageFactory = null,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);

        if ($this->fileLocator === null || $this->imageUploader === null) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.6',
                'Not passing a $fileLocator or/and an $imageUploader to %s constructor is deprecated and will be removed in Sylius 2.0.',
                self::class,
            );
        }

        if ($this->avatarImageFactory === null) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.10',
                'Not passing an $avatarImageFactory to %s constructor is deprecated and will be removed in Sylius 2.0.',
                self::class,
            );
        }
    }

    public function create(array $options = []): AdminUserInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var AdminUserInterface $user */
        $user = $this->userFactory->createNew();
        $user->setEmail($options['email']);
        $user->setUsername($options['username']);
        $user->setPlainPassword($options['password']);
        $user->setEnabled($options['enabled']);
        $user->addRole('ROLE_ADMINISTRATION_ACCESS');
        $user->setLocaleCode($options['locale_code']);

        if (isset($options['first_name'])) {
            $user->setFirstName($options['first_name']);
        }
        if (isset($options['last_name'])) {
            $user->setLastName($options['last_name']);
        }

        if ($options['api']) {
            $user->addRole('ROLE_API_ACCESS');
        }

        if ($options['avatar'] !== '') {
            $this->createAvatar($user, $options);
        }

        return $user;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('email', fn (Options $options): string => $this->faker->email)
            ->setDefault('username', fn (Options $options): string => $this->faker->firstName . ' ' . $this->faker->lastName)
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('password', 'password123')
            ->setDefault('locale_code', $this->localeCode)
            ->setDefault('api', false)
            ->setDefined('first_name')
            ->setDefined('last_name')
            ->setDefault('avatar', '')
            ->setAllowedTypes('avatar', 'string')
        ;
    }

    private function createAvatar(AdminUserInterface $adminUser, array $options): void
    {
        if ($this->fileLocator === null || $this->imageUploader === null) {
            throw new \RuntimeException('You must configure a $fileLocator or/and $imageUploader');
        }

        $imagePath = $this->fileLocator->locate($options['avatar']);
        $uploadedImage = new UploadedFile($imagePath, basename($imagePath));

        if ($this->avatarImageFactory === null) {
            $avatarImage = new AvatarImage();
        } else {
            /** @var AvatarImage $avatarImage */
            $avatarImage = $this->avatarImageFactory->createNew();
        }

        $avatarImage->setFile($uploadedImage);

        $this->imageUploader->upload($avatarImage);

        $adminUser->setAvatar($avatarImage);
    }
}
