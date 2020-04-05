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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

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
    /** @var FactoryInterface */
    private $userFactory;

    /** @var \Faker\Generator */
    private $faker;

    /** @var OptionsResolver */
    private $optionsResolver;

    /** @var string */
    private $localeCode;

    /** @var FileLocatorInterface|null */
    private $fileLocator;

    /** @var ImageUploaderInterface|null */
    private $imageUploader;

    public function __construct(
        FactoryInterface $userFactory,
        string $localeCode,
        ?FileLocatorInterface $fileLocator = null,
        ?ImageUploaderInterface $imageUploader = null
    ) {
        $this->userFactory = $userFactory;
        $this->localeCode = $localeCode;
        $this->fileLocator = $fileLocator;
        $this->imageUploader = $imageUploader;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);

        if ($this->fileLocator === null || $this->imageUploader === null) {
            @trigger_error(sprintf('Not passing a $fileLocator or/and $imageUploader to %s constructor is deprecated since Sylius 1.6 and will be removed in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('email', function (Options $options): string {
                return $this->faker->email;
            })
            ->setDefault('username', function (Options $options): string {
                return $this->faker->firstName . ' ' . $this->faker->lastName;
            })
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

        $avatarImage = new AvatarImage();
        $avatarImage->setFile($uploadedImage);

        $this->imageUploader->upload($avatarImage);

        $adminUser->setAvatar($avatarImage);
    }
}
