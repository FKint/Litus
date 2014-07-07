<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CommonBundle\Component\OldForm\Admin\Element;

use Zend\Form\ElementPrepareAwareInterface,
    Zend\Form\Fieldset,
    Zend\Form\FormInterface;

/**
 * Collection form element
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Collection extends \Zend\Form\Element\Collection
{
    /**
     * @param  string|null                        $name    Optional name for the element
     * @param  array                              $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('id', $name);
    }

    /**
     * @return boolean
     */
    public function isCollection()
    {
        return true;
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets to every elements in order to avoid
     * name clashes if the same fieldset is used multiple times
     *
     * @param  FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        foreach ($this->byName as $elementOrFieldset) {
            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }

        if ($this->shouldCreateTemplate())
            parent::prepareElement($form);
    }

    /**
     * Populate values
     *
     * @param  array|\Traversable                            $data
     * @throws \Zend\Form\Exception\InvalidArgumentException
     * @throws \Zend\Form\Exception\DomainException
     * @return void
     */
    public function populateValues($data)
    {
        if (empty($data))
            return;

        foreach ($this->getFieldsets() as $fieldset) {
            $fieldset->populateValues($data);
        }

        foreach ($data as $key => $value) {
            if (!$this->has($key) && !is_numeric($key))
                unset($data[$key]);
        }

        if ($this->shouldCreateTemplate()) {
            foreach ($data as $value) {
                foreach ($this->byName as $name => $element) {
                    if (!isset($data[$name]))
                        $data[$name] = '';
                }
            }
        } else {
            foreach ($this->byName as $name => $element) {
                if (!isset($data[$name])) {
                    if ($this->get($name) instanceof Fieldset)
                        $data[$name] = array();
                    else
                        $data[$name] = '';
                }
            }
        }

        parent::populateValues($data);
    }

    /**
     * Set a hash of element names/messages to use when validation fails
     *
     * @param  array|\Traversable                 $messages
     * @return Collection
     * @throws Exception\InvalidArgumentException
     */
    public function setMessages($messages)
    {
        parent::setMessages($messages);

        $fieldsets = $this->getFieldsets();
        foreach ($fieldsets as $fieldset) {
            $fieldset->setMessages($messages);
        }

        return $this;
    }
}