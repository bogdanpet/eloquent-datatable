<?php namespace Bogdanpet\Datatables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Datatable
{
    use DatatableActions;

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
     * Static instance of Datatable class
     *
     * @var Datatable
     */
    protected static $instance;

    /**
     * Datatable constructor.
     */
    public function __construct()
    {
        static::$instance = $this;
    }

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
     * Generate complete table
     *
     * @return string
     */
    public function show()
    {
        $result = $this->open() . PHP_EOL;
        $result .= $this->tableHead() . PHP_EOL;
        $result .= $this->tableBody() . PHP_EOL;
        $result .= $this->tableFoot() . PHP_EOL;
        $result .= $this->close() . PHP_EOL;
        $result .= $this->pagination();

        return $result;
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
     * Generate pagination links for paginated tables.
     *
     * @param null $class
     *
     * @return null|string
     */
    public function pagination($class = null)
    {
        if ($this->data instanceof LengthAwarePaginator) {
            if ($class != null) {
                return '<div class="' . $class . '">' . $this->data->links() . '</div>';
            }

            return '<div>' . $this->data->links() . '</div>';
        }

        return null;
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

    /**
     * Static execution of Datatable
     *
     * @param \IteratorAggregate $data
     * @param array $columns
     *
     * @return string
     */
    public static function make(\IteratorAggregate $data, array $columns, array $actions = [])
    {
        static::$instance->setData($data);
        static::$instance->setColumns($columns);
        static::$instance->setActions($actions);

        $result = static::$instance->show();

        return $result;
    }

    /**
     * Custom method for 'row_num' column heading.
     *
     * @return string
     */
    public function thRowNum()
    {
        return $this->th('#', 'small');
    }

    /**
     * Custom method for 'row_num' column cells.
     *
     * @return string
     */
    public function tdRowNum()
    {
        return $this->td($this->increment, 'small');
    }
}