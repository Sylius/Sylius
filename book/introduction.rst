.. index::
   single: Introduction

Introduction to Sylius
======================

There are several ways you can use Sylius for your project, but there are 2 main use cases.
You can use our main application, customize views, configuration and start the project, or use
standalone bundles to build a solution that will fit the most sophisticated needs.

Using Sylius as a whole
-----------------------

Sylius project provides a **full stack e-commerce solution**. In further parts of The Book you'll learn
how to master it and develop your next project really quickly. Our main application called **Sylius** provides
a complete webshop solution and some of its features include:

* Flexible product catalog, with multiple variants per product,
  options, properties (think attributes) and prototypes.
* Categorization engine, which allows you to categorize the products under various
  taxonomies, by "Brand", "Category" or whatever you can imagine.
* Inventory tracking system, where you can track every single unit of your inventory 
  or disable tracking at all.
* Powerful shipping with configurable shipping categories, item sizes, weight, shipments management and customizable
  cost calculators.
* Taxation engine, with support for many different tax categories, rates and zones.
* Orders system allowing you to easily create and manage sales, with super-flexible adjustments which
  can serve many different purposes, from taxation & shipping to promotions and manual order total changes.
* Customizable checkout process, built from reusable steps.
* ... and more!

If that is what you were looking for, great! But we have other great news for you.
All features mentioned above are available also as individual bundles.

Leveraging Sylius bundles
-------------------------

Even if the main goal of the project is to provide the full stack solution mentioned above, we have built it from
decoupled and independent bundles. Every functionality you like in the main Sylius application can be 
integrated into your existing project. You can also build a tailored solution from ground-up using those
components. (**Bundles** in Symfony2 glossary)

If you have an existing Symfony2 application, for example: A book catalogue, you can use 
:doc:`SyliusSalesBundle </bundles/SyliusSalesBundle/index>` and add orders management feature.

Here you can find a documentation index for all Sylius bundles available to you:

.. include:: /bundles/map.rst.inc

Final Thoughts
--------------

Now, depending on how you want to use Sylius, continue reading The Book, which covers the usage of the full stack solution, or browse the Bundles Reference.
