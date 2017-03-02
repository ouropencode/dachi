<?php

namespace Dachi\Core;

/**
 * The Collections class provides a selection of functions designed for usage
 * with Doctrine Collections.
 *
 * @version   4.0.0
 *
 * @since     4.0.0
 *
 * @license   LICENCE.md
 * @author    $ourOpenCode
 */
abstract class Collections
{
    /**
     * Retrieve all the Collections's data in an array.
     *
     * Models should implement this method themselves and provide the required
     * data. Models can omit this, it just means you can't use it.
     *
     * @param ArrayCollection $collection The collection to operate upon
     * @param bool            $safe       Should we return only data we consider "publicly exposable"?
     * @param bool            $eager      Should we eager load child data?
     *
     * @return array
     */
    public static function asArray($collection, $safe = false, $eager = false)
    {
        $elements = [];

        foreach ($collection as $key => $element) {
            $elements[] = $element->asArray($safe, $eager);
        }

        return $elements;
    }

    /**
     * Retrieve all the Collections's "publicly exposable" data in an array.
     *
     * @param ArrayCollection $collection The collection to operate upon
     * @param bool            $eager      Should we eager load child data?
     *
     * @return array
     */
    public static function asSafeArray($collection, $eager = false)
    {
        return self::asArray($collection, true, $eager);
    }
}
