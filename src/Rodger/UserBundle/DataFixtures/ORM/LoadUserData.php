<?php 
namespace Rodger\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Rodger\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface
{
    public function load($manager)
    {

      $rodger = new User();
      $rodger->setEmail('rodger@mura-show.com');
      $rodger->setUsername('rodger');
      $rodger->setPlainPassword('party#An');
      $rodger->setEnabled(true);
      $rodger->addRole(User::ROLE_SUPER_ADMIN);
      
      $this->addReference('rodger', $rodger);
      
      $manager->persist($rodger);
      
      $asol = new User();
      $asol->setEmail('lena@mura-show.com');
      $asol->setUsername('asol');
      $asol->setPlainPassword('asol');
      $asol->setEnabled(true);
      $rodger->addRole(User::ROLE_DEFAULT);
      
      $manager->persist($asol);
      
      $manager->flush();
    }
    
    public function getOrder()
    {
      return 2;
    }
}