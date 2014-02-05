<h1>Reaction Groups</h1>
<?php

$reactions = $data;

ee()->table->set_heading('ID', 'Name', 'Edit', 'Delete');

foreach($reactions as $reaction) {
    ee()->table->add_row(
        $reaction['reaction_group_id'],
        $reaction['reaction_group_name'],
        '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=reaction'.AMP.'method=edit_group'.AMP.'group='.$reaction['reaction_group_id'].'">edit</a>', '<a href="#">delete</a>');
}

echo ee()->table->generate();
