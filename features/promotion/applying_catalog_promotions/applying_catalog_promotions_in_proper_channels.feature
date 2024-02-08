@applying_catalog_promotions
Feature: Applying catalog promotions only in proper channels
    In order to be attracted to products
    As a Visitor
    I want to see discounted products in the catalog of proper channel

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store operates on another channel named "Web-GB" with hostname "web-gb"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And "PHP T-Shirt" variant priced at "$30.00" in "Web-GB" channel
        And this product is available in "Web-US" channel and "Web-GB" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying catalog promotion
        When I change my current channel to "Web-US"
        And I view product "T-Shirt"
        Then I should see the product price "$14.00"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Not applying catalog promotion if it is not available in current channel
        When I change my current channel to "Web-GB"
        And I view product "T-Shirt"
        Then I should see the product price "$30.00"
        And I should see this product has no catalog promotion applied
