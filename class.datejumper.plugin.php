<?php

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

    public function discussionsController_beforeDiscussionContent_handler($sender, $args) {
        $this->discussionDateHeading($sender, $args);
    }


    /* CategoriesController */

    public function categoriesController_render_before($sender) {
        $this->addResources($sender);
    }

    public function categoriesController_beforeDiscussionContent_handler($sender, $args) {
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
                echo wrap(wrap($date, 'div', ['class' => 'CommentDateSpacer']), 'li');
            } elseif (!$this->today) {
                echo wrap(wrap(t('Today'), 'div', ['class' => 'CommentDateSpacer']), 'li');
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
                echo wrap(wrap($date, 'span', ['class' => 'DiscussionDateSpacer']), 'div');
            } elseif (!strpos($this->keepDate, ':')) {
                echo wrap(wrap(t('Today'), 'span', ['class' => 'DiscussionDateSpacer']), 'div');
            }
            $this->keepDate = $date;
        }
    }


    public function settingsController_dateJumper_create($sender) {
        $sender->permission('Garden.Settings.Manage');

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
        $sender->setData('Description', 'See README.MD for instructions and further details.');
        $conf->renderAll();
    }

}
