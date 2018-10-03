@managing_products
Feature: Changing product positions
    In order to sort products by position
    As an Administrator
    I want to be able to change positions of existing products

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Changing position of existing products
        Given the store has a product "Lamborghini Gallardo Model"
        And this product has a position set to 1
        And the store has a product "Maserati Quattroporte"
        And this product has a position set to 2
        When I go to the list page for this products
        And I change the position for "Maserati Quattroporte" to 1
        And I change the position for "Lamborghini Gallardo Model" to 2
        And I click save positions
        Then I should be notified that the positions has been updated
        And  "Maserati Quattroporte" should now have position set to 1
        And "Lamborghini Gallardo Model" should now have position set to 2
