<?php
/**
 * @author    Phoenix <phoenix@twistersfury.com>
 * @license   proprietary
 * @copyright 2016 Twister's Fury
 */

namespace TwistersFury\Defines;

use TwistersFury\Phalcon\Shared\Helpers\Defines;

/**
 * Shortcut Function - Passes To Defines Helper Class
 *
 * @param string $constName
 * @param mixed $constValue
 */
function define(string $constName, $constValue): void
{
    static $definesHelper;
    if (!$definesHelper) {
        require_once __DIR__ . '/../src/Helpers/Defines.php'; //Autoloader Not Initialized Yet

        $definesHelper = new Defines();
    }

    $definesHelper->define($constName, $constValue);
}
