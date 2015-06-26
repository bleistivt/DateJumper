<?php

$PluginInfo['DateJumper'] = array(
    'Version' => '1.6',
    'Name' => 'Date Jumper',
    'Description' => 'Place a Date Label above discussions and comments for easier viewing of posts by date. Click on date label option to go to next date.',
    'SettingsUrl' => 'settings/datejumper',
    'MobileFriendly' => true,
    'Author' => 'peregrine',
    'License' => 'GNU GPL2'
);

class DateJumperPlugin extends Gdn_Plugin {

    private $KeepDate;

    public function Base_Render_Before($sender) {
        $ShowOnController = array(
            'discussioncontroller',
            'discussionscontroller',
            'categoriescontroller'
        );
        if (in_array(strtolower($sender->ControllerName), $ShowOnController))
            $sender->AddJsFile('datejumper.js', 'plugins/DateJumper');
        }
    }

    public function AssetModel_StyleCss_Handler($sender) {
        $sender->AddCssFile('datejumper.css', 'plugins/DateJumper');
    }

    public function SettingsController_DateJumper_Create($sender) {
        $sender->Title('Date Jumper');
        $sender->AddSideMenu('plugin/datejumper');
        $sender->Permission('Garden.Settings.Manage');
        $sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.DateJumper.ShowInDiscussions',
            'Plugins.DateJumper.ShowInComments'
        ));
        $sender->Form->SetModel($ConfigurationModel);

        if (!$sender->Form->AuthenticatedPostBack()) {
            $sender->Form->SetData($ConfigurationModel->Data);
        } elseif ($sender->Form->Save()) {
            $sender->informMessage = t('Your settings have been saved.');
        }
        $sender->Render('datejumper-settings', '', 'plugins/DateJumper');
    }

    public function DiscussionsController_BeforeDiscussionName_Handler($sender) {
        $this->DisplayDiscussionDateHeading($sender);
    }

    public function CategoriesController_BeforeDiscussionName_Handler($sender) {
        $this->DisplayDiscussionDateHeading($sender);
    }

    public function DiscussionsController_BetweenDiscussion_Handler($sender) {
        $this->DisplayDiscussionDateHeading($sender);
    }

    public function CategoriesController_BetweenDiscussion_Handler($sender) {
        $this->DisplayDiscussionDateHeading($sender);
    }

    private function DisplayDiscussionDateHeading($sender, $args) {
        if (!C('Plugins.DateJumper.ShowInDiscussions', false)) {
            return;
        }
        $date = Gdn_Format::Date($args['Discussion']->LastDate);
        if ($date != $this->KeepDate) {
            if (!strpos($date, ':')) {
                echo wrap(wrap($date, 'span', array('class' => 'DiscussionDateSpacer')), 'li');
            } elseif (!strpos($this->KeepDate, ':')) {
                echo wrap(wrap(t('Discussion Today'), 'span', array('class' => 'DiscussionDateSpacer')), 'li');
            }
            $this->KeepDate = $date;
        }
    }

    public function DiscussionController_BeforeCommentDisplay_Handler($sender, $args) {
        if (!C('Plugins.DateJumper.ShowInComments', false)) {
            return;
        }
        $date = Gdn_Format::Date($args['Comment']->DateInserted);
        if ($date != $this->KeepDate) {
            $this->KeepDate = $date;
            if (!strpos($date, ':')) {
                echo wrap(wrap($date, 'div', array('class' => 'CommentDateSpacer')), 'li');
            } elseif (!$this->today) {
                echo wrap(wrap(t('Comment Today'), 'div', array('class' => 'CommentDateSpacer')), 'li');
                $this->today = true;
            }
        }
    }

}
