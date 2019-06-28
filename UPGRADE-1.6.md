# UPGRADE FROM `v1.5.X` TO `v1.6.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.6.0
```

#### Shipping method fixture calculator configuration

If you have your custom `shipping_method` fixtures with
calculator configuration defined, you should update rates
from XXXX to XX.XX.

Before:
```yaml
sylius_fixtures:
    suites:
        default:
            fixtures:
                shipping_method:
                    options:
                        custom:
                            delivery:
                                code: "delivery"
                                name: "Delivery"
                                enabled: true
                                calculator:
                                    type: "flat_rate"
                                    configuration: 
                                        US_WEB:
                                            amount: 2037
```

After:
```yaml
sylius_fixtures:
   suites:
       default:
           fixtures:
               shipping_method:
                   options:
                       custom:
                           delivery:
                               code: "delivery"
                               name: "Delivery"
                               enabled: true
                               calculator:
                                   type: "flat_rate"
                                   configuration: 
                                       US_WEB:
                                           amount: 20.37
```
