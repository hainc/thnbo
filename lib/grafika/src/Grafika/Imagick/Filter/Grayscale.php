<?php

namespace THNBO\Grafika\Imagick\Filter;

use THNBO\Grafika\FilterInterface;
use THNBO\Grafika\Imagick\Image;

/**
 * Turn image into grayscale.
 */
class Grayscale implements FilterInterface{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {
        $image->getCore()->modulateImage(100, 0, 100);
        return $image;
    }

}