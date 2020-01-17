# -*- coding: utf-8 -*-
"""
    :copyright: (c) 2010-2012 Fabien Potencier
    :license: MIT, see LICENSE for more details.
"""

import re

from docutils import nodes, utils

from sphinx.util.nodes import split_explicit_title

def php_namespace_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    env = inliner.document.settings.env
    has_explicit_title, title, namespace = split_explicit_title(text)

    if len(re.findall(r'[^\\]\\[^\\]', rawtext)) > 0:
        env.warn(env.docname, 'backslash not escaped in %s' % rawtext, lineno)

    full_url = build_url('namespace', namespace, None, None, inliner)

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
    has_explicit_title, title, full_class = split_explicit_title(text)
    backslash = full_class.rfind('\\')
    namespace = full_class[:backslash]
    class_name = full_class[backslash+1:]

    if len(re.findall(r'[^\\]\\[^\\]', rawtext)) > 0:
        env.warn(env.docname, 'backslash not escaped in %s' % rawtext, lineno)

    full_url = build_url('class', namespace, class_name, None, inliner)

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
    has_explicit_title, title, class_and_method = split_explicit_title(text)

    ns = class_and_method.rfind('::')
    full_class = class_and_method[:ns]
    method = class_and_method[ns+2:]
    backslash = full_class.rfind('\\')
    namespace = full_class[:backslash]
    class_name = full_class[backslash+1:]

    if len(re.findall(r'[^\\]\\[^\\]', rawtext)) > 0:
        env.warn(env.docname, 'backslash not escaped in %s' % rawtext, lineno)

    full_url = build_url('method', namespace, class_name, method, inliner)

    if not has_explicit_title:
        title = method + '()'
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_class + '::' + method + '()')]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_phpclass_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    has_explicit_title, title, full_class = split_explicit_title(text)

    full_url = 'https://secure.php.net/manual/en/class.%s.php' % full_class.lower()

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

    full_url = 'https://secure.php.net/manual/en/%s.%s.php' % (full_class.lower(), method.lower())

    if not has_explicit_title:
        title = full_class + '::' + method + '()'
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_class)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def php_phpfunction_role(typ, rawtext, text, lineno, inliner, options={}, content=[]):
    text = utils.unescape(text)
    has_explicit_title, title, full_function = split_explicit_title(text)

    full_url = 'https://secure.php.net/manual/en/function.%s.php' % full_function.replace('_', '-').lower()

    if not has_explicit_title:
        title = full_function
    list = [nodes.reference(title, title, internal=False, refuri=full_url, reftitle=full_function)]
    pnode = nodes.literal('', '', *list)
    return [pnode], []

def setup(app):
    app.add_config_value('api_url', {}, 'env')
    app.add_config_value('api_url_pattern', None, 'env')
    app.add_config_value('namespace_separator', '/', 'env')
    app.add_role('namespace', php_namespace_role)
    app.add_role('class', php_class_role)
    app.add_role('method', php_method_role)
    app.add_role('phpclass', php_phpclass_role)
    app.add_role('phpmethod', php_phpmethod_role)
    app.add_role('phpfunction', php_phpfunction_role)

def build_url(role, namespace, class_name, method, inliner):
    env = inliner.document.settings.env

    if namespace is None:
        namespace = ''
    if class_name is None:
        class_name = ''
    if method is None:
        method = ''

    if ('namespace_separator' in env.app.config):
        namespace = namespace.replace('\\', env.app.config.namespace_separator)
    else:
        namespace = namespace.replace('\\', '/')

    if (env.app.config.api_url_pattern is None):
        fqcn = '%(namespace)s{class}/%(class)s{/class}{method}/%(class)s{/method}'
        api_url_pattern = env.app.config.api_url.replace('%s', fqcn)
        api_url_pattern += '.html{method}#method_%(method)s{/method}'
    else:
        api_url_pattern = str(env.app.config.api_url_pattern)

    api_url_pattern = api_url_pattern.replace('{'+role+'}', '')
    api_url_pattern = api_url_pattern.replace('{/'+role+'}', '')

    for unused_role in ('namespace', 'class', 'method'):
        api_url_pattern = re.sub(r'{'+unused_role+'}.*?{/'+unused_role+'}', '', api_url_pattern)

    try:
        full_url = api_url_pattern % {'namespace': namespace, 'class': class_name, 'method': method}
    except (TypeError, ValueError):
        env.warn(env.docname, 'unable to expand %s api_url with base '
                 'URL %r, please make sure the base contains \'%%s\' '
                 'exactly once' % (role, api_url_pattern))
        full_url = api_url_pattern + full_class

    return full_url
