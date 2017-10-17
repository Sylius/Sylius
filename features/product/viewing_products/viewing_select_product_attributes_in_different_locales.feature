@viewing_products
Feature: Viewing product's select attributes in different locales
    In order to see product's specification in different locales
    As a Visitor
    I want to be able to see product's select attributes in a chosen locale

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a select product attribute "T-shirt material"
        And this product attribute has a value "Banana skin" in "English (United States)" locale and "Skórka banana" in "Polish (Poland)" locale
        And the store has a select product attribute "T-shirt colour"
        And this product attribute has also a value "Yellow" in "English (United States)" locale
        And the store has a product "T-shirt banana"
        And this product has a select attribute "T-shirt material" with value "Banana skin" in "English (United States)" locale
        And this product has also a select attribute "T-shirt material" with value "Skórka banana" in "Polish (Poland)" locale
        And this product has also a select attribute "T-shirt colour" with value "Yellow" in "English (United States)" locale

    @ui
    Scenario: Viewing a detailed page with product's select attribute in default locale
        When I view product "T-shirt banana"
        Then I should see the product attribute "T-shirt material" with value "Banana skin"
        And I should also see the product attribute "T-shirt colour" with value "Yellow"

    @ui
    Scenario: Viewing a detailed page with product's select attribute in different locale
        When I view product "T-shirt banana" in the "Polish (Poland)" locale
        Then I should see the product attribute "T-shirt material" with value "Skórka banana"
        And I should also see the product attribute "T-shirt colour" with value "Yellow"
