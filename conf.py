# -*- coding: utf-8 -*-
extensions = ['sphinx.ext.autodoc', 'sphinx.ext.doctest', 'sphinx.ext.todo', 'sphinx.ext.coverage', 'sphinx.ext.pngmath', 'sphinx.ext.mathjax', 'sphinx.ext.ifconfig']
source_suffix = '.rst'
master_doc = 'index'
project = 'Sylius'
copyright = u'2011-2012, Paweł Jędrzejewski'
version = '0.0.1'
release = '0.0.1'
exclude_patterns = []
pygments_style = 'monokai'
html_theme = 'default'
htmlhelp_basename = 'Syliusdoc'
man_pages = [
    ('index', 'sylius', u'Sylius Documentation',
     [u'Paweł Jędrzejewski'], 1)
]
