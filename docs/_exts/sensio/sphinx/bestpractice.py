from docutils.parsers.rst import Directive, directives
from docutils import nodes
from sphinx.util.compat import make_admonition
from sphinx import addnodes
from sphinx.locale import _

class bestpractice(nodes.Admonition, nodes.Element):
    pass

class BestPractice(Directive):
    has_content = True
    required_arguments = 0
    optional_arguments = 1
    final_argument_whitespace = True
    option_spec = {}

    def run(self):
        ret = make_admonition(
            bestpractice, self.name, [_('Best Practice')], self.options,
            self.content, self.lineno, self.content_offset, self.block_text,
            self.state, self.state_machine)
        if self.arguments:
            argnodes, msgs = self.state.inline_text(self.arguments[0],
                                                    self.lineno)
            para = nodes.paragraph()
            para += argnodes
            para += msgs
            ret[0].insert(1, para)

        return ret

def visit_bestpractice_node(self, node):
    self.body.append(self.starttag(node, 'div', CLASS=('admonition best-practice')))
    self.set_first_last(node)

def depart_bestpractice_node(self, node):
    self.depart_admonition(node)

def setup(app):
    app.add_node(bestpractice, html=(visit_bestpractice_node, depart_bestpractice_node))
    app.add_directive('best-practice', BestPractice)
