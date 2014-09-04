Model insterfaces
=================

SoftDeletableInterface
----------------------

This inferface will ask you to implement the following methods to your model, they will use by the soft
`deletable Doctrine2 extension <https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/softdeleteable.md>`_. :

:isDeleted(): it will tell to you if the resource has been deleted (it must return a boolean)
:getDeletedAt(): It will return the date when the resouce has been deleted (it must return a DateTime)
:setDeletedAt(\DateTime $deletedAt):

TimestampableInterface
----------------------

This inferface will ask you to implement the following methods to your model, they will use by the
`timestampable Doctrine2 extension <https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/timestampable.md/>`_. :

:getCreatedAt(): it will return the date when the resource has been created (it must return a DateTime)
:getUpdatedAt(): it will return the date when the resource has been updated for the last time (it must return a DateTime)
:setCreatedAt(\DateTime $createdAt):
:setUpdatedAt(\DateTime $updatedAt):