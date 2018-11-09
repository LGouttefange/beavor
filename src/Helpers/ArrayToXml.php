<?php


namespace Beavor\Helpers;


class ArrayToXml
{

    static function normalizeSimpleXML($obj, &$result) {
        $data = $obj;
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $res = null;
                self::normalizeSimpleXML($value, $res);
                if (($key === '@attributes') && ($key)) {
                    $result = $res;
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $data;
        }
    }

    static public function convert($xml)
    {
        $result = [];
        self::normalizeSimpleXML(simplexml_load_string($xml), $result);
        return $result;
    }
}