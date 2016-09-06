@checkout
Feature: Specifying province for a country manually
    In order to specify a province for a country without default provinces
    As a Customer
    I want to be able to specify a province for selected country manually

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships to "Poland"
        And the store has a zone "World" with code "WR"
        And this zone has the "Poland" country member
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am a logged in customer

    @ui @javascript
    Scenario: Specifying province name manually for country without provinces defined
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Cavendish", "Green Gables", "91-242", "Poland" for "Anne Shirley"
        And I specify the province name manually as "Lubelskie" for shipping address
        And I specify the billing address as "Warsaw", "Obarowska", "91-242", "Poland" for "Frog Monica"
        And I specify the province name manually as "Mazowieckie" for billing address
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @ui @javascript
    Scenario: Being unable to specify province name manually for country with defined provinces
        Given the country "United States" has the "Utah" province with "UT" code
        And I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        When I specify the shipping address as "Cavendish", "Green Gables", "91-242", "United States" for "Anne Shirley"
        And I specify the billing address as "Frogpolis", "Leaf", "91-242", "United States" for "Frog Monica"
        Then I should not be able to specify province name manually for shipping address
        And I should not be able to specify province name manually for billing address
