.. rst-class:: plus-doc

How to add a custom model accessible for respective channel administrators?
===========================================================================

Given that you are using Sylius Plus, the licensed edition of Sylius, you may have
the Administrators per Channel defined in your application. Thus when you add a new,
channel-based entity to it, you will need to enable this entity to be viewed only by the relevant channel admins.

1. Define your custom model, our example will be the **Supplier entity**
------------------------------------------------------------------------

In order to prepare a simple Entity follow :doc:`this guide </cookbook/entities/custom-model>`.

Remember to then add your entity to the admin menu. Adding a new entity to the admin menu
is described in the section ``How to customize Admin Menu`` of :doc:`this guide </customization/menu>`.

* Having your Supplier entity created, add a channel field with relation to the ``Channel`` entity:

.. code-block:: php

    /**
     * @var ChannelInterface
     * @ORM\ManyToOne(targetEntity="Sylius\Plus\Entity\ChannelInterface")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", nullable=true)
     */
    protected $channel;

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

* Assuming that your database was up-to-date before these changes, create a proper migration and use it:

.. code-block:: bash

    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate

* Next, create a form type for your entity:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type;

    use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
    use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
    use Sylius\Plus\ChannelAdmin\Application\Provider\AvailableChannelsForAdminProviderInterface;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class SupplierType extends AbstractResourceType
    {
        /** @var AvailableChannelsForAdminProviderInterface */
        private $availableChannelsForAdminProvider;

        public function __construct(
            string $dataClass,
            array $validationGroups,
            AvailableChannelsForAdminProviderInterface $availableChannelsForAdminProvider
        ) {
            parent::__construct($dataClass, $validationGroups);

            $this->availableChannelsForAdminProvider = $availableChannelsForAdminProvider;
        }

        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('name', TextType::class, [
                    'label' => 'Name'
                ])
                ->add('channel', ChannelChoiceType::class, [
                    'choices' => $this->availableChannelsForAdminProvider->getChannels(),
                    'label' => 'sylius.ui.channel',
                ])
            ;
        }

        public function getBlockPrefix(): string
        {
            return 'supplier';
        }
    }

.. code-block:: yaml

    # config/services.yaml
    App\Form\Type\SupplierType:
        arguments:
            - 'App\Entity\Supplier'
            - 'sylius'
            - '@Sylius\Plus\ChannelAdmin\Application\Provider\AvailableChannelsForAdminProviderInterface'
        tags: ['form.type']

The ``Sylius\Plus\ChannelAdmin\Application\Provider\AvailableChannelsForAdminProviderInterface`` service allows getting
a list of proper channels for the currently logged in admin user.

Remember to register ``App\Form\SupplierType`` for resource:

.. code-block:: diff

    sylius_resource:
        resources:
            app.supplier:
                driver: doctrine/orm
                classes:
                    model: App\Entity\Supplier
       +            form: App\Form\Type\SupplierType

2. Restrict access to the entity for the respective channel administrator:
--------------------------------------------------------------------------

.. note::

    More information about using administrator roles (ACL/RBAC) can be found :doc:`here </book/customers/admin_user>`.

* Add `supplier` to restricted resources:

.. code-block:: yaml

    sylius_plus:
        channel_admin:
            restricted_resources:
                supplier: ~

* Create ``App\Checker\SupplierResourceChannelChecker`` and tag this service with `sylius_plus.channel_admin.resource_channel_checker`:

.. tip::

    If the created entity implements the ``Sylius\Component\Channel\Model\ChannelAwareInterface`` interface,
    everything will work without having to do this step and create ``SupplierResourceChannelChecker``.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Checker;

    use App\Entity\Supplier;
    use Sylius\Plus\ChannelAdmin\Application\Checker\ResourceChannelCheckerInterface;
    use Sylius\Plus\Entity\ChannelInterface;

    final class SupplierResourceChannelChecker implements ResourceChannelCheckerInterface
    {
        public function isFromChannel(object $resource, ChannelInterface $channel): bool
        {
            if ($resource instanceof Supplier && in_array($resource->getChannel(), [$channel, null], true)) {
                return true;
            }

            return false;
        }
    }

.. code-block:: yaml

    # config/services.yaml
    App\Checker\SupplierResourceChannelChecker:
        tags:
            - { name: sylius_plus.channel_admin.resource_channel_checker }

After that, access to the resource should work properly with all restrictions.

* Next add ``RestrictingSupplierListQueryBuilder``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Doctrine\ORM;

    use Doctrine\ORM\QueryBuilder;
    use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
    use Sylius\Component\Core\Model\ChannelInterface;
    use Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface;

    final class RestrictingSupplierListQueryBuilder
    {
        /** @var AdminChannelProviderInterface */
        private $adminChannelProvider;

        /** @var EntityRepository */
        private $supplierRepository;

        public function __construct(
            AdminChannelProviderInterface $adminChannelProvider,
            EntityRepository $supplierRepository
        ) {
            $this->adminChannelProvider = $adminChannelProvider;
            $this->supplierRepository = $supplierRepository;
        }

        public function create(): QueryBuilder
        {
            $listQueryBuilder = $this->supplierRepository->createQueryBuilder('o');

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

    # config/services.yaml
    App\Doctrine\ORM\RestrictingSupplierListQueryBuilder:
        public: true
        class: App\Doctrine\ORM\RestrictingSupplierListQueryBuilder
        arguments: ['@Sylius\Plus\ChannelAdmin\Application\Provider\AdminChannelProviderInterface', '@app.repository.supplier']

* Add method to the Suppliers grid:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_admin_supplier:
                driver:
                    name: doctrine/orm
                    options:
                        class: App\Entity\Supplier
  +                     repository:
  +                         method: [expr:service('App\\Doctrine\\ORM\\RestrictingSupplierListQueryBuilder'), create]

Well done! That's it, now you have a Supplier entity, that is accessible within the Sylius Plus Administrators per Channel feature!
