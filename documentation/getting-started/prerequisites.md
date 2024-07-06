---
description: This plugin requires a Firebase project to work.
---

# Prerequisites

For the plugin to work, you need to have an existing Firebase project or create one if not.

## Creating a new Firebase project

1. In your Firebase dashboard, click Add Project.
2. Complete the steps by filling in the necessary information.
3. Navigate into the newly created project.

## Setup the sign-in methods

For each sign-in provider to work on the WordPress site, each must be configured according to its requirements.

### Google

1. From your Firebase dashboard, click on Authentication to navigate to this page.
2. Click the "Sign-in method" tab.
3. Click Google Provider.
4. Toggle on the Enable switch.
5. Fill in your public-facing project name and support email.
6. Click the Save button to enable and finish the configuration.

### Facebook

1. From your Firebase dashboard, click on Authentication to navigate to this page.
2. Click the "Sign-in method" tab.
3. Click Facebook Provider.
4. Toggle on the Enable switch.
5. Go to your Meta Developer app and copy-paste the App ID and secret.
6. Click the Save button to enable and finish the configuration.
7. Copy the handler callback URL provided by Firebase.
8. From your Meta Developer dashboard, navigate to your Facebook login product. Under settings, paste the callback URL to the Valid OAuth Redirect URIs. Here is a [helpful video](https://youtu.be/9ztk1hKmcI0?si=UCTrOBdwhEGV7dav) on how to navigate this step.

