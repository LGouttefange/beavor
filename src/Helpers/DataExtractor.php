<?php
/**
 * Created by PhpStorm.
 * User: Loïc Gouttefangeas <loic.gouttefangeas.pro@gmail.com>
 * Date: 25/11/2018
 * Time: 19:39
 */

namespace Beavor\Helpers;


class DataExtractor
{

    /**
     * DataExtractor constructor.
     */
    public function __construct()
    {
    }

    public function getValue($data)
    {

        $data = (new SanitizedSourceString($data))->getValue();
        $data = json_decode($data, true) ?: json_decode(json_encode(simplexml_load_string($data)), true);
        return $data;
    }


}