<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\Denormalization\Relate;

use Administration;
use BeanFactory;

final class HookConfig
{
    private const CATEGORY = 'denormalization';
    private const NAME = 'fields';

    /** @var Administration */
    private $administration;

    public function __construct()
    {
        $this->administration = BeanFactory::newBean('Administration');
    }

    public function setFieldConfiguration(string $moduleName, string $fieldName, array $value): void
    {
        $this->administration->settings[self::CATEGORY . '_' . self::NAME][$moduleName][$fieldName] = $value;
        $this->save();
    }

    public function getModuleConfiguration(string $moduleName): array
    {
        $settings = $this->getSettings();

        return $settings[$moduleName] ?? [];
    }

    private function save(): void
    {
        $this->administration->saveSetting(
            self::CATEGORY,
            self::NAME,
            $this->administration->settings[self::CATEGORY . '_' . self::NAME],
            'base'
        );
    }

    private function getSettings(): array
    {
        if (!isset($this->administration->settings[self::CATEGORY])) {
            $this->administration->retrieveSettings(self::CATEGORY);
        }

        return $this->administration->settings[self::CATEGORY . '_' . self::NAME] ?? [];
    }
}
