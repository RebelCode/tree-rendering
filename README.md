# RebelCode - Tree Rendering

[![Build Status](https://travis-ci.org/RebelCode/tree-rendering.svg?branch=master)](https://travis-ci.org/RebelCode/tree-rendering)
[![Code Climate](https://codeclimate.com/github/RebelCode/tree-rendering/badges/gpa.svg)](https://codeclimate.com/github/RebelCode/tree-rendering)
[![Test Coverage](https://codeclimate.com/github/RebelCode/tree-rendering/badges/coverage.svg)](https://codeclimate.com/github/RebelCode/tree-rendering/coverage)
[![Latest Stable Version](https://poser.pugx.org/rebelcode/tree-rendering/version)](https://packagist.org/packages/rebelcode/tree-rendering)

A lightweight PHP library for rendering trees and other similar hierarchies.

## Requirements 🛠

PHP 5.4 or later.

## Installation ⬇

```
composer require rebelcode/tree-rendering
```

## Features ⭐

* Simple and intuitive interfaces
* Lightweight implementation with minimal overhead
* Master-slave pattern for simple per-node rendering and delegation
* Suitable for trees, nested structures and other similar hierarchical data structures

## How it works 📖

A **master** renderer instance is instantiated with a container of slave renderers. This renderer acts as the main entry point for all rendering.

```php
$renderer = new MasterTreeRenderer(new Container([
    'slave_1' => new MySlaveRenderer(),
    // ...
]));
```

When the master renderer's `render()` method is called, the master renderer will use the node's _render type_ string to determine which slave renderer should be used for rendering. The chosen slave is then told to `render()` the node, receiving both the node and the master renderer as parameters.

Slave renderers are not required to know how to render the entire node/subtree, and may use the master renderer reference (that they receive as the second parameter) to further delegate certain rendering to other slaves. Optionally, the slave may request that the master treats a node with a different render type.

```php
class MySlaveRenderer implements SlaveTreeRendererInterface
{
    public function render(RenderNodeInterface $node, TreeRendererInterface $master)
    {
        if (! $node instanceof UserNodeInterface) {
            throw new InvalidArgumentException();
        }
        
        $name = $node->getName();
        $friends = $node->getFriends();
        
        // Delegate back to the master renderer, using `user_list` as the render type
        $list = $master->render($friends, 'user_list');

        return sprintf('[%s] %s', $name, $list);
    }
}
```

[`TreeRendererInterface`]: src/TreeRendererInterface.php
[`RenderNodeInterface`]: src/RenderNodeInterface.php
[`MasterTreeRenderer`]: src/MasterTreeRenderer.php
