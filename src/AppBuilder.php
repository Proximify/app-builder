<?php

/**
 * @author    Proximify Inc <support@proximify.com>
 * @copyright Copyright (c) 2020, Proximify Inc
 * @license   MIT
 */

namespace Proximify;

/**
 * Application builder for the Proximify Framework.
 *
 * Note: This component has no dependencies in order to make it maximally
 * independent of other components since it is responsible to build them.
 *
 * The component can be started manually from the CLI by:
 *
 *   $ php src/build.php
 *
 * It will then process all other components and build them one at a time.
 */
class AppBuilder
{
    const TRUSTED_NAMESPACE = 'Proximify';
    const TRUSTED_VENDOR = 'proximify';

    public function build(array $options = [], array $env = []): ?int
    {
        // Merge all options (env keys have priority)
        $env += $options + self::getopt();
        $trustedDir = self::getTrustedDir($env['vendor-dir'] ?? null);

        if (!$trustedDir) {
            return null;
        }

        self::echoMsg("Trusted dir: $trustedDir", $env);

        $names = self::getDirContents($trustedDir);
        $action = 'build';
        $count = 0;

        foreach ($names as $name) {
            if (!is_dir($path = $trustedDir . '/' . $name)) {
                continue;
            }

            if ($script = $this->getScript($action, $path)) {
                $count++;
                self::echoMsg("Running '$script' on '$path'...", $env);
                $output = $this->runScript($action, $path, $script);
                self::echoMsg($output, $env);
            }
        }

        return $count;
    }

    public static function getTrustedDir(?string $vendorDir): ?string
    {
        if (!$vendorDir) {
            $vendorDir = dirname(__DIR__, 2);

            if (basename($vendorDir) != 'vendor') {
                return null;
            }
        }

        return realpath($vendorDir . '/' . self::TRUSTED_VENDOR) ?: null;
    }

    /**
     * Get the contents of a directory.
     *
     * @param string $path The path to a directory.
     * @param array $exclude An array with filenames to exclude. Eg, ['.', '..', '.DS_Store']
     * @param boolean $skipDotFiles Whether to exclude filenames that start with a dot.
     * @return array The list of items on the directory.
     */
    public static function getDirContents($path, $exclude = false, $skipDotFiles = true)
    {
        $items = [];

        if (is_dir($path)) {
            if ($dh = opendir($path)) {
                while (($file = readdir($dh)) !== false) {
                    if ($skipDotFiles && $file && $file[0] === '.') {
                        continue;
                    }

                    $items[] = $file;
                }

                closedir($dh);
            }
        }

        return ($items && $exclude) ? array_diff($items, $exclude) : $items;
    }

    public static function execute(string $cmd, string $workDir, ?array $env = null): array
    {
        $descriptor = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($cmd, $descriptor, $pipes, $workDir, $env);

        if (!$process) {
            throw new \Exception("Cannot execute command");
        }

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        return [
            'out' => trim($stdout),
            'err' => trim($stderr),
            'code' => proc_close($process)
        ];
    }

    public function getScript(string $action, string $packageDir)
    {
        $path = $packageDir . '/composer.json';

        if (!is_file($path)) {
            return false;
        }

        $str = file_get_contents($path) ?? '';
        $scripts = json_decode($str, true)['scripts'] ?? [];

        return $scripts[$action] ?? null;
    }

    public function hasScript(string $action, string $packageDir): bool
    {
        return $this->getScript($action, $packageDir);
    }

    public function runScript(string $action, string $workDir, string $script): string
    {
        $cmd = $script;

        $std = self::execute($cmd, $workDir);

        return $std['out'];
    }

    /**
     * Get the current options set from the command line.
     *
     * @return array
     */
    public static function getopt(): array
    {
        $options = [];

        foreach ($_SERVER['argv'] ?? [] as $index => $arg) {
            if (substr($arg, 0, 2) == '--') {
                if ($arg = substr($arg, 2)) {
                    $parts = explode('=', $arg);
                    $options[$parts[0]] = $parts[1] ?? true;
                }
            } else {
                $options[$index] = $arg;
            }
        }

        return $options;
    }

    /**
     * Echo a message to the console.
     *
     * @param string $msg The message to echo.
     * @param array $options Boolean options: separator and newline. If true,
     * they are added to the output as a suffix.
     * @return void
     */
    protected static function echoMsg(string $msg, array $options = []): void
    {
        if (!($options['verbose'] ?? false)) {
            return;
        }

        if ($options['separator'] ?? false) {
            $msg .= "\n" . str_repeat('-', min(80, strlen($msg)));
        }

        if ($options['newline'] ?? true) {
            $msg .= "\n";
        }

        echo $msg;
    }
}
