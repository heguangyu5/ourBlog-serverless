<?php

class Zend_View_Helper_OurFormSelect
{
    public function ourFormSelect($name, $value, $attributes, array $options, $escape = true, $prependOptions = null)
    {
        $select = '<select';

        if (!is_array($attributes)) {
            $attributes = array();
        }
        if (isset($attributes['no-id'])) {
            unset($attributes['no-id']);
        } else {
            $attributes['id'] = $name;
        }
        if (isset($attributes['multiple'])) {
            $attributes['name'] = $name . '[]';
        } else {
            $attributes['name'] = $name;
        }
        foreach ($attributes as $key => $attr) {
            $select .= " $key=\"$attr\"";
        }

        $select .= '>';

        if ($prependOptions) {
            $select .= $prependOptions;
        }

        if ($escape) {
            foreach ($options as $val => $text) {
                $options[$val] = htmlspecialchars($text);
            }
        }

        if (isset($attributes['multiple'])) {
            if ($value) {
                $value = array_flip($value);
            }
            foreach ($options as $val => $text) {
                if ($value && isset($value[$val])) {
                    $select .= '<option value="' . $val . '" selected>' . $text . '</option>';
                } else {
                    $select .= '<option value="' . $val . '">' . $text . '</option>';
                }
            }
        } else {
            foreach ($options as $val => $text) {
                if ($val == $value) {
                    $select .= '<option value="' . $val . '" selected>' . $text . '</option>';
                } else {
                    $select .= '<option value="' . $val . '">' . $text . '</option>';
                }
            }
        }

        $select .= '</select>';

        return $select;
    }
}
