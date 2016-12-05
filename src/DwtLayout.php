<?php namespace Primalbase\ViewBuilder;

use File;

/**
 * Class DwtLayout
 * required PHP5.3
 *
 * Convert a DreamWeaver Template to blade or twig format.
 */
class DwtLayout
{
  protected $layout;
  protected $engine = 'blade';
  protected $document_root;
  protected $source;
  protected $output_path;
  protected $map;
  protected $style = 'layout';
  protected $enabled_make_base;
  protected $baseView;

  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  public function setSource($path)
  {
    $this->source = $path;
  }

  public function setBaseView($view)
  {
    $this->baseView = $view;
  }

  public function setDocumentRoot($path)
  {
    $this->document_root = $path;
  }

  public function setStyle($style)
  {
    $this->style = $style;
  }

  public function setOutputPath($path)
  {
    $this->output_path = $path;
  }

  public function setEnabledMakeBase($enabled)
  {
    $this->enabled_make_base = $enabled;
  }

  public function getMap()
  {
    if (!$this->map)
    {
      $map = $this->getCommonMap();
      if ($this->engine == 'twig')
      {
        $map = array_merge($map, $this->getTwigMap());
      }
      else
      {
        $map = array_merge($map, $this->getBladeMap());
      }
      $this->map = $map;
    }
    return $this->map;
  }

  public function getInstanceMap()
  {
    if (!$this->map)
    {
      if ($this->engine == 'twig')
      {
        $map = $this->getTwigMap();
      }
      else
      {
        $map = $this->getInstanceBladeMap();
      }
      $this->map = $map;
    }
    return $this->map;
  }

  public function saved()
  {
    return $this->output_path;
  }

  public function getCommonMap()
  {
    $root   = $this->document_root;
    $source = dirname($this->source);

    $self = $this;

    $map = array(
      array(
        'exp' => '/<link ([\s\S]*?)href="([\s\S]*?)"([\s\S]*?)>/iu',
        'replace' => function ($m) use ($source, $self) {
          if (preg_match('/^((https?:)?\/|#)/iu', $m[2]))
          {
            return $m[0];
          }
          $path = $self->normalize_path($source, $m[2]);
          return '<link '.$m[1].'href="'.$path.'"'.$m[3].'>';
        },
      ),
      array(
        'exp' => '/<a ([\s\S]*?)href="([\s\S]*?)"([\s\S]*?)>/iu',
        'replace' => function ($m) use ($source, $self) {
          if (preg_match('/^((https?:)?\/|#)/iu', $m[2]))
          {
            return $m[0];
          }
          $path = $self->normalize_path($source, $m[2]);
          return '<a '.$m[1].'href="'.$path.'"'.$m[3].'>';
        },
      ),
      array(
        'exp' => '/<img ([\s\S]*?)src="([\s\S]*?)"([\s\S]*?)>/iu',
        'replace' => function ($m) use ($source, $self) {
          if (preg_match('/^((https?:)?\/|#)/iu', $m[2]))
          {
            return $m[0];
          }
          $path = $self->normalize_path($source, $m[2]);
          return '<img '.$m[1].'src="'.$path.'"'.$m[3].'>';
        },
      ),
      array(
        'exp' => '/<script ([\s\S]*?)src="([\s\S]*?)"([\s\S]*?)><\/script>/iu',
        'replace' => function ($m) use ($source, $self) {
          if (preg_match('/^((https?:)?\/|#)/iu', $m[2]))
          {
            return $m[0];
          }
          $path = $self->normalize_path($source, $m[2]);
          return '<script '.$m[1].'src="'.$path.'"'.$m[3].'></script>';
        },
      ),
    );
    return $map;
  }

  public function getBladeMap()
  {
    $map = array(
      array(
        'exp' => '/(?:\n*)<!-- TemplateBeginEditable name="([\s\S]+?)" -->(?:\n*)([\s\S]*?)(?:\n*)<!-- TemplateEndEditable -->(?:\n*)/iu',
        'replace' => function ($m) {
          $blade = '';
          $blade.= PHP_EOL;
          if ($m[1] == 'head')
          {
             $blade.='@yield(\'stylesheet\')'.PHP_EOL;
          }
          $blade.= '@section(\''.addslashes($m[1]).'\')';
          $blade.= PHP_EOL;
          $blade.= $m[2];
          $blade.= PHP_EOL;
          $blade.= '@show';
          $blade.= PHP_EOL;
          return $blade;
        },
      ),
      array(
        'exp' => '/<\/body>/i',
        'replace' => function ($m) {
          $blade = '';
          $blade.= '@yield(\'inline_script\')'.PHP_EOL;
          $blade.= $m[0];
          return $blade;
        }
      ),
    );

    return $map;
  }

