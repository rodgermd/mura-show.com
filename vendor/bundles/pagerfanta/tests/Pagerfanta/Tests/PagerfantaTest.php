<?php

namespace Pagerfanta\Tests;

use Pagerfanta\Pagerfanta;

class PagerfantaTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $pagerfanta;

    protected function setUp()
    {
        $this->adapter = $this->getMock('Pagerfanta\Adapter\AdapterInterface');
        $this->pagerfanta = new Pagerfanta($this->adapter);
    }

    public function testGetAdapter()
    {
        $this->assertSame($this->adapter, $this->pagerfanta->getAdapter());
    }

    public function testFluentInterface()
    {
        $this->assertInstanceOf('Pagerfanta\Pagerfanta', $this->pagerfanta->setCurrentPage(1, true));
        $this->assertInstanceOf('Pagerfanta\Pagerfanta', $this->pagerfanta->setMaxPerPage(20));
    }

    /**
     * @dataProvider providerSetGetMaxPerPage
     */
    public function testSetGetMaxPerPage($maxPerPage, $expectedMaxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
        $this->assertSame($expectedMaxPerPage, $this->pagerfanta->getMaxPerPage());
    }

    public function providerSetGetMaxPerPage()
    {
        return array(
            // normal, integer
            array(1, 1),
            array(10, 10),
            array(25, 25),
            // to normalize
            array('1', 1),
            array('10', 10),
            array('25', 25),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\NotIntegerMaxPerPageException
     * @dataProvider      providerSetMaxPerPageNotInteger
     */
    public function testSetMaxPerPageNotInteger($maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
    }

    public function providerSetMaxPerPageNotInteger()
    {
        return array(
            array(1.1),
            array('1.1'),
            array(true),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\LessThan1MaxPerPageException
     * @dataProvider      providerSetMaxPerPageLessThan1
     */
    public function testSetMaxPerPageLessThan1($maxPerPage)
    {
        $this->pagerfanta->setMaxPerPage($maxPerPage);
    }

    public function providerSetMaxPerPageLessThan1()
    {
        return array(
            array(0),
            array(-1),
        );
    }

    /**
     * @dataProvider providerSetGetCurrentPage
     */
    public function testSetCurrentPage($currentPage, $expectedCurrentPage)
    {
        $this->pagerfanta->setCurrentPage($currentPage, true);
        $this->assertSame($expectedCurrentPage, $this->pagerfanta->getCurrentPage());
    }

    public function providerSetGetCurrentPage()
    {
        return array(
            // normal
            array(1, 1),
            array(3, 3),
            // to normalize
            array('1', 1),
            array('5', 5),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\NotIntegerCurrentPageException
     * @dataProvider      providerSetCurrentPageNotInteger
     */
    public function testSetCurrentPageNotInteger($currentPage)
    {
        $this->pagerfanta->setCurrentPage($currentPage);
    }

    public function providerSetCurrentPageNotInteger()
    {
        return array(
            array(1.1),
            array('1.1'),
            array(true),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\LessThan1CurrentPageException
     * @dataProvider      providerSetCurrentPageLessThan1
     */
    public function testSetCurrentPageLessThan1($currentPage)
    {
        $this->pagerfanta->setCurrentPage($currentPage);
    }

    public function providerSetCurrentPageLessThan1()
    {
        return array(
            array(0),
            array(-1),
        );
    }

    /**
     * @expectedException Pagerfanta\Exception\OutOfRangeCurrentPageException
     */
    public function testSetCurrentPageOutOfRange()
    {
        $this->adapter
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(10))
        ;

        $this->pagerfanta->setMaxPerPage(5);
        $this->pagerfanta->setCurrentPage(3);
    }

    public function testGetCurrentPageResults()
    {
        $returnValues = array(
            array('foo' => 'bar', 'bar' => 'foo'),
            array('paga', 'fanta', 'foo', 'bar'),
        );

        $this->adapter
            ->expects($this->once())
            ->method('getSlice')
            ->with($this->equalTo(20), $this->equalTo(10))
            ->will($this->returnValue($returnValues[0]))
        ;

        $this->pagerfanta->setMaxPerPage(10);
        $this->pagerfanta->setCurrentPage(3, true);
        $this->assertSame($returnValues[0], $this->pagerfanta->getCurrentPageResults());

        // cached
        $this->assertSame($returnValues[0], $this->pagerfanta->getCurrentPageResults());
    }

    public function testGetNbResults()
    {
        $this->adapter
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(100))
        ;

        $this->assertSame(100, $this->pagerfanta->getNbResults());
    }

    public function testGetNbPages()
    {
        $this->adapter
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(100))
        ;

        $this->pagerfanta->setMaxPerPage(10);
        $this->assertSame(10, $this->pagerfanta->getNbPages());

        // max per page reset the nb results
        $this->pagerfanta->setMaxPerPage(6);
        $this->assertSame(17, $this->pagerfanta->getNbPages());
    }

    public function testHaveToPaginate()
    {
        $this->adapter
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(10))
        ;

        $this->pagerfanta->setMaxPerPage(11);
        $this->assertFalse($this->pagerfanta->haveToPaginate());
        $this->pagerfanta->setMaxPerPage(10);
        $this->assertFalse($this->pagerfanta->haveToPaginate());
        $this->pagerfanta->setMaxPerPage(9);
        $this->assertTrue($this->pagerfanta->haveToPaginate());
    }

    public function testHasGetPreviousNextPage()
    {
        $this->adapter
            ->expects($this->any())
            ->method('getNbResults')
            ->will($this->returnValue(100))
        ;

        $this->pagerfanta->setMaxPerPage(10);

        $this->pagerfanta->setCurrentPage(1);
        $this->assertFalse($this->pagerfanta->hasPreviousPage());
        try {
            $this->pagerfanta->getPreviousPage();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Pagerfanta\Exception\LogicException', $e);
        }
        $this->assertTrue($this->pagerfanta->hasNextPage());
        $this->assertSame(2, $this->pagerfanta->getNextPage());

        $this->pagerfanta->setCurrentPage(5);
        $this->assertTrue($this->pagerfanta->hasPreviousPage());
        $this->assertSame(4, $this->pagerfanta->getPreviousPage());
        $this->assertTrue($this->pagerfanta->hasNextPage());
        $this->assertSame(6, $this->pagerfanta->getNextPage());

        $this->pagerfanta->setCurrentPage(10);
        $this->assertTrue($this->pagerfanta->hasPreviousPage());
        $this->assertSame(9, $this->pagerfanta->getPreviousPage());
        $this->assertFalse($this->pagerfanta->hasNextPage());
        try {
            $this->pagerfanta->getNextPage();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Pagerfanta\Exception\LogicException', $e);
        }
    }
}
