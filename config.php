<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\components\Application;

return [
    'id' => 'rest',
    'class' => 'humhub\modules\rest\Module',
    'namespace' => 'humhub\modules\rest',
    'events' => [
        [\humhub\components\console\Application::class, \humhub\components\console\Application::EVENT_ON_INIT, ['\humhub\modules\rest\Events', 'onConsoleInit']],
        [Application::class, Application::EVENT_BEFORE_REQUEST, ['\humhub\modules\rest\Events', 'onBeforeRequest']],
        ['humhub\modules\legal\services\ExportService', 'collectUserData', ['humhub\modules\rest\Events', 'onLegalModuleUserDataExport']],
    ],
];
