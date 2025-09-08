# 🎨 Frontend & Themes

This guide helps frontend developers navigate the Sylius documentation to create a custom look and feel for their Sylius 2.0 projects. With the move to Bootstrap 5 and Symfony UX, customizing styles, templates, and behavior is now more streamlined. Whether you're adjusting layouts, branding, or building full themes, this page points you to the right tools and docs to get it done.

## Frontend Customization Use Cases

With the adoption of Bootstrap, customizing the Sylius frontend has become more straightforward. Common customization scenarios include:

* **Template Customization:**
  * Modifying existing Twig templates to change the layout or content of pages.
  * Overriding templates to implement custom designs or functionalities.
* **Styling and Branding:**
  * Customizing Bootstrap variables (e.g., `--bs-primary`) to align with brand colors.
  * Adding custom CSS or SCSS to override default styles
* **JavaScript Enhancements:**
  * Integrating custom JavaScript functionalities using Stimulus controllers.
  * Replacing or extending default behaviors to meet specific requirements.
* **Theme Development:**
  * Creating and applying custom themes to different channels, allowing for varied presentations across storefronts.

***

## Frontend Tech Stack Overview

In Sylius 2.0, the frontend is built upon the following technologies:

* **Templating Engine:** Utilizes **Twig**, Symfony's default templating engine, for rendering views.
* **CSS Framework:** Adopts **Bootstrap 5** for styling both the Shop and Admin interfaces, replacing the previous Semantic UI framework.
* **JavaScript:** Incorporates **Symfony UX** components, including **Stimulus** and **Twig Components**, to enhance interactivity and maintainability.
* **Asset Management:** Employs **Webpack Encore**, Symfony's wrapper for Webpack, facilitating the compilation and optimization of CSS, JavaScript, and other assets.
* **Theming System:** Supports a theming system that allows for the customization and overriding of templates, assets, and translations on a per-channel basis.

***

## Documentation Resources for Frontend Customization

To effectively customize the Sylius frontend, refer to the following documentation sections:

* [**Customizing Templates**](../the-customization-guide/customizing-templates.md)\
  Learn how to modify Twig templates using Twig hooks, override existing templates, and implement themes to alter the layout and structure of your storefront.
* [**Customizing Styles**](../the-customization-guide/customizing-styles.md) \
  Discover methods to adjust styles using Bootstrap 5 variables, override CSS, and manage assets with Webpack Encore for a tailored visual appearance.
* [**Customizing Dynamic Elements**](../the-customization-guide/customizing-dynamic-elements.md) \
  Understand how to enhance interactivity by integrating StimulusJS controllers, utilizing Symfony UX, and injecting dynamic behavior through Twig hooks.
* [**Customizing Forms**](../the-customization-guide/customizing-forms/) \
  Explore techniques to modify forms, including adding or removing fields, altering labels, and implementing live components for dynamic form interactions.
* [**Customizing Menus**](https://docs.sylius.com/the-customization-guide/customizing-menus) \
  Add new menu items or modify existing ones in both the Shop and Admin panels.
* [**Customizing Flashes**](https://docs.sylius.com/the-customization-guide/customizing-flashes)\
  Customize flash messages for success, error, and info feedback using Twig and Bootstrap-compatible styles.
* [**Customizing Grids**](https://docs.sylius.com/the-customization-guide/customizing-grids)\
  Extend or modify admin data tables (called “grids”), including filtering, sorting, and adding custom actions.

