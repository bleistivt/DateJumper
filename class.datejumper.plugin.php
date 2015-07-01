<?php

$PluginInfo['DateJumper'] = array(
    'Version' => '1.7',
    'Name' => 'Date Jumper',
    'Description' => 'Places a date label above discussions and comments to separate them by age.',
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

    public function discussionsController_render_before($sender) {
        $this->addResources($sender);
    }

    public function discussionsController_beforeDiscussionName_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args);
    }

    public function discussionsController_betweenDiscussion_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args);
    }

    /* CategoriesController */

    public function categoriesController_render_before($sender) {
        $this->addResources($sender);
    }

    public function categoriesController_beforeDiscussionName_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args, true);
    }

    public function categoriesController_betweenDiscussion_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args, true);
    }

    /* DiscussionController */

    public function discussionController_render_before($sender) {
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
        $sender->addSideMenu();

        $conf = new ConfigurationModule($sender);
        $conf->initialize(array(
            'Plugins.DateJumper.ShowInDiscussions' => array(
                'Control' => 'CheckBox',
                'LabelCode' => 'Show Date Jumper Labels on Discussion Topic Pages'
            ),
            'Plugins.DateJumper.ShowInComments' => array(
                'Control' => 'CheckBox',
                'LabelCode' => 'Show Date Jumper Labels within Comments in a Discussion'
            )
        ));

        $sender->title('Date Jumper');
        $sender->setData('Description', 'See readme for instructions for further details.');
        $conf->renderAll();
    }

}
