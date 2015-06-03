<?php

namespace Ibrows\SonataTranslationBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitManagerInterface;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class TranslationAdmin extends Admin
{
    /**
     * @var TransUnitManagerInterface
     */
    protected $transUnitManager;
    /**
     * @var array
     */
    protected $editableOptions;

    /**
     * @var array
     */
    protected $defaultSelections = array();

    /**
     * @var array
     */
    protected $emptyFieldPrefixes = array();

    /**
     * @param array $options
     */
    public function setEditableOptions(array $options)
    {
        $this->editableOptions = $options;
    }

    /**
     * @param TransUnitManagerInterface $translationManager
     */
    public function setTransUnitManager(TransUnitManagerInterface $translationManager)
    {
        $this->transUnitManager = $translationManager;
    }

    /**
     * @param array $managedLocales
     */
    public function setManagedLocales($managedLocales)
    {
        $this->managedLocales = $managedLocales;
    }

    /**
     * @return array
     */
    public function getEmptyFieldPrefixes()
    {
        return $this->emptyFieldPrefixes;
    }

    /**
     * @return array
     */
    public function getDefaultSelections()
    {
        return $this->defaultSelections;
    }

    /**
     * @return array
     */
    public function getFilteredLanguages()
    {
        $languages = array();
        array_walk_recursive(
            $this->defaultSelections['languages'],
            function ($language) use (&$languages) {
                $languages[$language] = $language;
            }
        );

        return $languages;
    }

    /**
     * @return array
     */
    public function getNonTranslatedOnly()
    {
        return array_key_exists('nonTranslatedOnly', $this->getDefaultSelections()) && (bool) $this->defaultSelections['nonTranslatedOnly'];
    }

    /**
     * @param array $selections
     */
    public function setDefaultSelections(array $selections)
    {
        $this->defaultSelections = $selections;
    }

    /**
     * @param array $prefixes
     */
    public function setEmptyPrefixes(array $prefixes)
    {
        $this->emptyFieldPrefixes = $prefixes;
    }

    /**
     * @return array
     */
    public function getFilterParameters()
    {
        $this->datagridValues = array_merge(
            array(
                'domain' => array(
                    'value' => $this->getDefaultDomain(),
                ),
            ),
            $this->datagridValues

        );

        return parent::getFilterParameters();
    }

    /**
     * @param unknown $name
     * @return multitype:|NULL
     */
    public function getTemplate($name)
    {
        if ($name === 'layout') {
            return 'IbrowsSonataTranslationBundle::translation_layout.html.twig';
        }

        if ($name === 'list') {
            return 'IbrowsSonataTranslationBundle:CRUD:list.html.twig';
        }

        return parent::getTemplate($name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getOriginalTemplate($name)
    {
        return parent::getTemplate($name);
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('clear_cache')
            ->add('create_trans_unit');
    }

    /**
     * @param ListMapper $list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('id', 'integer')
            ->add('key', 'string')
            ->add('domain', 'string');

        foreach ($this->managedLocales as $locale) {
            $fieldDescription = $this->modelManager->getNewFieldDescriptionInstance($this->getClass(), $locale);
            $fieldDescription->setTemplate(
                'IbrowsSonataTranslationBundle:CRUD:base_inline_translation_field.html.twig'
            );
            $fieldDescription->setOption('locale', $locale);
            $fieldDescription->setOption('editable', $this->editableOptions);
            $list->add($fieldDescription);
        }
    }

    /**
     * @param FormMapper $form
     */
    protected function configureFormFields(FormMapper $form)
    {
        $subject = $this->getSubject();

        if (null === $subject->getId()) {
            $subject->setDomain($this->getDefaultDomain());
        }

        $form
            ->add('key', 'text')
            ->add('domain', 'text');
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    /**
     * @return string
     */
    protected function getDefaultDomain()
    {
        return $this->getContainer()->getParameter('ibrows_sonata_translation.defaultDomain');
    }
}
