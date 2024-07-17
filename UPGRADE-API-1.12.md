# UPGRADE FROM `v1.12.18` TO `v1.12.19`

1. The `api/v2/shop/adjustments/{id}` endpoint has been disabled.

# UPGRADE FROM `v1.12.13` TO `v1.12.14`

1. The `api/v2/admin/zone-members/{code}` endpoint has been changed to `api/v2/admin/zone-members/{id}`.

# UPGRADE FROM `v1.12.1` TO `v1.12.2`

1. So far, on production environment when any non-validation related error occurred, the `FOS\RestBundle\Serializer\Normalizer\FlattenExceptionHandler` was used, even on API Platform endpoints.
   Now, depending on the path of the request, the `FOS\RestBundle\Serializer\Normalizer\FlattenExceptionHandler` or `ApiPlatform\Hydra\Serializer\ErrorNormalizer` is used. If your code
   rely on the previous behavior, you should add the following configuration to your `config/packages/_sylius.yaml` file:
    ```yaml
    sylius_api:
        legacy_error_handling: true
    ```

   Example response before bugfix:
    ```json
    {
        "code": 500,
        "message": "Internal Server Error"
    }
    ```

   Example response after bugfix:
    ```json
    {
        "@context": "/api/v2/contexts/Error",
        "@type": "hydra:Error",
        "hydra:title": "An error occurred",
        "hydra:description": "Internal Server Error"
    }
    ```
   The status code is passed along the response as an HTTP status code, and the `message` value is returned in a `hydra:description` field.

# UPGRADE FROM `v1.11.X` TO `v1.12.0`

1. The `Sylius\Bundle\ApiBundle\DataProvider\CartShippingMethodsSubresourceDataProvider` has been removed and replaced by `Sylius\Bundle\ApiBundle\DataProvider\ShippingMethodsCollectionDataProvider`.

1. The `Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface` has been refactored and moved to `CoreBundle` use `Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface` instead.

1. The `Sylius\Bundle\ApiBundle\Serializer\ShippingMethodNormalizer` logic and constructor has been changed due to refactor above.

    ```diff
        public function __construct(
            private OrderRepositoryInterface $orderRepository,
            private ShipmentRepositoryInterface $shipmentRepository,
            private ServiceRegistryInterface $shippingCalculators,
    +       private RequestStack $requestStack,
    +       private ChannelContextInterface $channelContext
        ) {
            ...
        }
    ```

1. The  `GET` `api/v2/shop/orders/{token}/shipments/{id}/methods` and `api/v2/shop/shipments/{id}/methods` endpoints have been removed and changed into collection request with 2 parameters `api/v2/shop/shipping-methods?shipmentId={id}&tokenValue={token}`.
Now when we do not provide parameters in response it returns all available `shippingMethods` in channel.
Wrong parameters otherwise cause empty array `[]` in response and correct parameters return `shippingMethods` available for your `shipment`.     
Here is how the response looks like:
   
   ```
      {
        "@context": "/api/v2/contexts/ShippingMethod",
        "@id": "/api/v2/shop/shipping-methods",
        "@type": "hydra:Collection",
        "hydra:member": [
          {
            "@id": "/api/v2/shop/shipping-methods/ups",
            "@type": "ShippingMethod",
            "id": 1,
            "code": "ups",
            "position": 0,
            "name": "UPS",
            "description": "Quasi perferendis debitis officiis ut inventore exercitationem."
          }
        ],
        "hydra:totalItems": 1,
        "hydra:search": {
          "@type": "hydra:IriTemplate",
          "hydra:template": "/api/v2/shop/shipping-methods{?shipmentId,tokenValue}",
          "hydra:variableRepresentation": "BasicRepresentation",
          "hydra:mapping": [
            {
              "@type": "IriTemplateMapping",
              "variable": "shipmentId",
              "property": null,
              "required": false
            },
            {
              "@type": "IriTemplateMapping",
              "variable": "tokenValue",
              "property": null,
              "required": false
            }
          ]
        }
      }
   ```

1. Service `src/Sylius/Bundle/ApiBundle/DataProvider/CartPaymentMethodsSubresourceDataProvider.php` has been removed and logic was replaced by `src/Sylius/Bundle/ApiBundle/DataProvider/PaymentMethodsCollectionDataProvider.php`

1. Endpoints `/shop/orders/{tokenValue}/payments/{payments}/methods`, `/shop/payments/{id}/methods` has been removed and replaced by `/shop/payment-methods` with filter `paymentId` and `tokenValue`
   `/shop/payment-methods` returns all enable payment methods if filters are not set, payment methods related to payment if filters are filled or empty response if filters ale filled with invalid data.
