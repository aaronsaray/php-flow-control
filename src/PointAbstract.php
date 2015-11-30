<?php
/**
 * The abstract point class
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHPFlowControl;

/**
 * Class PointAbstract
 * @package AaronSaray\PHPFlowControl
 */
abstract class PointAbstract
{
    /**
     * Get next point
     *
     * @param ProcessableInterface $item
     * @throws IllegalPointException
     */
    public function next(ProcessableInterface $item)
    {
        throw new IllegalPointException();
    }

    /**
     * @return PayloadInterface
     */
    abstract public function getPayload();
}