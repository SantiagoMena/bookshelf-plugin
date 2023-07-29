<?php

namespace santiagomena\craftbookshelf;

use Craft;
use craft\base\Element;
use craft\base\Event;
use craft\base\Plugin as BasePlugin;
use craft\elements\Entry;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\SetElementRouteEvent;
use craft\services\Fs;
use craft\web\UrlManager;
use craft\web\twig\variables\Cp;
use santiagomena\craftbookshelf\fs\Files;
use yii\base\Event as EventAlias;

/**
 * bookshelf plugin
 *
 * @method static Plugin getInstance()
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => [
                // Define component configs here...
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // Routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['bookshelf'] = '_bookshelf/book/index';
                $event->rules['wishlist'] = '_bookshelf/wish-list/index';
                $event->rules['wishlist/switch'] = '_bookshelf/wish-list/switch';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                $event->rules['bookshelf'] = '_bookshelf/public-bookshelf/index';
            }
        );
        // Menu item

        // Register the sidebar menu item
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_CP_NAV_ITEMS,
            function(RegisterCpNavItemsEvent $event) {
                $event->navItems[] = [
                    'url' => '/bookshelf',
                    'label' => 'Bookshelf',
                    'icon' => '@santiagomena/craftbookshelf/icon.svg',
                ];
            }
        );

        EventAlias::on(Fs::class, Fs::EVENT_REGISTER_FILESYSTEM_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Files::class;
        });
    }
}
