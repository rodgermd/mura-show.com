<?php

namespace Rodger\GalleryBundle\Features\Context;

use Behat\BehatBundle\Context\BehatContext,
    Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Exception\PendingException,
    Behat\Behat\Context\Step;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Rodger\GalleryBundle\Entity\User;

/**
 * Feature context.
 */
class CommonFeatureContext extends MinkContext {

  /**
   * @BeforeScenario
   */
  public function cleanUsers() {
    $this->getEntityManager()->getRepository("RodgerUserBundle:User")
            ->createQueryBuilder('e')
            ->delete()
            ->getQuery()
            ->execute();
  }

  /**
   * @Given /^я вхожу как пользователь "([^"]*)" с паролем "([^"]*)"$/
   */
  public function iaLoghiniusKakSParoliem($username, $password) {
    return array(
        new Step\Given('I am on "/login"'),
        new Step\When("fill in \"username:\" with \"$username\""),
        new Step\When("fill in \"password:\" with \"$password\""),
        new Step\When("I press \"try login\""),
    );
  }

  /**
   * @Given /^на сайте зарегистрированы:$/
   */
  public function naSaitieZarieghistrirovany(TableNode $table) {
    $em = $this->getEntityManager();
    $um = $this->getUserManager();

    foreach ($table->getHash() as $userHash) {
      $user = $um->createUser();
      $user->setUsername($userHash['user']);
      $user->setEmail($userHash['email']);
      $user->setPlainPassword($userHash['password']);
      $user->setRoles(explode(',', $userHash['roles']));
      $user->setEnabled(true);

      $um->updatePassword($user);
      $em->persist($user);
    }
    $em->flush();
  }

  public function getEntityManager() {
    return $this->getContainer()->get('doctrine.orm.entity_manager');
  }

  public function getUserManager() {
    return $this->getContainer()->get('fos_user.user_manager');
  }

}
