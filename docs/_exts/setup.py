# -*- coding: utf-8 -*-

from setuptools import setup, find_packages

setup(
    name = 'sphinx-php',
    version = '1.0',
    author = 'Fabien Potencier',
    author_email = 'fabien@symfony.com',
    description = 'Sphinx Extensions for PHP and Symfony',
    license = 'MIT',
    packages = find_packages(),
    install_requires = ['Sphinx>=0.6'],
)
