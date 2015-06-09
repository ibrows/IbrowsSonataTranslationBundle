<?php

namespace Ibrows\SonataTranslationBundle\Controller;

use Ibrows\SonataTranslationBundle\Event\RemoveLocaleCacheEvent;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslationCRUDController extends CRUDController
{
    /**
     * Edit action
     *
     *
     * @param int|string|null $id
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException If the object does not exist
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     * @throws AccessDeniedException If access is not granted
     */
    public function editAction($id = null, Request $request = null)
    {
        if (!$request) {
            $request = $this->getRequest();
        }
        if (!$request->isMethod('POST')) {
            return $this->redirect($this->admin->generateUrl('list'));
        }

        /* @var $transUnit \Lexik\Bundle\TranslationBundle\Model\TransUnit */
        $transUnit = $this->get('lexik_translation.translation_storage')->getTransUnitById($id);
        if (!$transUnit) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('EDIT', $transUnit)) {
            return $this->renderJson(
                array(
                    'message' => 'access denied'
                ),
                403
            );
        }

        $this->admin->setSubject($transUnit);

        /* @var $transUnitManager \Lexik\Bundle\TranslationBundle\Manager\TransUnitManager */
        $transUnitManager = $this->get('lexik_translation.trans_unit.manager');

        $parameters = $this->getRequest()->request;

        $locale = $parameters->get('locale');
        $content = $parameters->get('value');

        if (!$locale) {
            return $this->renderJson(
                array(
                    'message' => 'locale missing'
                ),
                422
            );
        }

        /* @var $translation \Lexik\Bundle\TranslationBundle\Model\Translation */
        if ($parameters->get('pk')) {
            $translation = $transUnitManager->updateTranslation($transUnit, $locale, $content, true);
        } else {
            $translation = $transUnitManager->addTranslation($transUnit, $locale, $content, null, true);
        }

        if ($request->query->get('clear_cache')) {
            $this->get('translator')->removeLocalesCacheFiles(array($locale));
        }

        return $this->renderJson(
            array(
                'key'    => $transUnit->getKey(),
                'domain' => $transUnit->getDomain(),
                'pk'     => $translation->getId(),
                'locale' => $translation->getLocale(),
                'value'  => $translation->getContent()
            )
        );
    }

    public function createTransUnitAction()
    {
        $request = $this->getRequest();
        $parameters = $this->getRequest()->request;
        if (!$request->isMethod('POST')) {
            return $this->renderJson(
                array(
                    'message' => 'method not allowed'
                ),
                403
            );
        }
        $admin = $this->admin;
        if (false === $admin->isGranted('EDIT')) {
            return $this->renderJson(
                array(
                    'message' => 'access denied'
                ),
                403
            );
        }
        $keyName = $parameters->get('key');
        $domainName = $parameters->get('domain');
        if (!$keyName || !$domainName) {
            return $this->renderJson(
                array(
                    'message' => 'missing key or domain'
                ),
                422
            );
        }

        /* @var $transUnitManager \Lexik\Bundle\TranslationBundle\Manager\TransUnitManager */
        $transUnitManager = $this->get('lexik_translation.trans_unit.manager');
        $transUnit = $transUnitManager->create($keyName, $domainName, true);

        return $this->editAction($transUnit->getId());
    }

    public function clearCacheAction()
    {
        $this->get('event_dispatcher')->dispatch(RemoveLocaleCacheEvent::PRE_REMOVE_LOCAL_CACHE, new RemoveLocaleCacheEvent($this->getManagedLocales()));
        $this->get('translator')->removeLocalesCacheFiles($this->getManagedLocales());
        $this->get('event_dispatcher')->dispatch(RemoveLocaleCacheEvent::POST_REMOVE_LOCAL_CACHE, new RemoveLocaleCacheEvent($this->getManagedLocales()));

        /** @var $session Session */
        $session = $this->get('session');
        $session->getFlashBag()->set('sonata_flash_success', 'translations.cache_removed');

        return $this->redirect($this->admin->generateUrl('list'));
    }

    protected function getManagedLocales()
    {
        return $this->container->getParameter('lexik_translation.managed_locales');
    }
}
