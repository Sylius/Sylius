# UPGRADE FROM `v1.12.X` TO `v1.13.0`

1. The `\Sylius\Component\User\Model\User::getSalt` method has been deprecated. Modern hashing algorithms do not
    require salt. Therefore, since Sylius 1.13 for all new users the salt is not generated and stored in the database
    anymore.

2. The `\Sylius\Component\User\Model\CredentialsHolderInterface` used with Symfony 6 extends
   `LegacyPasswordAuthenticatedUserInterface`.

3. Since Sylius 1.12 the `\Sylius\Component\User\Security\UserPbkdf2PasswordEncoder` is not used and
   the `\Sylius\Bundle\UserBundle\Security\UserPasswordHasher` is used instead. Therefore, since Sylius 1.13
   the `UserPbkdf2PasswordEncoder` becomes deprecated. 
