@managing_products
Feature: Viewing product in different locale
    In order to see product page in current locale
    As an Administrator
    I want to be able to see product's page in selected by me locale

    Background:
        Given the store operates on a single channel
        And that channel allows to shop using "English (United States)" and "Spanish (Mexico)" locales
        And it uses the "English (United States)" locale by default
        And I am logged in as an administrator

    @ui
    Scenario: Viewing a detailed page of product in different locale
        Given I am using "Spanish (Mexico)" locale for my panel
        When I want to create a new simple product
        Then I should see the "English (United States)" name section translated as "inglés (Estados Unidos)"
        And I should see the "Spanish (Mexico)" name section translated as "español (México)"
