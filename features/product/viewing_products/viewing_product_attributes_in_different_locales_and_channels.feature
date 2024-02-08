@viewing_products
Feature: Viewing product's attributes in different locales and channels
    In order to see product's specification in different locales and channels
    As a Visitor
    I want to be able to see product's attributes in a chosen locale and a channel

    Background:
        Given the store operates on a channel named "US Channel" with hostname "shop.us"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store operates on another channel named "PL Channel" with hostname "shop.pl"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "Polish (Poland)" locale by default
        And the store has a product "T-Shirt banana" available in "US Channel" channel
        And this product is also available in "PL Channel" channel
        And this product has a text attribute "T-Shirt material" with value "Banana skin" in "English (United States)" locale
        And this product has a text attribute "T-Shirt material" with value "Skórka banana" in "Polish (Poland)" locale
        And this product has a textarea attribute "T-Shirt details" with value "Banana is a very good material." in "English (United States)" locale
        And I am browsing the channel "US Channel"

    @ui @api
    Scenario: Viewing a detailed page with product's attribute for current channel with its default locale
        When I view product "T-Shirt banana" in the "Polish (Poland)" locale
        Then I should see the product attribute "T-Shirt material" with value "Skórka banana"
        And I should also see the product attribute "T-Shirt details" with value "Banana is a very good material."

    @ui @api
    Scenario: Viewing a detailed page with product's attribute for current channel in different locale
        When I view product "T-Shirt banana" in the "English (United States)" locale
        Then I should see the product attribute "T-Shirt material" with value "Banana skin"
        And I should also see the product attribute "T-Shirt details" with value "Banana is a very good material."

    @ui @api
    Scenario: Viewing a detailed page with product's attribute for different channel with its default locale
        When I change my current channel to "PL Channel"
        And I view product "T-Shirt banana" in the "Polish (Poland)" locale
        Then I should see the product attribute "T-Shirt material" with value "Skórka banana"
        And I should also see the product attribute "T-Shirt details" with value "Banana is a very good material."

    @ui @api
    Scenario: Viewing a detailed page with product's attribute for different channel in different locale
        When I change my current channel to "PL Channel"
        And I view product "T-Shirt banana" in the "English (United States)" locale
        Then I should see the product attribute "T-Shirt material" with value "Banana skin"
        And I should also see the product attribute "T-Shirt details" with value "Banana is a very good material."
