<?php
require_once __DIR__.'/vendor/autoload.php';
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\IpUtils;
use \Symfony\Component\HttpFoundation\Request;

if (!file_exists(__DIR__.'/config.yml')) {
    echo "Please, define your satis configuration in a config.yml file.\nYou can use the config.yml.dist as a template.";
    exit(-1);
}

$request = Request::createFromGlobals();

$defaults = array(
    'bin' => 'bin/satis',
    'json' => 'satis.json',
    'webroot' => 'web/',
    'user' => null,
    'authorized_ips' => null
);
$config = Yaml::parse(__DIR__.'/config.yml');
$config = array_merge($defaults, $config);

if (null !== $config['authorized_ips']) {
    $ip = $request->getClientIp();
    $authorized = false;

    if (is_array($config['authorized_ips'])) {
        foreach ($config['authorized_ips'] as $authorizedIp) {
            $authorized = IpUtils::checkIp($ip, $authorizedIp);
            if ($authorized) {
                break;
            }
        }
    } else {
        $authorized = IpUtils::checkIp($ip, $config['authorized_ips']);
    }

    if (! $authorized) {
        http_response_code(403);
        exit(-1);
    }
}
var_dump(getcwd(), $config['bin']);
$errors = array();
if (!file_exists($config['bin'])) {
    $errors[] = 'The Satis bin could not be found.';
}

if (!file_exists($config['json'])) {
    $errors[] = 'The satis.json file could not be found.';
}

if (!file_exists($config['webroot'])) {
    $errors[] = 'The webroot directory could not be found.';
}

if (!empty($errors)) {
    echo 'The build cannot be run due to some errors. Please, review them and check your config.yml:'."\n";
    foreach ($errors as $error) {
        echo '- '.$error."\n";
    }
    exit(-1);
}

$builder = new ProcessBuilder(array('php', $config['bin'], 'build', $config['json'], $config['webroot']));
$process = $builder->getProcess();
var_dump($process);
$exitCode = $process->run(function ($type, $buffer) {
    if ('err' === $type) {
        echo 'E';
        error_log($buffer);
    } else {
        echo '.';
    }
});
echo "\n\n" . ($process->getOutput());
echo "\n\n" . ($exitCode === 0 ? 'Successful rebuild!' : 'Oops! An error occured!') . "\n";
