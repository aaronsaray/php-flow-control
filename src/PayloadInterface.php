<?php
/**
 * The interface for a payload
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHPFlowControl;

/**
 * Interface PayloadInterface
 * @package AaronSaray\PHPFlowControl
 */
interface PayloadInterface
{
    /**
     * Gets the payload of an item to indicate the next item
     *
     * @return mixed
     */
    public function getPayload();
}