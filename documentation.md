# Documentation

In this documentation, I will explain what happens when a report comes in. This very much depends on how you setup the plugin. By default, when a report comes in the plugin will automatically check the answers of **Enable Plugin** and **Restrict Staff**. Depending on the answer given, will see the plugin either stop there or continue. See the rest of the process below.

A report comes in > Is it a spam report? > If yes, see **Spam Detection below**. If no, see **General detection**.

## Spam Detection

By default, the plugin will check if the Spam Detection module is activated in the settings. If set to no, the script will exit and MyBB will handle the report as usual.

1. Has the content been reported at least X amount of times? If no, exit. 
2. Check to see if the user reporting the post is genuine. If no, exit.
3. Run genuine test on author of reported content (See below to see what this consists of). Determine score.
4. If score is `1` or `0` > Unapprove content. Log action.
5. If score is higher than 1, is Purge Spammer setting enabled? If no, Unapprove content and Moderate user. Log action.
6. If yes, Purge Spammer. Log action.

## General Detection

1. Check to see if the user reporting the post is genuine. If no, exit.
2. Unapprove content. 
3. Has author of reported content have more than X amount of reported content and has an active warning? If yes, Moderate user. Log action.
4. If no, has author of reported content have more than X amount of reported content? If no, Unapprove post and Log action.
5. If yes, does plugin have permission to warn users? If yes, warn user. Log action.
6. If no, send Friendly Reminder Private Message. Log action. 

## Account Genuine test

Based on the given settings, the user reporting the content has to have more than `X` amount of posts, `zero` warnings, and have been registered for `X` amount of time. If unsuccessful, an action towards the autor of the content may not be warranted. 

When the author of reported content participates in the Account Genuine test, if they fail based on the conditions listed above, it will then determine a score (for spam detection). Based on that score, their content may be unapproved, purged or moderated.
