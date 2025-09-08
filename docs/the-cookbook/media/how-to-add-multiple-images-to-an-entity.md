# How to add multiple images to an entity?

This guide explains how to associate **multiple images** with a single entity in Sylius 2.x using a **one-to-many relationship**. We'll use the `ShippingMethod` entity as an example, but this applies to any entity.

***

### Prerequisites

* Sylius 2.x is installed
* LiipImagineBundle is configured
* Doctrine is configured with migrations
* The media path (`public/media/image/`) is writable

***

### Step 1: Create the Image Entity

```php
<?php

// src/Entity/Shipping/ShippingMethodImage.php

namespace App\Entity\Shipping;

use Sylius\Component\Core\Model\Image;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'app_shipping_method_image')]
class ShippingMethodImage extends Image
{
    #[ORM\ManyToOne(
        targetEntity: ShippingMethod::class,
        inversedBy: 'images'
    )]
    #[ORM\JoinColumn(
        name: 'owner_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    protected $owner = null;
}
```

***

### Step 2: Extend the Owner Entity

```php
<?php

// src/Entity/Shipping/ShippingMethod.php

namespace App\Entity\Shipping;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_shipping_method')]
class ShippingMethod extends BaseShippingMethod implements ImagesAwareInterface
{
    #[ORM\OneToMany(
        targetEntity: ShippingMethodImage::class,
        mappedBy: 'owner',
        orphanRemoval: true,
        cascade: ['persist', 'remove', 'merge', 'detach']
    )]
    private Collection $images;

    public function __construct()
    {
        parent::__construct();
        $this->images = new ArrayCollection();
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getImagesByType(string $type): Collection
    {
        return $this->images->filter(fn(ImageInterface $image) => $image->getType() === $type);
    }

    public function hasImages(): bool
    {
        return !$this->images->isEmpty();
    }

    public function hasImage(ImageInterface $image): bool
    {
        return $this->images->contains($image);
    }

    public function addImage(ImageInterface $image): void
    {
        if (!$this->hasImage($image)) {
            $image->setOwner($this);
            $this->images->add($image);
        }
    }

    public function removeImage(ImageInterface $image): void
    {
        if ($this->hasImage($image)) {
            $image->setOwner(null);
            $this->images->removeElement($image);
        }
    }
}
```

***

### Step 3: Configure Resources

```yaml
# config/packages/_sylius.yaml
sylius_resource:
    resources:
        app.shipping_method_image:
            classes:
                model: App\Entity\Shipping\ShippingMethodImage
                form: App\Form\Type\ShippingMethodImageType
```

***

### Step 4: Create the Image Form Type

```php
<?php

// src/Form/Type/ShippingMethodImageType.php

namespace App\Form\Type;

use App\Entity\Shipping\ShippingMethodImage;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;

final class ShippingMethodImageType extends ImageType
{
    public function __construct()
    {
        parent::__construct(ShippingMethodImage::class, ['sylius']);
    }

    public function getBlockPrefix(): string
    {
        return 'app_shipping_method_image';
    }
}
```

Register the form type if necessary:

```yaml
# config/services.yaml
services:
    App\Form\Type\ShippingMethodImageType:
        tags:
            - { name: form.type }
```

***

### Step 5: Extend the Form for Shipping Method

```php
<?php

// src/Form/Extension/ShippingMethodTypeExtension.php

namespace App\Form\Extension;

use App\Form\Type\ShippingMethodImageType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

final class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('images', LiveCollectionType::class, [
            'entry_type' => ShippingMethodImageType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'sylius.form.shipping_method.images',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [ShippingMethodType::class];
    }
}
```

Register the extension:

```yaml
# config/services.yaml
services:
    App\Form\Extension\ShippingMethodTypeExtension:
        tags:
            - { name: form.type_extension }
```

***

### Step 6: Enable Image Upload via Listener

