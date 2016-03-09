@legacy @product
Feature: Product reviews
    In order to rate and learn more about the products
    As a Visitor
    I want to browse and post product reviews

    Background:
        Given there is default currency configured
        And there are following users:
            | email       | password | enabled |
            | bar@foo.com | foo1     | yes     |
        And there are following channels configured:
            | code   | name       | currencies | locales             | url       |
            | WEB-EU | mystore.eu | USD        | en_GB, fr_FR, de_DE | localhost |
        And the following products exist:
            | name            | price | pricing calculator | calculator configuration | attributes |
            | Symfony T-Shirt | 15.00 |                    |                          |            |
        And channel "WEB-EU" has following products assigned:
            | product         |
            | Symfony T-Shirt |
        And there are following reviews:
            | title       | rating | comment                | author      | product         | subject type |
            | Lorem ipsum | 5      | Lorem ipsum dolor sit  | bar@foo.com | Symfony T-Shirt | product      |
            | Consectetur | 4      | Consectetur adipiscing | bar@foo.com | Symfony T-Shirt | product      |
            | Proin nibh  | 3      | Proin nibh augue       | bar@foo.com | Symfony T-Shirt | product      |

    Scenario: Displaying product rating and reviews:
        When I am on the product page for "Symfony T-Shirt"
        Then I should see "Rating: 4"
        And I should see 3 review on the reviews list

    @javascript
    Scenario: Adding review as logged in user
        Given I am logged in as "bar@foo.com"
        And I am on the product page for "Symfony T-Shirt"
        When I fill in "Title" with "Very good"
        And I fill in "Comment" with "Very good shirt."
        And I select the "5" radio button
        And I press "Submit"
        And I wait 3 seconds
        Then I should see "Product review has been successfully created."

    @javascript
    Scenario: Adding review as guest
        Given I am on the product page for "Symfony T-Shirt"
        When I fill in "Title" with "Very good"
        And I fill in "Comment" with "Very good shirt."
        And I fill in "Email" with "guest@example.com"
        And I select the "5" radio button
        And I press "Submit"
        And I wait 3 seconds
        Then I should see "Product review has been successfully created."

    @javascript
    Scenario: Trying to add review as guest
        Given I am on the product page for "Symfony T-Shirt"
        When I fill in "Title" with "Very good"
        And I fill in "Comment" with "Very good shirt."
        And I fill in "Email" with "bar@foo.com"
        And I select the "5" radio button
        And I press "Submit"
        And I wait 3 seconds
        Then I should see "This email is already registered, please login or use forgotten password."
