@applying_promotion_coupon
Feature: Applying promotion coupon with per customer usage limit
    In order to pay proper amount after using the promotion coupon
    As a Visitor
    I want to have promotion coupon's discounts applied to my cart only if the given promotion coupon is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has a product "PHP Socks" priced at "$30.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this coupon can be used twice per customer
        And this promotion gives "$10.00" discount to every order
        And the store ships everywhere for free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @ui
    Scenario: Receiving discount from valid coupon with a per customer usage limit as a logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving discount from valid coupon with a per customer usage limit as a logged in customer
        Given I placed an order "#00000022"
        And I bought a "PHP T-Shirt" and a "PHP Socks"
        And I used "SANTA2016" coupon
        And I chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving no discount from valid coupon that has reached its per customer usage limit
        Given I placed an order "#00000022"
        And I bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And I chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I placed an order "#00000023"
        And I bought a single "PHP Socks" using "SANTA2016" coupon
        And I chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$100.00"
        And there should be no discount

    @ui
    Scenario: Cancelled orders should not influence per customer usage limit
        Given I placed an order "#00000022"
        And I bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And I chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I placed an order "#00000023"
        And I bought a single "PHP Socks" using "SANTA2016" coupon
        And I chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        But I cancelled this order
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"
