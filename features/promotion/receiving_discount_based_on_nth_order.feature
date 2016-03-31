@promotion
Feature: Receiving discount based on nth order
    In order to pay less while placing order
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a promotion "5 order promotion"
        And it gives "€20.00" off if placing order is customer's 5th order
        And I am logged in customer

    @ui
    Scenario: Receiving discount on order if it's nth order placed
        Given I have 4 orders already placed
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€80.00"
        And my discount should be "-€20.00"

    @ui
    Scenario: Receiving no discount on order if it's not nth order placed
        Given I have 3 orders already placed
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€100.00"
        And there should be no discount
