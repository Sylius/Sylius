.. rst-class:: plus-doc

How to add a custom model accessible per admin channel?
=======================================================

In some cases you may be needing to add new model to your application that is accessible on admin site per admin channel

1. Define custom model, our example will be basic on **Supplier entity**
------------------------------------------------------------------------

For making entity use guide: :doc:`here </cookbook/entities/custom-model>`.

Next create channel field with relation to ``channel`` entity:

.. code-block:: php

    /**
      * @var ChannelInterface
      * @ORM\ManyToOne(targetEntity="Sylius\Plus\Entity\ChannelInterface")
      * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", nullable=true)
      */
     protected $channel;

Create properly migration for this changes

In order to be owned access to choosing only proper channel, create ChannelPerAdminChannelType:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form;

    use Sylius\Component\Resource\Repository\RepositoryInterface;
    use Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface;
    use Sylius\Plus\Entity\ChannelInterface;
    use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\Options;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    final class ChannelPerAdminChannelType extends AbstractType
    {
        /** @var RepositoryInterface */
        private $channelRepository;

        /** @var AdminChannelProviderInterface */
        private $adminChannelProvider;

        public function __construct(
            RepositoryInterface $channelRepository,
            AdminChannelProviderInterface $adminChannelProvider
        ) {
            $this->channelRepository = $channelRepository;
            $this->adminChannelProvider = $adminChannelProvider;
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            if ($options['multiple']) {
                $builder->addModelTransformer(new CollectionToArrayTransformer());
            }
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'choices' => function (Options $options): array {
                    /** @var ChannelInterface|null $adminChannel */
                    $adminChannel = $this->adminChannelProvider->getChannel();

                    if ($adminChannel != null) {
                        return [$adminChannel];
                    }

                    return $this->channelRepository->findAll();
                },
                'choice_value' => 'code',
                'choice_label' => 'name',
                'choice_translation_domain' => false,
            ]);
        }

        public function getParent(): string
        {
            return ChoiceType::class;
        }

        public function getBlockPrefix(): string
        {
            return 'sylius_channel_choice';
        }
    }

.. code-block:: yaml

    App\Form\ChannelPerAdminChannelType:
        arguments: ['@sylius.repository.channel', '@Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface']
        tags: ['form.type']

Service ``Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface`` allows to get proper channel for present admin.

Next create ``App\Form\SupplierType``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form;

    use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class SupplierType extends AbstractResourceType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Name'
                ])
                ->add('channel', ChannelPerAdminChannelType::class, [
                    'label' => 'Channel'
                ])
            ;
        }

        public function getBlockPrefix(): string
        {
            return 'supplier';
        }
    }

.. code-block:: yaml

    App\Form\ChannelPerAdminChannelType:
            arguments: ['@sylius.repository.channel', '@Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface']
            tags: ['form.type']

Remember to register ``App\Form\ChannelPerAdminChannelType`` for resource:

.. code-block:: yaml

    sylius_resource:
    resources:
        app.supplier:
            driver: doctrine/orm
            classes:
                model: App\Entity\Supplier
                repository: App\Repository\SupplierRepository
   +            form: App\Form\SupplierType


1. Add your entity to admin menu:
----------------------------------

Adding new entity to admin menu is described in point ``How to customize Admin Menu`` into :doc:`this </customization/menu>`. guide

1. Add access via administrator roles (ACL/RBAC):
-------------------------------------------------

Add access to all resource sections: ``index, create, update, delete, show, bulk_delete``

.. code-block:: yaml

    sylius_plus:
    permissions:
        app_admin_supplier_index:
            parent: suppliers
            label: action.index
        app_admin_supplier_create:
            parent: suppliers
            label: action.create
        app_admin_supplier_update:
            parent: suppliers
            label: action.update
        app_admin_supplier_delete:
            parent: suppliers
            label: action.delete
        app_admin_supplier_show:
            parent: suppliers
            label: action.show
        app_admin_supplier_bulk_delete:
            parent: suppliers
            label: action.bulk_delete

More information about accessing administrator roles (ACL/RBAC) you can find :doc:`here </book/customers/admin_user>`.

