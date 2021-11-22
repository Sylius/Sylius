@receiving_discount
Feature: Receiving discounts with product minimum price specified
    In order to pay avoid paying less than product minimum price
    As a Visitor
    I want to receive discount for my purchase up to product minimum price

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$50.00"
        And the "PHP T-Shirt" variant has minimum price "$45.00" in "United States" channel
        And the store has a product "PHP Mug" priced at "$20.00"
        And there is a promotion "Christmas sale"

    @ui @api
    Scenario: Receiving percentage discount on a single item fulfilling minimum price criteria
        Given this promotion gives "50%" off on every product with minimum price at "$50.00"
        When I add product "T-Shirt" to the cart
        Then its price should be decreased by "$5.00"
        And my cart total should be "$45.00"

    @api
    Scenario: Receiving fixed discount for my cart
        Given there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order
        When I add product "T-Shirt" to the cart
        Then its price should be decreased by "$5.00"
        And my cart total should be "$45.00"
        And my discount should be "-$5.00"

    @ui @api
    Scenario: Receiving percentage discount on a single item fulfilling range price criteria
        Given this promotion gives "50%" off on every product priced between "$15.00" and "$50.00"
        When I add product "T-Shirt" to the cart
        Then its price should be decreased by "$5.00"
        And my cart total should be "$45.00"

    @ui @api
    Scenario: Distributing promotion when product price reaches minimum
        Given this promotion gives "50%" off on every product with minimum price at "$10.00"
        And there is a promotion "Mugs promotion"
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$10.00"
        And my cart total should be "$55.00"

    @ui @api
    Scenario: Distributing fixed discount promotion
        And this promotion gives "$10.00" off on every product with minimum price at "$10.00"
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$10.00"
        And my cart total should be "$55.00"

    @api
    Scenario: Distributing fixed order discount promotion
        Given the promotion gives "$20.00" discount to every order with quantity at least 2
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$15.00"
        And my cart total should be "$50.00"

    @api
    Scenario: Distributing fixed order discount promotion
        Given it gives "20%" discount to every order
        When I add product "T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "T-Shirt" price should be decreased by "$5.00"
        And product "PHP Mug" price should be decreased by "$9.00"
        And my cart total should be "$56.00"
