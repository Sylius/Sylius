| Q               | A                                                            |
|-----------------|--------------------------------------------------------------|
| Branch?         | 1.10, 1.11 or master <!-- see the comment below -->          |
| Bug fix?        | no/yes                                                       |
| New feature?    | no/yes                                                       |
| BC breaks?      | no/yes                                                       |
| Deprecations?   | no/yes <!-- don't forget to update the UPGRADE-*.md file --> |
| Related tickets | fixes #X, partially #Y, mentioned in #Z                      |
| License         | MIT                                                          |

<!--
 - Bug fixes must be submitted against the 1.10 or 1.11 branch(the lowest possible)
 - Features and deprecations must be submitted against the master branch
 - Make sure that the correct base branch is set

 To be sure you are not breaking any Backward Compatibilities, check the documentation:
 https://docs.sylius.com/en/latest/book/organization/backward-compatibility-promise.html
-->
