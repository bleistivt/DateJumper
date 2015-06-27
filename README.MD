Thanks for using this plugin.

Please go to the settings page of this plugin to enable its functionality.

regards,

Peregrine.

**Important Notes:** 

1. This plugin may not work if your forum is embedded.
2. If you modified any of these settings in your translations this plugin may not work as expected. It is crucial that the time is only displayed on the current day. If the time is displayed on other days, this plugin will not work with the current logic.

  ```php
  $Definition['Date.DefaultTimeFormat'] = '%l:%M%p'; // If in current day
  $Definition['Date.DefaultDayFormat'] = '%B %e'; // If in same year
  $Definition['Date.DefaultYearFormat'] = '%B %Y'; // If not in same year
  $Definition['Date.DefaultFormat'] = '%B %e, %Y'; // general applications
  $Definition['Date.DefaultDateTimeFormat'] = '%c'; // date/time used in attributes
  ```

3. You can change the colors by overriding the `.DiscussionDateSpacer` or `.CommentDateSpacer ` CSS class. Check out the open source community for help.
