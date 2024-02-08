How to add an image to an entity? (One-To-One association)
==========================================================

As an example this cookbook will **add an image to the payment method**.
The example uses a mix of attributes, annotations and yaml for configuration, but you choose your preferred flavor.

1. Create the image entity class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The ``getPaymentMethod`` and ``setPaymentMethod`` are optional and are wrappers for the untyped ``getOwner`` and ``setOwner`` methods.
The image ``type`` is set in the constructor to an unspecific value for convenience because it's not relevant in a one-to-one relationship.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Payment;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Image;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_payment_method_image")
     *
     * @method PaymentMethod|null getOwner()
     */
    #[ORM\Entity]
    #[ORM\Table(name: 'sylius_payment_method_image')]
    class PaymentMethodImage extends Image
    {
        /**
         * @ORM\OneToOne(inversedBy="image", targetEntity="App\Entity\Payment\PaymentMethod")
         * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
         *
         * @var PaymentMethod|null
         */
        #[ORM\OneToOne(inversedBy: 'image', targetEntity: PaymentMethod::class)]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        protected $owner;

        public function __construct()
        {
            $this->type = 'default';
        }

        public function getPaymentMethod(): ?PaymentMethod
        {
            return $this->getOwner();
        }

        public function setPaymentMethod(?PaymentMethod $paymentMethod): void
        {
            $this->setOwner($paymentMethod);
        }
    }

2. Add the image to the owner entity class
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Payment;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ImageAwareInterface;
    use Sylius\Component\Core\Model\ImageInterface;
    use Sylius\Component\Core\Model\PaymentMethod as BasePaymentMethod;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_payment_method")
     */
    #[ORM\Entity]
    #[ORM\Table(name: 'sylius_payment_method')]
    class PaymentMethod extends BasePaymentMethod implements ImageAwareInterface
    {
        /**
         * @Assert\Valid
         * @ORM\OneToOne(mappedBy="owner", targetEntity="App\Entity\Payment\PaymentMethodImage", cascade={"all"}, orphanRemoval=true)
         */
        #[ORM\OneToOne(mappedBy: 'owner', targetEntity: PaymentMethodImage::class, cascade: ['all'], orphanRemoval: true)]
        protected ?PaymentMethodImage $image = null;

        /** @return PaymentMethodImage|null */
        public function getImage(): ?ImageInterface
        {
            return $this->image;
        }

        /** @var PaymentMethodImage|null $image */
        public function setImage(?ImageInterface $image): void
        {
            $image?->setOwner($this);

            $this->image = $image;
        }
    }

3. Create the image form type
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The constructor arguments are inlined to facilitate the autowiring, but you may inject them from the service configuration if necessary, an example of that is provided in the next step.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type;

    use App\Entity\Payment\PaymentMethodImage;
    use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class PaymentMethodImageType extends ImageType
    {
        public function __construct()
        {
            parent::__construct(PaymentMethodImage::class, ['sylius']);
        }

        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            parent::buildForm($builder, $options);

            $builder->remove('type');
        }

        public function getBlockPrefix(): string
        {
            return 'payment_method_image';
        }
    }

Register the image form type service if necessary, for example in case the autowiring or autoconfiguration are disabled or you wish to inject the constructor arguments from configuration:

.. code-block:: yaml

    # services.yml or another configuration file of your choice
    services:
        App\Form\Type\PaymentMethodImageType:
    # Removing the __constructor from the PaymentMethodImageType and configure the arguments if necessary
    #        arguments:
    #            - '%app.model.payment_method_image.class%'
    #            - ['sylius']
            tags:
                - { name: form.type }

4. Configure the image resource
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_resource:
        resources:
            app.payment_method_image:
                classes:
                    model: App\Entity\Payment\PaymentMethodImage
                    form: App\Form\Type\PaymentMethodImageType

5. Add the image field to the owner form type
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

A form type extension is used in this example because the image is added to a Sylius entity which already has a form type.
You should add the field directly to the owner entity's form type if it's part of your project source code.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Form\Extension;

    use App\Form\Type\PaymentMethodImageType;
    use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
    use Symfony\Component\Form\AbstractTypeExtension;
    use Symfony\Component\Form\FormBuilderInterface;

    final class PaymentMethodTypeExtension extends AbstractTypeExtension
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('image', PaymentMethodImageType::class, [
                    'label' => 'sylius.ui.image',
                    'required' => false,
                ])
            ;
        }

        public static function getExtendedTypes(): iterable
        {
            return [PaymentMethodType::class];
        }
    }