1. Service `Sylius\Bundle\ApiBundle\DataProvider/CartPaymentMethodsSubresourceDataProvider` has been removed and logic was replaced by `Sylius\Bundle\ApiBundle\DataProvider\PaymentMethodsCollectionDataProvider`

1. The  `GET` `api/v2/shop/orders/{tokenValue}/payments/{payments}/methods` and `api/v2/shop/payments/{id}/methods` endpoints have been removed and changed into collection request with 2 parameters `api/v2/shop/payment-methods?paymentId={id}&tokenValue={token}`.
   Now when we do not provide parameters in response it returns all available `paymentMethods` in channel.
   Wrong parameters otherwise cause empty array `[]` in response and correct parameters return `paymentMethods` available for your `payment`.

1. All arguments of `src/Sylius/Bundle/ApiBundle/CommandHandler/Account/ResetPasswordHandler` have been removed and substituted with `Sylius\Bundle\CoreBundle\Security\UserPasswordResetter`.

1. The file `src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/ResetPassword.xml` has been renamed to `src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/AccountResetPassword.xml` and its short name has been changed from `ResetPasswordRequest` to `AccountResetPasswordRequest`.

1. Constructor of `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler` has been extended with `Sylius\Calendar\Provider\DateTimeProviderInterface` argument:

    ```diff
        public function __construct(
            private UserRepositoryInterface $userRepository,
            private MessageBusInterface $commandBus,
    -       private GeneratorInterface $generator
    +       private GeneratorInterface $generator,
    +       private DateTimeProviderInterface $calendar
        ) {
        }
    ```

1. Constructor of `\Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler` has been extended with `Sylius\Calendar\Provider\DateTimeProviderInterface` argument:

    ```diff
    -   public function __construct(private RepositoryInterface $shopUserRepository)
    -   {
    +   public function __construct(
    +       private RepositoryInterface $shopUserRepository,
    +       private DateTimeProviderInterface $calendar
    +   ) {
        }
    ```

1. The 2nd parameter `localeCode` has been removed from `src/Sylius/Bundle/ApiBundle/Command/Cart/PickupCart.php` and now is set automatically by `src/Sylius/Bundle/ApiBundle/DataTransformer/LocaleCodeAwareInputCommandDataTransformer.php`.

1. The responses of endpoints `/api/v2/admin/products` and `/api/v2/admin/products/{code}` have been changed in such a way that the field `defaultVariant` has been removed.

1. The configuration of `config/packages/security.yaml` has to be updated:

    ```diff
        security:
            access_control:
    +           - { path: "%sylius.security.new_api_admin_route%/reset-password-requests", role: IS_AUTHENTICATED_ANONYMOUSLY }
    ```

1. The second argument `$billingAddress` of `Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface::modify` 
   has become nullable.

1. The `Sylius\Bundle\ApiBundle\Assigner\OrderPromoCodeAssignerInterface` has been renamed to `Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface`.

### API clients refactor

#### New way of defining the clients

In the `1.12` version we changed the way of defining API clients in our behat contexts.
We extended the `Sylius/Behat/Client/ApiClientInterface` methods with extra parameter `$resource`.

  ```diff
  -  public function index(): Response
  +  public function index(string $resource): Response
    
  -  public function subResourceIndex(string $subResource, string $id): Response;
  +  public function subResourceIndex(string $resource, string $subResource, string $id): Response;
    
  -  public function show(string $id): Response;
  +  public function show(string $resource, string $id): Response;
  
  -  public function delete(string $id): Response;
  +  public function delete(string $resource, string $id): Response;
  
  -  public function applyTransition(string $id, string $transition, array $content = []): Response;
  +  public function applyTransition(string $resource, string $id, string $transition, array $content = []): Response;
  
  -  public function customItemAction(string $id, string $type, string $action): Response;
  +  public function customItemAction(string $resource, string $id, string $type, string $action): Response;
  
  -  public function buildCreateRequest(): void;
  +  public function buildCreateRequest(string $resource): void;
  
  -  public function buildUpdateRequest(string $id): void;
  +  public function buildUpdateRequest(string $resource, string $id): void;
  
  -  public function buildUploadRequest(): void;
  +  public function buildUploadRequest(string $resource): void;
  ```

With this change, we reduced the number of clients from one per resource to only **two**, one for the `shop` section and one for the `admin`. To make a call for a specific resource you must pass it inside the methods shown above.
You can see the actual difference in the example below:

  ```diff
  -  $this->avatarImagesClient->buildUploadRequest();
  +  $this->client->buildUploadRequest(Resources::AVATAR_IMAGES);
  ```

