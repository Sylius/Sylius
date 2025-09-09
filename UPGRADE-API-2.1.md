# UPGRADE FROM `2.1.4` TO `2.1.5`

## Modified responses

### OrderItem

The `OrderItem` response has been modified to include the `variantName` property.

```diff
    "@context": "\/api\/v2\/contexts\/Order",
    "@id": "\/api\/v2\/shop\/orders\/token",
    "@type": "Order",
    ...
    "items": [
         {
             "@id": "\/api\/v2\/shop\/orders\/token\/items\/XYZ",
             ...
             "productName": "Mug",
+            "variantName": "Blue Mug"
             ...
         }
    ...
```

# UPGRADE FROM `2.0` TO `2.1`

## Modified responses

### Order

Various admin and shop Order responses have been modified to include the `checkoutCompletedAt`
date and a `promotionCoupon` object. For the details of where these properties are available,
check the Order's serialization context.

```diff
    "@context": "\/api\/v2\/contexts\/Order",
    "@id": "\/api\/v2\/shop\/orders\/token",
    "@type": "Order",
    ...
+   "promotionCoupon": {
+       "@id": "\/api\/v2\/shop\/promotion-coupons\/XYZ2",
+       "@type": "PromotionCoupon",
+       "code": "XYZ2"
+   },
+   "checkoutCompletedAt": null,
    ...
```

### Product

The shop responses of Product index and show have been modified to include the default variant as a serialized object under the `defaultVariantData` property.

```diff
    "@context": "\/api\/v2\/contexts\/Product",
    "@id": "\/api\/v2\/shop\/products\/product-code",
    "@type": "Product",
    ...
    "defaultVariant": "\/api\/v2\/shop\/product-variants\/MUG_BLUE",
+   "defaultVariantData": {
+       "@context": "\/api\/v2\/contexts\/ProductVariant",
+       "@id": "\/api\/v2\/shop\/product-variants\/MUG_BLUE",
+       "@type": "ProductVariant",
+       "name": "Blue Mug",
+       "inStock": true,
+       "price": 2000,
+       "originalPrice": 3000,
+       "lowestPriceBeforeDiscount": null
+   }
```

2 new groups have been added for the purpose of serializing of the default variant specifically:
- sylius:shop:product:index:default_variant
- sylius:shop:product:show:default_variant

## New filters for existing endpoints

### Product

A new compound filter has been added to the shop Product index endpoint to retrieve associated products of
either a product or/and an association type. The filter can be used by adding an `association[ownerCode]`
or `association[typeCode]` query parameter to the request.
Adding both at once will result in a logical AND operation, limiting the result to only products
associated with the given owner and within the association type.

```http
GET /api/v2/shop/products?association[ownerCode]={example-product-code}&association[typeCode]={example-association-type-code}
```

## New endpoints

### ProductOption

A new index endpoint has been added to the shop to retrieve the list of available product options.

```http
GET /api/v2/shop/product-options
```

Which results in:

```json
{
    "@context": "\/api\/v2\/contexts\/ProductOption",
    "@id": "\/api\/v2\/shop\/product-options",
    "@type": "hydra:Collection",
    "hydra:totalItems": 2,
    "hydra:member": [
        {
            "@id": "\/api\/v2\/shop\/product-options\/COLOR",
            "@type": "ProductOption",
            "code": "COLOR",
            "name": "Color"
        },
        {
            "@id": "\/api\/v2\/shop\/product-options\/SIZE",
            "@type": "ProductOption",
            "code": "SIZE",
            "name": "Size"
        }
    ],
    "hydra:search": {
        "@type": "hydra:IriTemplate",
        "hydra:template": "\/api\/v2\/shop\/product-options{?productCode}",
        "hydra:variableRepresentation": "BasicRepresentation",
        "hydra:mapping": [
            {
                "@type": "IriTemplateMapping",
                "variable": "productCode",
                "property": "productCode",
                "required": false
            }
        ]
    }
}
```

The result can be filtered by the `productCode` query parameter, which limits the
options to those that are available for the given product.

### ProductOptionValue

A new index endpoint has been added to the shop to retrieve the list of available product option values.

```http
GET /api/v2/shop/product-option-values
```

Which results in:

```json
{
    "@context": "\/api\/v2\/contexts\/ProductOptionValue",
    "@id": "\/api\/v2\/shop\/product-option-values",
    "@type": "hydra:Collection",
    "hydra:totalItems": 4,
    "hydra:member": [
        {
            "@id": "\/api\/v2\/shop\/product-options\/COLOR\/values\/COLOR_BLUE",
            "@type": "ProductOptionValue",
            "code": "COLOR_BLUE",
            "option": "\/api\/v2\/shop\/product-options\/COLOR",
            "value": "Blue"
        },
        ...
        {
            "@id": "\/api\/v2\/shop\/product-options\/SIZE\/values\/SIZE_LARGE",
            "@type": "ProductOptionValue",
            "code": "SIZE_LARGE",
            "option": "\/api\/v2\/shop\/product-options\/SIZE",
            "value": "Large"
        }
    ],
    "hydra:search": {
        "@type": "hydra:IriTemplate",
        "hydra:template": "\/api\/v2\/shop\/product-option-values{?productCode}",
        "hydra:variableRepresentation": "BasicRepresentation",
        "hydra:mapping": [
            {
                "@type": "IriTemplateMapping",
                "variable": "productCode",
                "property": "productCode",
                "required": false
            }
        ]
    }
}
```

The result can be filtered by the `productCode` query parameter, which limits the
option values to those that are available for the given product,
regardless whether they're used within it's variants or not.

### ProductAssociationType

A new index endpoint has been added to the shop to retrieve the list of available association types.

```http
GET /api/v2/shop/product-association-types
```

Which results in:

```json
{
	"@context": "/api/v2/contexts/ProductAssociationType",
	"@id": "/api/v2/shop/product-association-types",
	"@type": "hydra:Collection",
	"hydra:totalItems": 20,
	"hydra:member": [
		{
			"@id": "/api/v2/shop/product-association-types/similar-products",
			"@type": "ProductAssociationType",
			"code": "similar-products",
			"name": "Similar Products"
		},
        ...
		{
			"@id": "/api/v2/shop/product-association-types/accessories",
			"@type": "ProductAssociationType",
			"code": "accessories",
			"name": "Accessories"
		}
	],
	"hydra:search": {
		"@type": "hydra:IriTemplate",
		"hydra:template": "/api/v2/shop/product-association-types{?productCode}",
		"hydra:variableRepresentation": "BasicRepresentation",
		"hydra:mapping": [
			{
				"@type": "IriTemplateMapping",
				"variable": "productCode",
				"property": "productCode",
				"required": false
			}
		]
	}
}
```

Each of the returned product association types has at least one enabled associated product.
The `productCode` filter can be used to limit the association types to those that have an association with the given product set as the owner.
