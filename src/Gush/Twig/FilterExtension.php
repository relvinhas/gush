<?php

namespace Gush\Twig;

class FilterExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'filter';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('uniq', array($this, 'uniq')),
            new \Twig_SimpleFilter('filter_eq', array($this, 'filterEq')),
        ];
    }

    protected function valueFromPath($data, $path)
    {
        $path = explode('.', $path);

        foreach ($path as $i => $element) {
            if (!isset($data[$element])) {
                return null;
            }

            $data = $data[$element];

            if (is_array($data) && is_integer(key($data))) {
                throw new \InvalidArgumentException('Non-associative arrays not supported');
            }
        }

        return $data;
    }

    public function uniq($data, $path)
    {
        $uniq = [];
        foreach ($data as $datum) {
            $value = $this->valueFromPath($datum, $path);

            if (!isset($uniq[$value])) {
                $uniq[$value] = $datum;
            }
        }

        return array_values($uniq);
    }

    public function filterEq($data, $path, $value)
    {
        $res = array();

        foreach ($data as $datum) {
            $datumValue = $this->valueFromPath($datum, $path);
            if ($datumValue == $value) {
                $res[] = $datum;
            }
        }

        return $res;
    }
}
