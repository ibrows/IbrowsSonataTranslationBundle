<?php

namespace Ibrows\SonataTranslationBundle\Controller;

use Doctrine\DBAL\DBALException;
use Ibrows\SonataTranslationBundle\Event\RemoveLocaleCacheEvent;
use Lexik\Bundle\TranslationBundle\Entity\TransUnit;
use Lexik\Bundle\TranslationBundle\Manager\TranslationInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslationFileCRUDController extends CRUDController
{
    /**
     * Allways redirect after creation to the list of translations
     *
     * @param object $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        return new RedirectResponse($this->generateUrl('admin_lexik_translation_transunit_list'));
    }

    public function listAction($id = null) {
        return $this->redirect($this->admin->generateUrl('create'));
    }

    public function editAction($id = null) {
        return $this->redirect($this->admin->generateUrl('create'));
    }

    public function deleteAction($id) {
        return $this->redirect($this->admin->generateUrl('create'));
    }
}