Create ``App\Checker\ResourceChannelEnabilibityChecker`` and decorate ``Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelEnabilibityCheckerInterface`` next add ``Supplier`` as checking resource:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Checker;

    use Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelEnabilibityCheckerInterface;
    use Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelEnabilibityChecker as DecoratedResourceChannelEnabilibityChecker;

    final class ResourceChannelEnabilibityChecker implements ResourceChannelEnabilibityCheckerInterface
    {
        /** @var DecoratedResourceChannelEnabilibityChecker */
        private $decoratedResourceChannelEnabilibityChecker;

        public function __construct(DecoratedResourceChannelEnabilibityChecker $decoratedResourceChannelEnabilibityChecker)
        {
            $this->decoratedResourceChannelEnabilibityChecker = $decoratedResourceChannelEnabilibityChecker;
        }

        public function forResourceName(string $resourceName): bool
        {
            if (!$this->decoratedResourceChannelEnabilibityChecker->forResourceName($resourceName)) {
                return in_array($resourceName, [
                    'supplier'
                ]);
            }

            return true;
        }
    }

.. code-block:: yaml

    App\Checker\ResourceChannelEnabilibityChecker:
        decorates: Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelEnabilibityCheckerInterface
        arguments: ['@.inner']

Create ``App\Checker\ResourceChannelChecker`` and decorate ``Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelCheckerInterface`` next add ``Supplier`` as checking resource.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Checker;

    use Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelCheckerInterface;
    use Sylius\Plus\Entity\ChannelInterface;

    final class ResourceChannelChecker implements ResourceChannelCheckerInterface
    {
        /** @var ResourceChannelCheckerInterface */
        private $decoratedResourceChannelChecker;

        public function __construct(ResourceChannelCheckerInterface $decoratedResourceChannelChecker)
        {
            $this->decoratedResourceChannelChecker = $decoratedResourceChannelChecker;
        }

        public function isFromChannel(object $resource, ChannelInterface $channel): bool
        {
            // this condition is needed because while creating symfony form(our edit/create section), given object has all fields as null. After created an empty form this iteration is repeat and properly checked
            if ($resource instanceof Supplier && $resource->getChannel() === null) {
                return true;
            }

            return $this->decoratedResourceChannelChecker->isFromChannel($resource, $channel);
        }
    }

.. code-block:: yaml

    App\Checker\ResourceChannelChecker:
        decorates: Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelCheckerInterface
        arguments: ['@.inner']

In this moment access for our single resources should work properly with all restricties.

For restricting our index, we need extend our resource repository for ``App\Repository\SupplierRepository`` with ``getQueryBuilder()`` method:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Repository;

    use Doctrine\ORM\QueryBuilder;
    use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

    final class SupplierRepository extends EntityRepository
    {
        public function getQueryBuilder(): QueryBuilder
        {
            return $this->createQueryBuilder('o');
        }
    }

Remember to register ``App\Repository\SupplierRepository`` for resource:

.. code-block:: yaml

    sylius_resource:
    resources:
        app.supplier:
            driver: doctrine/orm
            classes:
                model: App\Entity\Supplier
                form: App\Form\SupplierType
  +             repository: App\Repository\SupplierRepository



Next add ``SupplierRestrictingProductListQueryBuilder``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Doctrine\ORM;

    use App\Repository\SupplierRepository;
    use Doctrine\ORM\QueryBuilder;
    use Sylius\Component\Core\Model\ChannelInterface;
    use Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface;

    final class SupplierRestrictingProductListQueryBuilder
    {
        /** @var AdminChannelProviderInterface */
        private $adminChannelProvider;

        /** @var SupplierRepository */
        private $supplierRepository;

        public function __construct(
            AdminChannelProviderInterface $adminChannelProvider,
            SupplierRepository $supplierRepository
        ) {
            $this->adminChannelProvider = $adminChannelProvider;
            $this->supplierRepository = $supplierRepository;
        }

        public function create(): QueryBuilder
        {
            $listQueryBuilder = $this->supplierRepository->getQueryBuilder();

            /** @var ChannelInterface|null $channel */
            $channel = $this->adminChannelProvider->getChannel();
            if ($channel === null) {
                return $listQueryBuilder;
            }

            return $listQueryBuilder
                ->andWhere('o.channel = :channel')
                ->setParameter('channel', $channel)
            ;
        }
    }

.. code-block:: yaml

    App\Doctrine\ORM\SupplierRestrictingProductListQueryBuilder:
        public: true
        class: App\Doctrine\ORM\SupplierRestrictingProductListQueryBuilder
        arguments: ['@Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface', '@app.repository.supplier']

Add method to your grid:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_admin_supplier:
                driver:
                    name: doctrine/orm
                    options:
                        class: App\Entity\Supplier
                        repository:
  +                       method: [expr:service('App\\Doctrine\\ORM\\SupplierRestrictingProductListQueryBuilder'), create]
