# UPGRADE FROM `v1.1.X` TO `v1.2.0`

* __BC BREAK:__ `Sylius\Bundle\UserBundle\Controller\UserController`'s method `addFlash` has been renamed to
  `addTranslatedFlash` with added scalar typehints for compatibility with both Symfony 3.4 and Symfony 4.0.

* `Sylius\Bundle\CoreBundle\Installer\Requirement\FilesystemRequirements::__construct` deprecates passing
  `string $rootDir` as a second argument, remove it from your calls to be compatible with 2.0 release.

* The deprecated form mapping feature in SonataCoreBundle has been disabled in the app configuration included from SyliusCoreBundle.
  If you depend on the feature in your application, you will need to make the necessary changes. Refer to
  https://github.com/sonata-project/SonataCoreBundle/pull/462 for more information. 

* Class `Sylius\Component\Core\Resolver\DefaultShippingMethodResolver` has been deprecated and will be removed in 2.0. `Sylius\Component\Core\Resolver\EligibleDefaultShippingMethodResolver` should be used instead.
