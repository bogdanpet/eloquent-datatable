<?php namespace Bogdanpet\Datatables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Datatable
{
    /**
     * Main data for showing in table.
     *
     * @var \IteratorAggregate
     */
    protected $data;

    /**
     * Array of columns to be displayed in table.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Row increment property, necessary for proper row number on paginated tables.
     *
     * @var int
     */
    protected $increment = 0;

    /**
     * $data property setter.
     *
     * @param \IteratorAggregate $data
     */
    public function setData(\IteratorAggregate $data)
    {
        $this->data = $data;
    }

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
     * $increment property setter.
     */
    protected function setIncrement()
    {
        if ($this->data instanceof LengthAwarePaginator) {
            $this->increment = ($this->data->currentPage() - 1) * $this->data->perPage();
        }
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
     * Generate table body.
     *
     * @return string
     */
    public function tableBody()
    {

        // Set increment property
        $this->setIncrement();

        $result = '<tbody>' . PHP_EOL;

        // Create table row for each Eloquent Collection item
        foreach ($this->data as $model) {

            // Increase increment value
            $this->increment ++;

            $result .= '<tr>' . PHP_EOL;

            // Create table cell for each column
            foreach ($this->columns as $column) {
                $method = 'td' . studly_case($column);

                if (method_exists($this, $method)) {
                    $result .= call_user_func([ $this, $method ], $model);
                } else {
                    $result .= $this->td($model->$column);
                }
            }

            $result .= '</tr>' . PHP_EOL;
        }

        $result .= '</tbody>';

        return $result;
    }

    /**
     * Generate table footer.
     * Contains data counter.
     *
     * @return string
     */
    public function tableFoot()
    {
        if ($this->data instanceof LengthAwarePaginator) {
            $total = $this->data->total();
            $min   = 1 + ($this->data->currentPage() - 1) * $this->data->perPage();
            $max   = $this->data->currentPage() * $this->data->perPage();
            if ($max > $total) {
                $max = $total;
            }
        } else {
            $total = $this->data->count();
            $min   = 1;
            $max   = $this->data->count();
        }

        $result = '<tfoot><tr class="active"><td colspan="100%" class="text-center">' . $min . ' - ' . $max . ' / ' . $total . '</td></tr></tfoot>';

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

    /**
     * Generate <td> element with optional class attribute for table body.
     *
     * @param $data
     * @param string $class
     *
     * @return string
     */
    protected function td($data, $class = null)
    {
        if ($class != null) {
            return '<td class="' . $class . '">' . $data . '</td>' . PHP_EOL;
        }

        return '<td>' . $data . '</td>' . PHP_EOL;
    }
}