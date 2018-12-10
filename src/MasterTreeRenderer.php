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
    public function render(RenderNodeInterface $node, $type = null)
    {
        $key = empty($type) ? $node->getRenderType() : $type;

        try {
            /* @var $slave SlaveTreeRendererInterface */
            $slave = $this->slaves->get($key);
        } catch (NotFoundExceptionInterface $nfExc) {
            throw new CouldNotRenderTreeException(
                $this->__('Could not find a slave renderer for type "%s"', [$key]), null, $nfExc, $this, $node
            );
        } catch (ContainerExceptionInterface $cExc) {
            throw new RuntimeException(
                $this->__('An error occurred while trying to render the tree node'), null, $cExc
            );
        }

        try {
            return $slave->render($node, $this);
        } catch (InvalidArgumentException $iaExc) {
            throw new CouldNotRenderTreeException(
                $this->__('The slave renderer for type "%s" cannot render the node instance"', [$key]),
                null, $iaExc, $this, $node
            );
        } catch (RuntimeException $rtException) {
            throw new RuntimeException(
                $this->__('The slave renderer for type "%s" encountered an error', [$key]), null, $rtException
            );
        }
    }
}
