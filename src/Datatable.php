<?php namespace Bogdanpet\Datatables;

class Datatable
{
    /**
     * Generate opening <table> tag with attributes
     *
     * @param array $attributes
     *
     * @return string
     */
    public function open(array $attributes = [ 'class' => 'table' ])
    {
        $attr = null;

        // Create html attribute for each of $attributes array items
        foreach ($attributes as $key => $value) {
            if (is_integer($key)) {
                $attr .= ' ' . $value;
            } else {
                $attr .= ' ' . $key . '="' . $value . '"';
            }
        }

        $result = '<table' . $attr . '>';

        return $result;
    }

    /**
     * Generate closing </table> tag.
     *
     * @return string
     */
    public function close()
    {
        return '</table>';
    }
}