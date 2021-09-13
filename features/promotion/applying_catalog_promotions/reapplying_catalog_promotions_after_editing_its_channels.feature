@applying_catalog_promotions
Feature: Reapplying catalog promotions after editing its channels
    In order to be attracted to products by its current prices
    As a Visitor
    I want to see current discounted prices of products in proper channel

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store operates on another channel named "Web-GB" with hostname "web-gb"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And "PHP T-Shirt" variant priced at "$30.00" in "Web-GB" channel
        And this product is available in "Web-US" channel and "Web-GB" channel
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-shirt" variant
        And I am currently in the "Web-US" channel

    @todo
    Scenario: Removing applied catalog promotion after removing its channel
        Given this catalog promotion is no longer available in "Web-US" channel
        When I view product "T-Shirt"
        Then I should see the product price "$20.00"
        And I should see the product original price "$20.00"

    @todo
    Scenario: Reapplying catalog promotion after adding new channel to them
        Given this catalog promotion is available in "Web-GB" channel
        When I change my current channel to "Web-GB"
        And I view product "T-Shirt"
        Then I should see the product price "$21.00"
        And I should see the product original price "$30.00"

    @todo
    Scenario: Reapplying catalog promotion after switching channels
        Given this catalog promotion is no longer available in "Web-US" channel
        And this catalog promotion is available in "Web-GB" channel
        When I view product "T-Shirt"
        Then I should see the product price "$20.00"
        And I should see the product original price "$20.00"
        And I should see the product price "$21.00" with original price "$30.00" in "Web-GB" channel
