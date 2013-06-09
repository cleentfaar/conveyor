<?php
/*
 * This file is part of the Conveyor package.
 *
 * (c) Jeroen Fiege <jeroen@webcreate.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webcreate\Conveyor\Util;

class FilePath
{
    public static function join()
    {
        $parts = func_get_args();

        $parts = array_filter($parts, 'trim');

        return implode(DIRECTORY_SEPARATOR, $parts);
    }
}
