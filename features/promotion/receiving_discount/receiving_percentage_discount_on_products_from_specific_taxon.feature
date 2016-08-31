@receiving_discount
Feature: Receiving percentage discount on products from specific taxon
    In order to pay less while buying goods from promoted taxons
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$20.00"
        And it belongs to "Mugs"
        And there is a promotion "T-Shirts promotion"
        And it gives "20%" off every product classified as "T-Shirts"

    @ui
    Scenario: Receiving percentage discount on a single item from specific taxon
        When I add product "PHP T-Shirt" to the cart
        Then its price should be decreased by "$20.00"
        And my cart total should be "$80.00"

    @ui
    Scenario: Receiving percentage discount on a multiple items from specific taxon
        When I add 3 products "PHP T-Shirt" to the cart
        Then theirs price should be decreased by "$60.00"
        And my cart total should be "$240.00"

    @ui
    Scenario: Receiving percentage discount only on items from specific taxon
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "PHP T-Shirt" price should be decreased by "$20.00"
        And product "PHP Mug" price should not be decreased
        And my cart total should be "$100.00"

    @ui
    Scenario: Receiving different discounts on items from different taxons
        Given there is a promotion "Mugs promotion"
        And it gives "50%" off every product classified as "Mugs"
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "PHP T-Shirt" price should be decreased by "$20.00"
        And product "PHP Mug" price should be decreased by "$10.00"
        And my cart total should be "$90.00"
