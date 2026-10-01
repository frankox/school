<?php

namespace App\Utils;

use ReflectionClass;

class JsonToClassConverter
{
    public static function convert(string $json, string $className): object
    {
        $reflection = new ReflectionClass($className);
        $instance   = $reflection->newInstanceWithoutConstructor();
        $data       = json_decode($json, true);

        foreach ($reflection->getProperties() as $property) {
            if (isset($data[$property->getName()])) {
                $property->setAccessible(true);
                $property->setValue($instance, $data[$property->getName()]);
            }
        }

        return $instance;
    }
}