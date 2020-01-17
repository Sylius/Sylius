# -*- coding: utf-8 -*-
"""
    :copyright: (c) 2010-2012 Fabien Potencier
    :license: MIT, see LICENSE for more details.
"""

from docutils.parsers.rst import Directive, directives
from docutils import nodes

class configurationblock(nodes.General, nodes.Element):
    pass

class ConfigurationBlock(Directive):
    has_content = True
    required_arguments = 0
    optional_arguments = 0
    final_argument_whitespace = True
    option_spec = {}
    formats = {
        'html':            'HTML',
        'xml':             'XML',
        'php':             'PHP',
        'yaml':            'YAML',
        'jinja':           'Twig',
        'html+jinja':      'Twig',
        'jinja+html':      'Twig',
        'twig':            'Twig',
        'html+twig':       'Twig',
        'twig+html':       'Twig',
        'php+html':        'PHP',
        'html+php':        'PHP',
        'ini':             'INI',
        'php-annotations': 'Annotations',
        'php-standalone':  'Standalone Use',
        'php-symfony':     'Framework Use',
    }

    def __init__(self, *args):
        Directive.__init__(self, *args)
        env = self.state.document.settings.env
        config_block = env.app.config.config_block

        for language in config_block:
            self.formats[language] = config_block[language]

    def run(self):
        env = self.state.document.settings.env

        node = nodes.Element()
        node.document = self.state.document
        self.state.nested_parse(self.content, self.content_offset, node)

        entries = []
        for i, child in enumerate(node):
            if isinstance(child, nodes.literal_block):
                # add a title (the language name) before each block
                #targetid = "configuration-block-%d" % env.new_serialno('configuration-block')
                #targetnode = nodes.target('', '', ids=[targetid])
                #targetnode.append(child)
                if 'language' in child:
                    language = child['language']
                else:
                    language = env.app.config.highlight_language

                innernode = nodes.emphasis(self.formats[language], self.formats[language])

                para = nodes.paragraph()
                para += [innernode, child]

                entry = nodes.list_item('')
                entry.append(para)
                entries.append(entry)

        resultnode = configurationblock()
        resultnode.append(nodes.bullet_list('', *entries))

        return [resultnode]

def visit_configurationblock_html(self, node):
    self.body.append(self.starttag(node, 'div', CLASS='configuration-block'))

def depart_configurationblock_html(self, node):
    self.body.append('</div>\n')

def visit_configurationblock_latex(self, node):
    pass

def depart_configurationblock_latex(self, node):
    pass

def setup(app):
    app.add_node(configurationblock,
                 html=(visit_configurationblock_html, depart_configurationblock_html),
                 latex=(visit_configurationblock_latex, depart_configurationblock_latex))
    app.add_directive('configuration-block', ConfigurationBlock)
    app.add_config_value('config_block', {}, 'env')
