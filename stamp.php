<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Interfaces\PageInterface;
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
        if ($object instanceof PageInterface) {
            $date_modified = date($this->grav['config']->get('system.pages.dateformat.default', 'H:i:s d-m-Y'));
            $object->header()->date_modified = $date_modified;
            $editor = $this->grav['user']['fullname'];
            $object->header()->editor = $editor;
            if (isset($object->header()->revision) && is_int($object->header()->revision) && $object->header()->revision > -1) {
                $object->header()->revision = $object->header()->revision + 1;
            } else {
                if (!($object->exists()) || ((isset($object->header()->revision) && ($object->header()->revision == 'new') && ($this->config->get('plugins.stamp.override'))))) {
                    if ((!isset($object->header()->date) && ($this->config->get('plugins.stamp.date') == 'add'))) { $object->header()->date = $date_modified; }
                    if ((!isset($object->header()->author) && ($this->config->get('plugins.stamp.author') == 'add'))) { $object->header()->author = $editor; }
                    if (($this->config->get('plugins.stamp.date') == 'both') || ($this->config->get('plugins.stamp.date') == 'modify')) { $object->header()->date = $date_modified; }
                    if (($this->config->get('plugins.stamp.author') == 'both') || ($this->config->get('plugins.stamp.author') == 'modify')) { $object->header()->author = $editor; }
                }
                $object->header()->revision = 0;
            }
            $event['object'] = $object;
        }
    }
}