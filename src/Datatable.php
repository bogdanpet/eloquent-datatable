<?php namespace Bogdanpet\Datatables;

class Datatable
{
    /**
     * Array of columns to be displayed in table.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * $columns property setter.
     *
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

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
     * Generate table head.
     *
     * @return string
     */
    public function tableHead()
    {
        $result = '<thead>' . PHP_EOL;
        $result .= '<tr>' . PHP_EOL;

        // Create <th> element for each $column
        foreach ($this->columns as $column) {
            $method = 'th' . studly_case($column);

            if (method_exists($this, $method)) {
                $result .= call_user_func([ $this, $method ]);
            } else {
                $result .= $this->th($column);
            }
        }

        $result .= '</tr>' . PHP_EOL;
        $result .= '</thead>';

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

    /**
     * Generate <th> element with optional class attribute for table head.
     *
     * @param $data
     * @param string $class
     *
     * @return string
     */
    protected function th($data, $class = null)
    {
        if ($class != null) {
            return '<th class="' . $class . '">' . ucfirst($data) . '</th>' . PHP_EOL;
        }

        return '<th>' . ucfirst($data) . '</th>' . PHP_EOL;
    }
}