api = 2
core = 8.x
includes[] = drupal-org-core.make

projects[lightning][type] = "profile"
projects[lightning][download][type] = "git"
projects[lightning][download][branch] = "8.x-1.x"
projects[lightning][download][url] = "https://github.com/acquia/lightning.git"

projects[df][type] = "profile"
projects[df][download][type] = "git"
projects[df][download][branch] = "8.x-lightning"
projects[df][download][url] = "https://github.com/acquia/df.git"
