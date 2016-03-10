@legacy @metadata
Feature: Metadata rendering
    In order to manage metadata on my store
    As a store owner
    I want to have easy and intuitive access to managing metadata

    Background:
        Given store has default configuration
        And there are products:
            | name   | price |
            | Banana | 4.20  |
        And product "Banana" has the following page metadata:
            | Title         | Tasty banana                  |
            | Description   | The best you have ever eaten! |
            | Keywords      | banana, fruit, healthy food   |
            | Twitter.Card  | Summary                       |
            | Twitter.Site  | @example                      |
            | Twitter.Image | http://example.com/image.jpg  |
        And all products are assigned to the default channel

    Scenario: Rendering page metadata
        When I am on the product page for "Banana"
        Then I should see "Tasty banana" as page title
        And the page keywords should contain "fruit"

    Scenario: Rendering page Twitter metadata
        When I am on the product page for "Banana"
        Then there should be Twitter summary card metadata on this page
        And Twitter site should be "@example"
        And Twitter image should be "http://example.com/image.jpg"
