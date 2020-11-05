<?php
namespace Kernel\Utils;

use Cocur\Slugify\Slugify as RealSlugify;

class Slugify
{
    /**
     * @var RealSlugify
     */
    protected $slugify;


    public function __construct()
    {
        $this->slugify = new RealSlugify();
    }


    /**
     * Slugify a string
     *
     * @param string $text
     * @param mixed $options
     *
     * @return string
     */
    public function slugify($text, $options = null): string
    {
        $text = str_replace('&', 'and', $text);

        return $this->slugify->slugify($text, $options);
    }
}
