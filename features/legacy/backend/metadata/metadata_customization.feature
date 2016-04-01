@legacy @metadata
Feature: Metadata customization
    In order to customize metadata
    As a store owner
    I want to have user-friendly metadata customization form

    Background:
        Given store has default configuration
        And I am logged in as administrator

    Scenario: Showing customization form
        When I am customizing metadata
        Then I should see metadata customization form

    Scenario: Creating page metadata without Twitter metadata
        Given I am customizing metadata with identifier "FOO-BAR"
        When I fill in "Title" with "Lorem ipsum"
        And I fill in "Keywords" with "sylius, ecommerce"
        And I press "Save changes"
        Then I should be on the page of metadata container with id "FOO-BAR"
        And I should see the following metadata:
            | Title       | Lorem ipsum       |
            | Keywords    | sylius, ecommerce |
            | Description | <empty>           |
            | Twitter     | <empty>           |

    Scenario: Modifying existing page metadata
        Given there is the following metadata "ZBIGNIEW-WODECKI":
            | Title | Regular musician |
        And I am customizing metadata with identifier "ZBIGNIEW-WODECKI"
        When I fill in "Title" with "Expressive virtuoso"
        And I press "Save changes"
        Then I should be on the page of metadata container with id "ZBIGNIEW-WODECKI"
        And I should see the following metadata:
            | Title | Expressive virtuoso |

    @javascript
    Scenario: Managing dynamic Twitter form
        Given I am customizing metadata
        When I select "Application" from "Twitter Card"
        Then I should see Twitter's application card form

    @javascript
    Scenario: Removing dynamic Twitter form content when select is empty
        Given I am customizing metadata
        And I select "Application" from "Twitter Card"
        When I deselect "Twitter Card"
        Then I should not see Twitter's application card form

    @javascript
    Scenario: Creating page metadata with Twitter metadata
        Given I am customizing metadata with identifier "TWEET-IT"
        When I fill in "Title" with "Lorem ipsum"
        And I select "Player" from "Twitter Card"
        And I fill in "Site" with "@sylius"
        And I press "Save changes"
        Then I should be on the page of metadata container with id "TWEET-IT"
        And I should see the following metadata:
            | Title        | Lorem ipsum |
            | Twitter.Site | @sylius     |

    @javascript
    Scenario: Setting Twitter metadata as empty after filling a few fields removes Twitter metadata
        Given I am customizing metadata with identifier "DEFAULT"
        When I fill in "Title" with "Lorem ipsum"
        And I select "Player" from "Twitter Card"
        And I fill in "Site" with "@sylius"
        And I deselect "Twitter Card"
        And I press "Save changes"
        Then I should be on the page of metadata container with id "DEFAULT"
        And I should see the following metadata:
            | Title   | Lorem ipsum |
            | Twitter | <empty>     |

    @javascript
    Scenario: Setting Twitter metadata as empty removes it
        Given there is the following metadata "YOLO-616":
            | Twitter.Card  | Summary     |
            | Twitter.Title | Lorem ipsum |
        And I am customizing metadata with identifier "YOLO-616"
        When I deselect "Twitter Card"
        And I press "Save changes"
        Then I should be on the page of metadata container with id "YOLO-616"
        And I should see the following metadata:
            | Twitter | <empty> |
