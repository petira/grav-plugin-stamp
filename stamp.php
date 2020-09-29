<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use RocketTheme\Toolbox\Event\Event;

class StampPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onAdminSave' => ['onAdminSave', 0]
        ];
    }
    public function onAdminSave(Event $event)
    {
        $object = $event['object'];
        if ($object instanceof Page) {
            $date_modified = date($this->grav['config']->get('system.pages.dateformat.default', 'H:i:s d-m-Y'));
            $object->header()->date_modified = $date_modified;
            $editor = $this->grav['user']['fullname'];
            $object->header()->editor = $editor;
            if (isset($object->header()->revision) && is_numeric($object->header()->revision) && $object->header()->revision > -1) {         
                $object->header()->revision = $object->header()->revision + 1;
            } else {
                if ($this->config->get('plugins.stamp.override')) {
                    $object->header()->date = $date_modified;
                    $object->header()->author = $editor;
                }
                $object->header()->revision = 0;
            }
            $event['object'] = $object;
        }
    }
}                            
