@shopping_cart
Feature: Seeing shipping costs only when order requires shipping
    In order to pay the correct amount for my order
    As a Customer
    I want to be only be charged for shipping when it is necessary

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Guards! Guards!" configurable product
        And this product has "Guards! Guards! - book" variant priced at "$20.00"
        And this product has "Guards! Guards! - ebook" variant priced at "$12.55" which does not require shipping
        And the store has "SHL" shipping method with "$5.00" fee
        And I am a logged in customer

    @api @ui
    Scenario: Not seeing shipping cost if none of the order items require shipping
        Given I added "Guards! Guards! - ebook" variant of product "Guards! Guards!" to the cart
        When I see the summary of my cart
        Then I should not see shipping total for my cart

    @api @ui
    Scenario: Seeing shipping cost if some of the order items require shipping
        Given I added "Guards! Guards! - book" variant of product "Guards! Guards!" to the cart
        And I added "Guards! Guards! - ebook" variant of product "Guards! Guards!" to the cart
        When I see the summary of my cart
        Then my cart shipping total should be "$5.00"

    @api @ui
    Scenario: Not seeing free cost if the order items that require shipping are removed
        Given I added "Guards! Guards! - book" variant of product "Guards! Guards!" to the cart
        When I added "Guards! Guards! - ebook" variant of product "Guards! Guards!" to the cart
        And I removed "Guards! Guards! - book" variant from the cart
        Then I see the summary of my cart
        And I should not see shipping total for my cart
