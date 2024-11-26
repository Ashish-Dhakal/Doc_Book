<?php
namespace App\View\Components;

use Illuminate\View\Component;

class SelectInput extends Component
{
    public $name;
    public $value;
    public $options;

    public function __construct($name, $value = null, $options = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->options = $options;
    }

    public function render()
    {
        return view('components.select-input');
    }
}
