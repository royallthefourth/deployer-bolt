<?php

// All Deployer recipes are based on `recipe/common.php`.
require_once __DIR__ . '/vendor/deployer/deployer/recipe/common.php';

task('deploy:permissions', function () {
  $releases = env('releases_list');

  run("mkdir {{deploy_path}}/releases/{$releases[0]}/app/cache");
  run("mkdir {{deploy_path}}/releases/{$releases[0]}/app/database");
  run("mkdir {{deploy_path}}/releases/{$releases[0]}/web/extensions");

  // in my repository, config_local is for the development environment.
  // The Bolt documentation makes a different recommendation.
  run("rm {{deploy_path}}/releases/{$releases[0]}/app/config/config_local.yml");

  // my http user doesn't have shell access
  run("sudo chown -R http:http {{deploy_path}}/releases/{$releases[0]}");
  run("sudo chmod -R 755 {{deploy_path}}/releases/{$releases[0]}");

})->desc('Set ownership and permissions');

task('clean', function () {
  $releases = env('releases_list');

  $keep = get('keep_releases');

  while ($keep > 0) {
    array_shift($releases);
    --$keep;
  }

  foreach ($releases as $release) {
    run("sudo rm -rf {{deploy_path}}/releases/$release");
  }

  run("cd {{deploy_path}} && if [ -e release ]; then rm release; fi");
  run("cd {{deploy_path}} && if [ -h release ]; then rm release; fi");

  run("sudo systemctl restart php-fpm");

})->desc('Cleaning up old releases');

task('deploy', [
  'deploy:prepare',
  'deploy:release',
  'deploy:update_code',
  'deploy:vendors',
  'deploy:shared',
  'deploy:writable',
  'deploy:symlink',
  'deploy:permissions',
  'clean'
]);

//bolt shared dirs
set('shared_dirs', [
  'web/files',
  'web/thumbs'
]);

//bolt Writable dirs
set('writable_dirs', [
  'app/config',
  'extensions'
]);

set('http_user', 'http');

// Define a server for deployment.
server('prod', 'example.com', 22)
  ->user('royall')
  ->identityFile() // You can use identity key, ssh config, or username/password to auth on the server.
  ->stage('production')
  ->env('deploy_path', '/srv/http/example.com'); // Define the base path to deploy your project to.

// Specify the repository from which to download your project's code.
// The server needs to have git installed for this to work.
// If you're not using a forward agent, then the server has to be able to clone
// your project from this repository.
set('repository', 'https://username:token@github.com/username/example.git');
