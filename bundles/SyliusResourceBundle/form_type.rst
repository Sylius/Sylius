Extra Form types
================

Collection form extension
-------------------------
Two options are added to the basic collection form type :
    - **button_add_label** (default : 'form.collection.add') : Label of the addition button
    - **button_delete_label** (default : 'form.collection.delete') : Label of the deletion button

HTML markup
+++++++++++
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
+++++++++++++++++++++++++++++++++++++++
When a HTML element which has **data-form-prototype="update"** as HTML attribute fired change event, the plugin will find the new
prototype from the server if the **data-form-url="Url"** is specified. The value of the input/select and the position of the collection
item will be submitted to the server.

.. code-block:: html

    <div data-form-collection="item" data-form-collection-index="1">
        <select data-form-prototype="update" data-form-url="example.com/update">
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

        <select data-form-prototype="update">
            <option value="rule">Rule</option>
            <option value="action">Action</option>
        </select>
    </div>

In this example, when you select Rule, the plugin will replace the current form prototype by the value of the hidden input
which has data-form-prototype="rule".

Activation
++++++++++

You need to use the jquery plugin and the form theme provided by this bundle.

.. code-block:: html

    {% javascripts
        'bundles/syliusresource/js/form-collection.js'
    %}
    <script type="text/javascript" src="{{ asset(asset_url) }}"></script>
    {% endjavascripts %}

.. code-block:: yaml

    twig:
    form:
        resources:
            - SyliusResourceBundle::form-collection.html.twig



Entity hidden type
------------------

It creates a input type hidden, its value will be the identifier of the resource. you need to specify the class (data_class)
will be used and the property (identifier) that you want to get.

In the following example, we will add the sku of the product in the form.

.. code-block:: php

    ProductType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('product', 'entity_hidden', array(
                    'data_class' => 'App\Bundle\Product\Model\Product'
                    'identifier' => 'sku'
                ))
            ;
        }
    }

The symfony form type will render this HTML :

.. code-block:: html

    <input type="hidden" name="product" value="132FDQS12" />

Resource choice type
--------------------

It creates a document or entity or phpcr_document form type depending on the driver used by the resource. You need to register
it as a service, its contructor requires three paramters, the first one is the FQDN of the resource, the second one is the
driver used by it and the last on is the name of the form type.

.. code-block:: yml

    services:
        app.form.type.entity:
            class:
            argument:
                - App/Bundle/Model/Product
                - doctrine/orm
                - product_choice

.. note::

    Caution : If you use the "advanced configuration", the resource extension will register it automatically.
