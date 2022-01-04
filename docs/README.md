# Sylius Documentation

This directory contains documentation for Sylius - Open Source eCommerce platform on top of Symfony, available on [**docs.sylius.com**](https://docs.sylius.com). 

It is hosted by the great [readthedocs.org](https://readthedocs.org).

## Sylius on Twitter

If you want to keep up with all the updates, [follow the official Sylius account on twitter](https://twitter.com/Sylius).

## Issues

The documentation uses [GitHub issues](https://github.com/Sylius/Sylius/issues).

## Build

### Traditional

To test the documentation before a commit:

* [Install `pip`, Python package manager](https://pip.pypa.io/en/stable/installing/)

* Download the documentation requirements: 

    `pip install -r requirements.txt`
    
    This makes sure that the version of Sphinx you'll get is >=1.4.2!

* Install [Sphinx](https://www.sphinx-doc.org/en/stable/)

    `pip install Sphinx`

* In the `docs` directory run `sphinx-build -b html . build` and view the generated HTML files in the `build` directory.

### Docker

Execute Docker Compose command and enter `localhost`

```bash
docker compose up -d
open localhost
```

## Authors

See the list of [our amazing contributors](https://github.com/Sylius/Sylius/contributors).