  public function getTwigMap()
  {
    $map = array(
      array(
        'exp' => '/(?:\n*)<!-- TemplateBeginEditable name="([\s\S]+?)" -->(?:\n*)([\s\S]*?)(?:\n*)<!-- TemplateEndEditable -->(?:\n*)/iu',
        'replace' => function ($m) {
          $blade = '';
          $blade.= PHP_EOL;
          $blade.= '{% block '.addslashes($m[1]).' %}';
          $blade.= PHP_EOL;
          $blade.= $m[2];
          $blade.= PHP_EOL;
          $blade.= '{% endblock %}';
          $blade.= PHP_EOL;
          return $blade;
        },
      ),
    );

    return $map;
  }

  public function getInstanceBladeMap()
  {
    $map = array(
      array(
        'exp' => '/(?:\n*)<!-- InstanceBeginEditable name="([\s\S]+?)" -->(?:\n*)([\s\S]*?)(?:\n*)<!-- InstanceEndEditable -->(?:\n*)/iu',
        'replace' => function ($m) {
          $blade = '';
          $blade.= PHP_EOL;
          $blade.= '@section(\''.$m[1].'\')'.PHP_EOL;
          $html = $m[2];
          foreach ($this->getCommonMap() as $map)
          {
            $html = preg_replace_callback($map['exp'], $map['replace'], $html);
          }
          $blade.= $html.PHP_EOL;
          $blade.= '@stop'.PHP_EOL;
          return $blade;
        },
      ),
    );

    return $map;
  }

  public function render($engine = null)
  {
    if (!is_null($engine))
    {
      $this->engine = $engine;
    }

    $path = $this->document_root.'/'.$this->source;

    $html = file_get_contents($path);
    $html = str_replace("\r\n", "\n", $html);

    if ($this->style == 'layout')
    {
      foreach ($this->getMap() as $map)
      {
        $html = preg_replace_callback($map['exp'], $map['replace'], $html);
      }
    }
    elseif ($this->style == 'view')
    {
      $view = '';
      $layout = $this->layout;
      $source = $this->source;
      $output = $this->output_path;
      $view.=<<<__BLADE__
<?php
/**
 * auto generated.
 * From $source
 * To $output
 */
?>
@extends('$layout')

__BLADE__;
      foreach ($this->getInstanceMap() as $map)
      {
        if (preg_match_all($map['exp'], $html, $m, PREG_SET_ORDER))
        {
          foreach ($m as $match)
          {
            $view.= call_user_func($map['replace'], $match);
          }
        }
      }
      return $view;
    }

    return $html;
  }

  public function renderExtends($engine = null)
  {
    if (!is_null($engine))
    {
      $this->engine = $engine;
    }

    $view = '';
    $baseView = $this->baseView;
    $view.=<<<__BLADE__
@extends('$baseView')

__BLADE__;

    return $view;
  }

  public function save($engine = null)
  {
    if (!is_null($engine))
    {
      $this->engine = $engine;
    }

    $output_dir = dirname($this->output_path);
    if (!file_exists($output_dir))
    {
      File::makeDirectory($output_dir, 0775, true);
    }

    $base_dir = $output_dir.'/base';
    if ($this->enabled_make_base && !file_exists($base_dir))
    {
      File::makeDirectory($base_dir, 0775, true);
    }

    if ($this->enabled_make_base)
    {
      $base_name = basename($this->output_path);
      file_put_contents($base_dir.'/'.$base_name, $this->render($engine));
      if (!file_exists($this->output_path))
      {
        file_put_contents($this->output_path, $this->renderExtends($engine));
      }
    }
    else
    {
      file_put_contents($this->output_path, $this->render($engine));
    }
  }

  public function normalize_path($cwd, $path)
  {
    if (preg_match('/^((https?:)?\/)/iu', $path))
    {
      return $path;
    }

    if (strpos($path, '#') === 0)
      return $path;

    $hash = '';
    if ($index = strpos($path, '#') !== false)
    {
      $path = substr($path, 0, $index);
      $hash = substr($path, $index);
    }

    if (strpos($path, '/') === 0)
    {
      $pathList = [];
    }
    else
    {
      $pathList = explode('/', $cwd);
    }

    $pathList = array_merge($pathList, explode('/', $path));
    foreach ($pathList as $i => $partial)
    {
      if ($partial == '.')
      {
        $pathList[$i] = '';
      }
      if ($partial == '..')
      {
        $pathList[$i] = '';
        if ($i > 0)
          $pathList[$i-1] = '';
      }

    }
    $pathList = array_filter($pathList, "strlen");

    return '/'.implode('/', $pathList).$hash;
  }
}
