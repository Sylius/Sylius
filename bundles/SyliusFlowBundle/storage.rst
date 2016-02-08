Using storage
=============

Storing data with default storage
---------------------------------

By default, flow bundle will use session for data storage. Here is simple example how to use it in your steps:

.. code-block:: php

    <?php

    namespace Acme\DemoBundle\Process\Step;

    use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
    use Sylius\Bundle\FlowBundle\Process\Step\ControllerStep;

    class FirstStep extends ControllerStep
    {
        // ...

        public function forwardAction(ProcessContextInterface $context)
        {
            $request = $this->getRequest();
            $form = $this->createForm('my_form');
            
            $form->handleRequest($request);

            if ($request->isMethod('POST') && $form->isValid()) {
                $context->getStorage()->set('my_data', $form->getData());

                return $this->complete();
            }

            return $this->render('AcmeDemoBundle:Process/Step:first.html.twig', array(
                'form' => $form->createView(),
            ));
        }
    }

You can later get data with ``$context->getStorage()->get('my_data')``.

For more details about storage, check **Sylius\Bundle\FlowBundle\Storage\StorageInterface** class.
