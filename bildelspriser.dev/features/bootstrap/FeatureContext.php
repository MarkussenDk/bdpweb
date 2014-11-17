<?php
require 'vendor/autoload.php';
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Behat context class.
 */
//class FeatureContext implements SnippetAcceptingContext

class FeatureContext extends MinkContext
{
    //var $base_url = '//dr.dk';
    /**
     * Initializes context.
     *
     * Every scenario gets its own context object.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct()
    {   
        //$this->getSession()->visit('http://dr.dk/');
    }


    /**
     * @Given the site :arg1 exists
     */
    public function theSiteExist($arg1)
    {   
        /** @var $s MinkSession */
        //$s = $this->getSession();
        //$p = $s->getPage();
        //assert(strpos($p->getHtml(), $arg1));
        //die($p->getHtml());
        return 1;
        //$this->assertPageContainsText($arg);
/*      die("XXXXSession"
            &var_dump(get_class_methods(get_class($s)))
            &var_dump(get_class_methods(get_class($p)))
            );  
        assertPage*/
        
        //throw new PendingException();
    }

    /**
     * @Then the title should be :arg1
     */
    public function theTitleShouldBe($arg1)
    {
        return 1;
        throw new PendingException();
    }

}
