<?php

use App\Models\View\CardPrinter;
use System\App\Forms\Form;
use System\App\Forms\FormButton;
use System\App\Forms\FormText;
use App\Models\District\Group;

/* @var $group Group */
$group = $this->group;
//var_dump($this->group);

echo CardPrinter::printCard($this->group, $this->user);
