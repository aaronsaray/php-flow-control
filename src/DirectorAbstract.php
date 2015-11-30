<?php
/**
 * Controls the flow of points
 *
 * @author Aaron Saray
 */

namespace AaronSaray\PHPFlowControl;

/**
 * Class DirectorAbstract
 * @package AaronSaray\PHPFlowControl
 */
abstract class DirectorAbstract
{
    /**
     * @var PointAbstract|null
     */
    protected $point;

    /**
     * DirectorAbstract constructor.
     * @param PointAbstract|null $point
     */
    public function __construct(PointAbstract $point = null)
    {
        $this->point = $point;
    }

    /**
     * Get the last allowed point (usually good for restarting)
     *
     * @param ProcessableInterface $item
     * @return PointAbstract|void
     */
    public function getLastAllowed(ProcessableInterface $item)
    {
        $point = $this->getFirstAllowed();
        try {
            while ($point = $point->next($item));
        }
        catch (IllegalPointException $e) {}

        return $point;
    }

    /**
     * Get the next point from the current point
     *
     * @param ProcessableInterface $item
     * @return PointAbstract|null|void
     * @throws IllegalPointException
     */
    public function getNextPoint(ProcessableInterface $item)
    {
        $this->point = $this->point->next($item);
        return $this->point;
    }

    /**
     * @return PointAbstract
     */
    abstract public function getFirstAllowed();

}