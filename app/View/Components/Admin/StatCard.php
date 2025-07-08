<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class StatCard extends Component
{
    public $title;
    public $value;

    public function __construct($title, $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.admin.stat-card');
    }
}
