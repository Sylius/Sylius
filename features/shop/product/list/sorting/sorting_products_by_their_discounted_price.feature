@viewing_products
Feature: Sorting products by discounted price
    In order to see product variants sorted by their discounted price
    As a Visitor
    I want to be able to see a properly sorted products by their discounted price

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Merchandise"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Merchandise"
        And the product "T-Shirt" has "PHP T-Shirt" variant priced at "$20.00"
        And the product "T-Shirt" has "Symfony T-Shirt" variant priced at "$40.00"
        And the store has a "Mug" configurable product
        And this product belongs to "Merchandise"
        And the product "Mug" has "PHP Mug" variant priced at "$25.00"
        And the product "Mug" has "Symfony Mug" variant priced at "$30.00"
        And there is a catalog promotion "Mugs sale" that reduces price by "25%" and applies on "PHP Mug" variant and "Symfony Mug" variant

    @api @ui
    Scenario: Sorting products by discounted price of their first variant with ascending order
        When I browse products from taxon "Merchandise"
        And I sort products by the lowest price first
        Then the first product on the list should have name "Mug"
        And the last product on the list should have name "T-Shirt"
