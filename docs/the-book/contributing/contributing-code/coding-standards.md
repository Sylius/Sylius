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

# Coding Standards

You can check your code for Sylius coding standard by running the following command:

```bash
vendor/bin/ecs check src tests
```

Some of the violations can be automatically fixed by running the same command with `--fix` suffix like:

```bash
vendor/bin/ecs check src tests --fix
```

{% hint style="info" %}
Most of Sylius coding standard checks are extracted to [SyliusLabs/CodingStandard](https://github.com/SyliusLabs/CodingStandard) package so that reusing them in your own projects or Sylius plugins is effortless. To learn about details, take a look at its README.
{% endhint %}
