<?php

namespace App\Presenters;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    private $db;
    
    public function __construct(Nette\Database\Context $db)
    {
        $this->db=$db;
    }

    public function renderDefault()
    {
        $this->template->posts=$this->db->table('posts')->order('date DESC')->limit(5);
    }
}
