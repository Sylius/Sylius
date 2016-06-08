Sylius Documentation
====================

This directory contains documentation for Sylius - Decoupled eCommerce Platform, available on [**docs.sylius.org**](http://docs.sylius.org). 

It is hosted by the great [readthedocs.org](http://readthedocs.org).

Sylius on Twitter
-----------------

If you want to keep up with all the updates, [follow the official Sylius account on twitter](http://twitter.com/Sylius).

Issues
------

The documentation uses [GitHub issues](https://github.com/Sylius/Sylius/issues).

Build
-----

To test the documentation before a commit:

* [Install `pip`, Python package manager](https://pip.pypa.io/en/stable/installing/)

* Download the documentation requirements: 

    `$ pip install -r requirements.txt`
    
    This makes sure that the version of Sphinx you'll get is >=1.4.2!

* Install [Sphinx](http://www.sphinx-doc.org/en/stable/)

    `$ pip install Sphinx`

* In the `docs` directory run `$ sphinx-build -b html . build` and view the generated HTML files in the `build` directory.

Authors
-------

See the list of [our amazing contributors](http://github.com/Sylius/Sylius/contributors).
