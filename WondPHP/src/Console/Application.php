<?php
namespace WondPHP\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use WondPHP\App;
use WondPHP\Command\ModelCommand;

/**
 * @author  lenix 
 */
class Application extends BaseApplication
{
    private $kernel;
    private $commandsRegistered = false;
    private $registrationErrors = [];

    protected $defaultCommands = [
        'model' =>ModelCommand::class,
        // 'help'             => Help::class,
        // 'list'             => Lists::class,
        // 'clear'            => Clear::class,
        // 'make:command'     => MakeCommand::class,
        // 'make:controller'  => Controller::class,
        // 'make:model'       => Model::class,
        // 'make:middleware'  => Middleware::class,
        // 'make:validate'    => Validate::class,
        // 'make:event'       => Event::class,
        // 'make:listener'    => Listener::class,
        // 'make:service'     => Service::class,
        // 'make:subscribe'   => Subscribe::class,
        // 'optimize:route'   => Route::class,
        // 'optimize:schema'  => Schema::class,
        // 'run'              => RunServer::class,
        // 'version'          => Version::class,
        // 'route:list'       => RouteList::class,
        // 'service:discover' => ServiceDiscover::class,
        // 'vendor:publish'   => VendorPublish::class,
    ];

    public function __construct(App $kernel)
    {
        $this->kernel = $kernel;

        parent::__construct('WondPHP', App::VERSION);

        $inputDefinition = $this->getDefinition();
        $inputDefinition->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnvironment()));
        $inputDefinition->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));
    }

    /**
     * Gets the Kernel associated with this Console.
     *
     * @return KernelInterface A KernelInterface instance
     */
    public function getKernel()
    {
        return $this->kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if ($this->kernel->getContainer()->has('services_resetter')) {
            $this->kernel->getContainer()->get('services_resetter')->reset();
        }
    }

    /**
     * Runs the current application.
     *
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();

        if ($this->registrationErrors) {
            $this->renderRegistrationErrors($input, $output);
        }

        // $this->setDispatcher($this->kernel->getContainer()->get('event_dispatcher'));////////////todo?

        return parent::doRun($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        if (!$command instanceof ListCommand) {
            if ($this->registrationErrors) {
                $this->renderRegistrationErrors($input, $output);
                $this->registrationErrors = [];
            }

            return parent::doRunCommand($command, $input, $output);
        }

        $returnCode = parent::doRunCommand($command, $input, $output);

        if ($this->registrationErrors) {
            $this->renderRegistrationErrors($input, $output);
            $this->registrationErrors = [];
        }

        return $returnCode;
    }

    /**
     * {@inheritdoc}
     */
    public function find($name)
    {
        $this->registerCommands();

        return parent::find($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $this->registerCommands();

        $command = parent::get($name);

        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->kernel->getContainer());
        }

        return $command;
    }

    /**
     * {@inheritdoc}
     */
    public function all($namespace = null)
    {
        $this->registerCommands();

        return parent::all($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getLongVersion()
    {
        return parent::getLongVersion().sprintf(' (env: <comment>%s</>, debug: <comment>%s</>)', $this->kernel->getEnvironment(), $this->kernel->isDebug() ? 'true' : 'false');
    }

    public function add(Command $command)
    {
        $this->registerCommands();

        return parent::add($command);
    }

    protected function registerCommands()
    {
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->kernel->boot();

        $container = $this->kernel->getContainer();

        

        if ($container->has('console.command_loader')) {
            $this->setCommandLoader($container->get('console.command_loader'));
        }


        $commands =  config('console.commands') ?? [];
        $commands = array_merge($this->defaultCommands, $commands);

        foreach ($commands as $key => $command) {
            if (is_subclass_of($command, Command::class)) {

                // 注册指令
                $this->add( new $command);
            }
        }
    }

    private function renderRegistrationErrors(InputInterface $input, OutputInterface $output)
    {
        if ($output instanceof ConsoleOutputInterface) {
            $output = $output->getErrorOutput();
        }

        (new SymfonyStyle($input, $output))->warning('Some commands could not be registered:');

        foreach ($this->registrationErrors as $error) {
            if (method_exists($this, 'doRenderThrowable')) {
                $this->doRenderThrowable($error, $output);
            } else {
                if (!$error instanceof \Exception) {
                    $error = new FatalThrowableError($error);
                }

                $this->doRenderException($error, $output);
            }
        }
    }
}
