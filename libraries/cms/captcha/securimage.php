<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Joomla! Captcha helper object.
 * Based on Securimage (http://www.phpcaptcha.org/).
 *
 * @abstract
 * @package     Joomla.Libraries
 * @subpackage  Captcha
 * @since       2.5
 */
class JCaptchaSecurimage extends JObject
{
	/**
	 * Create a normal alphanumeric captcha
	 * @var int
	 */
	const SI_CAPTCHA_STRING = 0;

	/**
	 * Create a captcha consisting of a simple math problem
	 * @var int
	 */
	const SI_CAPTCHA_MATHEMATIC = 1;

	/**
	 * The width of the captcha image
	 * @var int
	 */
	public $image_width = 215;

	/**
	 * The height of the captcha image
	 * @var int
	 */
	public $image_height = 80;

	/**
	 * The type of the image, default = png
	 * @var int
	 */
	public $image_type = 'png';

	/**
	 * The background color of the captcha
	 * @var JCaptchaColor
	 */
	public $image_bg_color = '#ffffff';
	/**
	 * The color of the captcha text
	 * @var JCaptchaColor
	 */
	public $text_color = '#707070';
	/**
	 * The color of the lines over the captcha
	 * @var JCaptchaColor
	 */
	public $line_color = '#707070';
	/**
	 * The color of the noise that is drawn
	 * @var JCaptchaColor
	 */
	public $noise_color = '#707070';

	/**
	 * How transparent to make the text 0 = completely opaque, 100 = invisible
	 * @var int
	 */
	public $text_transparency_percentage = 50;

	/**
	 * Whether or not to draw the text transparently, true = use transparency, false = no transparency
	 * @var bool
	 */
	public $use_transparent_text = false;

	/**
	 * The length of the captcha code
	 * @var int
	 */
	public $code_length = 6;

	/**
	 * Whether the captcha should be case sensitive (not recommended, use only for maximum protection)
	 * @var bool
	 */
	public $case_sensitive = false;

	/**
	 * The character set to use for generating the captcha code
	 * @var string
	 */
	public $charset = 'ABCDEFGHKLMNPRSTUVWYZabcdefghklmnprstuvwyz23456789';

	/**
	 * true to use the wordlist file, false to generate random captcha codes
	 * @var bool
	 */
	public $use_wordlist = false;

	/**
	 * The level of distortion, 0.75 = normal, 1.0 = very high distortion
	 * @var double
	 */
	public $perturbation = 0.75;

	/**
	 * How many lines to draw over the captcha code to increase security
	 * @var int
	 */
	public $num_lines = 8;

	/**
	 * The level of noise (random dots) to place on the image, 0-10
	 * @var int
	 */
	public $noise_level = 0;

	/**
	 * The signature text to draw on the bottom corner of the image
	 * @var string
	 */
	public $image_signature = '';

	/**
	 * The color of the signature text
	 * @var JCaptchaColor
	 */
	public $signature_color = '#616161';

	/**
	 * The path to the ttf font file to use for the signature text, defaults to $ttf_file (AHGBold.ttf)
	 * @var string
	 */
	public $signature_font;

	/**
	 * The type of captcha to create, either alphanumeric, or a math problem<br />
	 * Securimage::SI_CAPTCHA_STRING or Securimage::SI_CAPTCHA_MATHEMATIC
	 * @var int
	 */
	public $captcha_type = self::SI_CAPTCHA_STRING;

	/**
	 * The captcha namespace, use this if you have multiple forms on a single page, blank if you do not use multiple forms on one page
	 * @var string
	 */
	public $namespace = '_default';

	/**
	 * The font file to use to draw the captcha code, leave blank for default font AHGBold.ttf
	 * @var string
	 */
	public $ttf_file;

	/**
	 * The path to the wordlist file to use, leave blank for default words.txt
	 * @var string
	 */
	public $wordlist_file;

	/**
	 * The directory to scan for background images, if set a random background will be chosen from this folder
	 * @var string
	 */
	public $background_directory;

	public $output = 'file';

	protected $im;
	protected $tmpimg;
	protected $bgimg;
	protected $iscale = 5;

	protected $code;
	protected $code_display;

	protected $captcha_code;

	protected $gdbgcolor;
	protected $gdtextcolor;
	protected $gdlinecolor;
	protected $gdsignaturecolor;

	/**
	 * Create a new securimage object, pass options to set in the constructor.<br />
	 * This can be used to display a captcha or validate an entry
	 * @param array $options
	 *
	 * @since 2.5
	 */
	public function __construct($options = array())
	{
		if (isset($options['namespace'])) {
			$this->namespace = $options['namespace'];
		}
		$this->file_path = JFactory::getConfig()->get('tmp_path');
	}

	/**
	 * Set the object properties based on a named array/hash.
	 *
	 * @param   mixed  $properties  Either an associative array or another object.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 *
	 * @see     set()
	 *
	 * @since 2.5
	 */
	public function setProperties($properties)
	{
		parent::setProperties($properties);

		$this->image_bg_color  = $this->initColor($this->image_bg_color,  '#ffffff');
		$this->text_color      = $this->initColor($this->text_color,      '#616161');
		$this->line_color      = $this->initColor($this->line_color,      '#616161');
		$this->noise_color     = $this->initColor($this->noise_color,     '#616161');
		$this->signature_color = $this->initColor($this->signature_color, '#616161');

		if (!in_array($this->image_type, array('jpg', 'gif', 'png'))){
			$this->image_type = 'png';
		}

		if (empty($this->ttf_file)) {
			$this->ttf_file = 'AHGBold.ttf';
		}

		$mparams	= JComponentHelper::getParams('com_media');
		$image_path	= $mparams->get('image_path');
		$file_path	= $mparams->get('file_path');

		$ttf_file = JPath::find(array($image_path, $file_path, JPATH_PLATFORM . '/cms/captcha'), $this->ttf_file);

		if ($ttf_file && is_readable($ttf_file)){
			$this->ttf_file = $ttf_file;
		} else {
			$this->ttf_file = JPATH_PLATFORM . '/cms/captcha/AHGBold.ttf';
		}

		if (empty($this->signature_font)) {
			$this->signature_font = $this->ttf_file;
		}

		if (empty($this->wordlist_file)) {
			$this->wordlist_file = JPATH_PLATFORM . '/cms/captcha/words.txt';
		}

		if (empty($this->code_length) || $this->code_length < 1) {
			$this->code_length = 6;
		}

		if (!is_numeric($this->perturbation)) {
			$this->perturbation = 0.75;
		}
	}

	/**
	 * The main image drawing routing, responsible for constructing the entire image and serving it
	 *
	 * @since 2.5
	 */
	public function create()
	{
		// Remove old captchas
		$this->purgeCaptchas();

		if (($this->use_transparent_text == true || $this->bgimg) && function_exists('imagecreatetruecolor')) {
			$imagecreate = 'imagecreatetruecolor';
		} else {
			$imagecreate = 'imagecreate';
		}

		$this->im     = $imagecreate($this->image_width, $this->image_height);
		$this->tmpimg = $imagecreate($this->image_width * $this->iscale, $this->image_height * $this->iscale);

		$this->allocateColors();
		imagepalettecopy($this->tmpimg, $this->im);

		$this->setBackground();

		$this->generateId();

		$this->createCode();
		
		if ($this->noise_level > 0) {
			$this->drawNoise();
		}

		$this->drawWord();

		if ($this->perturbation > 0 && is_readable($this->ttf_file)) {
			$this->distortedCopy();
		}

		if ($this->num_lines > 0) {
			$this->drawLines();
		}

		if (trim($this->image_signature) != '') {
			$this->addSignature();
		}

		// Output to...
		if (strtolower($this->output) == 'browser')
		{
			// the browser
			$this->outputBrowser();
		}
		else
		{
			// a file.
			$this->outputFile();
		}

		return true;
	}

	/**
	 * Allocate the colors to be used for the image
	 *
	 * @since 2.5
	 */
	protected function allocateColors()
	{
		// allocate bg color first for imagecreate
		$this->gdbgcolor = imagecolorallocate($this->im,
											  $this->image_bg_color->r,
											  $this->image_bg_color->g,
											  $this->image_bg_color->b);

		$alpha = intval($this->text_transparency_percentage / 100 * 127);

		if ($this->use_transparent_text == true)
		{
			$this->gdtextcolor = imagecolorallocatealpha($this->im,
														 $this->text_color->r,
														 $this->text_color->g,
														 $this->text_color->b,
														 $alpha);
			$this->gdlinecolor = imagecolorallocatealpha($this->im,
														 $this->line_color->r,
														 $this->line_color->g,
														 $this->line_color->b,
														 $alpha);
			$this->gdnoisecolor = imagecolorallocatealpha($this->im,
														  $this->noise_color->r,
														  $this->noise_color->g,
														  $this->noise_color->b,
														  $alpha);
		}
		else 
		{
			$this->gdtextcolor = imagecolorallocate($this->im,
													$this->text_color->r,
													$this->text_color->g,
													$this->text_color->b);
			$this->gdlinecolor = imagecolorallocate($this->im,
													$this->line_color->r,
													$this->line_color->g,
													$this->line_color->b);
			$this->gdnoisecolor = imagecolorallocate($this->im,
														  $this->noise_color->r,
														  $this->noise_color->g,
														  $this->noise_color->b);
		}

		$this->gdsignaturecolor = imagecolorallocate($this->im,
													 $this->signature_color->r,
													 $this->signature_color->g,
													 $this->signature_color->b);
	}

	/**
	 * The the background color, or background image to be used
	 *
	 * @since 2.5
	 */
	protected function setBackground()
	{
		// set background color of image by drawing a rectangle since imagecreatetruecolor doesn't set a bg color
		imagefilledrectangle($this->im, 0, 0,
							 $this->image_width, $this->image_height,
							 $this->gdbgcolor);
		imagefilledrectangle($this->tmpimg, 0, 0,
							 $this->image_width * $this->iscale, $this->image_height * $this->iscale,
							 $this->gdbgcolor);

		if (!$this->bgimg || !$this->getBackgroundFromDirectory())
		{
			return;
		}

		
		$dat = @getimagesize($this->bgimg);
		if ($dat == false) {
			return;
		}

		switch ($dat[2])
		{
			case 1:
				$newim = @imagecreatefromgif($this->bgimg);
				break;
			case 2:
				$newim = @imagecreatefromjpeg($this->bgimg);
				break;
			case 3:
				$newim = @imagecreatefrompng($this->bgimg);
				break;
			default:
				return;
		}

		if (!$newim) return;

		imagecopyresized($this->im, $newim, 0, 0, 0, 0,
						 $this->image_width, $this->image_height,
						 imagesx($newim), imagesy($newim));
	}

	/**
	 * Scan the directory for a background image to use
	 *
	 * @since 2.5
	 */
	protected function getBackgroundFromDirectory()
	{
		$len = count($this->bgimg);

		do {
			if ($len <= 0) return false;
			--$len;
			$k = rand(0, $len);
			$file = JPATH_ROOT.'/images/'.$this->bgimg[$k];
			array_splice($this->bgimg, $k, 1);
		} while (!is_readable($file));

		$this->bgimg = $file;
		return true;
	}

	/**
	 * Generates the code or math problem and saves the value to the session
	 *
	 * @since 2.5
	 */
	protected function createCode()
	{
		$this->code = false;

		switch ($this->captcha_type)
		{
			case self::SI_CAPTCHA_MATHEMATIC:
				$signs = array('+', '-', 'x');
				$left  = rand(1, 10);
				$right = rand(1, 5);
				$sign  = $signs[rand(0, 2)];

				switch ($sign)
				{
					case 'x':
						$c = $left * $right;
						break;
					case '-':
						$c = $left - $right;
						break;
					default:
						$c = $left + $right;
						break;
				}

				$this->code         = $c;
				$this->code_display = "$left $sign $right";
				break;

			case self::SI_CAPTCHA_STRING:
			default:
				if ($this->use_wordlist && is_readable($this->wordlist_file)) {
					$this->code = $this->readCodeFromFile();
				}

				if ($this->code == false) {
					$this->code = $this->generateCode($this->code_length);
				}

				$this->code_display = $this->code;
				$this->code         = ($this->case_sensitive) ? $this->code : strtolower($this->code);
		}

		$this->saveData();
	}

	/**
	 * Create the id and the filename
	 *
	 * @since 2.5
	 */
	private function generateId()
	{
		$this->id		= mt_rand();
		$this->filename	= 'jcaptcha.'.$this->id.'.'.$this->image_type;
	}

	/**
	 * Draws the captcha code on the image
	 *
	 * @since 2.5
	 */
	protected function drawWord()
	{
		$width2  = $this->image_width * $this->iscale;
		$height2 = $this->image_height * $this->iscale;

		if (!is_readable($this->ttf_file))
		{
			imagestring($this->im, 4, 10, ($this->image_height / 2) - 5, 'Failed to load TTF font file!', $this->gdtextcolor);
		}
		else
		{
			if ($this->perturbation > 0)
			{
				$font_size = $height2 * .4;
				$bb = imageftbbox($font_size, 0, $this->ttf_file, $this->code_display);
				$tx = $bb[4] - $bb[0];
				$ty = $bb[5] - $bb[1];
				$x  = floor($width2 / 2 - $tx / 2 - $bb[0]);
				$y  = round($height2 / 2 - $ty / 2 - $bb[1]);

				imagettftext($this->tmpimg, $font_size, 0, $x, $y, $this->gdtextcolor, $this->ttf_file, $this->code_display);
			}
			else
			{
				$font_size = $this->image_height * .4;
				$bb = imageftbbox($font_size, 0, $this->ttf_file, $this->code_display);
				$tx = $bb[4] - $bb[0];
				$ty = $bb[5] - $bb[1];
				$x  = floor($this->image_width / 2 - $tx / 2 - $bb[0]);
				$y  = round($this->image_height / 2 - $ty / 2 - $bb[1]);

				imagettftext($this->im, $font_size, 0, $x, $y, $this->gdtextcolor, $this->ttf_file, $this->code_display);
			}
		}
	}

	/**
	 * Copies the captcha image to the final image with distortion applied
	 *
	 * @since 2.5
	 */
	protected function distortedCopy()
	{
		$numpoles = 3; // distortion factor
		// make array of poles AKA attractor points
		for ($i = 0; $i < $numpoles; ++ $i)
		{
			$px[$i]  = rand($this->image_width  * 0.2, $this->image_width  * 0.8);
			$py[$i]  = rand($this->image_height * 0.2, $this->image_height * 0.8);
			$rad[$i] = rand($this->image_height * 0.2, $this->image_height * 0.8);
			$tmp     = ((- $this->frand()) * 0.15) - .15;
			$amp[$i] = $this->perturbation * $tmp;
		}

		$bgCol = imagecolorat($this->tmpimg, 0, 0);
		$width2 = $this->iscale * $this->image_width;
		$height2 = $this->iscale * $this->image_height;
		imagepalettecopy($this->im, $this->tmpimg); // copy palette to final image so text colors come across
		// loop over $img pixels, take pixels from $tmpimg with distortion field
		for ($ix = 0; $ix < $this->image_width; ++ $ix)
		{
			for ($iy = 0; $iy < $this->image_height; ++ $iy)
			{
				$x = $ix;
				$y = $iy;
				for ($i = 0; $i < $numpoles; ++ $i)
				{
					$dx = $ix - $px[$i];
					$dy = $iy - $py[$i];
					if ($dx == 0 && $dy == 0) {
						continue;
					}
					$r = sqrt($dx * $dx + $dy * $dy);
					if ($r > $rad[$i]) {
						continue;
					}
					$rscale = $amp[$i] * sin(3.14 * $r / $rad[$i]);
					$x += $dx * $rscale;
					$y += $dy * $rscale;
				}
				$c = $bgCol;
				$x *= $this->iscale;
				$y *= $this->iscale;
				if ($x >= 0 && $x < $width2 && $y >= 0 && $y < $height2) {
					$c = imagecolorat($this->tmpimg, $x, $y);
				}
				if ($c != $bgCol) { // only copy pixels of letters to preserve any background image
					imagesetpixel($this->im, $ix, $iy, $c);
				}
			}
		}
	}

	/**
	 * Draws distorted lines on the image
	 *
	 * @since 2.5
	 */
	protected function drawLines()
	{
		for ($line = 0; $line < $this->num_lines; ++ $line)
		{
			$x = $this->image_width * (1 + $line) / ($this->num_lines + 1);
			$x += (0.5 - $this->frand()) * $this->image_width / $this->num_lines;
			$y = rand($this->image_height * 0.1, $this->image_height * 0.9);

			$theta = ($this->frand() - 0.5) * M_PI * 0.7;
			$w = $this->image_width;
			$len = rand($w * 0.4, $w * 0.7);
			$lwid = rand(0, 2);

			$k = $this->frand() * 0.6 + 0.2;
			$k = $k * $k * 0.5;
			$phi = $this->frand() * 6.28;
			$step = 0.5;
			$dx = $step * cos($theta);
			$dy = $step * sin($theta);
			$n = $len / $step;
			$amp = 1.5 * $this->frand() / ($k + 5.0 / $len);
			$x0 = $x - 0.5 * $len * cos($theta);
			$y0 = $y - 0.5 * $len * sin($theta);

			$ldx = round(- $dy * $lwid);
			$ldy = round($dx * $lwid);

			for ($i = 0; $i < $n; ++ $i)
			{
				$x = $x0 + $i * $dx + $amp * $dy * sin($k * $i * $step + $phi);
				$y = $y0 + $i * $dy - $amp * $dx * sin($k * $i * $step + $phi);
				imagefilledrectangle($this->im, $x, $y, $x + $lwid, $y + $lwid, $this->gdlinecolor);
			}
		}
	}

	/**
	 * Draws random noise on the image
	 *
	 * @since 2.5
	 */
	protected function drawNoise()
	{
		if ($this->noise_level > 10) {
			$noise_level = 10;
		} else {
			$noise_level = $this->noise_level;
		}

		$t0 = microtime(true);

		$noise_level *= 125; // an arbitrary number that works well on a 1-10 scale

		$points = $this->image_width * $this->image_height * $this->iscale;
		$height = $this->image_height * $this->iscale;
		$width  = $this->image_width * $this->iscale;
		for ($i = 0; $i < $noise_level; ++$i)
		{
			$x = rand(10, $width);
			$y = rand(10, $height);
			$size = rand(7, 10);
			if ($x - $size <= 0 && $y - $size <= 0) continue; // dont cover 0,0 since it is used by imagedistortedcopy
			imagefilledarc($this->tmpimg, $x, $y, $size, $size, 0, 360, $this->gdnoisecolor, IMG_ARC_PIE);
		}

		$t1 = microtime(true);

		$t = $t1 - $t0;
	}

	/**
	 * Print signature text on image
	 *
	 * @since 2.5
	 */
	protected function addSignature()
	{
		$bbox = imagettfbbox(10, 0, $this->signature_font, $this->image_signature);
		$textlen = $bbox[2] - $bbox[0];
		$x = $this->image_width - $textlen - 5;
		$y = $this->image_height - 3;

		imagettftext($this->im, 10, 0, $x, $y, $this->gdsignaturecolor, $this->signature_font, $this->image_signature);
	}

	/**
	 * Sends the appropriate image and cache headers and outputs image to the browser
	 *
	 * @since 2.5
	 */
	protected function outputBrowser()
	{
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		switch ($this->image_type)
		{
			case 'jpg':
				header("Content-Type: image/jpeg");
				imagejpeg($this->im, null, 90);
				break;
			case 'gif':
				header("Content-Type: image/gif");
				imagegif($this->im);
				break;
			case 'png':
			default:
				header("Content-Type: image/png");
				imagepng($this->im);
				break;
		}

		imagedestroy($this->im);
		JFactory::getApplication()->close();
	}

	/**
	 * Output image to a file
	 *
	 * @since 2.5
	 */
	private function outputFile()
	{
		$path = $this->file_path;
		$filename = $path.'/'.$this->filename;

		switch ($this->image_type)
		{
			case 'jpg':
				imagejpeg($this->im, $filename, 90);
				break;
			case 'gif':
				imagegif($this->im, $filename);
				break;
			case 'png':
			default:
				imagepng($this->im, $filename);
				break;
		}

		imagedestroy($this->im);

		// Set the URI of the file
		$r = JURI::root(true);
		$path = explode($r, str_replace(DS, '/', $path));
		$this->fileUri = $r.$path[1].'/'.$this->filename;

		return true;
	}

	/**
	 * Gets a captcha code from a wordlist
	 *
	 * @since 2.5
	 */
	protected function readCodeFromFile()
	{
		$fp = @fopen($this->wordlist_file, 'rb');
		if (!$fp) return false;

		$fsize = filesize($this->wordlist_file);
		if ($fsize < 128) return false; // too small of a list to be effective

		fseek($fp, rand(0, $fsize - 64), SEEK_SET); // seek to a random position of file from 0 to filesize-64
		$data = fread($fp, 64); // read a chunk from our random position
		fclose($fp);
		$data = preg_replace("/\r?\n/", "\n", $data);

		$start = @strpos($data, "\n", rand(0, 56)) + 1; // random start position
		$end   = @strpos($data, "\n", $start);          // find end of word

		if ($start === false) {
			return false;
		} else if ($end === false) {
			$end = strlen($data);
		}

		return strtolower(substr($data, $start, $end - $start)); // return a line of the file
	}

	/**
	 * Generates a random captcha code from the set character set
	 *
	 * @since 2.5
	 */
	protected function generateCode()
	{
		$code = '';

		for ($i = 1, $cslen = JString::strlen($this->charset); $i <= $this->code_length; ++$i) {
			$code .= $this->charset{rand(0, $cslen - 1)};
		}

		return $code;
	}

	/**
	 * Remove old Captchas images files from the tmp folder
	 *
	 * @since 2.5
	 */
	private function purgeCaptchas($session = true, $tmp = true)
	{
		// Import
		jimport('joomla.filesystem.file');

		// Initialise variables
		$return = false;

		// Get the path
		$path = $this->file_path;

		if ($session)
		{
			// Get the Application
			$app = JFactory::getApplication();

			// Get the captchas
			$captchas = $app->getUserState($this->namespace.'.JCaptcha', array());

			if (!empty($captchas))
			{
				// Initialise variables
				$session_excludes = array();

				// Iterate the captchas and delete the files
				foreach ($captchas as $captcha)
				{
					// Store for delete
					$file = $path.'/'.$captcha['file'];
					if (JFile::exists($file)) $session_excludes[] = $file;
				}

				// Delete the files
				if (!empty($session_excludes)) $return = JFile::delete($session_excludes);
			}
			
			// Leave only the las 4 captchas in the session
			if (count($captchas) >= 4){
				$captchas = array_slice($captchas, -3, null, true);
				$app->setUserState($this->namespace.'.JCaptcha', $captchas);
			}
		}

		if ($tmp)
		{
			jimport('joomla.filesystem.folder');
			$past = time() - JFactory::getConfig()->get('lifetime', 15) * 60;

			// Get the files
			if (($files = JFolder::files($path, '^jcaptcha\.[0-9]{9, 10}\.(png|gif|jpg)$')) === false)
			{
				$e = new JException(JText::_('JLIB_CAPTCHA_ERROR_GETTING_FILES_FROM_TMP_FOLDER'));
				$this->setError($e);
				return false;
			}

			if (!empty($files))
			{
				// Initialise variables
				$file_excludes = array();

				foreach ($files as $file)
				{
					$ftime = filemtime($path.'/'.$file);

					// Check if the file is older than the session time
					if ($ftime < $past)
					{
						// Store for delete
						$file = $path.'/'.$file;
						if (JFile::exists($file)) $file_excludes[] = $file;
					}
				}

				// Delete the files
				if (!empty($file_excludes)) $return = JFile::delete($file_excludes);
			}
		}

		return $return;
	}

	/**
	 * Checks the entered code against the value stored in the session, handles case sensitivity
	 * Also clears the stored codes if the code was entered correctly to prevent re-use
	 *
	 * @param  int     $id    The Id of the captcha
	 * @param  string  $code  The code the user entered
	 * @return boolean true if the code was correct, false if not
	 *
	 * @since 2.5
	 */
	public function validate($id, $input)
	{
		// Get the Application
		$app = JFactory::getApplication();

		// Get the captchas
		$captchas = $app->getUserState($this->namespace.'.JCaptcha', array());

		// Check that the specified captcha test exists
		if (!isset($captchas[$id]))
		{
			return false;
		}

		// Pull out the code
		$code = $captchas[$id]['code'];

		// Cleanup
		unset($captchas[$id]);
		$app->setUserState($this->namespace.'.JCaptcha', $captchas);
		$this->purgeCaptchas();

		// Adjust case if necessary
		if (!$this->case_sensitive)
		{
			$code	= strtolower($code);
			$input	= strtolower($input);
		}

		// Check if the strings match
		return ($code === $input);
	}

	/**
	 * Save data to session
	 *
	 * @since 2.5
	 */
	protected function saveData()
	{
		// Get the Application
		$app = JFactory::getApplication();

		// Get the captchas
		$captchas = $app->getUserState($this->namespace.'.JCaptcha', array());

		if (!$this->id) {
			$this->id = mt_rand();
		}

		// Add a new captcha
		$captchas[$this->id] = array('id' => $this->id, 'code' => $this->code, 'file' => $this->filename);

		// Save in the session
		$app->setUserState($this->namespace.'.JCaptcha', $captchas);
	}

	/**
	 * Generate random number less than 1
	 * 
	 * @return float
	 */
	function frand()
	{
		return 0.0001 * rand(0,9999);
	}

	/**
	 * Convert an html color code to a JCaptchaColor
	 * @param string $color
	 * @param JCaptchaColor $default The defalt color to use if $color is invalid
	 *
	 * @since 2.5
	 */
	protected function initColor($color, $default)
	{
		if ($color == null) {
			return new JCaptchaColor($default);
		} else if (is_string($color)) {
			try {
				return new JCaptchaColor($color);
			} catch(Exception $e) {
				return new JCaptchaColor($default);
			}
		} else if (is_array($color) && count($color) == 3) {
			return new JCaptchaColor($color[0], $color[1], $color[2]);
		} else {
			return new JCaptchaColor($default);
		}
	}
}
