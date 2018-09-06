@checkout
Feature: Choosing a country from a set of shipping countries
    In order to avoid selecting an invalid country during the addressing checkout step step
    As a Customer
    I want to only be able to choose from countries that are shippable

    Background:
        Given the store operates on a single channel in the "United States" named "Webstore"
        And the store operates in "Germany" and "France"
        And the store ships everywhere for free
        And the store has a product "PHP T-Shirt" priced at "$19.99"

    @ui
    Scenario: Selecting a country on a channel without shipping countries
        Given I have product "PHP T-shirt" in the cart
        And I am at the checkout addressing step
        Then I should be able to select "United States", "Germany" or "France" as shipping country

    @ui
    Scenario: Selecting a country on a channel with shipping countries
        Given I have product "PHP T-shirt" in the cart
        And the channel "Webstore" has a shipping country "Germany"
        And the channel "Webstore" has a shipping country "United States"
        And I am at the checkout addressing step
        Then I should be able to select "Germany" or "United States" as shipping country
        But I should not be able to select "France" as shipping country
