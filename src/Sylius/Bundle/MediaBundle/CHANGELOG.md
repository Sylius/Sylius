CHANGELOG
=========

### v0.15.0
PR [#2975](https://github.com/Sylius/Sylius/pull/2975)

* Now we're using CMF Media's `Image` *document*.
* SyliusMediaBundle offers an `Image` *entity* which is the one developers should use, since they cannot directly create a relationship from an entity to a document.
* ProductVariantImage entity is removed.
* ProductVariant, Taxon and Taxonomy images are now having a relationship with `Image` *entity*.
* Form type for `Image` fields is `sylius_image` not `cmf_media_image`. Actually we're doing some extra work with `cmf_media_image`, if you do that yourself it can be OK to use it.
* Since we're using CMF Media Bundle upload helper, every rule from that bundle is applied here as well. (e.g. File name cannot contain a space character)
