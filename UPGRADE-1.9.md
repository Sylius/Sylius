# UPGRADE FROM `v1.8.x` TO `v1.9.0`

1. `Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface` has been split into `Sylius\Bundle\ApiBundle\Command\PaymentIdSubresourceAwareInterface`
 and `Sylius\Bundle\ApiBundle\Command\ShipmentIdSubresourceAwareInterface`. Classes which implement these interfaces don't 
 need to specify request attribute. This logic was transferred to coresponding data transformer. Please adjust your code accordingly.