The `Sylius\Behat\Context\Api\Resources` class contains constants for all the defined resources.
We also removed the `Sylius\Behat\Client\ApiPlatformIriClient` alongside with `Sylius\Behat\Client\ApiIriClientInterface` as it's no more used.

#### Changes in contexts

The constructors of the behat contexts have changed in relation to the above improvements.
Here is the example change of the constructor of `Sylius\Behat\Context\Api\Admin\ManagingOrdersContext`

  ```diff
    public function __construct(
      private ApiClientInterface $client,
  -   private ApiClientInterface $shipmentsClient,
  -   private ApiClientInterface $paymentsClient,
       ...
    )
  ```

The list of changed contexts:

###### Admin:

- Sylius\Behat\Context\Api\Admin\ManagingAdministratorsContext
- Sylius\Behat\Context\Api\Admin\ManagingCatalogPromotionsContext
- Sylius\Behat\Context\Api\Admin\ManagingChannelsContext
- Sylius\Behat\Context\Api\Admin\ManagingCountriesContext
- Sylius\Behat\Context\Api\Admin\ManagingCurrenciesContext
- Sylius\Behat\Context\Api\Admin\ManagingCustomerGroupsContext
- Sylius\Behat\Context\Api\Admin\ManagingExchangeRatesContext
- Sylius\Behat\Context\Api\Admin\ManagingLocalesContext
- Sylius\Behat\Context\Api\Admin\ManagingOrdersContext
- Sylius\Behat\Context\Api\Admin\ManagingPaymentsContext
- Sylius\Behat\Context\Api\Admin\ManagingProductAssociationTypesContext
- Sylius\Behat\Context\Api\Admin\ManagingProductOptionsContext
- Sylius\Behat\Context\Api\Admin\ManagingProductReviewsContext
- Sylius\Behat\Context\Api\Admin\ManagingProductVariantsContext
- Sylius\Behat\Context\Api\Admin\ManagingProductsContext
- Sylius\Behat\Context\Api\Admin\ManagingPromotionsContext
- Sylius\Behat\Context\Api\Admin\ManagingShipmentsContext
- Sylius\Behat\Context\Api\Admin\ManagingShippingCategoriesContext
- Sylius\Behat\Context\Api\Admin\ManagingShippingMethodsContext
- Sylius\Behat\Context\Api\Admin\ManagingTaxCategoriesContext
- Sylius\Behat\Context\Api\Admin\ManagingZonesContext

###### Shop:

- Sylius\Behat\Context\Api\Shop\AddressContext
- Sylius\Behat\Context\Api\Shop\CartContext
- Sylius\Behat\Context\Api\Shop\ChannelContext
- Sylius\Behat\Context\Api\Shop\CheckoutContext
- Sylius\Behat\Context\Api\Shop\CurrencyContext
- Sylius\Behat\Context\Api\Shop\CustomerContext
- Sylius\Behat\Context\Api\Shop\HomepageContext
- Sylius\Behat\Context\Api\Shop\LocaleContext
- Sylius\Behat\Context\Api\Shop\LoginContext
- Sylius\Behat\Context\Api\Shop\OrderContext
- Sylius\Behat\Context\Api\Shop\OrderItemContext
- Sylius\Behat\Context\Api\Shop\PaymentContext
- Sylius\Behat\Context\Api\Shop\ProductContext
- Sylius\Behat\Context\Api\Shop\ProductReviewContext
- Sylius\Behat\Context\Api\Shop\ProductVariantContext
- Sylius\Behat\Context\Api\Shop\PromotionContext
- Sylius\Behat\Context\Api\Shop\RegistrationContext
- Sylius\Behat\Context\Api\Shop\ShipmentContext

### Creating Request refactored

We removed some methods from `Sylius\Behat\Client\RequestInterface`.
All of them have their corresponding implementation in `Sylius\Behat\Client\RequestFactory`.

 ```diff
 -    public static function index(
 -      ?string $section,
 -       string $resource,
 -       string $authorizationHeader,
 -       ?string $token = null
 -   ): self;

 -   public static function subResourceIndex(?string $section, string $resource, string $id, string $subResource): self;

 -   public static function show(
 -       ?string $section,
 -       string $resource,
 -       string $id,
 -       string $authorizationHeader,
 -       ?string $token = null
 -   ): self;

 -   public static function create(
 -       ?string $section,
 -       string $resource,
 -       string $authorizationHeader,
 -       ?string $token = null
 -   ): self;

 -   public static function update(
 -       ?string $section,
 -       string $resource,
 -       string $id,
 -       string $authorizationHeader,
 -       ?string $token = null
 -   ): self;

 -   public static function delete(
 -       ?string $section,
 -       string $resource,
 -       string $id,
 -       string $authorizationHeader,
 -       ?string $token = null
 -   ): self;

 -   public static function transition(?string $section, string $resource, string $id, string $transition): self;

 -   public static function customItemAction(?string $section, string $resource, string $id, string $type, string $action): self;

 -   public static function upload(
 -       ?string $section,
 -       string $resource,
 -       string $authorizationHeader,
 -       ?string $token = null
 -   ): self;

 -   public static function custom(string $url, string $method, array $additionalHeaders = [], ?string $token = null): self;
 ```

