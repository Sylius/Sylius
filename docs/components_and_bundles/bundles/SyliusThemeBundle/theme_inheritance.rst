Theme inheritance
=================

While you can't set two themes active at once, you can make use of multiple inheritance. Eg.:

.. code-block:: json

    {
        "name": "vendor/child-theme",
        "extra": {
            "sylius-theme": {
                "title": "Child theme",
                "parents": ["vendor/first-parent-theme", "vendor/second-parent-theme"]
            }
        }
    }

.. code-block:: json

    {
        "name": "vendor/first-parent-theme",
        "extra": {
            "sylius-theme": {
                "title": "First parent theme",
                "parents": ["vendor/grand-parent-theme"]
            }
        }
    }

.. code-block:: json

    {
        "name": "vendor/grand-parent-theme",
        "extra": {
            "sylius-theme": {
                "title": "Grandparent theme"
            }
        }
    }

.. code-block:: json

    {
        "name": "vendor/second-parent-theme",
        "extra": {
            "sylius-theme": {
                "title": "Second parent theme",
            }
        }
    }

Configuration showed below will result in given order:

    - Child theme
    - First parent theme
    - Grandparent theme
    - Second parent theme

Grandparent theme gets overrided by first parent theme. First parent theme and second parent theme get overrided by child theme.
