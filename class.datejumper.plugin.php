<?php

$PluginInfo['DateJumper'] = array(
    'Version' => '1.7',
    'Name' => 'Date Jumper',
    'Description' => 'Place a Date Label above discussions and comments for easier viewing of posts by date. Click on date label option to go to next date.',
    'SettingsUrl' => 'settings/datejumper',
    'SettingsPermission' => 'Garden.Settings.Manage',
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
        if ($args['Discussion']->Announce == 1 || ($args['Discussion']->Announce == 2 && $sender->ClassName === 'CategoriesController') ) {
        	return;
	    }
        $date = Gdn_Format::Date($args['Discussion']->LastDate);
        if ($date != $this->KeepDate) {
            if (!strpos($date, ':')) {
                echo wrap(wrap($date, 'span', array('class' => 'DiscussionDateSpacer')), 'li');
            } elseif (!strpos($this->KeepDate, ':')) {
                echo wrap(wrap(t('Today'), 'span', array('class' => 'DiscussionDateSpacer')), 'li');
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
                echo wrap(wrap(t('Today'), 'div', array('class' => 'CommentDateSpacer')), 'li');
                $this->today = true;
            }
        }
    }
    
    public function settingsController_dateJumper_create($sender) {
        $sender->permission('Garden.Settings.Manage');
        $sender->addSideMenu('plugin/datejumper');

        $conf = new ConfigurationModule($sender);
        $conf->initialize([
            'Plugins.DateJumper.ShowInDiscussions' => [
                'Control' => 'CheckBox',
                'LabelCode' => 'Show Date Jumper Labels on Discussion Topic Pages'
            ],
            'Plugins.DateJumper.ShowInComments' => [
                'Control' => 'CheckBox',
                'LabelCode' => 'Show Date Jumper Labels within Comments in a Discussion'
            ]
        ]);

        $sender->title('Date Jumper');
        $sender->setData('Description', 'See readme for instructions for further details.');
        $conf->renderAll();
    }

}
