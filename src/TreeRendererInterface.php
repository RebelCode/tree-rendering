<?php

namespace RebelCode\Tree\Rendering;

use RebelCode\Tree\Rendering\Exception\CouldNotRenderTreeException;
use RuntimeException;

/**
 * The interface for objects that can render trees of nodes.
 *
 * @since [*next-version*]
 */
interface TreeRendererInterface
{
    /**
     * Renders a tree instance.
     *
     * @since [*next-version*]
     *
     * @param RenderNodeInterface $node The root node of the (sub)tree instance to render.
     * @param string|null         $type Optional render type override. If given, the renderer will render the node as
     *                                  if it were of this type. If not given, the node's render type will be used, as
     *                                  returned by {@link RenderNodeInterface::getRenderType()}. Cannot be empty!
     *
     * @throws CouldNotRenderTreeException If the renderer could not render the node, usually due to an unsupported
     *                                     render type of unexpected/malformed node data.
     * @throws RuntimeException            If an error occurred during rendering.
     *
     * @return string The rendered result.
     */
    public function renderTree(RenderNodeInterface $node, $type = null);
}
