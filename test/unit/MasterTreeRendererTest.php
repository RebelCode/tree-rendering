<?php

namespace RebelCode\Tree\Rendering\UnitTest;

use Dhii\Data\Container\Exception\ContainerException;
use Dhii\Data\Container\Exception\NotFoundException;
use Exception;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use Psr\Container\ContainerInterface;
use RebelCode\Tree\Rendering\Exception\CouldNotRenderTreeException;
use RebelCode\Tree\Rendering\MasterTreeRenderer;
use RebelCode\Tree\Rendering\RenderNodeInterface;
use RuntimeException;

/**
 * Tests the {@link MasterTreeRenderer} class.
 *
 * @since [*next-version*]
 */
class MasterTreeRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests if a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->getMockBuilder('RebelCode\Tree\Rendering\MasterTreeRenderer')
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->assertInstanceOf('RebelCode\Tree\Rendering\TreeRendererInterface', $subject);
    }

    /**
     * Tests the constructor.
     *
     * @since [*next-version*]
     */
    public function testConstructContainer()
    {
        /* @var $slaves ContainerInterface|MockObject */
        $slaves = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');

        new MasterTreeRenderer($slaves);
    }

    /**
     * Tests the rendering method to assert whether the subject correctly delegates its rendering to the correct slave.
     *
     * @since [*next-version*]
     */
    public function testRenderTree()
    {
        /* @var $cntr MockObject|ContainerInterface */
        $cntr = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject = new MasterTreeRenderer($cntr);

        $expected = uniqid('expected-render-result');

        $type = uniqid('render-type');
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');
        $node->method('getRenderType')->willReturn($type);

        $slave = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\SlaveTreeRendererInterface');
        $slave->expects($this->once())
              ->method('render')
              ->with($node, $subject)
              ->willReturn($expected);

        $cntr->expects($this->once())
             ->method('get')
             ->with($type)
             ->willReturn($slave);

        try {
            /* @var $node RenderNodeInterface|MockObject */
            $actual = $subject->render($node);

            $this->assertEquals($expected, $actual);
        } catch (CouldNotRenderTreeException $e) {
            $this->fail('Test subject threw an exception: '.$e->getMessage());
        }
    }

    /**
     * Tests the rendering method to assert whether the subject throws the correct exception when a slave renderer is
     * not found for a particular node render type.
     *
     * @since [*next-version*]
     */
    public function testRenderTreeNoSlave()
    {
        /* @var $cntr MockObject|ContainerInterface */
        $cntr = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject = new MasterTreeRenderer($cntr);

        $type = uniqid('render-type');
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');
        $node->method('getRenderType')->willReturn($type);

        $cntr->expects($this->once())
             ->method('get')
             ->with($type)
             ->willThrowException($prev = new NotFoundException());

        try {
            /* @var $node RenderNodeInterface|MockObject */
            $subject->render($node);

            $this->fail('Test subject should have thrown a CouldNotRenderTreeException');
        } catch (CouldNotRenderTreeException $cnrte) {
            $this->assertEquals($subject, $cnrte->getTreeRenderer());
            $this->assertEquals($node, $cnrte->getRenderNode());
            $this->assertSame($prev, $cnrte->getPrevious());
        } catch (Exception $e) {
            $this->fail('Thrown exception is not a CouldNotRenderTreeException instance');
        }
    }

    /**
     * Tests the rendering method to assert whether the subject throws the correct exception when the slaves
     * container encounters an error.
     *
     * @since [*next-version*]
     */
    public function testRenderTreeContainerException()
    {
        /* @var $cntr MockObject|ContainerInterface */
        $cntr = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject = new MasterTreeRenderer($cntr);

        $type = uniqid('render-type');
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');
        $node->method('getRenderType')->willReturn($type);

        $cntr->expects($this->once())
             ->method('get')
             ->with($type)
             ->willThrowException($prev = new ContainerException());

        try {
            /* @var $node RenderNodeInterface|MockObject */
            $subject->render($node);

            $this->fail('Test subject should have thrown a RuntimeException');
        } catch (RuntimeException $cnrte) {
            $this->assertSame($prev, $cnrte->getPrevious());
        } catch (Exception $e) {
            $this->fail('Thrown exception is not a RuntimeException instance');
        }
    }

    /**
     * Tests the rendering method to assert whether the subject correctly wraps {@link InvalidArgumentException}
     * instances thrown by a slave.
     *
     * @since [*next-version*]
     */
    public function testRenderTreeSlaveInvalidArgumentException()
    {
        /* @var $cntr MockObject|ContainerInterface */
        $cntr = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject = new MasterTreeRenderer($cntr);

        $type = uniqid('render-type');
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');
        $node->method('getRenderType')->willReturn($type);

        $slave = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\SlaveTreeRendererInterface');
        $slave->expects($this->once())
              ->method('render')
              ->with($node, $subject)
              ->willThrowException($prev = new InvalidArgumentException());

        $cntr->expects($this->once())
             ->method('get')
             ->with($type)
             ->willReturn($slave);

        try {
            /* @var $node RenderNodeInterface|MockObject */
            $subject->render($node);

            $this->fail('Test subject should have thrown a CouldNotRenderTreeException');
        } catch (CouldNotRenderTreeException $cnrte) {
            $this->assertEquals($subject, $cnrte->getTreeRenderer());
            $this->assertEquals($node, $cnrte->getRenderNode());
            $this->assertSame($prev, $cnrte->getPrevious());
        } catch (Exception $e) {
            $this->fail('Thrown exception is not a CouldNotRenderTreeException instance');
        }
    }

    /**
     * Tests the rendering method to assert whether the subject correctly wraps {@link RuntimeException} instances
     * thrown by a slave.
     *
     * @since [*next-version*]
     */
    public function testRenderTreeSlaveRuntimeException()
    {
        /* @var $cntr MockObject|ContainerInterface */
        $cntr = $this->getMockForAbstractClass('Psr\Container\ContainerInterface');
        $subject = new MasterTreeRenderer($cntr);

        $type = uniqid('render-type');
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');
        $node->method('getRenderType')->willReturn($type);

        $slave = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\SlaveTreeRendererInterface');
        $slave->expects($this->once())
              ->method('render')
              ->with($node, $subject)
              ->willThrowException($prev = new RuntimeException());

        $cntr->expects($this->once())
             ->method('get')
             ->with($type)
             ->willReturn($slave);

        try {
            /* @var $node RenderNodeInterface|MockObject */
            $subject->render($node);

            $this->fail('Test subject should have thrown a RuntimeException');
        } catch (RuntimeException $rte) {
            $this->assertSame($prev, $rte->getPrevious());
        } catch (Exception $e) {
            $this->fail('Thrown exception is not a RuntimeException instance');
        }
    }
}
