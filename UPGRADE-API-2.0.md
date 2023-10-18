# UPGRADE FROM `1.13` TO `2.0`

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
