<?php

namespace RebelCode\Tree\Rendering;

/**
 * The interface for all objects that represent tree nodes for rendering.
 *
 * @since [*next-version*]
 */
interface RenderNodeInterface
{
    /**
     * Retrieves the node type.
     *
     * The render type is a key-like string intended to allow consumers to classify nodes.
     *
     * @since [*next-version*]
     *
     * @return string The render type, as a non-empty key-like string
     */
    public function getRenderType();
}
