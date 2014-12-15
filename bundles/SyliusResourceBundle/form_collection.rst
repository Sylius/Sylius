Collection Form Type
====================

Collection form extension
-------------------------
Two options are added to the basic collection form type :
    - **button_add_label** (default : 'form.collection.add') : Label of the addition button
    - **button_delete_label** (default : 'form.collection.delete') : Label of the deletion button

HTML markup
-----------
The container element needs to have `data-form-type="collection"` as html attribute. It is used to enable the form collection plugin.
All data attributes beginning by data-form-collection are used by the plugin and should not be removed.

.. code-block:: html

    <div data-form-type="collection" data-prototype='...'>
        <div data-form-collection="list" class="row collection-list">

            <input type="hidden" data-form-prototype="prototypeName" value="..." />

            <div data-form-collection="item"
                 data-form-collection-index="XX"
                 class="col-md-{{ boxWidth }} collection-item">

                <!-- Put here your sub form -->

                <a href="#" data-form-collection="delete">
                    Delete
                </a>
             </div>
        </div>

        <a href="#" data-form-collection="add">
            Add
        </a>
    </div>

**List of HTML attributes :**
    - **data-prototype** : form prototype
    - **data-form-collection="list"** : container of the list of the collection item
    - **data-form-collection="item"** : container of the collection item
    - **data-form-collection-index="XX"** : index of the current collection item
    - **data-form-collection="delete"** : HTML element used to remove collection item
    - **data-form-collection="add"** : HTML element used to add a new collection item using the form prototype.
    - **data-form-collection="update"** : HTML element used to update the collection item when on change event is fired. Element has to belong to the current collection item.
    - **data-form-prototype="update"** : HTML element used to update the form prototype when change event is fired.

Updating dynamically the form Prototype
---------------------------------------
When a HTML element which has **data-form-prototype="update"** as HTML attribute fired change event, the plugin will find the new
prototype from the server if the **data-form-url="Url"** is specified. The value of the input/select and the position of the collection
item will be submitted to the server.

.. code-block:: html

    <div data-form-collection="item" data-form-collection-index="1">
        <select data-form-url="example.com/update">
            <option value="rule">Rule</option>
            <option value="action">Action</option>
        </select>
    </div>

In this example, the value of the select and the position will be sent to the server.

Another way exists, hidden inputs can be used too if you don't want to generate the prototype by the server.
You need to insert as many hidden inputs as select options in the page. They need to have attribute like
**data-form-prototype="prototypeName"**. "prototypeName" needs to match to one of all the select options.

.. code-block:: html

    <div data-form-collection="item" data-form-collection-index="1">

        <input type="hidden" data-form-prototype="rule" value="..." />
        <input type="hidden" data-form-prototype="action" value="..." />

        <select>
            <option value="rule">Rule</option>
            <option value="action">Action</option>
        </select>
    </div>

In this example, when you select Rule, the plugin will replace the current form prototype by the value of the hidden input
which has data-form-prototype="rule".
