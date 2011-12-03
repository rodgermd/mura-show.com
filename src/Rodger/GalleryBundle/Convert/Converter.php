<?php
namespace Rodger\GalleryBundle\Convert;
use Rodger\ImageSizeBundle\Entity\ImageSize;

class Converter
{
  protected $source, $target, $template, $convert;
  public function __construct($source, $target, ImageSize $template)
  {
    $this->source = $source;
    $this->target = $target;
    $this->template = $template;
    
    $this->convert = self::get_convert();
  }
  
  private static function get_convert() {
    // find the convert command
    // you can hard-code it to make the srcipt faster, example:
    // $convert = '/usr/bin/convert';
    
    $convert = trim(shell_exec('which convert'));
        // rely on path as shell-exec does not work on mac os

    if (!$convert)
    {
      if (file_exists('/usr/local/bin/convert'))
      {
        $convert = '/usr/local/bin/convert';
      }
      else
      {
        $convert = 'convert';
      }
    }
    
    return $convert;
  }
  
  public static function exif_rotate($file, $rotate_option)
  {
    $command = '';
    switch ($rotate_option):
      case 1: // nothing
      break;

      case 2: // horizontal flip
          $command = '-flop';
      break;

      case 3: // 180 rotate left
          $command = '-rotate 180';
      break;

      case 4: // vertical flip
          $command = '-flip';
      break;

      case 5: // vertical flip + 90 rotate right
          $command = '-flip -rotate 90';
      break;

      case 6: // 90 rotate right
          $command = '-rotate 90';
      break;

      case 7: // horizontal flip + 90 rotate right
          $command = '-flop -rotate 90';
      break;

      case 8:    // 90 rotate left
          $command = '-rotate -90';
      break;
    endswitch;

    if ($command) exec(implode(" ", self::get_convert(), $file, $command, $file));
  }
  
  public function convert()
  {
    switch ($this->template->getCrop()):
      case ImageSize::FITMODE_FIT:
        $inflate_modifier = ($this->template->getHeight()) ? '>' : '';
        $command = sprintf('-quality %d -thumbnail "%dx%s%s"', 81, $this->template->getWidth(), $this->template->getHeight(), $inflate_modifier);

        break;

      case ImageSize::FITMODE_CROP:
        $command = sprintf('-resize %dx%d^ -gravity center -extent %dx%d -quality %d', 
                $this->template->getWidth(), $this->template->getHeight(),
                $this->template->getWidth(), $this->template->getHeight(),
                81);
        break;

    endswitch;
    
    // prepare the command
    $preparedCommand = implode(" ", array($this->convert, $this->source, $command, $this->target));

    // run the conversion
    //var_dump($preparedCommand); die();
    exec($preparedCommand);
  }
  
}
?>
