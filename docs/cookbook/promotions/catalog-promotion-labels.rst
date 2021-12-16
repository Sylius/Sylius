How to customize catalog promotion labels?
==========================================

To customize catalog promotion labels only what need to do is overwrite their template.

.. tip::

    Learn more about template customization :doc:`here </customization/template>`.

Change the catalog promotion labels' styles
-------------------------------------------

By default Sylius displays catalog promotion labels in two locations: on the product's index on product's card and also on the product show page.
Both places use the same template (``@SyliusShop/Product/Show/_catalogPromotionLabels.html.twig``) which you need to override with your own:
``templates/bundles/SyliusShopBundle/Product/Show/_catalogPromotionLabels.html.twig`` that will have such content:

.. code-block:: twig

    <div id="appliedPromotions" data-applied-promotions-locale="{{ sylius.localeCode }}">
        {% for appliedPromotion in appliedPromotions %}
            <div class="ui red big label promotion_label" style="margin: 0.5rem 0;">
                <div class="row ui small sylius_catalog_promotion">
                    {{ appliedPromotion.label }}{% if appliedPromotion.description and withDescription %} - {{ appliedPromotion.description }}{% endif %}
                </div>
            </div>
        {% endfor %}
    </div>

How will it look after changes?

Product index:

.. image:: ../../_images/cookbook/customization-catalog-promotion/catalog_promotion_label_product_index.png
    :align: center

Product show:

.. image:: ../../_images/cookbook/customization-catalog-promotion/catalog_promotion_label_product_show.png
    :align: center

Of course, you can modify the styles how you need, also in your stylesheet.

Display catalog promotion labels on the product image
-----------------------------------------------------

In order to change the location of catalog promotion label on the product's card, we need to overwrite two templates.
We will remove the label from underneath the photo in product card template and then add it on the product's image.

Override the ``@SyliusShopBundle/Product/Box/_content.html.twig`` template with ``templates/bundles/SyliusShopBundle/Product/Box/_content.html.twig``:

.. code-block:: twig

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    <div class="ui fluid card" {{ sylius_test_html_attribute('product') }}>
        <a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="blurring dimmable image">
            <div class="ui dimmer">
                <div class="content">
                    <div class="center">
                        <div class="ui inverted button">{{ 'sylius.ui.view_more'|trans }}</div>
                    </div>
                </div>
            </div>
            {% include '@SyliusShop/Product/_mainImage.html.twig' with {'product': product} %}
        </a>
        <div class="content" {{ sylius_test_html_attribute('product-content') }}>
            <a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="header sylius-product-name" {{ sylius_test_html_attribute('product-name', product.name) }}>{{ product.name }}</a>

            {% if not product.enabledVariants.empty() %}
                {% set price = money.calculatePrice(product|sylius_resolve_variant) %}
                {% set originalPrice = money.calculateOriginalPrice(product|sylius_resolve_variant) %}
                {% if price != originalPrice %}
                    <div class="sylius-product-original-price" {{ sylius_test_html_attribute('product-original-price') }}><del>{{ originalPrice }}</del></div>
                {% endif %}
                <div class="sylius-product-price" {{ sylius_test_html_attribute('product-price') }}>{{ price }}</div>
            {% endif %}
        </div>
    </div>

And the ``@SyliusShopBundle/Product/_mainImage.html.twig`` with ``templates/bundles/SyliusShopBundle/Product/_mainImage.html.twig``:

.. code-block:: twig

    {% if product.imagesByType('thumbnail') is not empty %}
        {% set path = product.imagesByType('thumbnail').first.path|imagine_filter(filter|default('sylius_shop_product_thumbnail')) %}
    {% elseif product.images.first %}
        {% set path = product.images.first.path|imagine_filter(filter|default('sylius_shop_product_thumbnail')) %}
    {% else %}
        {% set path = '//placehold.it/200x200' %}
    {% endif %}

    {% set variant = product|sylius_resolve_variant %}
    {% set channelPricing = variant.getChannelPricingForChannel(sylius.channel) %}

    <div style="position: relative;">
        <img src="{{ path }}" {{ sylius_test_html_attribute('main-image') }} alt="{{ product.name }}" class="ui bordered image" />
        <div id="appliedPromotions" style="position: absolute; right: 5px; top: 5px" data-applied-promotions-locale="{{ sylius.localeCode }}">
            {% for appliedPromotion in channelPricing.appliedPromotions %}
                <div class="ui blue label promotion_label" style="margin: 1rem 0;">
                    <div class="row ui small sylius_catalog_promotion">
                        {{ appliedPromotion.name }}
                    </div>
                </div>
            {% endfor %}
        </div>
    </div>

After changes:

.. image:: ../../_images/cookbook/customization-catalog-promotion/catalog_promotion_repositioned_label.png
    :align: center

Well done! Now you can do anything you want with the catalog promotion labels.