```yaml
# config/services.yaml
services:
    app.listener.images_upload:
        class: Sylius\Bundle\CoreBundle\EventListener\ImagesUploadListener
        parent: sylius.listener.images_upload
        autowire: true
        public: false
        tags:
            - { name: kernel.event_listener, event: sylius.shipping_method.pre_create, method: uploadImages }
            - { name: kernel.event_listener, event: sylius.shipping_method.pre_update, method: uploadImages }
```

***

### Step 7: (Optional) Add Validation Constraints

```php
// src/Entity/Shipping/ShippingMethodImage.php

use Symfony\Component\Validator\Constraints as Assert;
​
    #[Assert\Image(
        groups: ['sylius'],
        mimeTypes: ['image/png', 'image/jpeg', 'image/gif'],
        maxSize: '10M'
    )]
    protected $file;
```

```php
// App\Entity\Shipping\ShippingMethod.php

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Valid]
private Collection $images;
```

***

### Step 8: Customize the Shipping Method twig hooks

Inspect the shipping method form, let's assume you want to add new new section `Images` that is between the general and configuration.

<figure><img src="../../.gitbook/assets/image (3) (2).png" alt=""><figcaption></figcaption></figure>

1. Configure hooks for your new images section

```yaml
# config/packages/_sylius.yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.shipping_method.update.content.form#left':
            images:
                template: '/admin/shipping_method/form/sections/images.html.twig'
                priority: 150 # to place it between general and configuration sections
                
        'sylius_admin.shipping_method.update.content.form.images':
            content:
                template: '/admin/shipping_method/form/sections/images/content.html.twig'
                priority: 100
            add_button:
                template: '/admin/shipping_method/form/sections/images/add_button.html.twig'
                priority: 0
                
        'sylius_admin.shipping_method.create.content.form#left':
            images:
                template: '/admin/shipping_method/form/sections/images.html.twig'
                priority: 150

        'sylius_admin.shipping_method.create.content.form.images':
            content:
                template: '/admin/shipping_method/form/sections/images/content.html.twig'
                priority: 100
            add_button:
                template: '/admin/shipping_method/form/sections/images/add_button.html.twig'
                priority: 0
```

2. Create templates for your hooks

```twig
{# templates/admin/shipping_method/form/sections/images.html.twig #}

<div class="card mb-3">
    <div class="card-header">
        <div class="card-title">
            {{ 'sylius.ui.images'|trans }}
        </div>
    </div>
    <div class="card-body">
        {% hook 'images' %}
    </div>
</div>
```

```twig
{# templates/admin/shipping_method/form/sections/images/content.html.twig #}

{% set images = hookable_metadata.context.form.images %}

<div class="row">
    {% for image_form in images %}
        <div class="col-12 col-md-6 row mb-4">
            <div class="col-auto">
                <div>
                    {% if image_form.vars.value.path is not null %}
                        <span class="avatar avatar-xl" style="background-image: url('{{ image_form.vars.value.path|imagine_filter('sylius_small') }}')"></span>
                    {% else %}
                        <span class="avatar avatar-xl"></span>
                    {% endif %}
                </div>
                <div class="mt-3 d-flex items-center">
                    {{ form_widget(image_form.vars.button_delete, { label: 'sylius.ui.delete'|trans, attr: { class: 'btn btn-outline-danger w-100' }}) }}
                </div>
            </div>
            <div class="col">
                <div class="mb-3">
                    {{ form_row(image_form.file) }}
                </div>
            </div>
        </div>
    {% endfor %}
</div>
```

```twig
{# templates/admin/shipping_method/form/sections/images/add_button.html.twig #}

<div class="d-grid gap-2">
    {{ form_widget(hookable_metadata.context.form.images.vars.button_add) }}
</div>
```

***

### Step 9: Generate and Run Migrations

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

***

### Step 10: Result <a href="#step-8-customize-the-shipping-method-twig-hooks" id="step-8-customize-the-shipping-method-twig-hooks"></a>

<figure><img src="../../.gitbook/assets/image (31).png" alt=""><figcaption></figcaption></figure>

The Shipping method has now collection of images :tada:!
