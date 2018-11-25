<?php


namespace Beavor\Commands;

use Beavor\Actions\BuildClass;
use Beavor\Helpers\DataExtractor;
use Beavor\Helpers\SanitizedSourceString;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateDto extends Command
{
    /** @var  OutputInterface */
    private $output;

    protected function configure()
    {
        $this
            ->addOption('class', 'c', InputOption::VALUE_OPTIONAL, 'Name of the class')
            ->addOption('namespace', 'N', InputOption::VALUE_OPTIONAL, 'Name of the namespace')
            ->addOption('json', 'j', InputOption::VALUE_OPTIONAL, 'JSON to generate class from');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $questionHelper = $this->getHelper('question');
        $this->output->write("Let's get started and generate a Dto !\n");
        $className = $input->getOption('class') ?: $questionHelper->ask($input, $this->output, new Question("What is the class name ?\n"));
        $namespaceName = $input->getOption('namespace') ?: $questionHelper->ask($input, $this->output, new Question("And its namespace ?\n"));
        $data = $input->getOption('json') ?: $questionHelper->ask($input, $this->output, new Question("Your JSON / XML ?"));
        $data = (new DataExtractor)->getValue($data);
        $classes = (new BuildClass)->buildRootClass($data, $className, $namespaceName);
        foreach ($classes as $class) {
            $this->generateFile($class);
        }

    }

    /**
     * @param $className
     * @param $class
     */
    protected function generateFile(ClassType $class)
    {
        $namespace = $class->getNamespace();

        $psr4 = require getcwd() ."/vendor/composer/autoload_psr4.php";
        $targetDirectory = getcwd() ;
        foreach ($psr4 as $psr4_namespace => $dir){
            $targetDirectory = str_replace($psr4_namespace, current($dir)."/", $class->getNamespace()->getName());
        }

        // dir doesn't exist, make it
        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0777, true) && !is_dir($targetDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $targetDirectory));
        }

        $fileName = $targetDirectory . "/" . $class->getName() . ".php";
        $fileContent = "<?php\n\r" . $namespace . $class;
        file_put_contents($fileName, $fileContent);

        $this->output->writeln("Generated $fileName");
    }


}
