<div class="container-fluid bg-white shadow p-3 mt-0 col-sm-10 col-md-9 col-lg-7 col-xl-5">
    <h3 class="pt-3">Create New District</h3>
    <?php

    use System\App\Forms\Form;

$form = new Form('/settings/district/create');
    $name = new \System\App\Forms\FormText('District Name', '', 'name');
    $createButton = new \System\App\Forms\FormButton('Create District');
    $form->addElementToCurrentRow($name)
            ->addElementToNewRow($createButton);
    echo $form->print();
    ?>
</div>


