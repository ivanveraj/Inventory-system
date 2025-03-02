<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ModalB extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public  $title, $modalId, $modalTitle;
    public function __construct($modalId, $title, $modal_title = "modalTitle")
    {
        $this->modalId = $modalId;
        $this->title = $title;
        $this->modalTitle = $modal_title;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal-b');
    }
}
