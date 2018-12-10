<?php

namespace RebelCode\Tree\Rendering;

use ArrayAccess;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Output\CreateCouldNotRenderExceptionCapableTrait;
use Dhii\Output\CreateRendererExceptionCapableTrait;
use Dhii\Output\TemplateInterface;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RebelCode\Tree\Rendering\Exception\CouldNotRenderTreeException;
use RuntimeException;
use stdClass;

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
    TemplateInterface,
    /* @since [*next-version*] */
    TreeRendererInterface
{
    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use NormalizeContainerCapableTrait;

    /* @since [*next-version*] */
    use CreateCouldNotRenderExceptionCapableTrait {
        // Aliased to help distinguish it from "CouldNotRenderTreeException"
        _createCouldNotRenderException as _createCouldNotRenderTemplateException;
    }

    /* @since [*next-version*] */
    use CreateRendererExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateContainerExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateNotFoundExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateRuntimeExceptionCapableTrait;

    /* @since [*next-version*] */
    use StringTranslatingTrait;

    /**
     * A container of slaves renderers, keyed by the node type that they render.
     *
     * @since [*next-version*]
     *
     * @var array|stdClass|ArrayAccess|ContainerInterface
     */
    protected $slaves;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|ArrayAccess|ContainerInterface $slaves A container of slaves renderers, keyed by the node
     *                                                              type that they render.
     *
     * @throws InvalidArgumentException If the argument is not a valid container.
     */
    public function __construct($slaves)
    {
        $this->slaves = $this->_normalizeContainer($slaves);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function render($ctx = null)
    {
        if (!$ctx instanceof RenderNodeInterface) {
            throw $this->_createCouldNotRenderTemplateException(
                $this->__('The render context is not a valid render node instance'), null, null, $this
            );
        }

        try {
            return $this->renderTree($ctx);
        } catch (CouldNotRenderTreeException $cnrtException) {
            throw $this->_createCouldNotRenderTemplateException(
                $this->__('Failed to render to the given tree node'), null, $cnrtException, $this
            );
        } catch (RuntimeException $rtException) {
            throw $this->_createRendererException(
                $this->__('An error occurred while trying to render the given tree node'), null, $rtException, $this
            );
        }
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
            $slave = $this->_containerGet($this->slaves, $key);
        } catch (NotFoundExceptionInterface $nfException) {
            throw new CouldNotRenderTreeException(
                $this->__('No slave renderer was found that could render nodes of type "%s"', [$key]),
                null, $nfException, $this, $node
            );
        } catch (ContainerExceptionInterface $cException) {
            throw $this->_createRuntimeException(
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
            throw $this->_createRuntimeException(
                $this->__('A "%s" slave renderer encountered an error while trying to render the tree node', [$key]),
                null, $rtException
            );
        }
    }
}
