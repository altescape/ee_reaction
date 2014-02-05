<?
echo form_open($action);

$this->table->set_heading(
    lang('Group name'),
    lang('')
);

$this->table->add_row(
    '<strong>'.lang('reaction_group_name').'</strong>',
    array(
        'style'	=> 'width:50%',
        'data'	=> form_input(array('name'=>'group_name','id'=> 'group_name','value'=>'','type'=>'text'))
    )
);
echo $this->table->generate();

$this->table->set_heading(
    lang('Reaction No.'),
    lang('Name')
);

for($i = 1; $i <= 5; $i++){
    $this->table->add_row(
        '<strong>'.lang('reaction_label').' '.$i.'</strong>',
        array(
            'style'	=> 'width:50%',
            'data'	=> form_input(array('name'=>'r'.$i,'id'=> 'r'.$i,'value'=>'','type'=>'text'))
        )
    );
}

echo $this->table->generate(); ?>

<p><?=form_submit(array('name'=>'submit','value'=>lang('update'),'class'=>'submit'));?></p>
<?=form_close();?>

