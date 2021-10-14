<?php

namespace App\Traits;

/**
 * Trait ApiVersionTrait
 * @package App\Traits
 */

trait ApiVersion
{
    public static function list_version(){
        $version = [
            '1.0.0' => [
                '1st version',
                'Role permission',
                'User create',
                'Some of controller CRUD'
            ],
        ];
        return $version;
    }

    public function getVersion($number = '')
    {
        $version = self::list_version();

        if (!empty($number)) {
            if (array_key_exists($number, $version)) {
                return [ $number => $version[$number]];
            }
            else {
                return '';
            }
        }

        return $this->latestVersion($version);
    }

    protected function latestVersion($array)
    {
        $key = NULL;
        if (is_array($array)) {
            end($array); //get last array
            $key = key($array); //get array key
            $value = $array[$key];
        }

        return [$key => $value];
    }
}
