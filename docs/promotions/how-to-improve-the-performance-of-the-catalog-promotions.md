# How to improve the performance of the catalog promotions?

Catalog promotions in Sylius can affect a large number of product variants. By default, Sylius processes them in batches of 100 variants. In performance-critical environments, you might want to adjust this  size for faster or more memory-efficient execution.

This guide explains how to **tune the batch size** to improve performance and stability.

***

## 🔧 Configuring the Batch Size

By default, Sylius processes **100 product variants per batch** when applying catalog promotions.

You can adjust this in your configuration to better fit your environment:

### Default Value

```yaml
sylius_core:
    catalog_promotions:
        batch_size: 100
```

### How to Change It

To override the default value, add the following configuration to your configuration:

```yaml
# config/packages/_sylius.yaml

sylius_core:
    catalog_promotions:
        batch_size: 250  # Adjust this value as needed
```

***

### 💡 Choose the Right Batch Size for Your Server

The appropriate batch size depends on your **server’s memory, CPU, and I/O capabilities**.

| Batch Size Range | When to Use                                                               |
| ---------------- | ------------------------------------------------------------------------- |
| `50–100`         | Safe default for most environments, avoids memory issues, might be slower |
| `250–1000`       | Suitable for optimized production servers with high throughput            |

### Best Practices

* **Smaller values** reduce risk of timeouts or memory leaks.
* **Larger values** improve speed but must be tested carefully.

***

### ✅ Summary

* Sylius applies catalog promotions in batches for performance and reliability.
* Adjust the `batch_size` setting to fit your server's capacity.
* Always test changes in staging and monitor key performance metrics.

***

{% hint style="info" %}
### 🔍 Performance Testing Before Deployment

Always test any batch size changes in a **staging environment** before applying them in production. Monitor key metrics such as **memory usage**, **CPU load**, and **execution time** during catalog promotion processing to ensure the system remains stable and responsive.
{% endhint %}

{% hint style="success" %}
#### Catalog Promotion Processing <a href="#understanding-catalog-promotion-processing" id="understanding-catalog-promotion-processing"></a>

Catalog promotions in Sylius are executed using the **Symfony Messenger** component.\
This allows for both **synchronous** (inline) and **asynchronous** (queued) message handling, depending on your project configuration.

Learn more about this component [here](https://symfony.com/doc/current/components/messenger.html).
{% endhint %}
