<?php
namespace MyForksFiles\FixThis;

use Lang;
use File;
use Illuminate\Console\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

/**
 * Class FixThis
 * @package MyForksFiles\FixThis
 */
class FixThisCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'fix:this';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'A super duper brilliant Laravel command to detect and fix or solve development problem.';

    /**
     * @var obj
     */
    protected $fix = [];

    /**
     * FixProblems constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->fix['status'][] = 'init';
        $this->app = $this->getApplication();
        $this->warn(PHP_EOL . $this->description);
        $this->info($this->t('init'));
        $this->showProgressBar();
        if ($this->confirm($this->t('are-you-sure?'))) {
            $this->fix['cnt'] = $this->langCounters();
            $this->detectAndFixThis();
        }
    }

    /**
     * Count and set counters from translations.
     */
    protected function langCounters()
    {
        return [
            'quote' => count(Lang::get('messages.quote')),
            'yes' => count(Lang::get('messages.yes')),
            'no' => count(Lang::get('messages.no')),
            'errors' => count(Lang::get('messages.errors')),
        ];
    }

    /**
     * Let's play.
     */
    protected function detectAndFixThis()
    {
        (array)$quotes = $this->randomQuote();
        (array)$fixes = $this->randomiseFixes();
        $this->fix['status'][] = __METHOD__;
        $this->initFixThis();

        $_i = 0;
        foreach ($fixes as $value) {
            $this->msg(PHP_EOL . PHP_EOL . ' >>> ' . $this->t(strtolower($value)) . '    ' . PHP_EOL, 'white', 'blue');
            $this->showProgressBar((int)100 / $this->getRandom());
            $this->yourChoice($this->letChoose(strtolower($value)));
            $this->$value();
            (isset($quotes[$_i])) ? $this->msg($quotes[$_i], 'magenta') : '';
            $_i++;
        }

        $this->msg(PHP_EOL . PHP_EOL . $this->t('diduknow') . PHP_EOL . $this->t('end-summary') . PHP_EOL . PHP_EOL, 'black', 'yellow');
        $this->showProgressBar(100);
        $this->msg(PHP_EOL . PHP_EOL . $this->t('call') . PHP_EOL . PHP_EOL, 'red');
    }

    /**
     *
     */
    protected function initFixThis()
    {
        $this->info($this->t('checking-console'));
        ($this->getLaravel()->runningInConsole()) ? $this->error($this->t('console-ok')) : $this->info($this->t('fail'));
        $this->line(PHP_EOL);
    }

    /**
     * @param $string
     * @return string
     */
    protected function t($string)
    {
        $key = 'fixthis::fixthis.' . $string;
        return (Lang::has($key)) ? Lang::get($key) : $string;
    }

    /**
     * @return array
     */
    protected function fixesList()
    {
        return [
            'sysInfo',
            'getLogs',
            'cpuInfo',
            'lvAscii',
            'cowSay',
        ];
    }

    /**
     * @return array
     */
    protected function randomiseFixes()
    {
        $fixes = $this->fixesList();
        $results = [];
        foreach ($this->getRandomise(count($fixes)) as $value) {
            $results[] = $fixes[$value - 1];
        }

        return $results;
    }

    /**
     * @param int $counter
     */
    protected function showProgressBar($counter = 10)
    {
        $this->info(PHP_EOL);
        $jumpBack = 1;//rand(1,5);
        $bar = $this->output->createProgressBar($counter);
        for ($_i = 0; $_i < $counter; $_i++) {
            if ($jumpBack && $_i == 6) {
                $jumpBack--;
                $_i = 1;
            }
            usleep(60000);
            $bar->advance();
        }
        $bar->finish();
        $this->info(PHP_EOL);
    }

    /**
     * @return array
     */
    protected function getSysInfo()
    {
        $data = [];
        $data = [
            'env' => $this->laravel->environment(),
            'version' => $this->laravel->version(),
            'php' => trim(exec('php -v')),
            'disabled' => ini_get('disable_functions'),
            'mysql' => exec('mysql --version'),
            'sys' => php_uname('s'),
            'path' => $this->laravel->path(),
            'base-path' => $this->laravel->basePath(),
            'storage' => $this->laravel->storagePath(),
            'upStatus' => ($this->laravel->isDownForMaintenance()) ? 'siteUp' : 'siteDown',
        ];
        $this->fix = array_merge($this->fix, $data);
        $this->fix['tmpNow'] = tempnam(sys_get_temp_dir(), 'myApp_');
        $this->fix['tmp'] = sys_get_temp_dir();

        return $data;
    }

    /**
     * Get some system info.
     */
    protected function sysInfo()
    {
        $this->warn($this->t('sysinfo'));
        $headers = [
            $this->t('key'),
            $this->t('value'),
        ];
        $table = [];
        foreach ($this->getSysInfo() as $key => $value) {
            (!empty($value)) ? $table[] = [$this->t($key), $value] : '';
        }
        $this->table($headers, $table);
        $this->line(PHP_EOL);
    }

    /**
     * Get cpu info.
     */
    protected function cpuInfo()
    {
        $this->info($this->t('check-math'));
        for ($_i = 0; $_i <= 10; $_i++) {
            $this->msg($this->t('i-can-count') . ': ' . $_i);
        }
        $cpuLoad = sys_getloadavg();
        if (is_array($cpuLoad)) {
            foreach ($cpuLoad as $k => &$cpu) {
                $cpu = 'CPU[' . $k . '] :' . number_format($cpu, 2, '.', '');
            }
            $cpuLoad = implode(', ', $cpuLoad);
        }
        $this->comment(PHP_EOL . 'cpu-load' . ' - ' . $cpuLoad . PHP_EOL);
    }

    /**
     * @param $what
     * @return string
     */
    protected function letChoose($what)
    {
        return $this->choice($this->t($what), ['yes' => 'yes', 'no' => 'no']);
    }

    /**
     * @param $choice
     */
    protected function yourChoice($choice)
    {
        $key = $choice . '.' . $this->getRandom($this->fix['cnt'][$choice]);
        $this->msg($this->t('choice') . ': ', 'magenta');
        $this->msg($this->t($key), 'cyan');
    }

    /**
     * Get logs.
     */
    protected function getLogs()
    {
        $errors = [];
        $this->fix['logs'] = $this->checkLogs();
        $this->info($this->t('checking-logs') . ' ' . $this->t('found') . ':: ' . count($this->fix['logs']));
        $bar = $this->output->createProgressBar($this->fix['logs']);
        foreach ($this->fix['logs'] as $key => $value) {
            $log = File::get($value);
            if (strlen($log)) {
                $log = explode(PHP_EOL, $log);
                $linesCounter = count($log);
                if ($key == 'laravel') {
                    for ($_i = ($linesCounter - 1); $_i >= 0; $_i--) {
                        if (stristr($log[$_i], 'Stack trace')) {
                            $log = $log[($_i - 1)];
                            continue;
                        }
                    }
                } else {
                    $log = $log[($linesCounter - 2)];
                }
                $this->fix['errors'][$key] = $log;
                $log = str_split($log, 70);
                $log = implode(PHP_EOL, $log) . PHP_EOL;
                $errors[] = [$key . ' :: ' . $log];
                $bar->advance();
            }
        }
        $bar->finish();
        $this->line(PHP_EOL);
        $headers = [$this->t('error')];
        $this->table($headers, $errors);
    }

    /**
     * @return array
     */
    protected function checkLogs()
    {
        $laravel = null;
        if (env('APP_DEBUG')) {
            $laravel = 'laravel.log';
            $logMode = env('APP_LOG');
            if ($logMode) {
                switch ($logMode) {
                    case 'daily':
                        $laravel = str_replace('.log', date('Y-m-d.log', $laravel));
                        break;
                    case 'syslog':
                        break;
                    case 'errorlog':
                        break;
                    case 'single':
                }
            }
        }

        $logs = [
            'php' => ini_get('error_log'),
            'apache' => '/var/log/apache2/error.log',
            'laravel' => $this->laravel->storagePath() . '/logs/' . $laravel,
        ];

        foreach ($logs as $key => $value) {
            if (empty(trim($value)) || !File::exists($value)) {
                unset($logs[$key]);
            }
        }

        return $logs;
    }

    /**
     *
     */
    protected function lvAscii()
    {
        $l = 'ICBfICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBfIAogfCB8ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfCB8CiB8IHwgICBfXyBfICAgXyBfXyAgICBfXyBfICBfXyAgIF9fICAgX19fICB8IHwKIHwgfCAgLyBfYCB8IHwgJ19ffCAgLyBfYCB8IFwgXCAvIC8gIC8gXyBcIHwgfAogfCB8IHwgKF98IHwgfCB8ICAgIHwgKF98IHwgIFwgViAvICB8ICBfXy8gfCB8CiB8X3wgIFxfXyxffCB8X3wgICAgIFxfXyxffCAgIFxfLyAgICBcX19ffCB8X3w=';
        $this->msg(PHP_EOL . base64_decode($l) . PHP_EOL, 'red', 'white');
    }

    /**
     * @param int $maxRange
     * @return int
     */
    protected function getRandom($maxRange = 10)
    {
        return rand(1, $maxRange);
    }

    /**
     * @param int $max
     * @return array
     */
    protected function getRandomise($max = 10)
    {
        $randomise = [];
        for ($_i = 0; $_i < $max; $_i++) {
            if (!$randomise) {
                $randomise[] = $this->getRandom($max);
                continue;
            }
            foreach ($randomise as $value) {
                $rand = $this->getRandom($max);
                if (!in_array($rand, $randomise)) {
                    $randomise[] = $rand;
                    $dots = array_fill(1, $rand, '.');
                    $dots = implode('', $dots);
                    $this->info($dots);
                    continue;
                }
            }
        }

        (isset($this->fix['morse'])) ? : $this->msg(PHP_EOL . PHP_EOL . $this->t('morse') . PHP_EOL, 'black', 'cyan');
        $this->fix['morse'] = true;

        return $randomise;
    }

    /**
     * @return array
     */
    protected function randomQuote()
    {
        $quotes = [];
        foreach ($this->getRandomise(count($this->fixesList())) as $value) {
            $quotes[] = $this->t('quote.' . $value) . PHP_EOL;
        }

        return $quotes;
    }

    /**
     *
     */
    protected function cowSay()
    {
        $this->line(PHP_EOL);
        $this->msg('< ' . $this->t('quote.' . ($this->getRandom($this->fix['cnt']['quote']) - 1)) . ' >', 'black', 'white');
        $this->msg($this->cow(), 'white');
    }

    /**
     * @return bool|string
     */
    protected function cow()
    {
        return base64_decode('IC0tLS0tLQogICAgICAgIFwgICBeX19eCiAgICAgICAgIFwgIChvbylcX19fX19fXwogICAgICAgICAgICAoX18pXCAgICAgICApXC9cCiAgICAgICAgICAgICAgICB8fC0tLS13IHwKICAgICAgICAgICAgICAgIHx8ICAgICB8fA==');
    }

    /**
     * @return array
     */
    protected function shellColors()
    {
        return [
            'black' => '0;30',
            'red' => '0;31',
            'green' => '0;32',
            'orange' => '0;33',
            'brown' => '0;33',
            'blue' => '0;34',
            'purple' => '0;35',
            'cyan' => '0;36',
            'light-gray' => '0;37',
            'dark-gray' => '1;30',
            'light-red' => '1;31',
            'light-green' => '1;32',
            'yellow' => '1;33',
            'light-blue' => '1;34',
            'magenta' => '1;35',
            'light-cyan' => '1;36',
            'white' => '1;37',
        ];
    }

    /**
     * @param string $string
     * @param string $color
     * @return string
     */
    protected function colorized($string = '', $color = 'white')
    {
        $code = ((!isset($this->shellColors()[$color])) ?
                $this->shellColors()['white']
                :
                $this->shellColors()[$color]) . 'm';
        $colorized = "\033[$code " . $string . "\e[0m";

        return $colorized;
    }

    /**
     * @param $string
     * @param string $color
     * @param null $background
     * @param array $options
     */
    protected function msg($string, $color = 'white', $background = null, $options = [])
    {
        $style = new OutputFormatterStyle();
        if ($options) {
            foreach ($options as $value) {
                $style->setOption($value);
            }
        }
        $style->setForeground($color);
        $style->setBackground($background);
        $this->output->getFormatter()->setStyle('colorized', $style);
        $this->output->writeln('<colorized>' . $string . '</colorized>');
    }

}