#### Changes in ApiPlatformClient

Followed by this change the constructor of `Sylius\Behat\Client\ApiPlatformClient` also changed:

 ```diff
     public function __construct(
            private AbstractBrowser $client,
            private SharedStorageInterface $sharedStorage,
 +          private RequestFactoryInterface $requestFactory,
            ...
        ) {
        }
 ```

Now we are calling method by parameter `$this->requestFactory` instead of calling `Request` class itself.
You can see the difference of usage below:

 ```diff
     public function index(string $resource): Response
    {
 -      $this->request = Request::index($this->section, $resource, $this->authorizationHeader, $this->getToken());
 +      $this->request = $this->requestFactory->index($this->section, $resource, $this->authorizationHeader, $this->getToken());

        return $this->request($this->request);
    }
 ```

The `Sylius\Behat\Client\ApiClientInterface::buildUploadRequest` method has been removed, as it's replaced by methods in `Sylius\Behat\Client\RequestBuilder`.
Example change of usage in `Sylius\Behat\Context\Api\Admin\ManagingAdministratorsContext`:

 ```diff
 -       $this->client->buildUploadRequest(Resources::AVATAR_IMAGES);
 -       $this->client->addParameter('owner', $this->iriConverter->getIriFromItem($administrator));
 -       $this->client->addFile('file', new UploadedFile($this->minkParameters['files_path'] . $avatar, basename($avatar)));
 -       $response = $this->client->upload();
        
 +       $builder = RequestBuilder::create(
 +           sprintf('/api/v2/%s/%s', 'admin', Resources::AVATAR_IMAGES),
 +           Request::METHOD_POST,
 +       );
 +       $builder->withHeader('CONTENT_TYPE', 'multipart/form-data');
 +       $builder->withHeader('HTTP_ACCEPT', 'application/ld+json');
 +       $builder->withHeader('HTTP_Authorization', 'Bearer ' . $this->sharedStorage->get('token'));
 +       $builder->withParameter('owner', $this->iriConverter->getIriFromItem($administrator));
 +       $builder->withFile('file', new UploadedFile($this->minkParameters['files_path'] . $avatar, basename($avatar)));

 +       $response = $this->client->request($builder->build());
 ```

As you can see the builder contains the methods that makes the request responsive for additional headers and parameters.

We also changed the `\Sylius\Behat\Client\ApiClientInterface::request` method visibility to public.

 ```diff
 -    private function request(RequestInterface $request): Response
 +    public function request(RequestInterface $request): Response
 ```

#### Defining ContentType changed

`Sylius\Behat\Client\ContentTypeGuide` provides solution for resolving http methods.
When we pass the proper `HttpRequest` method inside of the new `Sylius\Behat\Client\ContentTypeGuide::guide` method it returns appropriate json content type:

 ```
     public function guide(string $method): string
    {
        if ($method === HttpRequest::METHOD_PATCH) {
            return self::PATCH_CONTENT_TYPE;
        }

        if ($method === HttpRequest::METHOD_PUT) {
            return self::LINKED_DATA_JSON_CONTENT_TYPE;
        }

        return self::JSON_CONTENT_TYPE;
    }
 ```

Now to define the content type you just need to pass the appropriate `HttpRequest` type inside of the `guide()` method.
You can see the improvement of usage below:

 ```diff
  -   ['CONTENT_TYPE' => self::resolveHttpMethod($type)]
  +   $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide($type));
 ```

#### Contexts Change

The constructors of the behat contexts have changed in relation to the above improvements.

 ```diff
    public function __construct(
    +   private RequestFactoryInterface $requestFactory,
        ...
    ) {
 ```

List of changed contexts:

###### Admin

- Sylius\Behat\Context\Api\Admin\ManagingAdministratorsContext

###### Shop

- Sylius\Behat\Context\Api\Shop\CartContext
- Sylius\Behat\Context\Api\Shop\CheckoutContext
- Sylius\Behat\Context\Api\Shop\CustomerContext
- Sylius\Behat\Context\Api\Shop\LoginContext
- Sylius\Behat\Context\Api\Shop\OrderContext
- Sylius\Behat\Context\Api\Shop\ProductContext
