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

    private $keepDate;

    public function assetModel_styleCss_handler($sender) {
        $sender->addCssFile('datejumper.css', 'plugins/DateJumper');
    }

    private function addResources($sender) {
        $sender->addJsFile('datejumper.js', 'plugins/DateJumper');
    }

    /* DiscussionsController */

    public function discussionsController_Render_before($sender) {
        $this->addResources($sender);
    }

    public function discussionsController_beforeDiscussionName_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args);
    }

    public function discussionsController_betweenDiscussion_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args);
    }

    /* CategoriesController */

    public function categoriesController_Render_before($sender) {
        $this->addResources($sender);
    }

    public function categoriesController_beforeDiscussionName_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args, true);
    }

    public function categoriesController_betweenDiscussion_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args, true);
    }

    /* DiscussionController */

    public function discussionController_Render_before($sender) {
        $this->addResources($sender);
    }

    public function discussionController_beforeCommentDisplay_handler($sender, $args) {
        if (!C('Plugins.DateJumper.ShowInComments')) {
            return;
        }
        $date = Gdn_Format::date($args['Comment']->DateInserted);
        if ($date != $this->keepDate) {
            $this->keepDate = $date;
            if (!strpos($date, ':')) {
                echo wrap(wrap($date, 'div', array('class' => 'CommentDateSpacer')), 'li');
            } elseif (!$this->today) {
                echo wrap(wrap(t('Today'), 'div', array('class' => 'CommentDateSpacer')), 'li');
                $this->today = true;
            }
        }
    }

    private function discussionDateHeading($sender, $args, $inCategory = false) {
        if (!C('Plugins.DateJumper.ShowInDiscussions')) {
            return;
        }
        if ($args['Discussion']->Announce == 1 || ($args['Discussion']->Announce == 2 && $inCategory)) {
            return;
        }
        $date = Gdn_Format::date($args['Discussion']->LastDate);
        if ($date != $this->keepDate) {
            if (!strpos($date, ':')) {
                echo wrap(wrap($date, 'span', array('class' => 'DiscussionDateSpacer')), 'li');
            } elseif (!strpos($this->keepDate, ':')) {
                echo wrap(wrap(t('Today'), 'span', array('class' => 'DiscussionDateSpacer')), 'li');
            }
            $this->keepDate = $date;
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
