<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class Twig_Template implements Twig_TemplateInterface
{
  protected $env;

  public function __construct(Twig_Environment $env)
  {
    $this->env = $env;
  }

  /**
   * Renders the template with the given context and returns it as string.
   */
  public function render($context)
  {
    ob_start();
    $this->display($context);

    return ob_get_clean();
  }

  public function getEnvironment()
  {
    return $this->env;
  }

  protected function resolveMissingFilter($name)
  {
    throw new Twig_RuntimeError(sprintf('The filter "%s" does not exist', $name));
  }

  protected function getAttribute($object, $item)
  {
    $item = (string) $item;

    if (is_array($object) && isset($object[$item]))
    {
      return $object[$item];
    }

    if (
      !is_object($object) ||
      (
        !method_exists($object, $method = $item) &&
        !method_exists($object, $method = 'get'.ucfirst($item))
      )
    )
    {
      return null;
    }

    if ($this->env->hasExtension('sandbox'))
    {
      $this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
    }

    return $object->$method();
  }
}