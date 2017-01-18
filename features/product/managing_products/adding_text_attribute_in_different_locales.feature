@managing_products
Feature: Adding text attributes in different locales to a product
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add text attributes in different locales to a product

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "Symfony Mug"
        And the store has a text product attribute "Mug material"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a product with a text attribute in different locales
        When I want to create a new simple product
        And I specify its code as "mug"
        And I name it "PHP Mug" in "English (United States)"
        And I set its price to "$100.00" for "Web" channel
        And I set its "Mug material" attribute to "Wood" in "English (United States)"
        And I set its "Mug material" attribute to "Drewno" in "Polish (Poland)"
        And I add it
        Then I should be notified that it has been successfully created
        And the product "PHP Mug" should appear in the store
        And attribute "Mug material" of product "PHP Mug" should be "Wood" in "English (United States)"
        And attribute "Mug material" of product "PHP Mug" should be "Drewno" in "Polish (Poland)"

    @ui @javascript
    Scenario: Adding a text attribute in different locales to an existing product
        When I want to modify the "Symfony Mug" product
        And I set its "Mug material" attribute to "Wood" in "English (United States)"
        And I set its "Mug material" attribute to "Drewno" in "Polish (Poland)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And attribute "Mug material" of product "Symfony Mug" should be "Wood" in "English (United States)"
        And attribute "Mug material" of product "Symfony Mug" should be "Drewno" in "Polish (Poland)"
