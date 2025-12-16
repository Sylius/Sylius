@viewing_products
Feature: Sorting listed products on multiple channels
    In order to change the order in which products are displayed
    As a Customer
    I want to be able to sort products per channel

    Background:
        Given the store operates on a channel named "Poland" with hostname "shop.pl"
        And the store operates on another channel named "Germany" with hostname "shop.de"
        And the store classifies its products as "Sylius merch"
        And the store has a product "Sylius Con shirt" with code "sylius-con-shirt", created at "27-10-2022"
        And this product is available in the "Poland" channel
        And this product is available in the "Germany" channel
        And this product belongs to "Sylius merch"
        And this product's price in "Poland" channel is "$25.00"
        And this product's price in "Germany" channel is "$25.00"
        And the store also has a product "Sylius Con stickers" with code "sylius-con-stickers", created at "27-10-2022"
        And this product belongs to "Sylius merch"
        And this product is available in the "Poland" channel
        And this product is available in the "Germany" channel
        And this product's price in "Poland" channel is "$30.00"
        And this product's price in "Germany" channel is "$20.00"

    @api @ui
    Scenario: Sorting products by their prices with ascending order on channel Poland
        When I change my current channel to "Poland"
        And I browse products from taxon "Sylius merch"
        And I sort products by the lowest price first
        Then I should see 2 products in the list
        And the first product on the list should have name "Sylius Con shirt"

    @api @ui
    Scenario: Sorting products by their prices with descending order on channel Poland
        When I change my current channel to "Poland"
        And I browse products from taxon "Sylius merch"
        And I sort products by the highest price first
        Then I should see 2 products in the list
        And the first product on the list should have name "Sylius Con stickers"

    @api @ui
    Scenario: Sorting products by their prices with ascending order on channel Germany
        When I change my current channel to "Germany"
        And I browse products from taxon "Sylius merch"
        And I sort products by the lowest price first
        Then I should see 2 products in the list
        And the first product on the list should have name "Sylius Con stickers"

    @api @ui
    Scenario: Sorting products by their prices with descending order on channel Germany
        When I change my current channel to "Germany"
        And I browse products from taxon "Sylius merch"
        And I sort products by the highest price first
        Then I should see 2 products in the list
        And the first product on the list should have name "Sylius Con shirt"
