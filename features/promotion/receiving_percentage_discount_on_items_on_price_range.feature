@promotion
Feature: Receiving percentage discount on products from specific price range
    In order to pay less while buying goods from specific price range
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And the store has a product "PHP Mug" priced at "€20.00"
        And there is a promotion "Christmas sale"

    @ui
    Scenario: Receiving percentage discount on a item fulfilling minimum price criteria
        Given this promotion gives "10%" off on every product more expensive than "€50.00"
        When I add product "PHP T-Shirt" to the cart
        Then its price should be decreased by "€10.00"
        And my cart total should be "€90.00"
        And my discount should be "-€10.00"

    @ui
    Scenario: Receiving percentage discount on a single item fulfilling maximum price criteria
        Given this promotion gives "10%" off on every product less expensive than "€50.00"
        When I add product "PHP Mug" to the cart
        Then its price should be decreased by "€2.00"
        And my cart total should be "€18.00"
        And my discount should be "-€2.00"

    @ui
    Scenario: Receiving percentage discount on a single item fulfilling range price criteria
        Given this promotion gives "50%" off on every product more expensive than "€15.00" and less expensive than "€50.00"
        When I add product "PHP Mug" to the cart
        Then its price should be decreased by "€10.00"
        And my cart total should be "€10.00"
        And my discount should be "-€10.00"

    @ui
    Scenario: Receiving percentage discount on multiple items fulfilling maximum price criteria
        Given this promotion gives "20%" off on every product more expensive than "€50.00" and less expensive than "€150.00"
        When I add 3 products "PHP T-Shirt" to the cart
        Then theirs price should be decreased by "€60.00"
        And my cart total should be "€240.00"
        And my discount should be "-€60.00"

    @ui
    Scenario: Receiving percentage discount only on items that fit price range criteria
        Given this promotion gives "25%" off on every product more expensive than "€30.00" and less expensive than "€150.00"
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "PHP T-Shirt" price should be decreased by "€25.00"
        And product "PHP Mug" price should not be decreased
        And my cart total should be "€95.00"
        And my discount should be "-€25.00"

    @todo
    Scenario: Receiving different discounts on items from different price ranges
        Given this promotion gives "10%" off on every product more expensive than "€80.00"
        And there is a promotion "Mugs promotion"
        And it gives "90%" off on every product less expensive than "€25.00"
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "PHP T-Shirt" price should be decreased by "€10.00"
        And product "PHP Mug" price should be decreased by "€18.00"
        And my cart total should be "€92.00"
        And my discount should be "-€28.00"
