# Demo Framework (DF)
[![Travis build status](https://api.travis-ci.org/acquia/df.svg?branch=5.x)](https://travis-ci.org/acquia/df) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/acquia/df/badges/quality-score.png?b=5.x)](https://scrutinizer-ci.com/g/acquia/df/?branch=5.x)

Demo Framework is a distribution consisting of modules, themes and libraries. It highlights powerful features created by the Drupal community. It is intended to be used as a starterkit for promoting enterprise-ready solutions.

Demo Framework is powered by [Acquia CMS](https://www.acquia.com/products/drupal-cloud/acquia-cms).

## Installing Demo Framework

The preferred way to install Demo Framework is using our [Composer-based project template][template]. It's easy!

Once you have a docroot built, you can use DDev, Acquia Developer Studio or any other similar project to get started.

  ``ddev config --project-name df --project-type drupal9 && ddev start``

Now use the ``site-install`` command to install Drupal with the DF installation profile.

  ``drush si df``

You may now login to your site.

  ``drush uli``

Installation may take a few minutes depending on your environment. ☕️

Then login to your site.

  ``drush uli``

## Deploying Demo Framework using version control

If you are using version control to deploy the Demo Framework to a server (such as Acquia Cloud), note that you must edit the file `/profiles/df/.gitignore` and remove the following lines:

```
# Contrib
modules/contrib/*
themes/contrib/*

# Libraries
libraries/*
```

If you do not do so, you will see an error in the installation referring to missing modules.

## Resources

Please file issues in our [drupal.org issue queue][issue_queue].

[issue_queue]: https://www.drupal.org/project/issues/df "Demo Framework Issue Queue"
[template]: https://github.com/acquia/df-project "Composer-based project template"
[d.o_semver]: https://www.drupal.org/node/1612910
[df_composer_project]: https://github.com/acquia/df-project
