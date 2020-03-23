<?php

namespace DK\Helper\IO;

class File
{
    public static function getHRSize($intByte)
    {
        $metrics[0] = 'байт';
        $metrics[1] = 'KB';
        $metrics[2] = 'MB';
        $metrics[3] = 'GB';
        $metrics[4] = 'TB';
        $metric = 0;
        while (floor($intByte / 1024) > 0) {
            ++$metric;
            $intByte /= 1024;
        }
        return round($intByte, 1) . " " . (isset($metrics[$metric]) ? $metrics[$metric] : '??');
    }
}