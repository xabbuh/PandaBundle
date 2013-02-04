<?php

/*
* This file is part of the XabbuhPandaBundle package.
*
* (c) Christian Flothmann <christian.flothmann@xabbuh.de>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Xabbuh\PandaBundle\Model;

/**
 * Representation of notification as they are returned by the Panda encoding
 * service api.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Notifications
{
    private $url;
    
    private $events;
    
    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }
    
    
    public function addNotificationEvent(NotificationEvent $event)
    {
        $this->events[$event->getEvent()] = $event;
    }

    public function removeNotificationEvent(NotificationEvent $event)
    {
        if (isset($this->events[$event->getEvent()])) {
            unset($this->events[$event->getEvent()]);
        }
    }
    
    public function getNotificationEvent($eventName)
    {
        // normalise the event name
        $eventName = strtr($eventName, "_", "-");
        if (isset($this->events[$eventName])) {
            return $this->events[$eventName];
        } else {
            throw new \InvalidArgumentException("Event $eventName is not registered");
        }
    }
}
