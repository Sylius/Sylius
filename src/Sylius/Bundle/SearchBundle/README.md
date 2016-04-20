SyliusSearchBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusSearchBundle.png?branch=master)](http://travis-ci.org/Sylius/SyliusSearchBundle)
===================

Search system for [**Symfony2**](http://symfony.com) applications.

It supports search functionality for mysql and elastic search.

Usage:

Place the following snippets on a twig view and you are ready to go.

```php
{% render controller('SyliusSearchBundle:Search:form', {'request':app.request}) %}

{% include 'SyliusSearchBundle::filter_form.html.twig' %}
```

If you want to use the search pragmatically there are currently 2 query types, string query and taxon query.

```php
$finder = $this->get('sylius_search.finder')
            ->setTargetIndex('product') // target index searches in a specific type, if it's not set it searches in all types
            ->setFacetGroup('search_set') // configuration based, uses the relevant facet set, if not set it does not show facets
            ->find(new SearchStringQuery(...));
```

Taxon
```php
$finder = $this->get('sylius_search.finder')
            ->setFacetGroup('categories_set')
            ->find(new TaxonQuery(...));
```

Basic configuration guidelines:

### Form
```yaml
form: 'SyliusSearchBundle::form.html.twig'
```

The actual form you want to use for performing a search. As long as the naming of the elements is the same you can define a new twig file with your own design.

### Request method

The default value for both search and filter forms is GET but you can use post by adding the following snippet on the configuration

```yaml
request_method: POST
```

### Engine 

```yaml
engine: orm
```

Possible values: orm, elasticsearch

If orm is selected the search uses mysql as engine, if elasticsearch is selected if uses the elasticsearch search engine,
which must be configured through the fos_elastica bundle. For documentation on fos_elastica please check
the [fos_elastica github page](https://github.com/FriendsOfSymfony/FOSElasticaBundle).

### Query logger

By default query logger is disabled.

If you wish to using you need to add the following configuration parameters. Query logger currently supports orm for
smaller websites and elasticsearch for one with higher transactions. In the future we need to introduce queue support.

```yaml
query_logger:
    enabled: true
    engine: orm
```


### Indexes
```yaml
orm_indexes: # it is being used only when orm is selected as a driver
        product: # indentifier of an index
            class: Sylius\Component\Core\Model\Product # the corresponding model
            mappings: # appart from the id, the rest of the fields will be used to compile the searchable content
                id: ~
                name: ~
                description: ~
```

if elasticsearch is selected here is a sample configuration

```yaml
fos_elastica:
     clients:
         elasticsearch:
            servers:
                - { host: 10.0.0.100, port: 9200, logger: true }
                - { host: 10.0.0.101, port: 9200, logger: true }
           #for clustering you need to define the logger because of the https://github.com/FriendsOfSymfony/FOSElasticaBundle/issues/543
     indexes:
         sylius:
             client: elasticsearch
             finder: ~
             settings:
                 analysis:
                     filter:
                         synonym:
                             type: synonym
                             synonyms: synonym.txt
                     analyzer:
                         my_analyzer:
                             filter: synonym
                             type: standard
                             tokenizer: standard
             types:
                 product:
                     mappings:
                         name:
                             type: string
                             analyzer: my_analyzer
                         description: ~
                         slug: ~
                         color: ~
                         price:
                            type: integer
                         channels:
                            type: string
                            index: not_analyzed
                         taxons:
                            type: string
                            index: not_analyzed
                         size: ~
                         author: ~
                         made_of:
                            type: string
                            index: not_analyzed

                     persistence:
                         driver: orm
                         model: Sylius\Component\Core\Model\Product
                         model_to_elastica_transformer:
                            service: sylius.search.transformers.model.product
                         provider: ~
                         listener: ~
                         finder: ~

                 search_log:
                    mappings:
                        search_term: ~
                        ip_address: ~
```

### Filters

```yaml
filters:
        search_filter: # the small drop down menu on the side of the search field
            enabled: true
            taxon: category # possible values are taxons codes (category, brand for sylius)
        facet_groups: # possible facet groups, you assign them in a finder object
            search_set:
                values: [taxons, price, made_of, color]
            categories_set:
                values: [price, made_of, color]
            all_set:
                values: [taxons, price, made_of]
        facets: # possible facets, as long as a model exposes attributes, options or getters with the given name, it will be used as a facet
            taxons:
                display_name: 'Basic categories'
                type: terms
                value: ~
            price:
                display_name: 'Available prices'
                type: range
                values:
                    - { from: 0, to: 2000}
                    - { from: 2001, to: 5000}
                    - { from: 5001, to: 10000}
            made_of:
                display_name: 'Material'
                type: terms
                value: ~
            color:
                display_name: 'Available colors'
                type: terms
                value: ~
```

### Indexing data

After configuring the indexer properly you execute the following command to populate the appropriate engine:

```bash
app/console sylius:search:index
```

Sylius
------

**Sylius** - Modern ecommerce for Symfony2. Visit [Sylius.org](http://sylius.org).

[phpspec](http://phpspec.net) examples
--------------------------------------

```bash
$ composer install
$ bin/phpspec run -fpretty
```

[behat](http://behat.org) examples for search bundle
--------------------------------------

```bash
$ composer install
$ /bin/behat --suite=search
```

Documentation
-------------

Documentation is available on [**docs.sylius.org**](http://docs.sylius.org/en/latest/bundles/SyliusSearchBundle/index.html).

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://docs.sylius.org/en/latest/contributing/index.html).

Mailing lists
-------------

### Users

Questions? Feel free to ask on [users mailing list](http://groups.google.com/group/sylius).

### Developers

To contribute and develop this bundle, use the [developers mailing list](http://groups.google.com/group/sylius-dev).

Sylius twitter account
----------------------

If you want to keep up with updates, [follow the official Sylius account on twitter](http://twitter.com/Sylius).

Bug tracking
------------

This bundle uses [GitHub issues](https://github.com/Sylius/Sylius/issues).
If you have found bug, please create an issue.

Versioning
----------

Releases will be numbered with the format `major.minor.patch`.

And constructed with the following guidelines.

* Breaking backwards compatibility bumps the major.
* New additions without breaking backwards compatibility bumps the minor.
* Bug fixes and misc changes bump the patch.

For more information on SemVer, please visit [semver.org website](http://semver.org/).  
This versioning method is same for all **Sylius** bundles and applications.

MIT License
-----------

License can be found [here](https://github.com/Sylius/SyliusSearchBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Argyrios Gounaris](http://agounaris.github.io).
See the list of [contributors](https://github.com/Sylius/SyliusProductBundle/contributors).
