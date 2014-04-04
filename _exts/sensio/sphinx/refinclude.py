# -*- coding: utf-8 -*-
"""
    :copyright: (c) 2010-2012 Fabien Potencier
    :license: MIT, see LICENSE for more details.
"""

from docutils.parsers.rst import Directive, directives
from docutils import nodes

class refinclude(nodes.General, nodes.Element):
    pass

class RefInclude(Directive):
    has_content = False
    required_arguments = 1
    optional_arguments = 0
    final_argument_whitespace = False
    option_spec = {}

    def run(self):
        document = self.state.document

        if not document.settings.file_insertion_enabled:
            return [document.reporter.warning('File insertion disabled',
                                              line=self.lineno)]

        env = self.state.document.settings.env
        target = self.arguments[0]

        node = refinclude()
        node['target'] = target

        return [node]

def process_refinclude_nodes(app, doctree, docname):
    env = app.env
    for node in doctree.traverse(refinclude):
        docname, labelid, sectname = env.domaindata['std']['labels'].get(node['target'],
                                                                         ('','',''))

        if not docname:
            return [document.reporter.error('Unknown target name: "%s"' % node['target'],
                                            line=self.lineno)]

        resultnode = None
        dt = env.get_doctree(docname)
        for n in dt.traverse(nodes.section):
            if labelid in n['ids']:
                node.replace_self([n])
                break

def setup(app):
    app.add_node(refinclude)
    app.add_directive('include-ref', RefInclude)
    app.connect('doctree-resolved', process_refinclude_nodes)
