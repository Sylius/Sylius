# -*- coding: utf-8 -*-
"""
    :copyright: (c) 2010-2012 Fabien Potencier
    :license: MIT, see LICENSE for more details.
"""

from docutils import nodes, utils

from sphinx.util.nodes import split_explicit_title
from string import lower

def php_namespace_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    env = inliner.document.settings.env
    base_url = env.app.config.api_url
    has_explicit_title, title, namespace = split_explicit_title(text)

    try:
        full_url = base_url % namespace.replace('\\', '/') + '.html'
    except (TypeError, ValueError):
        env.warn(env.docname, 'unable to expand %s api_url with base '
                 'URL %r, please make sure the base contains \'%%s\' '
                 'exactly once' % (typ, base_url))
        full_url = base_url + utils.escape(full_class)
    if not has_explicit_title:
        name = namespace.lstrip('\\')
        ns = name.rfind('\\')
        if ns != -1:
            name = name[ns+1:]
        title = name
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=namespace)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_class_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    env = inliner.document.settings.env
    base_url = env.app.config.api_url
    has_explicit_title, title, full_class = split_explicit_title(text)

    try:
        full_url = base_url % full_class.replace('\\', '/') + '.html'
    except (TypeError, ValueError):
        env.warn(env.docname, 'unable to expand %s api_url with base '
                 'URL %r, please make sure the base contains \'%%s\' '
                 'exactly once' % (typ, base_url))
        full_url = base_url + utils.escape(full_class)
    if not has_explicit_title:
        class_name = full_class.lstrip('\\')
        ns = class_name.rfind('\\')
        if ns != -1:
            class_name = class_name[ns+1:]
        title = class_name
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_class)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_method_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    env = inliner.document.settings.env
    base_url = env.app.config.api_url
    has_explicit_title, title, class_and_method = split_explicit_title(text)

    ns = class_and_method.rfind('::')
    full_class = class_and_method[:ns]
    method = class_and_method[ns+2:]

    try:
        full_url = base_url % full_class.replace('\\', '/') + '.html' + '#method_' + method
    except (TypeError, ValueError):
        env.warn(env.docname, 'unable to expand %s api_url with base '
                 'URL %r, please make sure the base contains \'%%s\' '
                 'exactly once' % (typ, base_url))
        full_url = base_url + utils.escape(full_class)
    if not has_explicit_title:
        title = method + '()'
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_class + '::' + method + '()')]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_phpclass_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    has_explicit_title, title, full_class = split_explicit_title(text)

    full_url = 'http://php.net/manual/en/class.%s.php' % lower(full_class)

    if not has_explicit_title:
        title = full_class
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_class)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_phpmethod_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    has_explicit_title, title, class_and_method = split_explicit_title(text)

    ns = class_and_method.rfind('::')
    full_class = class_and_method[:ns]
    method = class_and_method[ns+2:]

    full_url = 'http://php.net/manual/en/%s.%s.php' % (lower(full_class), lower(method))

    if not has_explicit_title:
        title = full_class + '::' + method + '()'
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_class)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_phpfunction_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    has_explicit_title, title, full_function = split_explicit_title(text)

    full_url = 'http://php.net/manual/en/function.%s.php' % lower(full_function.replace('_', '-'))

    if not has_explicit_title:
        title = full_function
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_function)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def setup(app):
    app.add_config_value('api_url', {}, 'env')
    app.add_role('namespace', php_namespace_role)
    app.add_role('class', php_class_role)
    app.add_role('method', php_method_role)
    app.add_role('phpclass', php_phpclass_role)
    app.add_role('phpmethod', php_phpmethod_role)
    app.add_role('phpfunction', php_phpfunction_role)
