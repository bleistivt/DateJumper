<?php if (!defined('APPLICATION')) exit(); ?>

<h1><?php echo T('Date Jumper | by: Peregrine'); ?></h1>

<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<h3> See readme for instructions for further details. </h3>
<h3> Options </h3>
<ul>
    <li>
        <?php
        echo $this->Form->CheckBox('Plugins.DateJumper.ShowInDiscussions', "Show Date Jumper Labels on Discussion Topic Pages");
        ?>
    </li>
    <li>
        <?php
        echo $this->Form->CheckBox('Plugins.DateJumper.ShowInComments', "Show Date Jumper Labels within Comments in a Discussion");
        ?>
    </li>
</ul>

<?php echo $this->Form->Close('Save');
