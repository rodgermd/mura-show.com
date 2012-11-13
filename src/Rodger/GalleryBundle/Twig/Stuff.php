<?php

  namespace Rodger\GalleryBundle\Twig;

  use Twig_Extension;
  use Twig_Filter_Method;
  use Twig_Function_Method;
  use Symfony\Bundle\FrameworkBundle\Routing\Router;
  use Rodger\GalleryBundle\Entity\Image;
  use Avalanche\Bundle\ImagineBundle\Templating\Helper\ImagineHelper;

  class Stuff extends Twig_Extension
  {
    protected $webroot;
    protected $imagine_helper;

    public function __construct($web_root, ImagineHelper $helper)
    {
      $this->webroot = $web_root;
      $this->imagine_helper = $helper;
    }

    public function getFilters()
    {
      return array(
        'thumbnail'      => new Twig_Filter_Method($this, 'thumbnail', array('is_safe' => true)),
        'thumbnail_path' => new Twig_Filter_Method($this, 'thumbnail_path'),
      );
    }

    public function thumbnail_path(Image $image, $thumbnail)
    {
      $filename = $image->getUploadDir() . '/' . $image->getFilename();
      return $this->imagine_helper->filter($filename, $thumbnail);
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