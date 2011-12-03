<?php 
namespace Rodger\ImageSizeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Rodger\ImageSizeBundle\Entity\ImageSize;

class LoadImageSizeData extends AbstractFixture implements FixtureInterface
{
    public function load($manager)
    {
      $small = new ImageSize();
      $small->setName('small');
      $small->setWidth(50);
      $small->setHeight(50);
      
      $manager->persist($small);
      
      $manager->flush();
    }
    
}