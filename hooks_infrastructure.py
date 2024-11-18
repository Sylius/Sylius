import yaml


def parse_hooks(yaml_config):
    """
    Parses hooks from a YAML configuration and returns a dependency tree.
    """
    hooks = yaml.safe_load(yaml_config)["sylius_twig_hooks"]["hooks"]
    tree = {}

    for hook, config in hooks.items():
        parts = hook.split(".")
        parent = ".".join(parts[:-1])
        if parent:
            tree.setdefault(parent, []).append(hook)
        else:
            tree.setdefault(hook, [])

    return tree


def generate_mermaid_diagram(tree):
    """
    Generates a Mermaid-compatible dependency diagram from the tree.
    """
    mermaid = ["graph TD"]
    for parent, children in tree.items():
        for child in children:
            mermaid.append(f'    "{parent}" --> "{child}"')
    return "\n".join(mermaid)


# Example YAML input
yaml_config = """
sylius_twig_hooks:
    hooks:
        'sylius_admin.admin_user.create.content':
            form:
                component: 'sylius_admin:admin_user:form'
                props:
                    resource: '@=_context.resource'
                    form: '@=_context.form'
                    template: '@SyliusAdmin/shared/crud/common/content/form.html.twig'
                configuration:
                    render_rest: false
                priority: 0

        'sylius_admin.admin_user.create.content.form':
            sections:
                template: '@SyliusAdmin/admin_user/form/sections.html.twig'
                priority: 0

        'sylius_admin.admin_user.create.content.form.sections':
            general:
                enabled: false
            account:
                template: '@SyliusAdmin/admin_user/form/sections/account.html.twig'
                priority: 100
            personal_information:
                template: '@SyliusAdmin/admin_user/form/sections/personal_information.html.twig'
                priority: 0

        'sylius_admin.admin_user.create.content.form.sections.account':
            username:
                template: '@SyliusAdmin/admin_user/form/sections/account/username.html.twig'
                priority: 300
            email:
                template: '@SyliusAdmin/admin_user/form/sections/account/email.html.twig'
                priority: 200
            password:
                template: '@SyliusAdmin/admin_user/form/sections/account/password.html.twig'
                priority: 100
            enabled:
                template: '@SyliusAdmin/admin_user/form/sections/account/enabled.html.twig'
                priority: 0

        'sylius_admin.admin_user.create.content.form.sections.personal_information':
            first_name:
                template: '@SyliusAdmin/admin_user/form/sections/personal_information/first_name.html.twig'
                priority: 300
            last_name:
                template: '@SyliusAdmin/admin_user/form/sections/personal_information/last_name.html.twig'
                priority: 200
            locale:
                template: '@SyliusAdmin/admin_user/form/sections/personal_information/locale.html.twig'
                priority: 100
            avatar:
                template: '@SyliusAdmin/admin_user/form/sections/personal_information/avatar.html.twig'
                priority: 0
"""

# Parse and generate Mermaid diagram
tree = parse_hooks(yaml_config)
mermaid_diagram = generate_mermaid_diagram(tree)

# Output the diagram
print(mermaid_diagram)
