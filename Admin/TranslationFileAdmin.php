<?php
/**
 * @author    Mike Lohmann <mike@protofy.com>
 * @copyright 2015 Protofy GmbH & CO. KG
 */

namespace Ibrows\SonataTranslationBundle\Admin;


use Ibrows\SonataTranslationBundle\Entity\TranslationFile;
use Lexik\Bundle\TranslationBundle\Manager\TransUnitManagerInterface;
use Lexik\Bundle\TranslationBundle\Storage\DoctrineORMStorage;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Yaml\Parser;

class TranslationFileAdmin extends Admin
{
    /**
     * @var TransUnitManagerInterface
     */
    private $transUnitManager;

    /**
     * @var array
     */
    protected $managedLocales = array();

    /**
     * @var DoctrineORMStorage
     */
    private $translationStorage;

    /**
     * @var Parser
     */
    private $yamlParser;

    public function __construct($code, $class, $baseControllerName)
    {
        $this->setYamlParser(new Parser());
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param array $managedLocales
     */
    public function setManagedLocales(array $managedLocales)
    {
        $this->managedLocales = $managedLocales;
    }

    /**
     * @param Parser $yamlParser
     */
    public function setYamlParser(Parser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
    }


    /**
     * @param DoctrineORMStorage $translationStorage
     */
    public function setTranslationStorage($translationStorage)
    {
        $this->translationStorage = $translationStorage;
    }

    /**
     * @param TransUnitManagerInterface $translationManager
     */
    public function setTransUnitManager(TransUnitManagerInterface $translationManager)
    {
        $this->transUnitManager = $translationManager;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('file', 'file', array('required' => true));
    }

    public function prePersist($translationFile) {
        $this->manageFileUpload($translationFile);
    }

    public function postPersist($translationFile) {
        // delete after creation
        $this->delete($translationFile);
    }

    private function manageFileUpload(TranslationFile $translationFile) {
        if ($translationFile->getFile()) {
            $translationFile->setCreatedAt(new \DateTime());

            $file = $translationFile->getFile()->getRealPath();

            if (!is_file($file)) {
                throw new \Exception('File does not exists!');
            }

            $translations = $this->yamlParser->parse(file_get_contents($file));

            foreach($translations as $key => $value) {
                $keys = explode('__', $key);
                list($domain, $translationKey, $id, $locale) = $keys;
                $transUnit = $this->translationStorage->getTransUnitById($id);

                if (in_array($locale, $this->managedLocales)) {
                    $this->transUnitManager->updateTranslation($transUnit, $locale, $value, true);
                }
            }

            $this->translator->removeLocalesCacheFiles($this->managedLocales);
        }
    }

}