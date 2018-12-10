<?php

namespace RebelCode\Tree\Rendering\FuncTest\Exception;

use Exception;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase;
use RebelCode\Tree\Rendering\Exception\CouldNotRenderTreeException;
use RebelCode\Tree\Rendering\RenderNodeInterface;
use RebelCode\Tree\Rendering\TreeRendererInterface;

/**
 * Tests the {@link CouldNotRenderTreeException} class.
 *
 * @since [*next-version*]
 */
class CouldNotRenderTreeExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the constructor to assert whether the values are correctly set and later retrieved via getter methods.
     *
     * @since [*next-version*]
     */
    public function testConstructor()
    {
        $message = uniqid('message-');
        $code = rand();
        $previous = new Exception();
        $renderer = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\TreeRendererInterface');
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');

        /* @var $renderer MockObject|TreeRendererInterface */
        /* @var $node MockObject|RenderNodeInterface */
        $subject = new CouldNotRenderTreeException($message, $code, $previous, $renderer, $node);

        $this->assertEquals($message, $subject->getMessage());
        $this->assertEquals($code, $subject->getCode());
        $this->assertEquals($previous, $subject->getPrevious());
        $this->assertEquals($renderer, $subject->getTreeRenderer());
        $this->assertEquals($node, $subject->getRenderNode());
    }

    /**
     * Tests the constructor with a null tree renderer instance.
     *
     * @since [*next-version*]
     */
    public function testConstructorNullTreeRenderer()
    {
        $message = uniqid('message-');
        $code = rand();
        $previous = new Exception();
        $node = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\RenderNodeInterface');

        /* @var $node MockObject|RenderNodeInterface */
        $subject = new CouldNotRenderTreeException($message, $code, $previous, null, $node);

        $this->assertEquals($message, $subject->getMessage());
        $this->assertEquals($code, $subject->getCode());
        $this->assertEquals($previous, $subject->getPrevious());
        $this->assertNull($subject->getTreeRenderer());
        $this->assertEquals($node, $subject->getRenderNode());
    }

    /**
     * Tests the constructor with a null render node instance.
     *
     * @since [*next-version*]
     */
    public function testConstructorNullRenderNode()
    {
        $message = uniqid('message-');
        $code = rand();
        $previous = new Exception();
        $renderer = $this->getMockForAbstractClass('RebelCode\Tree\Rendering\TreeRendererInterface');

        /* @var $renderer MockObject|TreeRendererInterface */
        $subject = new CouldNotRenderTreeException($message, $code, $previous, $renderer, null);

        $this->assertEquals($message, $subject->getMessage());
        $this->assertEquals($code, $subject->getCode());
        $this->assertEquals($previous, $subject->getPrevious());
        $this->assertEquals($renderer, $subject->getTreeRenderer());
        $this->assertNull($subject->getRenderNode());
    }

    /**
     * Tests the constructor with a null tree renderer instance and a null render node instance.
     *
     * @since [*next-version*]
     */
    public function testConstructorNullTreeRendererAndRenderNode()
    {
        $message = uniqid('message-');
        $code = rand();
        $previous = new Exception();

        $subject = new CouldNotRenderTreeException($message, $code, $previous, null, null);

        $this->assertEquals($message, $subject->getMessage());
        $this->assertEquals($code, $subject->getCode());
        $this->assertEquals($previous, $subject->getPrevious());
        $this->assertNull($subject->getTreeRenderer());
        $this->assertNull($subject->getRenderNode());
    }
}
