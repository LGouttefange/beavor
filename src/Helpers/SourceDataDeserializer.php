<?php
/**
 * Created by PhpStorm.
 * User: Loïc Gouttefangeas <loic.gouttefangeas.pro@gmail.com>
 * Date: 02/04/2019
 * Time: 23:19
 */

namespace Beavor\Helpers;


use Psr\Http\Message\ResponseInterface;

class SourceDataDeserializer
{

    public function deserialize($source)
    {
        if ($source instanceof ResponseInterface) {
            return json_decode((string) $source->getBody());
        }

        if (is_array($source)) {
            return json_decode(json_encode($source));
        }
        if(is_object($source)){
            return $source;
        }
        throw new \InvalidArgumentException("Could not deserialize data of type :  " . gettype($source));
    }
}