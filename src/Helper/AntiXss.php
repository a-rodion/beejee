<?php
declare(strict_types=1);

namespace Rodion\TaskList\Helper;

/**
 *
 */
class AntiXss
{
    public static function encode(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }
}
