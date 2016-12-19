@checkout
Feature: Placing an order with different scopes for shipping and taxes
    In order to deliver my purchase to different zone than my tax zone
    As a Customer
    I want to be able to finish checkout for zones in different scopes

    Background:
        Given the store operates on a single channel in "USD" currency
        And the store operates in "United States"
        And this country has the "Texas" province with "US-TX" code
        And the store has a product "Jane's Vest" priced at "$20"
        And the store allows paying offline
        And I am a logged in customer

    @ui @javascript
    Scenario: Placing an order with different tax and shipping zone
        Given the store has a shipping zone "United States Shipping" with code "US-SHIPPING"
        And it has the "United States" country member
        And the store has a tax zone "Texas Tax" with code "US-TX-TAX"
        And it has the "Texas" province member
        And the store has "TX-VAT" tax rate of 8% for "Clothes" within the "US-TX-TAX" zone
        And the store ships everything for free within the "US-SHIPPING" zone
        And this product belongs to "Clothes" tax category
        And I have product "Jane's Vest" in the cart
        Given I am at the checkout addressing step
        When I specify the shipping address for "Patrick Jane" from "Sixth Street", "78701", "Austin", "United States", "Texas"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my tax total should be "$1.60"
        And my order total should be "$21.6"

    @ui @javascript
    Scenario: Placing an order with in the same tax and shipping zone
        Given the store has a zone "United States" with code "US"
        And it has the "United States" country member
        And the store has "US-VAT" tax rate of 8% for "Clothes" within the "US" zone
        And the store ships everything for free within the "US" zone
        And this product belongs to "Clothes" tax category
        And I have product "Jane's Vest" in the cart
        Given I am at the checkout addressing step
        When I specify the shipping address for "Patrick Jane" from "Sixth Street", "78701", "Austin", "United States", "Texas"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my tax total should be "$1.60"
        And my order total should be "$21.6"

    @ui @javascript
    Scenario: Placing an order within shipping zone
        Given the store has a shipping zone "United States Shipping" with code "US"
        And it has the "United States" country member
        And the store ships everything for free within the "US" zone
        And I have product "Jane's Vest" in the cart
        Given I am at the checkout addressing step
        When I specify the shipping address for "Patrick Jane" from "Sixth Street", "78701", "Austin", "United States", "Texas"
        And I complete the addressing step
        And I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And my order total should be "$20"
