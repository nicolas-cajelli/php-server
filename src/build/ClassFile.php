<?php
/**
 * Created by PhpStorm.
 * User: nicolas
 * Date: 6/23/16
 * Time: 11:15 PM
 */

namespace nicolascajelli\server\build;


class ClassFile
{
    protected $namespace;
    protected $class;
    protected $interface;
    protected $parsed = true;
    protected $comment;

    public function __toString()
    {
        $reference = $this->namespace . '\\';
        if (! empty($this->class)) {
            $reference .= $this->class;
        } else {
            $reference .= $this->interface;
        }
        return $reference;
    }

    public function parsed()
    {
        return $this->parsed;
    }

    public function __construct($file)
    {
        $classes = $this->classes_in_file($file);
        if (count($classes) == 1) {
            $this->namespace = isset($classes[0]['namespace']) ? $classes[0]['namespace'] : '';
            if (! isset($classes[0]['classes'])) {
                $this->class = $classes[0][0]['name'];
            } else {
                $this->class = $classes[0]['classes'][0]['name'];
            }

        } else {
            $this->parsed = false;
        }
    }

    protected function classes_in_file($file)
    {

        $classes = $nsPos = $final = array();
        $foundNS = FALSE;
        $ii = 0;

        if (!file_exists($file)) return NULL;

        $er = error_reporting();
        error_reporting(E_ALL ^ E_NOTICE);

        $php_code = file_get_contents($file);
        $tokens = token_get_all($php_code);
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++)
        {
            if(!$foundNS && $tokens[$i][0] == T_NAMESPACE)
            {
                $nsPos[$ii]['start'] = $i;
                $foundNS = TRUE;
            }
            elseif( $foundNS && ($tokens[$i] == ';' || $tokens[$i] == '{') )
            {
                $nsPos[$ii]['end']= $i;
                $ii++;
                $foundNS = FALSE;
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                if($i-4 >=0 && $tokens[$i - 4][0] == T_ABSTRACT)
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'ABSTRACT CLASS');
                }
                else
                {
                    $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'CLASS');
                }
            }
            elseif ($i-2 >= 0 && $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
            {
                $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'INTERFACE');
            }
        }
        error_reporting($er);
        if (empty($classes)) return [];

        if(!empty($nsPos))
        {
            foreach($nsPos as $k => $p)
            {
                $ns = '';
                for($i = $p['start'] + 1; $i < $p['end']; $i++)
                    $ns .= $tokens[$i][1];

                $ns = trim($ns);
                $final[$k] = array('namespace' => $ns, 'classes' => $classes[$k+1]);
            }
            $classes = $final;
        }
        return $classes;
    }
}