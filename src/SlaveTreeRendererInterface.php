<?php

namespace RebelCode\Tree\Rendering;

use InvalidArgumentException;
use RuntimeException;

/**
 * The interface of tree renders that only partially render a tree, delegating the rest back to their "master".
 *
 * This interface is an implementation detail for the {@link MasterTreeRenderer} class and is not meant to be used as
 * a tree-rendering standards interface.
 *
 * @since [*next-version*]
 */
interface SlaveTreeRendererInterface
{
    /**
     * Renders a tree node instance, delegating to a master renderer if required.
     *
     * @since [*next-version*]
     *
     * @param RenderNodeInterface   $node   The root node of the (sub)tree instance to render.
     * @param TreeRendererInterface $master The master tree renderer instance.
     *
     * @throws InvalidArgumentException If the slave renderer does not support the given render node instance.
     * @throws RuntimeException         If an error occurred while rendering the node.
     *
     * @return string The rendered result.
     */
    public function render(RenderNodeInterface $node, TreeRendererInterface $master);
}
