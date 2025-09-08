# How to add one image to an entity?

This guide demonstrates how to add a **one-to-one image association** to an entity in **Sylius 2.x**. We'll use the `PaymentMethod` entity as an example, but the same approach applies to any other entity.

### Prerequisites

* Your project uses Sylius 2.x
* [LiipImagineBundle](https://symfony.com/bundles/LiipImagineBundle/current/index.html) is installed and configured
* Writable `public/media/image/` directory
* Doctrine migrations are enabled

***

### Step 1: Create the Image Entity

<pre class="language-php"><code class="lang-php">&#x3C;?php

<strong>// src/Entity/Payment/PaymentMethodImage.php
</strong>
namespace App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Image;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_payment_method_image')]
class PaymentMethodImage extends Image
{
    #[ORM\OneToOne(inversedBy: 'image', targetEntity: PaymentMethod::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected $owner;

    public function __construct()
    {
        $this->type = 'default';
    }
}
</code></pre>

***

### Step 2: Update the Owner Entity

```php
<?php

// src/Entity/Payment/PaymentMethod.php

namespace App\Entity\Payment;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\PaymentMethod as BasePaymentMethod;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_payment_method')]
class PaymentMethod extends BasePaymentMethod implements ImageAwareInterface
{
    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: PaymentMethodImage::class, cascade: ['all'], orphanRemoval: true)]
    protected ?PaymentMethodImage $image = null;

    public function getImage(): ?ImageInterface
    {
        return $this->image;
    }

    public function setImage(?ImageInterface $image): void
    {
        $image?->setOwner($this);
        $this->image = $image;
    }
}
```

***

### Step 3: Create the Image Form Type

```php
<?php

// src/Form/Type/PaymentMethodImageType.php

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
```

***

### Step 4: Register the Form Type

```yaml
# config/services.yaml
services:
    App\Form\Type\PaymentMethodImageType:
        tags:
            - { name: form.type }
```

***

### Step 5: Configure the Image Resource

```yaml
# config/packages/_sylius.yaml
sylius_resource:
    resources:
        app.payment_method_image:
            classes:
                model: App\Entity\Payment\PaymentMethodImage
                form: App\Form\Type\PaymentMethodImageType
```

***

### Step 6: Extend the Owner Form Type

```php
<?php

// src/Form/Extension/PaymentMethodTypeExtension.php

namespace App\Form\Extension;

use App\Form\Type\PaymentMethodImageType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class PaymentMethodTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('image', PaymentMethodImageType::class, [
            'label' => 'sylius.ui.image',
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [PaymentMethodType::class];
    }
}
```

Register the form extension:

```yaml
# config/services.yaml
services:
    App\Form\Extension\PaymentMethodTypeExtension:
        tags:
            - { name: form.type_extension }
```

***

### Step 7: Handle Image Upload with a Subscriber

```php
<?php

// src/EventSubscriber/ImageUploadSubscriber.php

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
        if (null === $image) return;
        if ($image->hasFile()) $this->uploader->upload($image);
        if (null === $image->getPath()) $subject->setImage(null);
    }
}
```

```yaml
# config/services.yaml
services:
    App\EventSubscriber\ImageUploadSubscriber:
        arguments:
            - '@sylius.uploader.image'
        tags:
            - { name: kernel.event_subscriber }
```

***

### Step 8: Customize the Payment Method twig hooks

Inspect the payment method form, let's assume you want to add new field to `general` section.

<figure><img src="../../.gitbook/assets/image (33).png" alt=""><figcaption></figcaption></figure>

To add the image field in the `general` section of the Payment Method form using Twig hooks:

1. Create the template at `templates/admin/payment_method/form/sections/general/image.html.twig`:

```twig
{% set image = hookable_metadata.context.form.image %}

<div class="col-12 col-md-12">
    <div class="mb-3">
        {{ form_label(image) }}
    </div>
    <div class="mb-3">
        <span class="avatar avatar-xl">
            {% if image.vars.value.path is defined and image.vars.value.path is not empty %}
                <img src="{{ image.vars.value.path|imagine_filter('sylius_small') }}" />
            {% endif %}
        </span>
    </div>
    <div class="mb-3">
        {{ form_widget(image.file) }}
    </div>
</div>
```

2. Update the Twig hooks configuration for the given `sylius_admin.payment_method.create.content.form.sections.general` hook:

```yaml
# config/packages/_sylius.yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.payment_method.create.content.form.sections.general':
            image:
                template: '/admin/payment_method/form/sections/general/image.html.twig'
        
        'sylius_admin.payment_method.update.content.form.sections.general':
            image:
                template: '/admin/payment_method/form/sections/general/image.html.twig'
```

{% hint style="success" %}
Find out more about twig hooks [here](https://stack.sylius.com/twig-hooks/getting-started).
{% endhint %}

***

### Step 9: (Optional) Add Validation Constraints

```php
// App\Entity\Payment\PaymentMethodImage.php

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Image(
    maxSize: '10M',
    mimeTypes: ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
    mimeTypesMessage: 'Please upload a valid image (PNG, JPG, JPEG, GIF).',
    groups: ['sylius']
)]
protected $file;
```

```php
// App\Entity\Payment\PaymentMethod.php

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Valid]
protected ?PaymentMethodImage $image = null;
```

***

### Step 10: Generate and Run Migrations

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

***

### Step 11: Result

<figure><img src="../../.gitbook/assets/image (32).png" alt=""><figcaption></figcaption></figure>

The Payment method has now image field :tada:!
