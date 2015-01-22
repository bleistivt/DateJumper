<?php if(!defined('APPLICATION')) die();

$PluginInfo['DateJumper'] = array(
    'Version' => '1.6',
    'Name' => 'Date Jumper',
    'Description' => 'Place a Date Label above discussions and comments for easier viewing of posts by date. Click on date label option to go to next date.',
    'SettingsUrl' => '/dashboard/settings/datejumper',
    'MobileFriendly' => TRUE,
    'Author' => 'peregrine'
);

class DateJumperPlugin extends Gdn_Plugin {

    public function Base_Render_Before($Sender) {

        $Controller = $Sender->ControllerName;
        $ShowOnController = array(
            'discussioncontroller',
            'discussionscontroller',
            'categoriescontroller'
        );
        if (!InArrayI($Controller, $ShowOnController))
            return;
        $Sender->AddJsFile($this->GetResource('js/datejumper.js', FALSE, FALSE));
    }

    public function SettingsController_DateJumper_Create($Sender) {
        $Session = Gdn::Session();
        $Sender->Title('Date Jumper');
        $Sender->AddSideMenu('plugin/datejumper');
        $Sender->Permission('Garden.Settings.Manage');
        $Sender->Form = new Gdn_Form();
        $Validation = new Gdn_Validation();
        $ConfigurationModel = new Gdn_ConfigurationModel($Validation);
        $ConfigurationModel->SetField(array(
            'Plugins.DateJumper.ShowInDiscussions',
            'Plugins.DateJumper.ShowInComments'
        ));
        $Sender->Form->SetModel($ConfigurationModel);


        if ($Sender->Form->AuthenticatedPostBack() === FALSE) {
            $Sender->Form->SetData($ConfigurationModel->Data);
        } else {
            $Data = $Sender->Form->FormValues();

            if ($Sender->Form->Save() !== FALSE)
                $Sender->StatusMessage = T("Your settings have been saved.");
        }
        $Sender->Render($this->GetView('datejumper-settings.php'));
    }

    public function DiscussionsController_BeforeDiscussionName_Handler($Sender) {
        if (C('Plugins.DateJumper.ShowInDiscussions', FALSE)) {
            $this->DisplayDiscussionDateHeading($Sender);
        }
    }

    public function CategoriesController_BeforeDiscussionName_Handler($Sender) {
        if (C('Plugins.DateJumper.ShowInDiscussions', FALSE)) {
            $this->DisplayDiscussionDateHeading($Sender);
        }
    }

    public function DiscussionsController_BetweenDiscussion_Handler($Sender) {
        if (C('Plugins.DateJumper.ShowInDiscussions', FALSE)) {
            $this->DisplayDiscussionDateHeading($Sender);
        }
    }

    public function CategoriesController_BetweenDiscussion_Handler($Sender) {
        if (C('Plugins.DateJumper.ShowInDiscussions', FALSE)) {
            $this->DisplayDiscussionDateHeading($Sender);
        }
    }

    public function DiscussionController_BeforeCommentDisplay_Handler($Sender) {
        if (C('Plugins.DateJumper.ShowInComments', FALSE)) {
            $this->DisplayDateCommentHeading($Sender);
        }
    }

    private function DisplayDiscussionDateHeading($Sender) {
        $Discussion = $Sender->EventArguments["Discussion"];

        static $KeepDate;
        $CurDate = Gdn_Format::Date($Discussion->LastDate);
        if ($CurDate <> $KeepDate) {



            if (!strpos($CurDate, ":")) {
                echo wrap(wrap($CurDate, 'span', array('class' => "DiscussionDateSpacer")), 'li');
            } else {
              if (!strpos($KeepDate, ":")) {
              echo wrap(wrap(T("Discussion Today"), 'span', array('class' => "DiscussionDateSpacer")), 'li');
              }
            }
            $KeepDate = $CurDate;
        }
   }

    private function DisplayDateCommentHeading($Sender) {
        $Comment = $Sender->EventArguments["Comment"];

        static $KeepDate;
        static $x = 1;
        $CurDate = Gdn_Format::Date($Comment->DateInserted);

        if ($CurDate <> $KeepDate) {
            $KeepDate = $CurDate;
            if (!strpos($CurDate, ":")) {
                $CommentDateHeader = wrap(wrap($CurDate, 'div', array('class' => "CommentDateSpacer")), 'li');
                echo $CommentDateHeader;
            } else {
                if ($x < 2) {
                    $CommentDateHeader = wrap(wrap(T("Comment Today"), 'div', array('class' => "CommentDateSpacer")), 'li');
                    echo $CommentDateHeader;
                    $x++;
                }
            }
        }
    }

    public function AssetModel_StyleCss_Handler($Sender, $Args) {
        $Sender->AddCssFile('datejumper.css', 'plugins/DateJumper');
    }

}
