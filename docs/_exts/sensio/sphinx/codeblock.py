"""
    :copyright: (c) 2010-2015 Fabien Potencier
    :license: MIT, see LICENSE for more details.
"""

from sphinx.directives.code import CodeBlock

"""
A wrapper around the built-in CodeBlock class to always
enable line numbers.
"""
class NumberedCodeBlock(CodeBlock):
    def run(self):
        self.options['linenos'] = 'table'
        return super(NumberedCodeBlock, self).run();

def setup(app):
    app.add_directive('code-block', NumberedCodeBlock)
    app.add_directive('sourcecode', NumberedCodeBlock)
