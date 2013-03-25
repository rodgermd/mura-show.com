<?php

namespace Rodger\GalleryBundle\Twig;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Rodger\GalleryBundle\Entity\Image;
use Liip\ImagineBundle\Templating\Helper\ImagineHelper;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class Stuff extends Twig_Extension
{
  protected $webroot;
  protected $imagine_helper;
  protected $vich_helper;

  public function __construct($web_root, ImagineHelper $helper, UploaderHelper $vich_helper)
  {
    $this->webroot        = $web_root;
    $this->imagine_helper = $helper;
    $this->vich_helper    = $vich_helper;
  }

  public function getFilters()
  {
    return array(
      new Twig_SimpleFilter('thumbnail', array($this, 'thumbnail'), array('is_safe' => array('html'))),
      new Twig_SimpleFilter('thumbnail_path', array($this, 'thumbnail_path')),
    );
  }

  public function thumbnail_path(Image $image, $thumbnail)
  {
    return $this->imagine_helper->filter($this->vich_helper->asset($image, 'file'), $thumbnail);
  }

  public function thumbnail(Image $image, $thumbnail)
  {
    return strtr('<img src="%src%" alt=""/>', array('%src%' => $this->thumbnail_path($image, $thumbnail)));
  }

  public function getName()
  {
    return 'rodger.stuff';
  }
}