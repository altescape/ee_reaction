<?
$reaction = $data[0];

$reactions_names = unserialize($reaction['reactions']);

echo form_open($action);

$this->table->set_heading(
    lang('Group name'),
    lang('')
);

$this->table->add_row(
    '<strong>'.lang('reaction_group_name').'</strong>',
    array(
        'style'	=> 'width:50%',
        'data'	=> form_input(array('name'=>'group_name','id'=> 'group_name','value'=>$reaction['reaction_group_name'],'type'=>'text'))
    )
);
echo $this->table->generate();

$this->table->set_heading(
    lang('Reaction No.'),
    lang('Name')
);

$i = 1;
foreach($reactions_names as $reaction_name) {
    $this->table->add_row(
        '<strong>'.lang('reaction_label').' '.$i.'</strong>',
        array(
            'style'	=> 'width:50%',
            'data'	=> form_input(array('name'=>'r'.$i,'id'=> 'r'.$i,'value'=>$reaction_name,'type'=>'text'))
        )
    );
    $i++;
}

echo $this->table->generate(); ?>

<?= form_input(array('name'=>'id','id'=> 'id','value'=>$reaction['reaction_group_id'],'type'=>'hidden')); ?>

<p><?=form_submit(array('name'=>'submit','value'=>lang('update'),'class'=>'submit'));?></p>
<?=form_close();?>