Register the owner entity's form type or form type extension service if necessary, for example in case the autowiring or autoconfiguration are disabled:

.. code-block:: yaml

    # services.yml or another configuration file of your choice
    services:
        App\Form\Extension\PaymentMethodTypeExtension:
            tags:
                - { name: form.type_extension }

6. Create an event subscriber that will upload the image file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Alternatively, you can create an event listener or you may reuse the Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener service.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\EventSubscriber;

    use Sylius\Component\Core\Model\ImageAwareInterface;
    use Sylius\Component\Core\Uploader\ImageUploaderInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\EventDispatcher\GenericEvent;
    use Webmozart\Assert\Assert;

    final class ImageUploadSubscriber implements EventSubscriberInterface
    {
        public function __construct(private ImageUploaderInterface $uploader)
        {
        }

        public static function getSubscribedEvents(): array
        {
            return [
                'sylius.payment_method.pre_create' => 'uploadImage',
                'sylius.payment_method.pre_update' => 'uploadImage',
            ];
        }

        public function uploadImage(GenericEvent $event): void
        {
            $subject = $event->getSubject();

            Assert::isInstanceOf($subject, ImageAwareInterface::class);

            $this->uploadSubjectImage($subject);
        }

        private function uploadSubjectImage(ImageAwareInterface $subject): void
        {
            $image = $subject->getImage();

            if (null === $image) {
                return;
            }

            if ($image->hasFile()) {
                $this->uploader->upload($image);
            }

            // Remove image if upload failed
            if (null === $image->getPath()) {
                $subject->setImage(null);
            }
        }
    }

Configure the service if it's not done automatically:

.. code-block:: yaml

    # services.yml or another configuration file of your choice
    services:
        App\EventSubscriber\ImageUploadSubscriber:
            arguments:
                - '@sylius.image_uploader'
            tags:
                - { name: kernel.event_subscriber }

7. Render the image field in the form view
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

For this example, we need to customize the form view from ``SyliusAdminBundle/views/PaymentMethod/_form.html.twig``,
so we have to copy it to ``templates/bundles/SyliusAdminBundle/PaymentMethod/_form.html.twig`` file and render the
``{{ form_row(form.image) }}`` field.

.. code-block:: twig

    {# templates/bundles/SyliusAdminBundle/PaymentMethod/_form.html.twig #}

    {% form_theme form '@SyliusAdmin/Form/imagesTheme.html.twig' %}

    {# all the contents copied from SyliusAdminBundle/views/PaymentMethod/_form.html.twig #}

    <div class="ui segment">
        {{ form_row(form.image) }}
    </div>

To display the current image you have to customize the rendering of the image field.
For that, copy the ``SyliusAdmin/Form/imagesTheme.html.twig`` file to
``templates/bundles/SyliusAdminBundle/Form/imagesTheme.html.twig`` and add the following code to it:

.. code-block:: twig

    {# this is a generic block that can be reused for other images #}
    {% block image_widget %}
        <div class="ui upload box segment" id="{{ form.vars.id }}">
            {% if form.vars.value.path|default(null) is not null %}
                <img class="ui small bordered image" src="{{ form.vars.value.path|imagine_filter('sylius_small') }}" alt="{{ form.vars.value.type }}" />
            {% endif %}
            <div class="ui element">
                {{ form_widget(form.file) }}
            </div>
            <div class="ui element">
                {{- form_errors(form.file) -}}
            </div>
        </div>
    {% endblock %}

    {%- block payment_method_image_widget -%}
        {{- block('image_widget') -}}
    {%- endblock -%}

8. Add validation constraints as necessary
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Payment;

    use Symfony\Component\Validator\Constraints as Assert;

    class PaymentMethodImage extends Image
    {
        #[Assert\Image(groups: ['sylius'])] // configure the options according to your needs
        protected $file;
    }

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Payment;

    use Symfony\Component\Validator\Constraints as Assert;

    class PaymentMethod extends BasePaymentMethod implements ImageAwareInterface
    {
        #[Assert\Valid]
        protected ?PaymentMethodImage $image = null;
    }

9. Generate a Doctrine migration and execute it
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: shell

    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
