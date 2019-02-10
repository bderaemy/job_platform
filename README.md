# Job platform demo - Drupal website


This is the code base of a job platform demo.
It's a [dockerized](https://www.docker.com/) and [Composer](https://getcomposer.org/) based project : only custom theme and modules are commited within this repo, all other dependencies are fetch at build time by composer.

## Usage

###1) Install the following tools : 
* Docker
* Docker-compose
* IDE (e.g. PHPStorm) properly configured to handle Drupal app (e.g. see [Drupal Development using PhpStorm](https://confluence.jetbrains.com/display/PhpStorm/Drupal+Development+using+PhpStorm))

###2) Fetch the latest files/DB from prod
* Fetch files from prod and copy them to /app/sites/default/files
* Dump prod db and put it at the root of this project into directories you will name /website-data-dump/db/your_db_dump.sql (website-data-dump should be gitignored (do not add it as a git submodule))

After that you can run the installer :

```
$ ./scripts/local.deploy.sh
```

Finally, check the IP address of your container and adapt your /etc/hosts file to target http://xeer.localhost, e.g. :

```
172.21.0.3      xeer.localhost
```

Check that everything went well by opening your browser on your host targeting http://xeer.localhost:8042/


## What does the local installer do?

The installer will create a local img and container Apache/PHP, install the project using composer :

* Drupal will be installed in the `web`-directory.
* Autoloader is implemented to use the generated composer autoloader in `vendor/autoload.php`,
  instead of the one provided by Drupal (`web/vendor/autoload.php`).
* Modules (packages of type `drupal-module`) will be placed in `web/modules/contrib/`
* Theme (packages of type `drupal-theme`) will be placed in `web/themes/contrib/`

On local env, the img is extended with additional debugging and testing utilities.

Finally it will enable the local configuration settings, and import the DB and mounts volumes for all custom themes, modules, configurations files, settings and files (targeting the website-data-dump/files repo)


## Updating Drupal Core

This project will attempt to keep all of your Drupal Core files up-to-date; the 
project [drupal-composer/drupal-scaffold](https://github.com/drupal-composer/drupal-scaffold) 
is used to ensure that your scaffold files are updated every time drupal/core is 
updated. If you customize any of the "scaffolding" files (commonly .htaccess), 
you may need to merge conflicts if any of your modified files are updated in a 
new release of Drupal core.

Follow the steps below to update your core files.

1. Run `composer update drupal/core webflo/drupal-core-require-dev symfony/* --with-dependencies` to update Drupal Core and its dependencies.
1. Run `git diff` to determine if any of the scaffolding files have changed. 
   Review the files for any changes and restore any customizations to 
  `.htaccess` or `robots.txt`.
1. Commit everything all together in a single commit, so `web` will remain in
   sync with the `core` when checking out branches or running `git bisect`.
1. In the event that there are non-trivial conflicts in step 2, you may wish 
   to perform these steps on a branch, and use `git merge` to combine the 
   updated core files with your customized files. This facilitates the use 
   of a [three-way merge tool such as kdiff3](http://www.gitshah.com/2010/12/how-to-setup-kdiff-as-diff-tool-for-git.html). This setup is not necessary if your changes are simple; 
   keeping all of your modifications at the beginning or end of the file is a 
   good strategy to keep merges easy.


## FAQ

### Should I commit the contrib modules I download?

The answer is **no**. 
If patches are required, [use composer](http://www.anexusit.com/blog/how-to-apply-patches-drupal-8-composer) or list them in the `app/patches` directory and apply them through the `docker-build/docker-entrypoint.sh` cmd

### Should I commit the scaffolding files?

Same as above : **no**

The [drupal-scaffold](https://github.com/drupal-composer/drupal-scaffold) plugin will take care of downloading the scaffold files (like
index.php ) to the web/ directory of the project. 
