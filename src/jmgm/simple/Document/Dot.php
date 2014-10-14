<?php

namespace jmgm\simple\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Dot document
 * @ODM\Document(collection="Dot")
 **/
class Dot
{
    /**
     * @ODM\Id
     **/
    private $id;

    /**
     * @ODM\Int
     **/
    private $x;

    /**
     * @ODM\Int
     **/
    private $y;

    /**
     * Construct
     *
     * @param int $x X coord
     * @param int $y Y coord
     **/
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Object to array
     *
     * @return array $dot Dot object in array format
     **/
    public function toArray()
    {
        $arrayDot = array(
            'id' => $this->id,
            'x' => $this->x,
            'y' => $this->y
        );

        return $arrayDot;
    }
}

