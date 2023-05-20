<?php
namespace WondPHP\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ModelCommand extends Command
{
    protected function configure()
    {
        $this
            // 命令的名称 （"php console_command" 后面的部分）
            ->setName('model:create')
            // 运行 "php console_command list" 时的简短描述
            ->setDescription('Create new model')
            // 运行命令时使用 "--help" 选项时的完整命令描述
            ->setHelp('This command allow you to create models...')
            // 配置一个参数
            ->addArgument('name', InputArgument::REQUIRED, 'what\'s model you want to create ?')
            // 配置一个可选参数
            ->addArgument('optional_argument', InputArgument::OPTIONAL, 'this is a optional argument');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // 你想要做的任何操作
        $optional_argument = $input->getArgument('optional_argument');

        $output->writeln('creating...');
        $output->writeln('created ' . $input->getArgument('name') . ' model success !');

        if ($optional_argument)
            $output->writeln('optional argument is ' . $optional_argument);

        $output->writeln('the end.');
        return Command::SUCCESS;

    }
}