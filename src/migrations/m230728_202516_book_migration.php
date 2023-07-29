<?php

namespace santiagomena\craftbookshelf\migrations;

use Craft;
use craft\db\Migration;
use craft\errors\EntryTypeNotFoundException;
use craft\errors\SectionNotFoundException;
use craft\fieldlayoutelements\CustomField;
use craft\fields\PlainText;
use craft\helpers\ArrayHelper;
use craft\models\FieldGroup;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\EntryType;
use craft\models\Section_SiteSettings;
use yii\db\Exception;

/**
 * m230728_202516_book_migration migration.
 */
class m230728_202516_book_migration extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {

        // Don’t make the same config changes twice
        if (Craft::$app->projectConfig->get('plugins.my-plugin-handle', true) === null) {
            try {
//                1. Make FieldGroup
                $fieldGroup = new FieldGroup([
                    'name' => 'bookshelfBooksGroup',
                ]);
                $successFieldGroup = Craft::$app->fields->saveGroup($fieldGroup);

//                2. Make Fields

                $groups = Craft::$app->fields->getAllGroups();

                foreach ($groups as $group) {
                    if($group->name === 'bookshelfBooksGroup') {
                        $fieldGroupBooks = $group;
                    }
                }

                if($fieldGroupBooks) {
                    // Make Fields
                    $fieldAuthor = new PlainText([
                        'handle' => 'bookshelfAuthorField',
                        'name' => 'bookshelfAuthorField',
                        'groupId' => $fieldGroupBooks->id
                    ]);

                    $successFieldAuthor = Craft::$app->fields->saveField($fieldAuthor);
                }

//              3. Create Section
                $section = new Section([
                    'name' => 'bookshelfBookSection',
                    'handle' => 'bookshelfBookSection',
                    'type' => Section::TYPE_CHANNEL,
                    'siteSettings' => [
                        new Section_SiteSettings([
                            'siteId' => Craft::$app->sites->getPrimarySite()->id,
                            'enabledByDefault' => true,
                            'hasUrls' => true,
                            'uriFormat' => 'book/{slug}',
                            'template' => '@santiagomena/craftbookshelf/_book',
                        ]),
                    ]
                ]);
                $success = Craft::$app->sections->saveSection($section);

//              4. Save entry type
                // Get the entry type to add the new field to
                $entryType = Craft::$app->getSections()->getSectionByHandle('bookshelfBookSection')->entryTypes[0];

                $fieldAuthor = Craft::$app->getFields()->getFieldByHandle('bookshelfAuthorField');

                // Get current fieldLayout
                $fieldLayout = $entryType->getFieldLayout();
                $fieldLayout->setTabs([
                    ...$fieldLayout->getTabs(),
                    FieldLayoutTab::createFromConfig([
                        'name' => 'bookshelfTab',
                        'layoutId' => $fieldLayout->id,
                        'elements' => [
                            new CustomField($fieldAuthor, [
                                'required' => true,
                            ])
                        ]
                    ])
                ]);

                $entryTypeBooks = Craft::$app->sections->saveEntryType(new EntryType([
                    'sectionId' => Craft::$app->sections->getSectionByHandle('bookshelfBookSection')->id,
                    'name' => 'bookshelfBooksEntryType',
                    'handle' => 'bookshelfBooksEntryType',
                    'hasTitleField' => true,
                    'fieldLayout' => $fieldLayout
                ]));
            } catch (\Throwable $e) {
                throw new Exception($e);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230728_202516_book_migration cannot be reverted.\n";

        // Find and delete the section and its entry type

        if (Craft::$app->projectConfig->get('plugins.my-plugin-handle', true) !== null) {
            try {
//              1. Delete FieldGroup
                $groups = Craft::$app->fields->getAllGroups();

                foreach ($groups as $group) {
                    if($group->name === 'bookshelfBooksGroup') {
                        $fieldGroupBooks = $group;
                    }
                }

                if($fieldGroupBooks) {
                    // Remove fields
                    $fields = Craft::$app->fields->getFieldsByGroupId($fieldGroupBooks->id);

                    foreach ($fields as $field) {
//                      2. Delete Fields
                        if($field->handle === 'bookshelfAuthorField'){
                            Craft::$app->fields->deleteField($field);
                        }
                    }

                    // Remove Books Group
                    Craft::$app->fields->deleteGroup($fieldGroupBooks);
                }


//              3. Delete Section
                $section = Craft::$app->sections->getSectionByHandle('bookshelfBookSection');
                if ($section) {
                    $entryTypes = Craft::$app->sections->getEntryTypesBySectionId($section->id);
//                  4. Delete entry type
                    foreach ($entryTypes as $entryType){
                        if($entryType->handle === 'bookshelfBooksEntryType') {
                            Craft::$app->sections->deleteEntryType($entryType);
                        }
                    }

                    Craft::$app->sections->deleteSection($section);
                }
            } catch (\Throwable $e) {
                throw new Exception($e);
            }
        }

        return true;
    }
}
