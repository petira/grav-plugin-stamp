<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Interfaces\PageInterface;
use RocketTheme\Toolbox\Event\Event;
use Grav\Common\Grav;
use Grav\Common\User\User;

class StampPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onPagesInitialized' => ['onPagesInitialized', 0],
            'onAdminSave' => ['onAdminSave', 0]
        ];
    }
    public function onPagesInitialized(Event $event)
    {
        if ($this->config->get('plugins.stamp.twig')) {
            $directory = Grav::instance()['locator']->findResource('account://');
            $files = array_diff(scandir($directory), ['.', '..']);
            $accounts = [];
            foreach ($files as $file) {
                if (strpos($file, YAML_EXT) !== false) {
                    $accounts[] = User::load(trim(substr($file, 0, -5)));
                }
            }
            $users = [];
            foreach ($accounts as $account) {
                $users[$account['username']]['username'] = $account['username'];
                $users[$account['username']]['fullname'] = $account['fullname'];
                $users[$account['username']]['title'] = $account['title'];
                $users[$account['username']]['email'] = $account['email'];
            }
            $this->grav['twig']->twig_vars['users'] = $users;
        }
    }
        public function onAdminSave(Event $event)
    {
        $object = $event['object'];
        if ($object instanceof PageInterface) {
            $date_modified = date($this->grav['config']->get('system.pages.dateformat.default', 'd-m-Y H:i:s'));
            $object->header()->date_modified = $date_modified;
            if ($this->config->get('plugins.stamp.name') == 'full') {
                $editor = $this->grav['user']['fullname'];
            } else {
                $editor = $this->grav['user']['username'];
            }
            $object->header()->editor = $editor;
            $taxonomy_author = array();
            if (isset($object->header()->taxonomy['author'])) { $taxonomy_author = $object->header()->taxonomy['author']; }
            $taxonomy_author_action = $this->config->get('plugins.stamp.taxonomy_author');
            if (isset($object->header()->taxonomy_author)) { $taxonomy_author_action = $object->header()->taxonomy_author; }
            $taxonomy_editor[] = $editor;
            switch ($taxonomy_author_action) {
                case 'none':
                    $taxonomy_author = array_unique($taxonomy_author);
                    break;
                case 'editor_only':
                    $taxonomy_author = $taxonomy_editor;
                    break;
                case 'current_alphabetically':
                    $taxonomy_author = array_unique($taxonomy_author);
                    sort($taxonomy_author);
                    break;
                case 'all_alphabetically':
                    $taxonomy_author = array_merge($taxonomy_author, $taxonomy_editor);
                    $taxonomy_author = array_unique($taxonomy_author);
                    sort($taxonomy_author);
                    break;
                case 'editor_first_others_unchanged':
                    $taxonomy_author = array_diff($taxonomy_author, $taxonomy_editor);
                    $taxonomy_author = array_unique($taxonomy_author);
                    $taxonomy_author = array_merge($taxonomy_editor, $taxonomy_author);
                    break;
                case 'editor_last_others_unchanged':
                    $taxonomy_author = array_diff($taxonomy_author, $taxonomy_editor);
                    $taxonomy_author = array_unique($taxonomy_author);
                    $taxonomy_author = array_merge($taxonomy_author, $taxonomy_editor);
                    break;
                case 'editor_first_others_alphabetically':
                    $taxonomy_author = array_diff($taxonomy_author, $taxonomy_editor);
                    $taxonomy_author = array_unique($taxonomy_author);
                    sort($taxonomy_author);
                    $taxonomy_author = array_merge($taxonomy_editor, $taxonomy_author);
                    break;
                case 'editor_last_others_alphabetically':
                    $taxonomy_author = array_diff($taxonomy_author, $taxonomy_editor);
                    $taxonomy_author = array_unique($taxonomy_author);
                    sort($taxonomy_author);
                    $taxonomy_author = array_merge($taxonomy_author, $taxonomy_editor);
                    break;
            }
            if ($taxonomy_author) { $object->modifyHeader('taxonomy.author', $taxonomy_author); }
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
