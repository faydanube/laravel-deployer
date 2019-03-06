<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'captcha');

// Project repository
set('repository', 'https://github.com/faydanube/captcha');

set('shared_files', []);
set('shared_dirs', []);
set('writable_dirs', []);

// Hosts
host('47.93.228.155')
    ->user('root')
    ->become('www-data')
    ->set('deploy_path', '/var/www/{{application}}');

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

// 提升效率 先复制上一个版本的 node_modules 和 vendor 之后再执行 yarn production ，这样只更新下载变更你内容
add('copy_dirs', ['node_modules', 'vendor']);
before('deploy:vendors', 'deploy:copy_dirs');

// yarn production
task('deploy:yarn', function () {
    run('cd {{release_path}} && SASS_BINARY_SITE=http://npm.taobao.org/mirrors/node-sass yarn && yarn production', ['timeout' => 600]);
});
after('deploy:vendors', 'deploy:yarn');

// 缓存路由
// after('artisan:config:cache', 'artisan:route:cache');

after('deploy:failed', 'deploy:unlock');
// before('deploy:symlink', 'artisan:migrate');
