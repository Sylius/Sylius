@checkout
Feature: No shipping cost when order does not require any shipping
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

    @ui
    Scenario: Seeing free shipping cost if none of the order items require shipping
        Given I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        When I see the summary of my cart
        Then my cart shipping should be for free

    @ui
    Scenario: Seeing shipping cost if some of the order items require shipping
        Given I have "Guards! Guards! - book" variant of product "Guards! Guards!" in the cart
        And I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        When I see the summary of my cart
        Then my cart shipping total should be "$5.00"

    @ui @javascript
    Scenario: Seeing free cost if the order items that require shipping are removed
        Given I have "Guards! Guards! - book" variant of product "Guards! Guards!" in the cart
        And I have "Guards! Guards! - ebook" variant of product "Guards! Guards!" in the cart
        When I remove product "Guards! Guards!" from the cart
        And I see the summary of my cart
        Then my cart shipping should be for free
