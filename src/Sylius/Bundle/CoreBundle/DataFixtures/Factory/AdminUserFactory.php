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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory;

use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<AdminUserInterface>
 *
 * @method static AdminUserInterface|Proxy createOne(array $attributes = [])
 * @method static AdminUserInterface[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static AdminUserInterface|Proxy find(object|array|mixed $criteria)
 * @method static AdminUserInterface|Proxy findOrCreate(array $attributes)
 * @method static AdminUserInterface|Proxy first(string $sortedField = 'id')
 * @method static AdminUserInterface|Proxy last(string $sortedField = 'id')
 * @method static AdminUserInterface|Proxy random(array $attributes = [])
 * @method static AdminUserInterface|Proxy randomOrCreate(array $attributes = [])
 * @method static AdminUserInterface[]|Proxy[] all()
 * @method static AdminUserInterface[]|Proxy[] findBy(array $attributes)
 * @method static AdminUserInterface[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static AdminUserInterface[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method AdminUserInterface|Proxy create(array|callable $attributes = [])
 */
class AdminUserFactory extends ModelFactory implements AdminUserFactoryInterface
{
    public function __construct(
        private FactoryInterface $adminUserFactory,
        private FactoryInterface $avatarImageFactory,
        private FileLocatorInterface $fileLocator,
        private ImageUploaderInterface $imageUploader,
        private string $localeCode,
    ) {
        parent::__construct();
    }

    public function withEmail(string $email): self
    {
        return $this->addState(['email' => $email]);
    }

    public function withUsername(string $username): self
    {
        return $this->addState(['username' => $username]);
    }

    public function enabled(): self
    {
        return $this->addState(['enabled' => true]);
    }

    public function disabled(): self
    {
        return $this->addState(['enabled' => false]);
    }

    public function withPassword(string $password): self
    {
        return $this->addState(['password' => $password]);
    }

    public function withApiAccess(): self
    {
        return $this->addState(['api' => true]);
    }

    public function withFirstName(string $firstName): self
    {
        return $this->addState(['first_name' => $firstName]);
    }

    public function withLastName(string $lastName): self
    {
        return $this->addState(['last_name' => $lastName]);
    }

    public function withAvatar(string $avatar): self
    {
        return $this->addState(['avatar' => $avatar]);
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'username' => self::faker()->firstName() . ' ' . self::faker()->lastName(),
            'enabled' => true,
            'password' => 'password123',
            'api' => false,
            'locale_code' => $this->localeCode,
            'first_name' => null,
            'last_name' => null,
            'avatar' => '',
        ];
    }

    protected function initialize(): self
    {
        return $this
            ->instantiateWith(function(array $attributes): AdminUserInterface {
                /** @var AdminUserInterface $adminUser */
                $adminUser = $this->adminUserFactory->createNew();

                $adminUser->setEmail($attributes['email']);
                $adminUser->setUsername($attributes['username']);
                $adminUser->setEnabled($attributes['enabled']);
                $adminUser->setPlainPassword($attributes['password']);
                $adminUser->setLocaleCode($attributes['locale_code']);
                $adminUser->setFirstName($attributes['first_name']);
                $adminUser->setLastName($attributes['last_name']);

                if ($attributes['api']) {
                    $adminUser->addRole('ROLE_API_ACCESS');
                }

                if ('' !== $attributes['avatar']) {
                    $this->createAvatar($adminUser, $attributes);
                }

                return $adminUser;
            })
        ;
    }

    protected static function getClass(): string
    {
        return AdminUser::class;
    }

    private function createAvatar(AdminUserInterface $adminUser, array $options): void
    {
        $imagePath = $this->fileLocator->locate($options['avatar']);
        $uploadedImage = new UploadedFile($imagePath, basename($imagePath));

        /** @var AvatarImageInterface $avatarImage */
        $avatarImage = $this->avatarImageFactory->createNew();

        $avatarImage->setFile($uploadedImage);

        $this->imageUploader->upload($avatarImage);

        $adminUser->setAvatar($avatarImage);
    }
}
