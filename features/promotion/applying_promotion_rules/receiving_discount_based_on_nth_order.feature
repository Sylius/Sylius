@applying_promotion_rules
Feature: Receiving discount based on nth order
    In order to pay less while an placing order
    As a Customer
    I want to receive a discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store ships everywhere for free
        And the store allows paying "Cash on Delivery"
        And there is a promotion "5th order promotion"
        And it gives "$20.00" off customer's 5th order
        And I am a logged in customer

    @ui
    Scenario: Receiving a discount on an order if it's nth order placed
        Given I have already placed 4 orders choosing "PHP T-Shirt" product, "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving no discount on an order if it's not nth order placed
        Given I have already placed 3 orders choosing "PHP T-Shirt" product, "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And there should be no discount

    @ui
    Scenario: Receiving a discount on 6th order when 5th one was cancelled
        Given I have already placed 5 orders choosing "PHP T-Shirt" product, "Free" shipping method to "United States" with "Cash on Delivery" payment
        But I cancelled my last order
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"
