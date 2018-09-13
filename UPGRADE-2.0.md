# UPGRADE FROM `v1.X.X` TO `v2.0.0`

## Codebase

* Doctrine MongoDB and PHPCR is not longer supported in ResourceBundle and GridBundle:
    
    * The following classes were removed:

        * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource`
        * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\Driver`
        * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder`
        * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilderInterface`
        * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionVisitor`
        * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison`
        * `Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineODMDriver`
        * `Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrinePHPCRDriver`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\TranslatableRepository`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\DefaultParentListener`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameFilterListener`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameResolverListener`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Builder\DefaultFormBuilder`
        * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository`
        * `Sylius\Bundle\ResourceBundle\EventListener\ODMMappedSuperClassSubscriber`
        * `Sylius\Bundle\ResourceBundle\EventListener\ODMRepositoryClassSubscriber`
        * `Sylius\Bundle\ResourceBundle\EventListener\ODMTranslatableListener`

    * The following services were removed:
    
        * `sylius.event_subscriber.odm_mapped_super_class`
        * `sylius.event_subscriber.odm_repository_class`
        * `sylius.grid_driver.doctrine.phpcrodm`
        
    * The following parameters were removed:
    
        * `sylius.mongodb_odm.repository.class`
        * `sylius.phpcr_odm.repository.class`
