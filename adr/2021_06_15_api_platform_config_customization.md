# API - Customization

* Status: accepted
* Date: 2021-06-15

## Context and Problem Statement

Its difficult to overwrite or remove endpoints.

## Considered Options

### Overwriting resource configs

For now, only way to customize api resources is by overwriting every configuration associated to the endpoint.

* Bad, because its not easy to configure.
* Bad, because it requires to change many files in end application.

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
        admin_get (unset): ~
```

This is example config to remove specific operation, more examples can be found in docs.

* Good, because its easy to understand, you can overwrite any endpoint in just a few lines from any place specified in api platform config.

## Decision Outcome

Chosen option: "Config merging", because it allows us easily overwrite any endpoint, without getting deep into api platform resources.
