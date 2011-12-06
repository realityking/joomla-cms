<?php
/**
 * @package		Joomla.Libraries
 * @subpackage	Captcha
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Color object for CAPTCHA
 * Based on Securimage (http://www.phpcaptcha.org/).
 *
 * @package     Joomla.Libraries
 * @subpackage  Captcha
 * @since       2.5
 * @author      Drew Phillips
 * @copyright   Copyright (c) 2011, Drew Phillips
 */
class JCaptchaColor
{
    public $r;
    public $g;
    public $b;

    /**
     * Create a new JCaptchaColor object.<br />
     * Constructor expects 1 or 3 arguments.<br />
     * When passing a single argument, specify the color using HTML hex format,<br />
     * when passing 3 arguments, specify each RGB component (from 0-255) individually.<br />
     * $color = new JCaptchaColor('#0080FF') or <br />
     * $color = new JCaptchaColor(0, 128, 255)
     *
     * @param string $color
     * @throws Exception
     */
    public function __construct($color = '#ffffff')
    {
        $args = func_get_args();

        if (count($args) == 0)
        {
            $this->r = 255;
            $this->g = 255;
            $this->b = 255;
        }
        else if (count($args) == 1)
        {
            // set based on html code
            if (substr($color, 0, 1) == '#')
            {
                $color = substr($color, 1);
            }

            if (strlen($color) != 3 && strlen($color) != 6)
            {
                throw new InvalidArgumentException('Invalid HTML color code passed to JCaptcha_Color');
            }

            $this->constructHTML($color);
        }
        else if (count($args) == 3)
        {
            $this->constructRGB($args[0], $args[1], $args[2]);
        }
        else
        {
            throw new InvalidArgumentException('JCaptcha_Color constructor expects 0, 1 or 3 arguments; ' . count($args) . ' given');
        }
    }

    /**
     * Construct from an rgb triplet
     * @param int $red   The red component, 0-255
     * @param int $green The green component, 0-255
     * @param int $blue  The blue component, 0-255
     */
    protected function constructRGB($red, $green, $blue)
    {
        if ($red < 0)     $red   = 0;
        if ($red > 255)   $red   = 255;
        if ($green < 0)   $green = 0;
        if ($green > 255) $green = 255;
        if ($blue < 0)    $blue  = 0;
        if ($blue > 255)  $blue  = 255;

        $this->r = $red;
        $this->g = $green;
        $this->b = $blue;
    }

    /**
     * Construct from an html hex color code
     * @param string $color
     */
    protected function constructHTML($color)
    {
        if (strlen($color) == 3)
        {
            $red   = str_repeat(substr($color, 0, 1), 2);
            $green = str_repeat(substr($color, 1, 1), 2);
            $blue  = str_repeat(substr($color, 2, 1), 2);
        }
        else
        {
            $red   = substr($color, 0, 2);
            $green = substr($color, 2, 2);
            $blue  = substr($color, 4, 2);
        }

        $this->r = hexdec($red);
        $this->g = hexdec($green);
        $this->b = hexdec($blue);
    }
}