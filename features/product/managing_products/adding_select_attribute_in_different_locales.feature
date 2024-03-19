@managing_products
Feature: Adding select attributes in different locales to a product
    In order to extend my merchandise with more complex products
    As an Administrator
    I want to add select attribute choices in different locales to a product

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "Symfony Mug"
        And the store has a select product attribute "Mug material" with value "Ceramic"
        And this product attribute's "Ceramic" value is labeled "Ceramic" in the "English (United States)" locale
        And this product attribute's "Ceramic" value is labeled "Ceramika" in the "Polish (Poland)" locale
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a product with a select attribute with choices in different locales
        When I want to create a new configurable product
        And I specify its code as "mug"
        And I name it "PHP Mug" in "English (United States)"
        And I add the "Mug material" attribute
        And I select "Ceramic" value in "English (United States)" for the "Mug material" attribute
        And I select "Ceramika" value in "Polish (Poland)" for the "Mug material" attribute
        And I add it
        Then I should be notified that it has been successfully created
        And the product "PHP Mug" should appear in the store
        And select attribute "Mug material" of product "PHP Mug" should be "Ceramic" in "English (United States)"
        And select attribute "Mug material" of product "PHP Mug" should be "Ceramika" in "Polish (Poland)"
