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
      
      $edit = new ImageSize();
      $edit->setName('edit');
      $edit->setWidth(800);
      
      $manager->persist($edit);
      
      $list = new ImageSize();
      $list->setName('list');
      $list->setWidth(75);
      $list->setHeight(75);
      $list->setCrop(true);
      
      $manager->persist($list);
      
      $list_medium = new ImageSize();
      $list_medium->setName('list_medium');
      $list_medium->setWidth(200);
      $list_medium->setHeight(200);
      $list_medium->setCrop(true);
      
      $manager->persist($list);
      
      $showcase = new ImageSize();
      $showcase->setName('showcase');
      $showcase->setWidth(800);
      $showcase->setHeight(600);
      
      $manager->persist($showcase);
      
      $manager->flush();
    }
    
}