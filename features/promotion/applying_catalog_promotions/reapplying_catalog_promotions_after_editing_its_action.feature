@applying_catalog_promotions
Feature: Reapplying catalog promotions after editing its action
    In order to have proper discounts per catalog promotion action
    As a Store Owner
    I want to have discounts reapplied in product catalog once the action of catalog promotion changes

    Background:
        Given the store operates on a channel identified by "Web-US" code
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And there is a catalog promotion "Winter sale" that reduces price by "50%" and applies on "PHP T-shirt" variant
        And I am logged in as an administrator

    @api
    Scenario: Reapplying catalog promotion after editing action
        When I modify a catalog promotion "Winter sale"
        And I edit its action so that it reduces price by "25%"
        And I save my changes
        Then the visitor view "PHP T-Shirt" variant
        Then the product variant price should be "$15.00"
        And the product original price should be "$20.00"
