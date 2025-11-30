<?php

namespace THNBO\Grafika\Imagick\Filter;

use THNBO\Grafika\FilterInterface;
use THNBO\Grafika\Imagick\Image;

/**
 * Invert the image colors.
 */
class Invert implements FilterInterface{

    /**
     * @param Image $image
     *
     * @return Image
     */
    public function apply( $image ) {

        $image->getCore()->negateImage(false);
        return $image;
    }

}