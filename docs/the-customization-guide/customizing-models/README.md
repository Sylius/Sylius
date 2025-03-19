---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Customizing Models

In Sylius, all models are located in the `Sylius\Component\*ComponentName*\Model` namespaces with corresponding interfaces. Many models are also extended within the Core component. If you want to customize a model that exists in Core, make sure to extend the Core version rather than the base model from the component itself.

{% hint style="info" %}
Some models in Sylius are **translatable**, which means they support multiple languages. This guide covers customization for both regular and translatable models.
{% endhint %}

## Why customize a model?

You might want to customize models to meet specific business needs. Here are a few examples:

* Add a `flag` field to the `Country` model.
* Add `secondNumber` to the `Customer` model.
* Modify `reviewSubject` in the `Review` model.
* Add an `icon` to the `PaymentMethod` model.

## How to customize a model?

### Example: Adding a `flag` Field to the `Country` Model

In this example, we will customize the `Country` model by adding a `flag` field to store an image URL.

1.  **Extend the Model**\
    In `src/Entity/Addressing/Country.php`, extend the `Sylius\Component\Addressing\Model\Country` class.

    ```php
    <?php
    declare(strict_types=1);

    namespace App\Entity\Addressing;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Addressing\Model\Country as BaseCountry;
    use Sylius\Component\Addressing\Model\CountryInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_country")
     */
    class Country extends BaseCountry implements CountryInterface
    {
        /** @ORM\Column(type="string", nullable=true) */
        private $flag;

        public function getFlag(): ?string
        {
            return $this->flag;
        }

        public function setFlag(string $flag): void
        {
            $this->flag = $flag;
        }
    }
    ```
2.  **Configure the Model in `config/packages/_sylius.yaml`**\
    Under the `sylius_addressing` section, ensure the custom model is referenced correctly.

    ```yaml
    sylius_addressing:
        resources:
            country:
                classes:
                    model: App\Entity\Addressing\Country
    ```
3. **Update the Database**\
   Choose one of these two methods:
   *   **Direct Schema Update** (not recommended for production):

       ```bash
       php bin/console doctrine:schema:update --force
       ```
   *   **Migrations** (recommended):

       ```bash
       php bin/console doctrine:migrations:diff
       php bin/console doctrine:migrations:migrate
       ```
4. **Add the Field in the Admin Panel (Optional)**\
   To make the new `flag` field editable in the admin panel, you’ll need to update its form type. Check [here](https://old-docs.sylius.com/en/1.13/customization/form.html).

## How to customize a translatable model?

Translatable models, like `ShippingMethod`, support multilingual content. In this example, we’ll add an `estimatedDeliveryTime` field to `ShippingMethod`.

### Example: Adding an `estimatedDeliveryTime` Field to the `ShippingMethod` Model

1.  **Extend the Model**\
    In `src/Entity/Shipping/ShippingMethod.php`, extend the `Sylius\Component\Core\Model\ShippingMethod` class.

    ```php
    <?php
    declare(strict_types=1);

    namespace App\Entity\Shipping;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;
    use Sylius\Component\Core\Model\ShippingMethodInterface;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_shipping_method")
     */
    class ShippingMethod extends BaseShippingMethod implements ShippingMethodInterface
    {
        /** @ORM\Column(type="string", nullable=true) */
        private $estimatedDeliveryTime;

        public function getEstimatedDeliveryTime(): ?string
        {
            return $this->estimatedDeliveryTime;
        }

        public function setEstimatedDeliveryTime(?string $estimatedDeliveryTime): void
        {
            $this->estimatedDeliveryTime = $estimatedDeliveryTime;
        }

        protected function createTranslation(): ShippingMethodTranslationInterface
        {
            return new ShippingMethodTranslation();
        }
    }
    ```
2.  **Configure the Model in `_sylius.yaml`**\
    Update `config/packages/_sylius.yaml` with the following configuration:

    ```yaml
    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: App\Entity\Shipping\ShippingMethod
    ```
3.  **Update the Database**\
    Run the migrations:

    ```bash
    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate
    ```
4. **Add the Field in the Admin Panel (Optional)**\
   If you want administrators to edit `estimatedDeliveryTime`, update the form type accordingly. See form type customization here.

### Example: Adding a translatable `deliveryConditions` Field to the `ShippingMethod` Model

Let’s assume you want to add a multilingual `deliveryConditions` field to `ShippingMethod`.

1.  **Extend the Translation Class**\
    In `src/Entity/Shipping/ShippingMethodTranslation.php`, extend `ShippingMethodTranslation` and add the `deliveryConditions` field.

    ```php
    <?php
    declare(strict_types=1);

    namespace App\Entity\Shipping;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslation as BaseShippingMethodTranslation;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_shipping_method_translation")
     */
    class ShippingMethodTranslation extends BaseShippingMethodTranslation implements ShippingMethodTranslationInterface
    {
        /** @ORM\Column(type="string", nullable=true) */
        private $deliveryConditions;

        public function getDeliveryConditions(): ?string
        {
            return $this->deliveryConditions;
        }

        public function setDeliveryConditions(?string $deliveryConditions): void
        {
            $this->deliveryConditions = $deliveryConditions;
        }
    }
    ```
2.  **Add Access Methods in `ShippingMethod`**\
    Modify `src/Entity/Shipping/ShippingMethod.php` to get and set `deliveryConditions`:

    ```php
    public function getDeliveryConditions(): ?string
    {
        return $this->getTranslation()->getDeliveryConditions();
    }

    public function setDeliveryConditions(?string $deliveryConditions): void
    {
        $this->getTranslation()->setDeliveryConditions($deliveryConditions);
    }
    ```
3.  **Configure in `_sylius.yaml`**\
    Update `config/packages/_sylius.yaml` to include both the main model and its translation.

    ```yaml
    sylius_shipping:
        resources:
            shipping_method:
                classes:
                    model: App\Entity\Shipping\ShippingMethod
                translation:
                    classes:
                        model: App\Entity\Shipping\ShippingMethodTranslation
    ```
4.  **Database Update**\
    Run migrations as before:

    ```bash
    php bin/console doctrine:migrations:diff
    php bin/console doctrine:migrations:migrate
    ```

## Key Takeaways

* **Parameter Configuration**: Sylius automatically uses the customized model in parameters, repositories, and controllers.
* **Translatable Models**: Add properties to both the main and translation entities, and remember to update configuration and migrations.
* **Form Types**: Update forms if you want your new fields editable in the admin.
* Customizations can be done in your application or within Sylius Plugins.

## Learn more

* [Sylius Database Schema](https://drawsql.app/templates/sylius)

