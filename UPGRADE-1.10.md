# UPGRADE FROM `v1.9.X` TO `v1.10.0`

### New API

1. API CartShippingMethod key `cost` has been changed to `price`.

### From Gulp to Webpack Encore

After migrating to the Webpack, asset paths should be changed. By default, it will be compiled to the `public/build/admin/...` and `public/build/shop/...` folder:

```
- <img src="{{ asset('assets/admin/img/admin-logo.svg') }}" class="ui fluid image">
+ <img src="{{ asset('build/admin/images/admin-logo.svg', 'admin') }}" class="ui fluid image">
```

Output paths can be changed freely, but keep in mind, that before every build, the directory will be cleared, so old files may be removed.

Scripts and styles paths have also changed:

```
- {% include '@SyliusUi/_javascripts.html.twig' with {'path': 'assets/admin/js/app.js'} %}
+ {{ encore_entry_script_tags('admin-entry', null, 'admin') }}
```

```
- {% include '@SyliusUi/_stylesheets.html.twig' with {'path': 'assets/admin/css/style.css'} %}
+ {{ encore_entry_link_tags('admin-entry', null, 'admin') }}
```

```
- {% include '@SyliusUi/_javascripts.html.twig' with {'path': 'assets/shop/js/app.js'} %}
+ {{ encore_entry_script_tags('shop-entry', null, 'shop') }}
```

```
- {% include '@SyliusUi/_stylesheets.html.twig' with {'path': 'assets/shop/css/style.css'} %}
+ {{ encore_entry_link_tags('shop-entry', null, 'shop') }}
```
