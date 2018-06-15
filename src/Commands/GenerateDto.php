<?php


namespace Beavor\Commands;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\PhpNamespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateDto extends Command
{
    protected function configure()
    {
        $this
            ->addOption('class', 'c', InputOption::VALUE_OPTIONAL, 'Name of the class')
            ->addOption('namespace', 'N', InputOption::VALUE_OPTIONAL, 'Name of the namespace')
            ->addOption('json', 'j', InputOption::VALUE_OPTIONAL, 'JSON to generate class from');
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');
        $output->write("Let's get started and generate a Dto !\n");
        $className = $input->getOption('class') ?: $questionHelper->ask($input, $output, new Question("What is the class name ?\n"));
        $namespaceName = $input->getOption('namespace') ?: $questionHelper->ask($input, $output, new Question("And its namespace ?\n"));

        $class = new ClassType($className, new PhpNamespace($namespaceName));
        $class->addComment("Auto generated Beavor DTO class");
        $json = $input->getOption('json') ?: $questionHelper->ask($input, $output, new Question("Your JSON ?"));
        preg_replace("/\n?\r?/", "", $json);
        $data = json_decode($json, true);
        $this->buildClassFromJson($data, $class, new PhpNamespace($class->getNamespace()->getName() . "\\" . $class->getName()));

        $this->generateFile($class);
    }

    /**
     * @param           $data
     * @param ClassType $class
     * @param           $property
     */
    protected function buildClassFromJson($data, $class, $namespace = null)
    {
        foreach ($data as $propertyName => $value) {
            $this->buildProperty($class, $namespace, $propertyName, $value);
        }
        $this->generateFile($class);
    }

    /**
     * @param $className
     * @param $class
     */
    protected function generateFile($class)
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

        file_put_contents($targetDirectory."/" . $class->getName() . ".php", "<?php\n\r" . $namespace . $class);
    }

    /**
     * @param $class
     * @param $namespace
     * @param $propertyName
     * @param $value
     */
    protected function buildProperty($class, $namespace, $propertyName, $value)
    {
        // not a property
        if (!is_string($propertyName)) {
            return;
        }
        $class->addProperty($propertyName)->setVisibility('public');
        $getter = $class->addMethod("get".ucfirst($propertyName));
        $getter->setBody("return \$this->$propertyName;");

        // no more action is needed
        if (!is_array($value)) {
            return;
        }
        $class->getProperty($propertyName)->setValue([]);
        //we dont know what it has, so we default to a simple array
        if (count($value) === 0) {
            $class->getProperty($propertyName)->addComment("@var array");
            return;
        }
        $nestedClassName = ucfirst($propertyName . "Item");
        $newNestedClass = new ClassType($nestedClassName, $namespace ?: $class->getNamespace());
        $newNameSpace = new PhpNamespace($namespace->getName() ?: $class->getNamespace()->getName() . "\\" . $nestedClassName);
        $newClassFullName = "\\" . $newNameSpace->getName() . "\\" . $nestedClassName;
        // Class collection Has Many
        if ((array_key_exists(0, $value))) {
            if (is_array($value[0])) {
                $this->buildProperty($newNestedClass,$newNameSpace, $propertyName, $value[0]);
                $class->getProperty($propertyName)->addComment("@var ${newClassFullName}[]");
                $getter->addComment("@return ${newClassFullName}[]");
                $this->buildClassFromJson($value[0], $newNestedClass, $newNameSpace);
                return;
            }
        }

        // class property Has One
        $class->getProperty($propertyName)->addComment("@var $newClassFullName");
        $getter->addComment("@return $newClassFullName");
        $this->buildClassFromJson($value, $newNestedClass, $newNameSpace);



    }

}