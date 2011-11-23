<?php

namespace Rodger\UserBundle\Features\Context;

use Behat\BehatBundle\Context\BehatContext,
    Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Rodger\GalleryBundle\Features\Context\CommonFeatureContext;

/**
 * Feature context.
 */
class FeatureContext extends CommonFeatureContext {

  /**
   * @BeforeScenario
   */
  public function cleanDatabase() {
    $this->cleanUsers();
  }

}
