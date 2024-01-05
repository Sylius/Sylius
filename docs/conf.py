# -*- coding: utf-8 -*-
import sys, os
from sphinx.highlighting import lexers
from pygments.lexers.web import PhpLexer

extensions = [
    'sphinx.ext.autodoc',
    'sphinx.ext.doctest',
    'sphinx.ext.todo',
    'sphinx.ext.coverage',
    'sphinx.ext.imgmath',
    'sphinx.ext.ifconfig',
    'sphinx_copybutton',
    'sphinxcontrib-redirects',
    'ultimatereplacement'
]
source_suffix = '.rst'
master_doc = 'index'
project = 'Sylius'
copyright = u'2011-2024, Sylius Sp. z o.o.'
version = ''
release = ''
exclude_patterns = ['_includes/*.rst']
html_theme = 'sylius_rtd_theme'
html_theme_path = ["_themes"]
html_favicon = 'favicon.ico'
htmlhelp_basename = 'Syliusdoc'
man_pages = [
    ('index', 'sylius', u'Sylius Documentation',
     [u'Sylius Sp. z o.o.'], 1)
]
sys.path.append(os.path.abspath('_exts'))
lexers['php'] = PhpLexer(startinline=True)
lexers['php-annotations'] = PhpLexer(startinline=True)
ultimate_replacements = {
    "{future_version}": "1.13",
    "{current_version}": "1.12",
    "{lowest_bugfix_version}": "1.12",
    "{security_patch_version}": "1.11"
}
redirects_file = 'redirection_map'
