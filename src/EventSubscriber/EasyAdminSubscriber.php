<?php

# src/EventSubscriber/EasyAdminSubscriber.php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use App\Entity\Route;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $slugger;

    public function __construct($slugger = null)
    {
        $this->slugger = $slugger;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.pre_persist' => array('setRouteSlug'),
        );
    }

    public function setRouteSlug(GenericEvent $event)
    {
        $entity = $event->getSubject();

        if (!($entity instanceof Route)) {
            return;
        }

        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug);

        $event['entity'] = $entity;

        dd($event);
    }
}

?>