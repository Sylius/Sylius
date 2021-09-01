@applying_catalog_promotions
Feature: Applying catalog promotions only in proper channels
    In order to be attracted to products
    As a Visitor
    I want to see discounted products in the catalog of proper channel

    Background:
        Given the store operates on a channel named "Web-US"
        And the store operates on another channel named "Web-GB"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And "PHP T-Shirt" variant priced at "$30.00" in "Web-GB" channel
        And the store has a "Mug" configurable product
        And this product has "PHP Mug" variant priced at "$10.00" in "Web-GB" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel
        And it applies on "PHP T-Shirt" variant
        And it applies on "PHP Mug" variant
        And it reduces price by "30%"

    @todo @api
    Scenario: Applying catalog promotion
        When I change my current channel to "Web-US"
        And I view product "T-Shirt"
        Then I should see the product price "$14.00"
        And I should see the product original price "$20.00"

    @todo @api
    Scenario: Applying catalog promotion in different channel
        When I change my current channel to "Web-GB"
        And I view product "T-Shirt"
        Then I should see the product price "$21.00"
        And I should see the product original price "$30.00"

    @todo @api
    Scenario: Not applying catalog promotion if it is not available in current channel
        When I change my current channel to "Web-GB"
        And I view product "Mug"
        Then I should see the product price "$15.00"
        And I should see the product original price "$15.00"
