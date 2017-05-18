<?php namespace Bogdanpet\Datatables;

trait DatatableActions
{
    /**
     * Property for defining Datatable actions.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * $actions property setter.
     *
     * @param array $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Generate action button.
     *
     * @param $href
     * @param $text
     * @param array $attributes
     *
     * @return string
     */
    protected function actionButton($href, $text, array $attributes = [])
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

        return '<a href="' . $href . '" ' . $attr . '>' . $text . '</a>' . PHP_EOL;
    }

    /**
     * Custom method for 'actions' column heading.
     *
     * @return mixed
     */
    public function thActions()
    {
        return $this->th('Actions');
    }

    /**
     * Custom method for 'actions' column cells.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function tdActions($model)
    {
        $buttons = null;

        // Create action button for each action from $actions array.
        foreach ($this->actions as $action) {
            $href = $action[1];

            // Look for {wildcard}
            preg_match('/{.+}/', $href, $matches);

            // If there is {wildcard} change url
            if ( ! empty($matches)) {
                $wildcard = trim($matches[0], '{}');
                $wildcard = $model->$wildcard;
                $href     = preg_replace('/{.+}/', $wildcard, $href);
            }

            // Create button
            $buttons .= $this->actionButton(
                $href,
                $action[0],
                isset($action[2]) ? $action[2] : []
            );
        }

        return $this->td($buttons);
    }
}