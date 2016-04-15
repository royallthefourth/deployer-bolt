# deployer-bolt
Example Deployer deployment configuration for Bolt CMS

To setup an existing Bolt site to use Deployer, first make a copy of your site somewhere.

1. Import your code into git. Use the example .gitignore to avoid committing user uploads and vendor libraries. Deployer will manage the uploads in their own directory and install libraries.
1. Place deploy.php file in your project root. Edit to suit your configuration.
1. Run `dep deploy production` to send your code out to the server.
1. Copy the contents of your `web/files` directory from the copy of your old site to the new `shared` directory like so:
    `cp -R ../example.com.old/web/files/* shared/web/files/`

If all of the paths are correct, your site ought to reliably deploy and rollback now.
