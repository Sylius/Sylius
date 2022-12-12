# UPGRADE FROM `v1.12.X` TO `v1.13.0`

1. The `sylius_user.encoder` configuration key has been removed. If you have overridden the default encoder, you can
   use the `security.password_hashers` configuration key only.

2. The `\Sylius\Bundle\UserBundle\EventListener\UpdateUserEncoderListener` has been removed.

3. The following methods have been deprecated:
    * `\Sylius\Component\User\Model\User::getEncoderName`
    * `\Sylius\Component\User\Model\User::setEncoderName`
    * `\Sylius\Component\User\Model\UserInterface::setEncoderName`
   
    Therefore, since Sylius 1.13 for all new users, the encoder name is not stored in the database anymore.
