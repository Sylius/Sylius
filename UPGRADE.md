# UPGRADE FROM 1.0.0-beta.1 to 1.0.0-beta.2

## Bundles with components:

### Addressing / AddressingBundle

### Attribute / AttributeBundle

### Channel / ChannelBundle

### Core / CoreBundle

### Currency / CurrencyBundle

### Customer / CustomerBundle

### Grid / GridBundle

### Inventory / InventoryBundle

### Locale / LocaleBundle

### Mailer / MailerBundle

### Order / OrderBundle

### Payment / PaymentBundle

### Product / ProductBundle

### Promotion / PromotionBundle

### Registry / RegistryBundle

### Resource / ResourceBundle

### Review / ReviewBundle

### Shipping / ShippingBundle

### Taxation / TaxationBundle

### Taxonomy / TaxonomyBundle

### User / UserBundle

## Standalone bundles:

### AdminBundle

### ApiBundle

### FixturesBundle

### MoneyBundle

### PayumBundle

### ShopBundle

### ThemeBundle

### UiBundle

## Application:

### Configuration

### Behat

* `Sylius\Behat\Page\Admin\Crud\IndexPage`, `Sylius\Behat\Page\Admin\Crud\CreatePage`, `Sylius\Behat\Page\Admin\Crud\UpdatePage` now accepts route name instead of resource name.

* ImageUniqueCode and ImageUniqueCodeValidator was deleted and replaced by WithinCollectionUniqueCodeValidator, WithinCollectionUniqueCode form ResourceBundle.
  To use it replace name of constraint in constraint mapping file from: `Sylius\Bundle\CoreBundle\Validator\Constraints\ImageUniqueCode`
  to: `Sylius\Bundle\ResourceBundle\Validator\Constraints\WithinCollectionUniqueCode`
