<?php
/**
 * @author    Mike Lohmann <mike@protofy.com>
 * @copyright 2015 Protofy GmbH & Co. KG
 */
namespace Ibrows\SonataTranslationBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RemoveLocaleCacheEvent extends Event
{
    /**
     * @const String
     */
    const PRE_REMOVE_LOCAL_CACHE = 'pre_remove_local_cache.event';

    /**
     * @const String
     */
    const POST_REMOVE_LOCAL_CACHE = 'post_remove_local_cache.event';

    /**
     * @var array
     */
    private $managedLocales = array();

    public function __construct(array $managedLocales)
    {
        $this->managedLocales = $managedLocales;
    }

    /**
     * @return array
     */
    public function getManagedLocales()
    {
        return $this->managedLocales;
    }
}