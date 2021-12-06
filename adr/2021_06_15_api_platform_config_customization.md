# API - Customization

* Status: accepted
* Date: 2021-06-15

## Context and Problem Statement

Sylius is by design application that will be adjusted to customer needs. 
Therefore each part of it has to be crafted with replaceability or customizability at its core. 
Nonetheless, the current state of API Platform integration requires the replacement of the whole config of the whole resource. 
In terms of the more complicated cases like Order or Customer, this practice may become error-prone and hard to maintain for both maintainers and Sylius users.

## Considered Options

### Overwriting resource configs

For now, the only way to customize API resources is by overwriting the entire configuration associated with the endpoint.

* Bad, because its not easy to configure.
* Bad, because it requires to change many files in end application.
* Bad, because any changes in the new Sylius version will not be applicable to overwritten configs

### Config merging

To configure endpoint specify mapping file in config.

```yaml
mapping:
    paths: ['%kernel.project_dir%/config/api_platform']
```

in specified directory all we need to do is create config we want to overwrite, for example:

```yaml
'%sylius.model.zone.class%':
    collectionOperations:
        admin_get:
            enabled: false
```

This is example config to remove specific operation, more examples can be found in docs (https://docs.sylius.com/en/latest/customization/api/index.html).

* Good, because it's easy to understand, you can overwrite any endpoint in just a few lines from any place specified in api platform config.
* Bad, because we need to overwrite and modify services provided by API Platform, differences between API Platform versions can break our application.

## Decision Outcome

Chosen option: "Config merging", because it allows us easily overwrite any endpoint, without getting deep into api platform resources.
