# How to resize images?

twSylius uses [LiipImagineBundle](https://symfony.com/doc/current/bundles/LiipImagineBundle/index.html) to handle image manipulation, including resizing and generating thumbnails.

{% hint style="success" %}
For a full list of supported filters and configuration options, see the [LiipImagineBundle filter documentation](https://symfony.com/doc/current/bundles/LiipImagineBundle/filters.html).
{% endhint %}

***

### Where are image resizing filters defined?

In Sylius 2.x, image resizing filters are configured under `liip_imagine` in your `config/packages/liip_imagine.yaml` file. By default, Sylius provides a comprehensive set of filters for both the Admin and Shop interfaces.

Here are the default filters:

| Filter Name                            | Size (px) | Description                    |
| -------------------------------------- | --------- | ------------------------------ |
| `sylius_admin_product_original`        | Original  | Full-size admin product image  |
| `sylius_admin_avatar`                  | 200x200   | Admin avatar thumbnail         |
| `sylius_admin_product_large_thumbnail` | 600×800   | Large product image in admin   |
| `sylius_admin_product_thumbnail`       | 200×200   | Generic admin thumbnail        |
| `sylius_shop_product_original`         | Original  | Full-size shop product image   |
| `sylius_shop_product_small_thumbnail`  | 300×400   | Small product image in shop    |
| `sylius_shop_product_thumbnail`        | 600×800   | Main product thumbnail in shop |
| `sylius_shop_product_large_thumbnail`  | 1200×1600 | Large product image in shop    |
| `sylius_small`                         | 120×90    | Generic small image size       |
| `sylius_medium`                        | 240×180   | Generic medium image size      |
| `sylius_large`                         | 640×480   | Generic large image size       |

***

### How to Apply Image Filters in Twig

Use the `imagine_filter` Twig filter to apply it to an image path.

#### Example

```twig
<img src="{{ object.path|imagine_filter('sylius_small') }}" alt="Thumbnail" />
```

{% hint style="success" %}
Sylius stores image paths (e.g. for products or avatars) in the `path` field of image entities.\
The default public path for media is `/media/image`.
{% endhint %}

***

### How to Edit existing filters

Let's assume you want to change the size of images under `sylius_shop_product_large_thumbnail` filter to 100 x 200:

```yaml
# config/packages/liip_imagine.yaml
liip_imagine:
    filter_sets:
        sylius_shop_product_large_thumbnail:
            format: webp
            quality: 80
            filters:
                thumbnail: { size: [ 100, 200 ], mode: inset }
```

{% hint style="warning" %}
Remember to clear cache after the changes!

```bash
php bin/console liip:imagine:cache:remove
```
{% endhint %}

***

### How to Add Custom Image Resizing Filters

To define your own image resizing filter:

```yaml
# config/packages/liip_imagine.yaml
liip_imagine:
    filter_sets:
        advert_banner:
            filters:
                thumbnail: { size: [800, 200], mode: inset }
```

#### Example Usage in Twig

```twig
<img src="{{ banner.path|imagine_filter('advert_banner') }}" alt="Banner" />
```

***

### Learn More

* [LiipImagineBundle Documentation](https://symfony.com/doc/current/bundles/LiipImagineBundle/index.html)
