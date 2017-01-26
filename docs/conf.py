# -*- coding: utf-8 -*-
import sys, os
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

extensions = ['sphinx.ext.autodoc', 'sphinx.ext.doctest', 'sphinx.ext.todo', 'sphinx.ext.coverage', 'sphinx.ext.imgmath', 'sphinx.ext.ifconfig', 'sensio.sphinx.configurationblock']
source_suffix = '.rst'
master_doc = 'index'
project = 'Sylius'
copyright = u'2011-2017, Paweł Jędrzejewski'
version = ''
release = ''
exclude_patterns = ['_includes/*.rst']
html_theme = 'sylius_rtd_theme'
html_theme_path = ["_themes"]
htmlhelp_basename = 'Syliusdoc'
man_pages = [
    ('index', 'sylius', u'Sylius Documentation',
     [u'Paweł Jędrzejewski'], 1)
]
sys.path.append(os.path.abspath('_exts'))
lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)
rst_epilog = """
"""
