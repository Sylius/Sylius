# UPGRADE FROM `1.13` TO `2.0`

* API Platform has dropped `DataProviders` and `DataPersisters` in favor of `Providers` and `Processors`, respectively.
  Due to this change, Sylius custom `DataProviders` and `DataPersisters` have been adapted to the new API Platform interfaces
  and their namespaced have been changed to `StateProvider` and `StateProcessor` respectively:
- `Sylius\Bundle\ApiBundle\DataPersister\*DataPersister` => `Sylius\Bundle\ApiBundle\StateProcessor\*Processor`
- `Sylius\Bundle\ApiBundle\DataProvider\*DataProvider` => `Sylius\Bundle\ApiBundle\StateProvider\*Provider`

* API Platform has also dropped `DataTransformers` in favor of which `some of them` have been refactored into `SerializerContextBuilders` as follows:
- `Sylius\Bundle\ApiBundle\DataTransformer\ChannelCodeAwareInputCommandDataTransformer` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ChannelCodeAwareContextBuilder`
- `Sylius\Bundle\ApiBundle\DataTransformer\LocaleCodeAwareInputCommandDataTransformer` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\LocaleCodeAwareContextBuilder`
- `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailIfNotSetAwareCommandDataTransformer` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\LoggedInCustomerEmailIfNotSetAwareContextBuilder`
- `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserIdAwareCommandDataTransformer` => `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ChannelCodeAwareContextBuilder`

* The constructor of `Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser` has been changed:
```php
    public function __construct(
    -   public string $firstName,
    -   public string $lastName,
    -   public string $email,
    -   public string $password,
    -   public bool $subscribedToNewsletter = false,
    +   protected string $firstName,
    +   protected string $lastName,
    +   protected string $email,
    +   protected string $password,
    +   protected ?string $channelCode,
    +   protected ?string $localeCode,
    +   protected bool $subscribedToNewsletter = false,
    ) {
    }
```

* The constructor of `Sylius\Bundle\ApiBundle\Command\Account\RequestResetPasswordToken` has been changed:
```php
    public function __construct(
    -   public string $email,
    +   protected string $email,
    +   protected ?string $channelCode,
    +   protected ?string $localeCode,
    ) {
    }
```

* The constructor of `Sylius\Bundle\ApiBundle\Command\SendContactRequest` has been changed:
```php
    public function __construct(
    -   private ?string $email = null,
    -   private ?string $message = null,
    +   protected ?string $channelCode,
    +   protected ?string $localeCode,
    +   protected ?string $email = null,
    +   protected ?string $message = null,
    ) {
    }
```

* The constructor of `Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser` has been changed:
```php
    public function __construct(
    -   public string $token,
    -   private ?string $localeCode = null,
    -   private ?string $channelCode = null,
    +   protected string $token,
    +   protected string $channelCode,
    +   protected string $localeCode,
    ) {
    }
```

* The constructor of `Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword` has been changed:
```php
    public function __construct(
    -   public ?string $newPassword,
    -   public ?string $confirmNewPassword,
    -   public ?string $currentPassword,
    +   protected mixed $shopUserId,
    +   protected string $newPassword,
    +   protected string $confirmNewPassword,
    +   protected string $currentPassword,
    ) {
    }
```

* The constructor of `Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification` has been created:
```php
    public function __construct(
    +   protected string|int|null $shopUserId,
    +   protected ?string $channelCode,
    +   protected ?string $localeCode,
    ) {
    }
```

All the `setter` methods have been removed from the commands above and also there are some new `getter` methods accordingly to arguments visibility changes.

* The parameter type and order of the `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction::__construct` has been changed:
```php
    public function __construct(
        private FactoryInterface $avatarImageFactory,
        private AvatarImageRepositoryInterface $avatarImageRepository,
    -   private ImageUploaderInterface $imageUploader,
    -   private IriConverterInterface $iriConverter,
    +   private RepositoryInterface $adminUserRepository,
    +   private ImageUploaderInterface $imageUploader,
    )
```

* Updated API routes related to avatar management:

  Previous Routes:
    * `'GET' - /api/v2/admin/avatar-images/{id}`
    * `'POST' - /api/v2/admin/avatar-images`
    * `'DELETE' - /api/v2/admin/avatar-images/{id}`

  New Routes:
    * `'GET' - /api/v2/admin/administrators/{id}/avatar-image`
    * `'POST' - /api/v2/admin/administrators/{id}/avatar-image`
    * `'DELETE' - /api/v2/admin/administrators/{id}/avatar-image`

* Updated API routes related to shop user management:

  Previous Routes:
    * `'POST' - /api/v2/shop/reset-password-requests`
    * `'PATCH' - /api/v2/shop/reset-password-requests/{resetPasswordToken}`
    * `'POST' - /api/v2/shop/account-verification-requests`
    * `'PATCH - /api/v2/shop/account-verification-requests/{token}`

  New Routes:
    * `'POST' - /api/v2/shop/reset-password`
    * `'PATCH' - /api/v2/shop/reset-password/{resetPasswordToken}`
    * `'POST' - /api/v2/shop/verify-shop-user`
    * `'PATCH' - /api/v2/shop/verify-shop-user/{token}`

* Updated API routes related to admin user management:

  Previous Routes:
    * `'POST' - /api/v2/admin/reset-password-requests`
    * `'PATCH' - /api/v2/admin/reset-password-requests/{resetPasswordToken}`

  New Routes:
    * `'POST' - /api/v2/admin/reset-password`
    * `'PATCH' - /api/v2/admin/reset-password/{resetPasswordToken}`

* The `getCurrentPrefix` method has been removed from the `Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface`.

* The `Sylius\Bundle\ApiBundle\Provider\PathPrefixProvider` constructor has been changed due to refactor. 
  Now, we provide the list of possible prefixes that we check in this service. This list can be set under 
  the parameter: `sylius.api_path_prefixes`. 

    ```diff
        public function __construct(
    -       private UserContextInterface $userContext,
            private string $apiRoute,
    +       private array $pathPrefixes,
        ) {
            ...
        }
    ```

1. There has been introduced a new parameter:

    ```yaml
    sylius_api:
        non_archived_classes:
            - "%sylius.model.shipping_method.class%"
            - "%sylius.model.promotion.class%"
    ```

    That avoids duplication of logic and is used in one `Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\NonArchivedExtension` rather than dedicated extensions for each entity.
