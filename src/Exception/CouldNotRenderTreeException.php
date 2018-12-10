<?php

namespace RebelCode\Tree\Rendering\Exception;

use Exception;
use RebelCode\Tree\Rendering\RenderNodeInterface;
use RebelCode\Tree\Rendering\TreeRendererInterface;

/**
 * An exception thrown when a tree renderer fails to render a tree node.
 *
 * @since [*next-version*]
 */
class CouldNotRenderTreeException extends Exception
{
    /**
     * The renderer that failed to render.
     *
     * @since [*next-version*]
     *
     * @var TreeRendererInterface|null
     */
    protected $renderer;

    /**
     * The tree node that failed to be rendered.
     *
     * @since [*next-version*]
     *
     * @var RenderNodeInterface|null
     */
    protected $node;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param TreeRendererInterface|null $renderer The renderer that failed to render.
     * @param RenderNodeInterface|null   $node     The tree node that failed to be rendered.
     */
    public function __construct(
        $message = '',
        $code = 0,
        Exception $previous = null,
        $renderer = null,
        RenderNodeInterface $node = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->renderer = $renderer;
        $this->node     = $node;
    }
}
