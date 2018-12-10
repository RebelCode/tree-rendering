<?php

namespace RebelCode\Tree\Rendering;

use Dhii\I18n\StringTranslatingTrait;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RebelCode\Tree\Rendering\Exception\CouldNotRenderTreeException;
use RuntimeException;

/**
 * A tree renderer implementation that uses slave renders.
 *
 * This implementation delegates all rendering to its slave renderers. Slave renderers may delegate parts of their
 * rendering back to this master, which in turn will attempt to delegate it once again to one of its slaves (which
 * may be the same slave, which allows for recursive rendering). This implementation also implements the Dhii
 * template standard for interoperability with template consumers.
 *
 * @since [*next-version*]
 */
class MasterTreeRenderer implements
    /* @since [*next-version*] */
    TreeRendererInterface
{
    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * A container of slaves renderers, keyed by the node type that they render.
     *
     * @since [*next-version*]
     *
     * @var ContainerInterface
     */
    protected $slaves;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface $slaves A container of slaves renderers, keyed by the node type that they render.
     */
    public function __construct(ContainerInterface $slaves)
    {
        $this->slaves = $slaves;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function renderTree(RenderNodeInterface $node, $type = null)
    {
        $key = empty($type) ? $node->getRenderType() : $type;

        try {
            /* @var $slave SlaveTreeRendererInterface */
            $slave = $this->slaves->get($key);
        } catch (NotFoundExceptionInterface $nfException) {
            throw new CouldNotRenderTreeException(
                $this->__('No slave renderer was found that could render nodes of type "%s"', [$key]),
                null, $nfException, $this, $node
            );
        } catch (ContainerExceptionInterface $cException) {
            throw new RuntimeException(
                $this->__('An error occurred while trying to render the tree node'), null, $cException
            );
        }

        try {
            return $slave->renderTree($node, $this);
        } catch (InvalidArgumentException $iaException) {
            throw new CouldNotRenderTreeException(
                $this->__('The "%s" slave renderer cannot render the given tree node instance', [$key]),
                null, $iaException, $this, $node
            );
        } catch (RuntimeException $rtException) {
            throw new RuntimeException(
                $this->__('A "%s" slave renderer encountered an error while trying to render the tree node', [$key]),
                null, $rtException
            );
        }
    }
}
