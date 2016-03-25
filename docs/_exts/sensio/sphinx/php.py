# -*- coding: utf-8 -*-
"""
    :copyright: (c) 2010-2012 Fabien Potencier
    :license: MIT, see LICENSE for more details.
"""

from sphinx import addnodes
from sphinx.domains import Domain, ObjType
from sphinx.locale import l_, _
from sphinx.directives import ObjectDescription
from sphinx.domains.python import py_paramlist_re as js_paramlist_re
from sphinx.roles import XRefRole
from sphinx.util.nodes import make_refnode
from sphinx.util.docfields import Field, GroupedField, TypedField

def setup(app):
    app.add_domain(PHPDomain)

class PHPXRefRole(XRefRole):
    def process_link(self, env, refnode, has_explicit_title, title, target):
        # basically what sphinx.domains.python.PyXRefRole does
        refnode['php:object'] = env.temp_data.get('php:object')
        if not has_explicit_title:
            title = title.lstrip('\\')
            target = target.lstrip('~')
            if title[0:1] == '~':
                title = title[1:]
                ns = title.rfind('\\')
                if ns != -1:
                    title = title[ns+1:]
        if target[0:1] == '\\':
            target = target[1:]
            refnode['refspecific'] = True
        return title, target

class PHPDomain(Domain):
    """PHP language domain."""
    name = 'php'
    label = 'PHP'
    # if you add a new object type make sure to edit JSObject.get_index_string
    object_types = {
    }
    directives = {
    }
    roles = {
        'func':  PHPXRefRole(fix_parens=True),
        'class': PHPXRefRole(),
        'data':  PHPXRefRole(),
        'attr':  PHPXRefRole(),
    }
    initial_data = {
        'objects': {}, # fullname -> docname, objtype
    }

    def clear_doc(self, docname):
        for fullname, (fn, _) in self.data['objects'].items():
            if fn == docname:
                del self.data['objects'][fullname]

    def find_obj(self, env, obj, name, typ, searchorder=0):
        if name[-2:] == '()':
            name = name[:-2]
        objects = self.data['objects']
        newname = None
        if searchorder == 1:
            if obj and obj + '\\' + name in objects:
                newname = obj + '\\' + name
            else:
                newname = name
        else:
            if name in objects:
                newname = name
            elif obj and obj + '\\' + name in objects:
                newname = obj + '\\' + name
        return newname, objects.get(newname)

    def resolve_xref(self, env, fromdocname, builder, typ, target, node,
                     contnode):
        objectname = node.get('php:object')
        searchorder = node.hasattr('refspecific') and 1 or 0
        name, obj = self.find_obj(env, objectname, target, typ, searchorder)
        if not obj:
            return None
        return make_refnode(builder, fromdocname, obj[0], name, contnode, name)

    def get_objects(self):
        for refname, (docname, type) in self.data['objects'].iteritems():
            yield refname, refname, type, docname, refname, 1
