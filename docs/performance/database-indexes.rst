Database indexes
================

Indexing tables allows you to decrease fetching time from database.

As an example lets take a look on customers list

Default index page is sorted by registration date, to create table index all you need to do is modify `Customer` Entity and add index using annotations.
`ORM annotations <https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html#annref_haslifecyclecallbacks>`_

This should be considered for the most common sorting in your application.

Using this solution you can increase speed of customer listing by around 10%.
